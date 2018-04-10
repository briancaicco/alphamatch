<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Post_Types
 *
 * @class Inventor_Bookings_Post_Types
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Post_Types {
    public static $listings_types = array();

    /**
     * Initialize listing types
     *
     * @access public
     * @return void
     */
    public static function init() {
        self::includes();
    }

    /**
     * Loads listing types
     *
     * @access public
     * @return void
     */
    public static function includes() {
        require_once INVENTOR_BOOKINGS_DIR . 'includes/post-types/class-inventor-bookings-post-type-booking.php';
    }
}

Inventor_Bookings_Post_Types::init();