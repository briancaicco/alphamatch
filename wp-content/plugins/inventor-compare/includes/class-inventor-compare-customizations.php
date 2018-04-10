<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Compare_Customizations
 *
 * @access public
 * @package Inventor_Compare/Classes/Customizations
 * @return void
 */
class Inventor_Compare_Customizations {
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
        require_once INVENTOR_COMPARE_DIR . 'includes/customizations/class-inventor-compare-customizations-compare.php';
    }
}

Inventor_Compare_Customizations::init();