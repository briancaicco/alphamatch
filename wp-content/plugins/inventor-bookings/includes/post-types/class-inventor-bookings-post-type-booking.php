<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Post_Type_Booking
 *
 * @class Inventor_Bookings_Post_Type_Booking
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Post_Type_Booking {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_filter( 'cmb2_init', array( __CLASS__, 'fields' ) );

        add_filter( 'manage_edit-booking_columns', array( __CLASS__, 'custom_columns' ) );
        add_action( 'manage_booking_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

        // http://www.codesynthesis.co.uk/tutorials/filtering-custom-post-types-by-post-meta-in-the-wordpress-admin-area
        add_action( 'restrict_manage_posts', array( __CLASS__, 'restrict_manage_posts' ), 10, 2 );
        add_filter( 'parse_query', array( __CLASS__, 'posts_filter' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name'                  => __( 'Bookings', 'inventor-bookings' ),
            'singular_name'         => __( 'Booking', 'inventor-bookings' ),
            'add_new'               => __( 'Add New Booking', 'inventor-bookings' ),
            'add_new_item'          => __( 'Add New Booking', 'inventor-bookings' ),
            'edit_item'             => __( 'Edit Booking', 'inventor-bookings' ),
            'new_item'              => __( 'New Booking', 'inventor-bookings' ),
            'all_items'             => __( 'Bookings', 'inventor-bookings' ),
            'view_item'             => __( 'View Booking', 'inventor-bookings' ),
            'search_items'          => __( 'Search Booking', 'inventor-bookings' ),
            'not_found'             => __( 'No Bookings found', 'inventor-bookings' ),
            'not_found_in_trash'    => __( 'No Bookings Found in Trash', 'inventor-bookings' ),
            'parent_item_colon'     => '',
            'menu_name'             => __( 'Bookings', 'inventor-bookings' ),
        );

        register_post_type( 'booking',
            array(
                'labels'                => $labels,
                'show_in_menu'          => class_exists( 'Inventor_Admin_Menu') ? 'inventor' : true,
                'supports'              => array( 'title', 'author' ),
                'public'                => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'show_in_nav_menus'     => false,
                'has_archive'           => false,
                'show_ui'               => true,
                'categories'            => array(),
            )
        );
    }

    /**
     * Returns booking status display value
     *
     * @access public
     * @param string $status
     * @return string
     */
    public static function get_status_display( $status ) {
        $booking_statuses = Inventor_Bookings_Logic::booking_statuses();
        echo array_key_exists( $status, $booking_statuses ) ? $booking_statuses[ $status ] : $status;
    }

    /**
     * Defines custom fields
     *
     * @access public
     * @return array
     */
    public static function fields() {
        $metabox_id = INVENTOR_BOOKING_PREFIX . 'general';

        $cmb = new_cmb2_box( array(
            'id'                        => $metabox_id,
            'title'                     => __( 'General', 'inventor-bookings' ),
            'object_types'              => array( 'booking' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Book from', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'book_from',
            'type'              => 'text_datetime_timestamp',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Book to', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'book_to',
            'type'              => 'text_datetime_timestamp',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Price', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'price',
            'type'              => 'text_money',
            'sanitization_cb'	=> false,
            'before_field'      => Inventor_Price::default_currency_symbol(),
            'attributes'		=> array(
                'type'				=> 'number',
                'step'				=> 'any',
                'min'				=> 0,
                'pattern'			=> '\d*(\.\d*)?',
            )
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Listing ID', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'listing_id',
            'type'              => 'text_small',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Number of persons', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'number_of_persons',
            'type'              => 'text_small',
            'attributes'        => array(
                'type'              => 'number',
                'min'               => '1',
                'pattern'           => '\d*',
            )
        ) );

        $cmb->add_field( array(
            'name'              => __( 'E-mail', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'email',
            'type'              => 'text_email',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Name', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'name',
            'type'              => 'text_medium',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Phone', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'phone',
            'type'              => 'text_medium',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Message', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'message',
            'type'              => 'textarea',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Hash', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'hash',
            'type'              => 'text',
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Status', 'inventor-bookings' ),
            'id'                => INVENTOR_BOOKING_PREFIX . 'status',
            'type'              => 'select',
            'options'           => Inventor_Bookings_Logic::booking_statuses()
        ) );
    }

    /**
     * Custom admin columns
     *
     * @access public
     * @return array
     */
    public static function custom_columns() {
        $fields = array(
            'cb' 				=> '<input type="checkbox" />',
            'title' 		    => __( 'Title', 'inventor-bookings' ),
            'listing' 		    => __( 'Listing', 'inventor-bookings' ),
            'period' 	        => __( 'Period', 'inventor-bookings' ),
            'email' 			=> __( 'E-mail', 'inventor-bookings' ),
            'persons' 		    => __( 'Persons', 'inventor-bookings' ),
            'price' 		    => __( 'Price', 'inventor-bookings' ),
            'status' 		    => __( 'Status', 'inventor-bookings' ),
        );
        return $fields;
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public static function custom_columns_manage( $column ) {
        switch ( $column ) {
            case 'listing':
                $listing_id = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'listing_id', true );
                echo get_the_title( $listing_id );
                break;
            case 'period':
                $book_from = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_from', true );
                $book_to = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_to', true );
                echo date( "Y-m-d", $book_from ) . ' - ' . date( "Y-m-d", $book_to );
                break;
            case 'book_to':
                break;
            case 'email':
                echo get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'email', true );
                break;
            case 'persons':
                echo get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'number_of_persons', true );
                break;
            case 'price':
                $price = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'price', true );
                echo Inventor_Price::format_price( $price );
                break;
            case 'status':
                $booking_status = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'status', true );
                echo Inventor_Bookings_Post_Type_Booking::get_status_display( $booking_status );
                break;
        }
    }

    /**
     * Checks if booking is past
     *
     * @access public
     * @param $booking_id int
     * @return bool
     */
    public static function is_past( $booking_id ) {
        $book_from = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_from', true );
        $now = current_time( 'timestamp' );
        return ! empty( $book_from ) && $book_from < $now;
    }

    /**
     * Returns time until booking start date
     *
     * @access public
     * @param $booking_id int
     * @return bool
     */
    public static function time_until_start( $booking_id ) {
        $book_from = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_from', true );
        $now = current_time( 'timestamp' );
        return ! empty( $book_from ) ? $book_from - $now : null;
    }

    /**
     * Creates input for table filter in admin
     *
     * @param string $post_type
     * @param string $which
     *
     * @return void
     */
    public static function restrict_manage_posts( $post_type, $which = null ) {
        if ( $post_type != 'booking' ) {
            return;
        }

        $current_value = isset( $_GET['listing_id'] ) ? $_GET['listing_id'] : '';

        ?><input type="text" placeholder="<?php _e('Listing ID', 'inventor-bookings'); ?>" name="listing_id" value="<?php echo esc_attr( $current_value ) ?>"><?php
    }

    /**
     * Filters posts by post meta
     *
     * @return array
     */
    public static function posts_filter( $query ){
        global $pagenow;

        if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != 'booking' ) {
            return $query;
        }

        $filter_field = 'listing_id';

        if ( is_admin() && $pagenow == 'edit.php' && ! empty( $_GET[ $filter_field ] ) ) {
            $query->query_vars['meta_key'] = INVENTOR_BOOKING_PREFIX . 'listing_id';
            $query->query_vars['meta_value'] = $_GET[ $filter_field ];
        }

        return $query;
    }
}

Inventor_Bookings_Post_Type_Booking::init();