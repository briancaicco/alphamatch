<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Field_Types
 *
 * @class Inventor_Bookings_Field_Types
 * @package Inventor/Classes/Field_Types
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Field_Types {
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
     * Loads field types
     *
     * @access public
     * @return void
     */
    public static function includes() {
        require_once INVENTOR_BOOKINGS_DIR . 'includes/field-types/class-inventor-bookings-field-types-week-availability.php';
    }
}

Inventor_Bookings_Field_Types::init();
