<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Claims_Logic
 *
 * @class Inventor_Claims_Logic
 * @package Inventor_Claims/Classes
 * @author Pragmatic Mates
 */
class Inventor_Claims_Logic {
    /**
     * Initialize Claims functionality
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'process_claim_form' ), 9999 );
        add_action( 'transition_post_status', array( __CLASS__, 'claim_transition' ), 10, 3 );
        add_action( 'wp_ajax_nopriv_inventor_claims_ajax_can_claim', array( __CLASS__, 'ajax_can_claim' ) );
        add_action( 'wp_ajax_inventor_claims_ajax_can_claim', array( __CLASS__, 'ajax_can_claim' ) );
        add_action( 'cmb2_init', array( __CLASS__, 'add_verified_field' ), 9999 );  # need to be called after all post type registration

        add_filter( 'inventor_mail_actions_choices', array( __CLASS__, 'mail_actions_choices' ) );
        add_filter( 'inventor_listing_title', array( __CLASS__, 'listing_title' ), 10, 2 );
    }

    /**
     * Adds claims mail actions
     *
     * @access public
     * @param array $choices
     * @return array
     */
    public static function mail_actions_choices( $choices ) {
        $choices[ INVENTOR_MAIL_ACTION_CLAIMED_LISTING ] = __( 'Listing was claimed', 'inventor-claims' );
        $choices[ INVENTOR_MAIL_ACTION_CLAIM_APPROVED ] = __( 'Claim was approved', 'inventor-claims' );
        return $choices;
    }

    /**
     * List of allowed post types for claiming
     *
     * @access public
     * @return array
     */
    public static function get_allowed_post_types() {
        return apply_filters( 'inventor_claims_allowed_listing_post_types', array() );
    }

    /**
     * Claim button
     *
     * @access public
     * @param $post_id
     * @return string
     */
    public static function get_claim_button( $post_id ) {
        $verified = self::listing_is_verified( $post_id );

        $verified_string = '';

        if ( $verified ) {
            // show verified flag
            $verified_string = '<span class="listing-verified">' . esc_attr__( 'Verified', 'inventor-claims' ) . '</span>';
        } else {
            // render claim button
            $post_type = get_post_type( $post_id );
            $author_id = null;
            $post = get_post( $post_id );

            if ( $post ) {
                $author_id = $post->post_author;
            }

            if ( in_array( $post_type, self::get_allowed_post_types() ) ) {
                $config = self::get_config();
                $page = $config['page'];

                $attrs = array(
                    'listing_id'    => $post_id,
                    'claim_page'    => $page,
                    'is_verified'   => $verified,
                    'author_id'     => $author_id
                );

                $verified_string = Inventor_Template_Loader::load( 'claims-button', $attrs, $plugin_dir = INVENTOR_CLAIMS_DIR );
            }
        }

        return $verified_string;
    }

    /**
     * Alters listing title. Appends verified flag/claim button.
     *
     * @access public
     * @param string $title
     * @param int $post_id
     * @return string
     */
    public static function listing_title( $title, $post_id ) {
        $verified_string = self::get_claim_button( $post_id );
        $title = $title . ' ' . $verified_string;

        return $title;
    }

    /**
     * Adds verified field to flags meta box
     *
     * @access public
     * @return void
     */
    public static function add_verified_field() {
        $post_types = Inventor_Post_Types::get_listing_post_types();

        foreach ( $post_types as $post_type ) {
            $metabox_id = INVENTOR_LISTING_PREFIX . $post_type . '_flags';
            $metabox = CMB2_Boxes::get( $metabox_id );

            if ( ! empty( $metabox ) ) {
                $metabox->add_field(array(
                    'name' => __('Verified', 'inventor-claims'),
                    'id' => INVENTOR_LISTING_PREFIX . 'verified',
                    'type' => 'checkbox',
                    'default' => apply_filters( 'inventor_metabox_field_default', false, INVENTOR_LISTING_PREFIX . $post_type . '_flags', INVENTOR_LISTING_PREFIX . 'verified', $post_type ),
                ));
            }
        }
    }

    /**
     * Gets config
     *
     * @access public
     * @return array
     */
    public static function get_config() {
        $page = get_theme_mod( 'inventor_claims_page', false );

        $config = array(
            "page"      => $page,
        );

        return $config;
    }

    /**
     * Claims listing if claim was published
     *
     * @access public
     * @param $new_status
     * @param $old_status
     * @param $post
     * @return void
     */
    public static function claim_transition( $new_status, $old_status, $post ) {
        $post_type = get_post_type( $post );

        if ( $post_type != 'claim' ) {
            return;
        }

        if ( $new_status != 'publish' || ! in_array( $old_status, array( 'new', 'auto-draft', 'draft', 'pending', 'future' ) ) ) {
            return;
        }

        $verified = get_post_meta( get_the_ID(), INVENTOR_CLAIM_PREFIX . 'verified', true );
        if( $verified ) {
            return;
        }

        # send emails to old and new author
        self::mail_claim_approved( $post );

        # set new author and mark as verified
        $claim_author = $post->post_author;
        $listing_id = get_post_meta( $post->ID, INVENTOR_CLAIM_PREFIX . 'listing_id', true );
        $listing = get_post( $listing_id );
        $listing->post_author = $claim_author;
        wp_update_post( $listing );
        update_post_meta( $listing->ID, INVENTOR_LISTING_PREFIX . 'verified', true );
    }

