<?php

/**
 * Plugin Name: Inventor Compare
 * Version: 1.1.0
 * Description: Allows user to compare multiple listings by common fields.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-compare/
 * Text Domain: inventor-compare
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Compare' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Compare
     *
     * @class Inventor_Compare
     * @package Inventor_Compare
     * @author Pragmatic Mates
     */
    final class Inventor_Compare {
        const DOMAIN = 'inventor-compare';

        /**
         * Initialize Inventor_Compare plugin
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
            define( 'INVENTOR_COMPARE_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_COMPARE_DIR . 'includes/class-inventor-compare-customizations.php';
            require_once INVENTOR_COMPARE_DIR . 'includes/class-inventor-compare-shortcodes.php';
            require_once INVENTOR_COMPARE_DIR . 'includes/class-inventor-compare-scripts.php';
            require_once INVENTOR_COMPARE_DIR . 'includes/class-inventor-compare-logic.php';
        }
    }

    new Inventor_Compare();
}