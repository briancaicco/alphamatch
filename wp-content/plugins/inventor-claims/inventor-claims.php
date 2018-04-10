<?php

/**
 * Plugin Name: Inventor Claims
 * Version: 1.1.2
 * Description: Allows user to claim listing.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-claims/
 * Text Domain: inventor-claims
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Claims' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Claims
     *
     * @class Inventor_Claims
     * @package Inventor_Claims
     * @author Pragmatic Mates
     */
    final class Inventor_Claims {
        const DOMAIN = 'inventor-claims';

        /**
         * Initialize Inventor_Claims plugin
         */
        public function __construct() {
            $this->init();
            $this->constants();
            $this->includes();
            if ( class_exists( 'Inventor_Utilities' ) ) {
                Inventor_Utilities::load_plugin_textdomain( self::DOMAIN, __FILE__ );
            }
        }

        /**
         * Initialize claims functionality
         *
         * @access public
         * @return void
         */
        public static function init() {
        }

        /**
         * Defines constants
         *
         * @access public
         * @return void
         */
        public function constants() {
            define( 'INVENTOR_CLAIMS_DIR', plugin_dir_path( __FILE__ ) );
            define( 'INVENTOR_CLAIM_PREFIX', 'claim_' );
            define( 'INVENTOR_MAIL_ACTION_CLAIMED_LISTING', 'CLAIMED_LISTING' );
            define( 'INVENTOR_MAIL_ACTION_CLAIM_APPROVED', 'CLAIM_APPROVED' );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_CLAIMS_DIR . 'includes/class-inventor-claims-customizations.php';
            require_once INVENTOR_CLAIMS_DIR . 'includes/class-inventor-claims-post-types.php';
            require_once INVENTOR_CLAIMS_DIR . 'includes/class-inventor-claims-shortcodes.php';
            require_once INVENTOR_CLAIMS_DIR . 'includes/class-inventor-claims-scripts.php';
            require_once INVENTOR_CLAIMS_DIR . 'includes/class-inventor-claims-logic.php';
        }
    }

    new Inventor_Claims();
}