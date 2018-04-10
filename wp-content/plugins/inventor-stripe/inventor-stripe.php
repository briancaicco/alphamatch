<?php

/**
 * Plugin Name: Inventor Stripe
 * Version: 1.6.0
 * Description: Adds Stripe payment gateway.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-stripe/
 * Text Domain: inventor-stripe
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Stripe' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Stripe
     *
     * @class Inventor_Stripe
     * @package Inventor_Stripe
     * @author Pragmatic Mates
     */
    final class Inventor_Stripe {
        const DOMAIN = 'inventor-stripe';

        /**
         * Initialize Inventor_Stripe plugin
         */
        public function __construct() {
            $this->constants();
            $this->includes();
            if ( class_exists( 'Inventor_Utilities' ) ) {
                Inventor_Utilities::load_plugin_textdomain( self::DOMAIN, __FILE__ );
            }
        }

        /**
         * Defines constants
         *
         * @access public
         * @return void
         */
        public function constants() {
            define( 'INVENTOR_STRIPE_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_STRIPE_DIR . 'includes/class-inventor-stripe-customizations.php';
            require_once INVENTOR_STRIPE_DIR . 'includes/class-inventor-stripe-logic.php';
        }
    }

    new Inventor_Stripe();
}