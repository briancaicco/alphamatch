<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Reviews_Customizations
 *
 * @access public
 * @package Inventor_Reviews/Classes/Customizations
 * @return void
 */
class Inventor_Reviews_Customizations {
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
        require_once INVENTOR_REVIEWS_DIR . 'includes/customizations/class-inventor-reviews-customizations-reviews.php';
    }
}

Inventor_Reviews_Customizations::init();