    /**
     * Sends emails about approved claim to old and new listing author
     *
     * @access public
     * @param $claim
     * @return void
     */
    public static function mail_claim_approved( $claim ) {
        $listing_id = get_post_meta( get_the_ID(), INVENTOR_CLAIM_PREFIX . 'listing_id', true );
        $listing = get_post( $listing_id );
        $listing_title = get_the_title( $listing_id );

        # listing author
        $listing_author = $listing->post_author;
        $listing_author_email = get_the_author_meta( 'email', $listing_author );
        $listing_author_name = get_the_author_meta( 'nicename', $listing_author );

        # claim author
        $claim_author = $claim->post_author;
        $claim_author_email = get_the_author_meta( 'email', $claim_author );
        $claim_author_name = get_the_author_meta( 'nicename', $claim_author );

        # template args
        $template_args = array(
            'listing' => $listing_title,
            'listing_type' => Inventor_Post_Types::get_listing_type_name( $listing_id ),
            'url' => get_permalink( $listing->ID ),
            'listing_author_name'  => $listing_author_name,
            'listing_author_email' => $listing_author_email,
            'claim_author_name'  => $claim_author_name,
            'claim_author_email' => $claim_author_email

            # TODO: add arguments from claim object
        );

        # subject
        $subject = __( sprintf( 'Claim of %s approved', $listing_title ), 'inventor-claims' );
        $subject = apply_filters( 'inventor_mail_subject', $subject, INVENTOR_MAIL_ACTION_CLAIM_APPROVED, $template_args );

        # message
        $body = __( sprintf( 'New author of listing %s is %s', $listing_title, $claim_author_name ), 'inventor-claims');
        $body = apply_filters( 'inventor_mail_body', $body, INVENTOR_MAIL_ACTION_CLAIM_APPROVED, $template_args );

        # recipients
        $recipients = array();

        # admins
        $admin_email = get_bloginfo( 'admin_email' );
//        $admin = get_user_by( 'email', $admin_email );
//        $admin_name = $admin->user_nicename;
        $website_name = wp_specialchars_decode( get_option( 'blogname' ) );
        $from_name = $website_name;

        // notify admins
        $admins = Inventor_Utilities::get_site_admins();

        foreach ( $admins as $admin_login ) {
            $admin = get_user_by( 'login', $admin_login );
            $recipients[] = $admin->user_email;
        }

        # recipients
        $recipients[] = $listing_author_email;
        $recipients[] = $claim_author_email;
        $recipients = array_unique( $recipients );

        # headers
        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $from_name, $admin_email );

