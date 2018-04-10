<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Stripe_Customizations
 *
 * @access public
 * @package Inventor_Stripe/Classes/Customizations
 * @return void
 */
class Inventor_Stripe_Customizations {
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
        require_once INVENTOR_STRIPE_DIR . 'includes/customizations/class-inventor-stripe-customizations-stripe.php';
    }
}

Inventor_Stripe_Customizations::init();