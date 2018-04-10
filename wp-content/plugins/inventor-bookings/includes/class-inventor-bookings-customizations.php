<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Customizations
 *
 * @access public
 * @package Inventor_Bookings/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Customizations {
    /**
     * Initialize customizations
     *
     * @access public
     * @return void
     */
    public static function init() {
        self::includes();
    }

    /**
     * Include all customizations
     *
     * @access public
     * @return void
     */
    public static function includes() {
        require_once INVENTOR_BOOKINGS_DIR . 'includes/customizations/class-inventor-bookings-customizations-bookings.php';
    }
}

Inventor_Bookings_Customizations::init();