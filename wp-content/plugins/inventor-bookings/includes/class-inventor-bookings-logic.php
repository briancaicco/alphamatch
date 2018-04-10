<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Logic
 *
 * @class Inventor_Bookings_Logic
 * @package Inventor/Classes
 * @property $allowed_post_types
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Logic {
    /**
     * Initialize property system
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'cmb2_init', array( __CLASS__, 'fields' ), 14 );
        add_action( 'init', array( __CLASS__, 'process_booking_request_form' ), 9999 );
        add_action( 'init', array( __CLASS__, 'process_booking_action_form' ), 9999 );
        add_action( 'inventor_payment_form_before', array( __CLASS__, 'payment_form_before' ), 10, 3 );
        add_action( 'inventor_payment_processed', array( __CLASS__, 'catch_payment' ), 10, 9 );

        add_filter( 'inventor_payment_types', array( __CLASS__, 'add_payment_types' ) );
        add_filter( 'inventor_prepare_payment', array( __CLASS__, 'prepare_payment' ), 10, 3 );
        add_filter( 'inventor_payment_form_price_value', array( __CLASS__, 'payment_price_value' ), 10, 3 );

//        add_filter( 'inventor_packages_metabox_permissions', array( __CLASS__, 'metabox_permissions' ), 11 );
    }

    /**
     * Adds booking metabox to the package metabox permissions
     *
     * @access public
     * @param $fields array
     * @return array
     */
    public static function metabox_permissions( $fields ) {
        $fields['bookings'] = __( 'Bookings', 'inventor-bookings' );
        return $fields;
    }

    /**
     * List of allowed post types for bookings
     *
     * @access public
     * @return array
     */
    public static function get_allowed_post_types() {
        return apply_filters( 'inventor_bookings_allowed_listing_post_types', array() );
    }

    /**
     * Return array of booking statuses
     *
     * @access public
     * @return array
     */
    public static function booking_statuses() {
        return apply_filters( 'inventor_bookings_statuses', array(
            'awaiting_approval' => __( 'Awaiting approval', 'inventor-bookings' ),
            'pending_payment'   => __( 'Pending payment', 'inventor-bookings' ),
            'approved'		    => __( 'Approved', 'inventor-bookings' ),
            'cancelled'		    => __( 'Cancelled', 'inventor-bookings' ),
            'no_show'		    => __( 'No-show', 'inventor-bookings' ),
            'completed'		    => __( 'Completed', 'inventor-bookings' )
        ) );
    }

    /**
     * Adds booking payment type
     *
     * @access public
     * @param array $payment_types
     * @return array
     */
    public static function add_payment_types( $payment_types ) {
        $payment_types[] = 'booking';
        return $payment_types;
    }

    /**
     * Prepares payment data
     *
     * @access public
     * @param array $payment_data
     * @param string $payment_type
     * @param int $object_id
     * @return array
     */
    public static function prepare_payment( $payment_data, $payment_type, $object_id ) {
        if ( $payment_type != 'booking' ) {
            return $payment_data;
        }

        $payment_data['price'] = self::payment_price_value( null, $payment_type, $object_id );
        $listing = self::get_booking_listing( $object_id );

        if ( $payment_type == 'booking' ) {
            $payment_data['action_title'] = __( 'Booking', 'inventor-bookings' );
            $payment_data['description'] = sprintf( __( 'Booking of %s', 'inventor-bookings' ), $listing->post_title );
        }

        return $payment_data;
    }

    /**
     * Gets price value for payment object
     *
     * @access public
     * @param float $price
     * @param string $payment_type
     * @param int $object_id
     * @return float
     */
    public static function payment_price_value( $price, $payment_type, $object_id ) {
        if ( 'booking' == $payment_type ) {
            $price = get_post_meta( $object_id, INVENTOR_BOOKING_PREFIX . 'price', true );
        }

        return $price;
    }

    /**
     * Handles payment and decrements listing quantity
     *
     * @param bool $success
     * @param string $payment_type
     * @param int $object_id
     * @param float $price
     * @param string $currency_code
     * @param int $user_id
     * @return void
     */
    public static function catch_payment( $success, $gateway, $payment_type, $payment_id, $object_id, $price, $currency_code, $user_id, $billing_details ) {
        if( ! $success ) {
            return;
        }

        if ( $payment_type == 'booking' ) {
            update_post_meta( $object_id, INVENTOR_BOOKING_PREFIX . 'status', 'approved' );
            Inventor_Utilities::show_message( 'success', __( 'Booking has been successfully paid.', 'inventor-submission' ) );
        }
    }

    /**
     * Adds bookings metabox to allowed listing post types
     *
     * @access public
     * @return array
     */
    public static function fields() {
        foreach( self::get_allowed_post_types() as $post_type ) {
            $metabox_id = INVENTOR_LISTING_PREFIX . $post_type . '_' . INVENTOR_BOOKINGS_PREFIX . 'config';

            $bookings = new_cmb2_box( array(
                'id'            => $metabox_id,
                'title'         => __( 'Bookings configuration', 'inventor-bookings' ),
                'object_types'  => array( $post_type ),
                'context'       => 'advanced',
                'priority'      => 'low',
                'skip'          => 'true'
            ) );

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'enabled';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Enabled', 'inventor-bookings' ),
                    'description'       => __( 'Allow/disallow listing booking (also price needs to be set).', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'checkbox',
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'period_unit';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'id'                => $field_id,
                    'name'              => __( 'Period unit', 'inventor-bookings' ),
                    'description'       => __( 'Final booking price depends on booking range, period unit and listing price.', 'inventor-bookings' ),
                    'type'              => 'select',
                    'options'           => array(
                        'hour'              => __( 'Hour', 'inventor-bookings' ),
                        'day'               => __( 'Day', 'inventor-bookings' ),
                        'night'             => __( 'Night', 'inventor-bookings' ),
                    ),
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'min_period';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Min period', 'inventor-bookings' ),
                    'description'       => __( 'Minimum booking period required', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'text_small',
                    'attributes'        => array(
                        'type'              => 'number',
                        'min'               => '0',
                        'pattern'           => '\d*',
                    )
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'max_period';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Max period', 'inventor-bookings' ),
                    'description'       => __( 'Maximum booking period allowed', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'text_small',
                    'attributes'        => array(
                        'type'              => 'number',
                        'min'               => '0',
                        'pattern'           => '\d*',
                    )
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'price_per_person';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Price per person', 'inventor-bookings' ),
                    'description'       => __( 'Check if you want to calculate total booking price depending on number of persons.', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'checkbox',
                ) );
            }

            $min_persons = get_theme_mod( 'inventor_bookings_min_persons', 1 );
            $max_persons = get_theme_mod( 'inventor_bookings_max_persons', 10 );

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'min_persons';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Min persons', 'inventor-bookings' ),
                    'description'       => __( 'Minimum persons needed for booking', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'select',
                    'options'           => array_combine( range( $min_persons, $max_persons ), range( $min_persons, $max_persons ) ),
                    'attributes'        => array(
                        'type'              => 'number',
                        'min'               => '1',
                        'pattern'           => '\d*',
                    )
                ) );
            }

            // TODO: add validation (max >= min)
            $field_id = INVENTOR_BOOKINGS_PREFIX . 'max_persons';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Max persons', 'inventor-bookings' ),
                    'description'       => __( 'Total person capacity per booking', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'select',
                    'options'           => array_combine( range( $min_persons, $max_persons ), range( $min_persons, $max_persons ) ),
                    'attributes'        => array(
                        'type'              => 'number',
                        'min'               => '1',
                        'pattern'           => '\d*',
                    )
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'additional_information';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Additional information', 'inventor-bookings' ),
                    'description'       => __( 'Reservation details for customers', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'title',
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'check_in';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Check-in', 'inventor-bookings' ),
                    'description'       => __( 'When the user can check-in', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'text_time',
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'check_out';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Check-out', 'inventor-bookings' ),
                    'description'       => __( 'When the user must check-out', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'text_time',
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'cancellation_policy';
            if ( apply_filters( 'inventor_metabox_field_enabled', true, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'name'              => __( 'Cancellation policy', 'inventor-bookings' ),
                    'id'                => $field_id,
                    'type'              => 'textarea',
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'week_availability';
            if ( apply_filters( 'inventor_metabox_field_enabled', false, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'id'          	    => $field_id,
                    'type'        	    => 'week_availability',
                    'escape_cb'		    => array( 'Inventor_Bookings_Types_Week_Availability', 'escape' ),
                    'select_all_button' => false,
                    'name'              => __( 'Week availability', 'inventor-bookings' ),
                    'description'       => apply_filters( 'inventor_metabox_field_description', null, $metabox_id, $field_id, $post_type ),
                    'default'           => apply_filters( 'inventor_metabox_field_default', null, $metabox_id, $field_id, $post_type ),
                    'attributes'        => apply_filters( 'inventor_metabox_field_attributes', array(), $metabox_id, $field_id, $post_type ),
                ) );
            }

            $field_id = INVENTOR_BOOKINGS_PREFIX . 'exceptions';
            if ( apply_filters( 'inventor_metabox_field_enabled', false, $metabox_id, $field_id, $post_type ) ) {
                $bookings->add_field( array(
                    'id'                => INVENTOR_LISTING_PREFIX . 'exceptions_title',
                    'name'              => __( 'Exceptions', 'inventor' ),
                    'type'              => 'text_title',
                ) );

                $exceptions = $bookings->add_field( array(
                    'id'          => $field_id,
                    'type'        => 'group',
                    'title'       => 'text',
                    'options'     => array(
                        'group_title'   => __( 'Item', 'inventor-bookings' ),
                        'add_button'    => __( 'Add Another', 'inventor-bookings' ),
                        'remove_button' => __( 'Remove', 'inventor-bookings' ),
                    ),
                ) );

                $bookings->add_group_field( $exceptions, array(
                    'id'                => INVENTOR_BOOKINGS_PREFIX  . 'exception_date',
                    'name'              => __( 'Date', 'inventor' ),
                    'type'              => 'text_date_timestamp',
                ) );
            }
        }
    }

    /**
     * Process booking form
     *
     * @access public
     * @return void
     */
    public static function process_booking_request_form() {
        if ( ! isset( $_POST['booking_form'] ) || empty( $_POST['listing_id'] ) ) {
            return;
        }

        $booking_request_data = apply_filters( 'inventor_booking_request_data', $_POST );

        $listing_id = $booking_request_data['listing_id'];
        $listing = get_post( $listing_id );
        $book_from = empty( $booking_request_data['book_from'] ) ? '' : esc_html( $booking_request_data['book_from'] );
        $book_to = empty( $booking_request_data['book_to'] ) ? '' : esc_html( $booking_request_data['book_to'] );
        $number_of_persons = empty( $booking_request_data['number_of_persons'] ) ? '' : esc_html( $booking_request_data['number_of_persons'] );
        $name = empty( $booking_request_data['fullname'] ) ? '' : esc_html( $booking_request_data['fullname'] );
        $email = empty( $booking_request_data['email'] ) ? '' : esc_html( $booking_request_data['email'] );
        $phone = empty( $booking_request_data['phone'] ) ? '' : esc_html( $booking_request_data['phone'] );
        $message = empty( $booking_request_data['message'] ) ? '' : esc_html( $booking_request_data['message'] );

        $is_period_valid = self::is_period_valid( $listing_id, strtotime( $book_from ), strtotime( $book_to ) );

        if ( ! $is_period_valid ) {
            Inventor_Utilities::show_message( 'danger', __( 'Booking range is invalid.', 'inventor-bookings' ) );
            return;
        }

        $min_persons = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'min_persons', true );
        $max_persons = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'max_persons', true );

        if ( $number_of_persons < $min_persons || $number_of_persons > $max_persons ) {
            Inventor_Utilities::show_message( 'danger', __( 'Invalid number of persons.', 'inventor-bookings' ) );
            return;
        }

        $already_exists = self::booking_exists( $listing_id, strtotime( $book_from ), strtotime( $book_to ), $email );

        if ( $already_exists ) {
            Inventor_Utilities::show_message( 'danger', __( 'Same booking request already exists.', 'inventor-bookings' ) );
            return true;
        }

        $price = self::calculate_price( $listing_id, strtotime( $book_from ), strtotime( $book_to ), $number_of_persons );

        $hash = self::generate_unique_hash();

        // TODO: check if booking is available (check book period, num_persons and capacity)

        if ( class_exists( 'Inventor_Recaptcha' ) && Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) {
            if ( array_key_exists( 'g-recaptcha-response', $_POST ) ) {
                $is_recaptcha_valid = Inventor_Recaptcha_Logic::is_recaptcha_valid( $_POST['g-recaptcha-response'] );

                if ( ! $is_recaptcha_valid ) {
                    Inventor_Utilities::show_message( 'danger', __( 'reCAPTCHA is not valid.', 'inventor-bookings' ) );
                    return;
                }
            }
        }

        // Create booking post
        $booking_id = wp_insert_post( array(
            'post_type'     => 'booking',
            'post_title'    => __( 'Booking', 'inventor-bookings' ),
            'post_status'   => 'publish',
            'post_author'   => is_user_logged_in() ? get_current_user_id() : null,
        ) );

        // Append booking ID to its title
        wp_update_post( array(
            'ID'           => $booking_id,
            'post_title'   => get_the_title( $booking_id ) . ' #' . $booking_id
        ) );

        // Set booking meta attributes
        $default_booking_status = get_theme_mod( 'inventor_bookings_default_status', 'awaiting_approval' );
        $booking_status = apply_filters( 'inventor_bookings_default_status', $default_booking_status, $listing_id );

        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'status', $booking_status );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'book_from', strtotime( $book_from ) );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'book_to', strtotime( $book_to ) );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'price', $price );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'listing_id', $listing_id );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'number_of_persons', $number_of_persons );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'email', $email );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'name', $name );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'phone', $phone );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'message', $message );
        update_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'hash', $hash );

        // call custom WP action
        do_action( 'inventor_booking_created', $booking_id, $booking_request_data );

        // Prepare booking request email
        $subject = __( 'Booking request', 'inventor-bookings' );
        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $name, $email );

        ob_start();
        include Inventor_Template_Loader::locate( 'mails/booking-request', INVENTOR_BOOKINGS_DIR );
        $body = ob_get_contents();
        ob_end_clean();

        $recipients = array();

        // Author
        if ( ! empty( $_POST['receive_author'] ) ) {
            $recipients[] = get_the_author_meta( 'user_email', $listing->post_author );
        }

        // Admin
        if ( ! empty( $_POST['receive_admin'] ) ) {
            $recipients[] = get_bloginfo( 'admin_email' );

            // all admins
            $admins = Inventor_Utilities::get_site_admins();

            foreach ( $admins as $admin_login ) {
                $admin = get_user_by( 'login', $admin_login );
                $recipients[] = $admin->user_email;
            }
        }

        // Listing email
        if ( ! empty( $_POST['receive_listing_email'] ) ) {
            $listing_email = get_post_meta( $listing_id, INVENTOR_LISTING_PREFIX . 'email', true );

            if ( ! empty( $listing_email ) ) {
                $recipients[] = $email;
            }
        }

        // Default fallback
        if ( empty( $_POST['receive_admin'] ) && empty( $_POST['receive_author'] ) ) {
            $recipients[] = get_the_author_meta( 'user_email', $listing->post_author );
        }

        $recipients = array_unique( $recipients );

        foreach ( $recipients as $recipient ) {
            $status = wp_mail( $recipient, $subject, $body, $headers );
        }

        $success = ! empty( $status ) && 1 == $status;

        do_action(
            'inventor_bookings_request_sent',
            $success, $listing_id, $subject, $body, $booking_request_data,
            ! empty( $_POST['receive_author'] ), ! empty( $_POST['receive_admin'] ), ! empty( $_POST['receive_listing_email'] )
        );

        if ( $success ) {
            Inventor_Utilities::show_message( 'success', __( 'Booking request has been successfully sent.', 'inventor-bookings' ) );

        } else {
            Inventor_Utilities::show_message( 'danger', __( 'Unable to send a booking request.', 'inventor-bookings' ) );
        }

        // redirect to post
        $url = get_permalink( $listing_id );
        wp_redirect( $url );
        die();
    }

    /**
     * Process booking form
     *
     * @access public
     * @return void
     */
    public static function process_booking_action_form() {
        if ( isset( $_POST['cancel_booking'] ) ) {
            $action = 'cancel_booking';
        } elseif ( isset( $_POST['approve_booking'] ) ) {
            $action = 'approve_booking';
        } elseif ( isset( $_POST['complete_booking'] ) ) {
            $action = 'complete_booking';
        } elseif ( isset( $_POST['no_show_booking'] ) ) {
            $action = 'no_show_booking';
        }

        if ( ! isset( $action ) || empty( $_POST['hash'] ) ) {
            return;
        }

        $hash = esc_attr( $_POST['hash'] );

        $booking = self::get_booking_by_hash( $hash );

        if ( empty( $booking ) ) {
            return;
        }

        # get sender information by current user
        $user_id = get_current_user_id();
        $user_data = get_userdata( $user_id );
        $name = $user_data->display_name;
        $email = $user_data->user_email;
        $website_name = wp_specialchars_decode( get_option( 'blogname' ) );
        $from_name = $website_name;

        # headers
        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $from_name, $email );

        $subject = __( 'Booking reviewed', 'inventor-bookings' );
        $template = null;
        $new_booking_status = null;

        if ( $action == 'approve_booking' ) {
            $subject = __( 'Booking approved', 'inventor-bookings' );
            $template = 'mails/booking-approval';

            $has_to_be_prepaid = get_theme_mod( 'inventor_bookings_prepaid', false );
            # TODO: check if payment is required
            $new_booking_status = $has_to_be_prepaid ? 'pending_payment' : 'approved';

        } elseif ( $action == 'cancel_booking' ) {
            $subject = __( 'Booking cancelled', 'inventor-bookings' );
            $template = 'mails/booking-cancellation';
            $new_booking_status = 'cancelled';

        } elseif ( $action == 'complete_booking' ) {
            $subject = __( 'Booking marked as completed', 'inventor-bookings' );
//            $template = 'mails/booking-completion';
            $new_booking_status = 'completed';

        } elseif ( $action == 'no_show_booking' ) {
            $subject = __( 'Booking marked as no-show', 'inventor-bookings' );
//            $template = 'mails/booking-completion';
            $new_booking_status = 'no_show';
        }

        if ( empty( $new_booking_status ) ) {
            Inventor_Utilities::show_message( 'danger', __( 'Unable to moderate booking.', 'inventor-bookings' ) );
            return;
        }

        // Update booking status
        update_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'status', $new_booking_status );

        if ( ! empty( $template ) ) {
            ob_start();
            include Inventor_Template_Loader::locate( $template, INVENTOR_BOOKINGS_DIR );
            $body = ob_get_contents();
            ob_end_clean();

            // recipients
            $emails = array();

            // admin
            $emails[] = get_bloginfo( 'admin_email' );

            // all admins
            $admins = Inventor_Utilities::get_site_admins();

            foreach ( $admins as $admin_login ) {
                $admin = get_user_by( 'login', $admin_login );
                $emails[] = $admin->user_email;
            }

            // booking contact email
            $booking_email = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'email', true );
            $emails[] = $booking_email;

            $emails = array_unique( $emails );

            foreach ( $emails as $email ) {
                $status = wp_mail( $email, $subject, $body, $headers );
            }

            $success = ! empty( $status ) && 1 == $status;
        } else {
            $success = true;
        }

        if ( $success ) {
            Inventor_Utilities::show_message( 'success', __( 'Booking successfully moderated.', 'inventor-bookings' ) );
        } else {
            Inventor_Utilities::show_message( 'danger', __( "Booking status was changed but it wasn't able to send email.", 'inventor-bookings' ) );
        }
    }

    /**
     * Calculates booking price
     *
     * @access public
     * @param $listing_id
     * @param $book_from int
     * @param $book_to int
     * @param $number_of_persons int
     * @return bool
     */
    public static function calculate_price( $listing_id, $book_from, $book_to, $number_of_persons ) {
        $period = self::get_period( $listing_id, $book_from, $book_to, true );

        if ( ! $period ) {
            return null;
        }

        $price = get_post_meta( $listing_id, INVENTOR_LISTING_PREFIX . 'price', true );

        if ( empty ( $price ) ) {
            return null;
        }

        $price_per_person = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'price_per_person', true );

        $multiplier = $price_per_person ? $number_of_persons : 1;

        return $price * $period * $multiplier;
    }

    /**
     * Returns booking period
     *
     * @access public
     * @param $listing_id
     * @param $book_from int
     * @param $book_to int
     * @param $ignore_current_time bool
     * @return int
     */
    public static function get_period( $listing_id, $book_from, $book_to, $ignore_current_time = false ) {
        $period_unit = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'period_unit', true );

        if ( empty( $period_unit ) ) {
            return true;
        }

        if ( empty( $book_from ) || empty( $book_to ) ) {
            return null;
        }

        $now = current_time( 'timestamp' );

        if ( $book_to < $book_from || ! $ignore_current_time && $book_from < $now ) {
            return null;
        }

        $range = $book_to - $book_from;

        if ( $range <= 0 ) {
            return null;
        }

        if ( $period_unit == 'hour' ) {
            return floor( $range / (60*60) );
        }

        elseif ( $period_unit == 'day' ) {
            return floor( $range / (60*60*24) ) + 1;
        }

        elseif ( $period_unit == 'night' ) {
            return floor( $range / (60*60*24) );
        }

        return null;
    }

    /**
     * Checks if booking range is valid
     *
     * @access public
     * @param $listing_id
     * @param $book_from int
     * @param $book_to int
     * @return bool
     */
    public static function is_period_valid( $listing_id, $book_from, $book_to ) {
        $period = self::get_period( $listing_id, $book_from, $book_to );

        if ( empty( $period ) ) {
            return false;
        }

        $min_period = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'min_period', true );

        if ( ! empty( $min_period ) && $min_period > $period ) {
            return false;
        }

        $max_period = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'max_period', true );

        if ( ! empty( $max_period ) && $max_period < $period ) {
            return false;
        }

        return true;
    }

    /**
     * Checks if booking request already exists
     *
     * @access public
     * @param $listing_id
     * @param $book_from int
     * @param $book_to int
     * @param $email
     * @return bool
     */
    public static function booking_exists( $listing_id, $book_from, $book_to, $email ) {
        $query = new WP_Query( array(
            'post_type'         => 'booking',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'meta_query' => array(
                array(
                    'key'       => INVENTOR_BOOKING_PREFIX . 'book_from',
                    'value'     => $book_from,
                ),
                array(
                    'key'       => INVENTOR_BOOKING_PREFIX . 'book_to',
                    'value'     => $book_to,
                ),
                array(
                    'key'     => INVENTOR_BOOKING_PREFIX . 'email',
                    'value'   => $email,
                ),
                array(
                    'key'     => INVENTOR_BOOKING_PREFIX . 'listing_id',
                    'value'   => $listing_id,
                )
            )
        ) );

        return count( $query->posts ) > 0;
    }

    /**
     * Returns booking by its hash
     *
     * @access public
     * @param $hash string
     * @return WP_Post
     */
    public static function get_booking_by_hash( $hash ) {
        $query = new WP_Query( array(
            'post_type'         => 'booking',
            'posts_per_page'    => -1,
            'post_status'       => array( 'publish' ),
            'meta_query' => array(
                array(
                    'key'       => INVENTOR_BOOKING_PREFIX . 'hash',
                    'value'     => $hash,
                )
            )
        ) );

        return count( $query->posts ) > 0 ? $query->posts[0] : null;
    }

    /**
     * Generates unique booking hash
     *
     * @access public
     * @return string
     */
    public static function generate_unique_hash() {
        $hash = Inventor_Utilities::generate_hash();

        while( self::get_booking_by_hash( $hash ) != null ) {
            $hash = Inventor_Utilities::generate_hash();
        }

        return $hash;
    }

    /**
     * Returns listing for specified booking
     *
     * @access public
     * @param $booking_id
     * @return WP_Post
     */
    public static function get_booking_listing( $booking_id ) {
        $listing_id = get_post_meta( $booking_id, INVENTOR_BOOKING_PREFIX . 'listing_id', true );
        return get_post( $listing_id );
    }

    /**
     * Renders data before payment form
     *
     * @access public
     * @param string $payment_type
     * @param int $object_id
     * @param string $payment_gateway
     * @return void
     */
    public static function payment_form_before( $payment_type, $object_id, $payment_gateway ) {
        if ( ! in_array( $payment_type, array( 'booking' ) ) ) {
            return;
        }

        $attrs = array(
            'payment_type'      => $payment_type,
            'object_id'         => $object_id,
            'payment_gateway'   => $payment_gateway,
        );

        echo Inventor_Template_Loader::load( 'bookings/payment-form-before', $attrs, INVENTOR_BOOKINGS_DIR );
    }

    /**
     * Returns list of time options for given day by opening hours
     *
     * @access public
     * @param int $post_id
     * @param string $day
     * @param int $time_delta
     * @return array
     */
    public static function get_time_options_by_opening_hours( $post_id, $day, $time_delta = 1800 ) {
        $time_delta = apply_filters( 'inventor_bookings_time_delta', $time_delta, $post_id );  // 30 minutes by default
        $opening_hours = Inventor_Post_Types::opening_hours_for_day( $post_id, $day );
        $time_from = null;

        $times = array();

        if ( ! empty( $opening_hours['from'] ) && ! empty( $opening_hours['to'] ) ) {
            $time_from = strtotime( $opening_hours['from'] );
            $time_to = strtotime( $opening_hours['to'] );

            $is_same_day = $time_from <= $time_to;

            if ( ! $is_same_day ) {
                $time_to += 60 * 60 * 24;
            }

            if ( $time_delta > 0 && $time_from < $time_to ) {
                while ( $time_from <= $time_to ) {
                    if ( $time_from + $time_delta <= $time_to ) {
                        $times[] = date_i18n( get_option('time_format'), $time_from );
                    }
                    $time_from += $time_delta;
                }
            }
        }

        return $times;
    }

    /**
     * Returns discount by given date and time (in string format) and listing week availability
     *
     * @access public
     * @param string $listing_id
     * @param string $date
     * @param string $time
     * @return int
     */
    public static function get_discount_by_date_and_time( $listing_id, $date, $time ) {
        # TODO: check if field is enabled (to not to load old database values)

        $week_availability = get_post_meta( $listing_id, INVENTOR_BOOKINGS_PREFIX . 'week_availability', true );
        $timestamp = strtotime( $date );
        $week_day_index = date( "w", $timestamp );
        $week_day_index = ($week_day_index + 6) % 7;
        $days = Inventor_Post_Types::opening_hours_day_names();
        $week_day_keys = array_keys( $days );
        $week_day_key = $week_day_keys[ $week_day_index ];

        if ( empty( $week_availability ) || ! is_array( $week_availability ) || empty( $week_availability[ $week_day_key ] ) ) {
            return 0;
        }

        foreach( $week_availability[ $week_day_key ] as $time_config ) {
            if ( ! empty( $time_config['time'] ) && strtotime( $time_config['time'] ) == strtotime( $time ) && is_numeric( $time_config['discount'] ) ) {
                return (int) $time_config['discount'];
            }
        }

        return 0;
    }

    /**
     * Sets all user bookings into query
     *
     * @access public
     * @param $meta
     * @param $order
     *
     * @return void
     */
    public static function loop_my_bookings( $meta = array(), $order = 'asc' ) {
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;

        $query_args = array(
            'post_type'     => 'booking',
            'paged'         => $paged,
            'author'		=> get_current_user_id(),
            'post_status'   => 'publish',
            'meta_key'      => INVENTOR_BOOKING_PREFIX . 'book_from',
            'orderby'       => 'meta_value_num',
            'order'         => $order
        );

        if ( ! empty( $meta ) ) {
            $query_args['meta_query'] = $meta;
        }

        query_posts( $query_args );
    }

    /**
     * Sets all bookings of user listings into query
     *
     * @access public
     * @param $meta
     * @param $order
     *
     * @return void
     */
    public static function loop_bookings_of_my_listings( $meta = array(), $order = 'asc' ) {
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;

        $query_args = array(
            'post_type'     => 'booking',
            'paged'         => $paged,
            'post_status'   => 'publish',
            'meta_key'      => INVENTOR_BOOKING_PREFIX . 'book_from',
            'orderby'       => 'meta_value_num',
            'order'         => $order
        );

        $user_listings_query = Inventor_Query::get_listings_by_user( get_current_user_id() );
        $user_listings = $user_listings_query->posts;
        $listings_ids = wp_list_pluck( $user_listings, 'ID' );

        if ( empty( $listings_ids ) ) {
            $listings_ids = null;
        }

        $meta[] = array(
            'key'       => INVENTOR_BOOKING_PREFIX . 'listing_id',
            'value'     => $listings_ids,
            'compare'   => 'IN'
        );

        $query_args['meta_query'] = $meta;

        query_posts( $query_args );
    }
}

Inventor_Bookings_Logic::init();