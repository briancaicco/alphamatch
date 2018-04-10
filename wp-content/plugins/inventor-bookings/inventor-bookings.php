<?php

/**
 * Plugin Name: Inventor Bookings
 * Version: 0.3.0
 * Description: Listing booking support.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-bookings/
 * Text Domain: inventor-bookings
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Bookings' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Bookings
     *
     * @class Inventor_Bookings
     * @package Inventor_Bookings
     * @author Pragmatic Mates
     */
    final class Inventor_Bookings {
        const DOMAIN = 'inventor-bookings';

        /**
         * Initialize Inventor_Bookings plugin
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
            define( 'INVENTOR_BOOKINGS_DIR', plugin_dir_path( __FILE__ ) );
            define( 'INVENTOR_BOOKINGS_PREFIX', 'bookings_' );
            define( 'INVENTOR_BOOKING_PREFIX', 'booking_' );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-customizations.php';
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-field-types.php';
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-logic.php';
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-post-types.php';
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-shortcodes.php';
            require_once INVENTOR_BOOKINGS_DIR . 'includes/class-inventor-bookings-widgets.php';
        }
    }

    new Inventor_Bookings();
}