        foreach ( $recipients as $email ) {
            wp_mail( $email, $subject, $body, $headers );
        }
    }

    /**
     * Ajax request. Checks if user can claim listing.
     *
     * @access public
     * @return string
     */
    public static function ajax_can_claim() {
        header( 'HTTP/1.0 200 OK' );
        header( 'Content-Type: application/json' );

        if ( ! empty( $_GET['id'] ) ) {
            $listing_already_verified = self::listing_is_verified( $_GET['id'] );

            if ( $listing_already_verified ) {
                $data = array(
                    'success' => false,
                    'message' => __( 'Listing is already verified.', 'inventor-claims' ),
                );
            } else {
                $user_already_claimed = self::user_already_claimed( $_GET['id'], get_current_user_id() );

                if ( $user_already_claimed ) {
                    $data = array(
                        'success' => false,
                        'message' => __( 'You already claimed this listing.', 'inventor-claims' ),
                    );
                } else {
                    $data = array(
                        'success' => true,
                    );
                }
            }
        } else {
            $data = array(
                'success' => false,
                'message' => __( 'Listing ID is missing.', 'inventor-claims' ),
            );
        }

        echo json_encode( $data );
        exit();
    }

    /**
     * Saves claim
     *
     * @access public
     * @param $listing_id int
     * @param $user_id int
     * @param $name string
     * @param $email string
     * @param $phone string
     * @param $message string
     * @return int
     */
    public static function save_claim( $listing_id, $user_id, $name, $email, $phone, $message ) {
        $claim_id = wp_insert_post( array(
            'post_type'     => 'claim',
            'post_status'   => 'pending',
            'post_author'   => $user_id,
            'post_title'    => get_the_title( $listing_id ),
        ) );

        update_post_meta( $claim_id, INVENTOR_CLAIM_PREFIX . 'listing_id', $listing_id );
        update_post_meta( $claim_id, INVENTOR_CLAIM_PREFIX . 'name', $name );
        update_post_meta( $claim_id, INVENTOR_CLAIM_PREFIX . 'email', $email );
        update_post_meta( $claim_id, INVENTOR_CLAIM_PREFIX . 'phone', $phone );
        update_post_meta( $claim_id, INVENTOR_CLAIM_PREFIX . 'message', $message );

        return $claim_id;
    }

    /**
     * Returns claim by user_id and listing_id
     *
     * @access public
     * @param $listing_id int
     * @param $user_id int
     * @return object
     */
    public static function get_claim( $listing_id, $user_id ) {
        $wp_query = new WP_Query( array(
            'post_type'     => 'claim',
            'post_status'   => 'any',
            'author'        => $user_id,
            'meta_query'        => array(
                array(
                    'key' => INVENTOR_CLAIM_PREFIX . 'listing_id',
                    'value' => $listing_id,
                )
            )
        ) );

        if ( $wp_query->post_count <= 0 ) {
            return null;
        }

        return $wp_query->posts[0];
    }

    /**
     * Checks if listing is already verified
     *
     * @access public
     * @param $listing_id int
     * @return bool
     */
    public static function listing_is_verified( $listing_id ) {
        return get_post_meta( $listing_id, INVENTOR_LISTING_PREFIX . 'verified', true );
    }

    /**
     * Checks if user already claimed for specified listing
     *
     * @access public
     * @param $listing_id int
     * @param $user_id int
     * @return bool
     */
    public static function user_already_claimed( $listing_id, $user_id ) {
        $claim = self::get_claim( $listing_id, $user_id );
        return ( ! empty( $claim ) );
    }

    /**
     * Gets user claims
     *
     * @access public
     * @param int $user_id
     * @return WP_Query
     */
    public static function get_claims_by_user( $user_id = null ) {
        return new WP_Query( array(
            'post_type'         => 'claim',
            'author'		    => $user_id,
            'post_status'       => 'any',
        ) );
    }

    /**
     * Process claim form
     *
     * @access public
     * @return void
     */
    public static function process_claim_form() {
        if ( ! isset( $_POST['claim_form'] ) || empty( $_POST['listing_id'] ) ) {
            return;
        }

        if ( class_exists( 'Inventor_Recaptcha' ) && Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) {
            if ( array_key_exists( 'g-recaptcha-response', $_POST ) ) {
                $is_recaptcha_valid = Inventor_Recaptcha_Logic::is_recaptcha_valid( $_POST['g-recaptcha-response'] );

                if ( ! $is_recaptcha_valid ) {
                    Inventor_Utilities::show_message( 'danger', __( 'reCAPTCHA is not valid.', 'inventor-claims' ) );
                    return;
                }
            }
        }

        $user_already_claimed = self::user_already_claimed( $_POST['listing_id'], get_current_user_id() );

        if ( $user_already_claimed ) {
            Inventor_Utilities::show_message( 'danger', __( 'You already claimed this listing.', 'inventor-claims' ) );
            return;
        }

        $listing = get_post( $_POST['listing_id'] );
        $email = esc_html( $_POST['email'] );
        $phone = esc_html( $_POST['phone'] );
        $name = esc_html( $_POST['fullname'] );
        $message = esc_html( $_POST['message'] );

        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $name, $email );

        # template args
        $template_args = array(
            'listing' => get_the_title( $listing ),
            'listing_type' => Inventor_Post_Types::get_listing_type_name( $listing->ID ),
            'url' => get_permalink( $listing->ID ),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
        );

        # subject
        $subject = __( sprintf( '%s has been claimed', get_the_title( $listing ) ), 'inventor-claims' );
        $subject = apply_filters( 'inventor_mail_subject', $subject, INVENTOR_MAIL_ACTION_CLAIMED_LISTING, $template_args );

        # body
        $body = '';
        $body = apply_filters( 'inventor_mail_body', $body, INVENTOR_MAIL_ACTION_CLAIMED_LISTING, $template_args );

        if ( empty( $body ) ) {
            ob_start();
            include Inventor_Template_Loader::locate( 'claims-mail', $plugin_dir = INVENTOR_CLAIMS_DIR );
            $body = ob_get_contents();
            ob_end_clean();
        }

        # recipients
        $emails = array();

        # author
        $emails[] = get_the_author_meta( 'user_email', $listing->post_author );

        # admins
        $admins = Inventor_Utilities::get_site_admins();

        foreach ( $admins as $admin_login ) {
            $admin = get_user_by( 'login', $admin_login );
            $admin->user_email;
            $emails[] = $admin->user_email;
        }

        $emails = array_unique( $emails );

        foreach ( $emails as $recipient_email ) {
            $status = wp_mail( $recipient_email, $subject, $body, $headers );
        }

        if ( ! empty( $status ) && 1 == $status ) {
            self::save_claim( $listing->ID, get_current_user_id(), $name, $email, $phone, $message );
            Inventor_Utilities::show_message( 'success', __( 'Message has been successfully sent. Please, wait for admin review.', 'inventor-claims' ) );
        } else {
            Inventor_Utilities::show_message( 'danger', __( 'Unable to send a message.', 'inventor-claims' ) );
        }

        // redirect to post
        $url = get_permalink( $_POST['listing_id'] );
        wp_redirect( $url );
        die();
    }
}

Inventor_Claims_Logic::init();
