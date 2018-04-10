<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Google_Map_Customizations
 *
 * @access public
 * @package Inventor_Google_Map/Classes/Customizations
 * @return void
 */
class Inventor_Google_Map_Customizations {
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
        require_once INVENTOR_GOOGLE_MAP_DIR . 'includes/customizations/class-inventor-google-map-customizations-google-map.php';
    }
}

Inventor_Google_Map_Customizations::init();