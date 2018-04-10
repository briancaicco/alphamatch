<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Compare_Logic
 *
 * @class Inventor_Compare_Logic
 * @package Inventor_Compare/Classes
 * @author Pragmatic Mates
 */
class Inventor_Compare_Logic {
    /**
     * Initialize Compare functionality
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'wp', array( __CLASS__, 'redirect_to_compare_uri' ) );
        add_action( 'template_redirect', array( __CLASS__, 'feed_catch_template' ), 0 );
        add_action( 'wp_footer', array( __CLASS__, 'render_comparison_info' ), 0 );
        add_action( 'wp_ajax_nopriv_inventor_compare_remove', array( __CLASS__, 'remove_from_comparison' ) );
        add_action( 'wp_ajax_inventor_compare_remove', array( __CLASS__, 'remove_from_comparison' ) );
        add_action( 'wp_ajax_nopriv_inventor_compare_add', array( __CLASS__, 'add_to_comparison' ) );
        add_action( 'wp_ajax_inventor_compare_add', array( __CLASS__, 'add_to_comparison' ) );
        add_action( 'inventor_listing_actions', array( __CLASS__,  'render_compare_button' ), 0, 1 );
        add_action( 'inventor_listing_banner_actions', array( __CLASS__, 'render_compare_button' ), 1, 1 );

        add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ) );
        add_filter( 'inventor_filter_query_ids', array( __CLASS__, 'filter_query_ids' ), 10, 2 );
        add_filter( 'inventor_google_map_use_cache', array( __CLASS__, 'disable_map_cache' ), 10, 2 );
    }

    /**
     * Gets config
     *
     * @access public
     * @return array
     */
    public static function get_config() {
        return array(
            "page"      => get_theme_mod( 'inventor_compare_page', false ),
        );
    }

    /**
     * Adds query vars
     *
     * @access public
     * @param $vars
     * @return array
     */
    public static function add_query_vars( $vars ) {
        $vars[] = 'compare-feed';
        return $vars;
    }

    /**
     * Removes listing from list
     *
     * @access public
     * @return string
     */
    public static function remove_from_comparison() {
        header( 'HTTP/1.0 200 OK' );
        header( 'Content-Type: application/json' );

        if ( ! empty( $_GET['id'] ) ) {
            $compare = Inventor_Visitor::get_data('compare');

            if ( ! empty( $compare ) && is_array( $compare ) ) {
                foreach ( $compare as $key => $listing_id ) {
                    if ( $listing_id == $_GET['id'] ) {
                        unset( $compare[ $key ] );
                    }
                }

                $timeout_in_seconds = apply_filters( 'inventor_compare_timeout', 60*60 );
                Inventor_Visitor::save_data( 'compare', $compare, time() + $timeout_in_seconds );

                $count = count($compare);

                $data = array(
                    'success' => true,
                    'count'     => $count,
                    'text'      => sprintf( _n( 'Compare %d item', 'Compare %d items', $count, 'inventor-compare' ), $count )
                );
            } else {
                $data = array(
                    'success' => true
                );
            }
        } else {
            $data = array(
                'success' => false,
                'message' => __( 'Listing ID is missing.', 'inventor-compare' ),
            );
        }

        echo json_encode( $data );
        exit();
    }

    /**
     * Adds listing into compare
     *
     * @access public
     * @return void
     */
    public static function add_to_comparison() {
        header( 'HTTP/1.0 200 OK' );
        header( 'Content-Type: application/json' );

        if ( ! empty( $_GET['id'] ) ) {
            $compare = Inventor_Visitor::get_data('compare');
            $compare = ! is_array( $compare ) || empty( $compare ) ? array() : $compare;

            $post = get_post( $_GET['id'] );
            $post_type = get_post_type( $post->ID );

            if ( ! in_array( $post_type, Inventor_Post_Types::get_listing_post_types() ) ) {
                $data = array(
                    'success' => false,
                    'message' => __( 'This is not listing ID.', 'inventor-compare' ),
                );
            } else {
                $found = false;

                foreach ( $compare as $listing_id ) {
                    if ( $listing_id == $_GET['id']) {
                        $found = true;
                        break;
                    }
                }

                if ( ! $found ) {
                    $max_compare = 5;

                    if ( count( $compare ) >= $max_compare ) {
                        $data = array(
                            'success' => false,
                            'message' => sprintf( __( 'You can compare at most %d listings', 'inventor-compare' ), $max_compare )
                        );
                    } else {
                        $compare[] = $post->ID;
                        $count = count( $compare );

                        $timeout_in_seconds = apply_filters( 'inventor_compare_timeout', 60*60 );
                        Inventor_Visitor::save_data( 'compare', $compare, time() + $timeout_in_seconds );

                        $data = array(
                            'success'   => true,
                            'count'     => $count,
                            'text'      => sprintf( _n( 'Compare %d item', 'Compare %d items', $count, 'inventor-compare' ), $count )
                        );
                    }

                } else {
                    $data = array(
                        'success' => false,
                        'message' => __( 'Listing is already in list', 'inventor-compare' ),
                    );
                }
            }
        } else {
            $data = array(
                'success' => false,
                'message' => __( 'Listing ID is missing.', 'inventor-compare' ),
            );
        }

        echo json_encode( $data );
        exit();
    }

