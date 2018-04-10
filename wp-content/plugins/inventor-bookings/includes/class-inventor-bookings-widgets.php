<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Widgets
 *
 * @class Inventor_Bookings_Widgets
 * @package Inventor_Bookings/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Widgets {
    /**
     * Initialize widgets
     *
     * @access public
     * @return void
     */
    public static function init() {
        self::includes();
        add_action( 'widgets_init', array( __CLASS__, 'register' ) );
    }

    /**
     * Include widget classes
     *
     * @access public
     * @return void
     */
    public static function includes() {
        require_once INVENTOR_BOOKINGS_DIR . 'includes/widgets/class-inventor-bookings-widget-booking-form.php';
    }

    /**
     * Register widgets
     *
     * @access public
     * @return void
     */
    public static function register() {
        register_widget( 'Inventor_Bookings_Widget_Booking_Form' );
    }
}

Inventor_Bookings_Widgets::init();