    /**
     * Gets list of listings from compare
     *
     * @access public
     * @return void
     */
    public static function feed_catch_template() {
        if ( get_query_var( 'compare-feed' ) ) {
            header( 'HTTP/1.0 200 OK' );
            header( 'Content-Type: application/json' );

            $data = array();
            $compare = self::get_comparison_list();

            if ( ! empty( $compare ) && is_array( $compare ) ) {
                foreach ( $compare as $listing_id ) {
                    $post = get_post( $listing_id );

                    $data[] = array(
                        'id'        => $post->ID,
                        'title'     => get_the_title( $post->ID ),
                        'permalink' => get_permalink( $post->ID ),
                        'src'       => wp_get_attachment_url( get_post_thumbnail_id( $post->ID) ),
                    );
                }
            }

            echo json_encode( $data );
            exit();
        }
    }

    /**
     * Checks if listing is in user compare
     *
     * @access public
     * @param $post_id
     * @return bool
     */
    public static function is_in_comparison( $post_id ) {
        $compare = self::get_comparison_list();

        if ( ! empty( $compare ) && is_array( $compare ) ) {
            return in_array( $post_id, $compare );
        }

        return false;
    }

    /**
     * Gets IDs of listings to compare
     *
     * @access public
     * @return array
     */
    public static function get_comparison_list() {
        $config = self::get_config();
        $page = $config['page'];
        $ids = empty( $_GET['ids'] ) ? array() : esc_attr( $_GET['ids'] );

        if ( is_page( $page ) && ! empty( $ids ) ) {
            $ids = array_map( 'intval', explode( ',', $ids ) );
            return $ids;
        }

        $compare = Inventor_Visitor::get_data('compare');

        if ( is_array( $compare ) && ! empty( $compare ) ) {
            return $compare;
        }

        return array();
    }

    /**
     * Gets user comparison list
     *
     * @access public
     * @return WP_Query
     */
    public static function get_comparison_query() {
        $compare = self::get_comparison_list();

        if( ! is_array( $compare ) || count( $compare ) == 0 ) {
            $compare = array( '', );
        }

        return new WP_Query( array(
            'post_type'         => Inventor_Post_Types::get_listing_post_types(),
            'post__in'		    => $compare,
            'post_status'       => 'any',
        ) );
    }

    /**
     * Gets common metaboxes of listings in comparison
     *
     * @access public
     * @return array
     */
    public static function get_common_fields() {
        $fields = null;
        $metaboxes_to_compare = apply_filters( 'inventor_compare_metaboxes', array(
            'details', 'color', 'listing_category',
            'date', 'time', 'date_interval', 'datetime_interval',
            'time_interval', 'date_and_time_interval') );

        $ignored_fields = apply_filters( 'inventor_compare_ignored_fields', array(
            INVENTOR_LISTING_PREFIX . 'map_location',
            INVENTOR_LISTING_PREFIX . 'map_location_polygon',
            INVENTOR_LISTING_PREFIX . 'street_view',
            INVENTOR_LISTING_PREFIX . 'street_view_location',
            INVENTOR_LISTING_PREFIX . 'inside_view',
            INVENTOR_LISTING_PREFIX . 'inside_view_location',
            INVENTOR_LISTING_PREFIX . 'metabox_general_title',
        ) );

        $compare = self::get_comparison_list();

        foreach( $compare as $lising_id ) {
            $post_type = get_post_type( $lising_id );
            $post_type_metaboxes = Inventor_Metaboxes::get_for_post_type( $post_type );

            $post_type_fields = array();

            foreach( $post_type_metaboxes as $metabox_id => $cmb ) {
                $metabox_key = Inventor_Metaboxes::get_metabox_key( $metabox_id, $post_type );

                if ( ! in_array( $metabox_key, $metaboxes_to_compare ) ) {
                    continue;
                }

                foreach ( $cmb->meta_box['fields'] as $field_id => $field_attrs ) {
                    if( isset( $field_attrs['name'] ) && ! in_array( $field_id, $ignored_fields ) ) {
                        $field_name = esc_attr( $field_attrs['name'] );
                        $post_type_fields[ $field_id ] = $field_name;
                    }
                }
            }

            if ( $fields === null ) {
                $fields = $post_type_fields;
            } else {
                // TODO: add customization settings: inventor_compare_policy: common fields, all fields
//                $fields = array_merge( $fields, $post_type_fields );
                $fields = array_intersect_assoc( $fields, $post_type_fields );
            }
        }

        return $fields === null ? array() : $fields;
    }

    /**
     * Renders compare button
     *
     * @access public
     * @param int $listing_id
     * @return void
     */
    public static function render_compare_button( $listing_id ) {
        echo Inventor_Template_Loader::load( 'compare-button', array( 'listing_id' => $listing_id ), $plugin_dir = INVENTOR_COMPARE_DIR );
    }

    /**
     * Renders comparison info
     *
     * @access public
     * @return void
     */
    public static function render_comparison_info() {
        $compare = self::get_comparison_list();
        $count = count( $compare );

        $config = self::get_config();
        $page = $config['page'];

        if ( $page && is_page( $page ) ) {
            return;
        }

        echo Inventor_Template_Loader::load( 'compare-info', array( 'count' => $count ), $plugin_dir = INVENTOR_COMPARE_DIR );
    }

    /**
     * Redirect to compare page with IDs of listings in the URL
     *
     * @access public
     * @return void
     */
    public static function redirect_to_compare_uri() {
        $config = self::get_config();
        $page = $config['page'];

        $compare = Inventor_Visitor::get_data('compare');

        $ids = empty( $_GET['ids'] ) ? array() : esc_attr( $_GET['ids'] );

        if ( $page && ! empty( $compare ) && is_page( $page ) && empty( $ids ) ) {
            $url = get_permalink( $page );
            $ids = join( ',', $compare );
            $url .= '?ids=' . $ids;
            wp_redirect( $url );
            exit();
        }
    }

    /**
     * Disabled Google Map markers cache on compare page
     *
     * @access public
     * @param bool $use_cache
     * @param array $params
     * @return array
     */
    public static function disable_map_cache( $use_cache, $params ) {
        $config = self::get_config();
        $page = $config['page'];

        $context = empty( $params['context'] ) ? null : $params['context'];
        $post_id = empty( $params['post-id'] ) ? null : $params['post-id'];

        return $context == 'map' && ! empty( $page ) && ! empty( $post_id ) && $post_id == $page ? false : $use_cache;
    }

    /**
     * Filters map listings by compare list
     *
     * @access public
     * @param array $ids
     * @param array $params
     * @return array
     */
    public static function filter_query_ids( $ids, $params ) {
        $config = self::get_config();
        $page = $config['page'];
        $context = empty( $params['context'] ) ? null : $params['context'];

        if ( $context == 'map' && ! empty( $page ) && ! empty( $params['post-id'] ) && $params['post-id'] == $page ) {
            $referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_attr( $_SERVER['HTTP_REFERER'] ) : null;

            if( ! empty( $referer ) ) {
                // Get IDs from the referer URL
                $parts = parse_url( $referer );
                parse_str( $parts['query'], $query );
                $ids = empty( $query['ids'] ) ? array() : esc_attr( $query['ids'] );

                if ( ! empty( $ids ) ) {
                    $ids = array_map( 'intval', explode( ',', $ids ) );
                    return $ids;
                }
            } else {
                // Get IDs from comparison list
                $compare = self::get_comparison_list();

                if ( ! empty( $compare ) ) {
                    return $compare;
                }
            }
        }

        return $ids;
    }
}

Inventor_Compare_Logic::init();