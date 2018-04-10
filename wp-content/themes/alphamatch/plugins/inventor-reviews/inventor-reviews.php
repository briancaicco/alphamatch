<?php

/**
 * Plugin Name: Inventor Reviews
 * Version: 1.4.0
 * Description: Provides reviews for listings.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-reviews/
 * Text Domain: inventor-reviews
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Reviews' ) && class_exists( 'Inventor' ) ) {
	/**
	 * Class Inventor_Reviews
	 *
	 * @class Inventor_Reviews
	 * @package Inventor_Reviews
	 * @author Pragmatic Mates
	 */
	final class Inventor_Reviews {
        const DOMAIN = 'inventor-reviews';

		/**
		 * Initialize Inventor_Reviews plugin
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
            if ( class_exists( 'Inventor_Utilities' ) ) {
                Inventor_Utilities::load_plugin_textdomain( self::DOMAIN, __FILE__ );
            }

            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ) );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_backend' ) );
            add_action( 'init', array( __CLASS__, 'set_capabilities' ) );
		}

		/**
		 * Defines constants
		 *
		 * @access public
		 * @return void
		 */
		public function constants() {
			define( 'INVENTOR_REVIEWS_DIR', plugin_dir_path( __FILE__ ) );
			define( 'INVENTOR_REVIEWS_TOTAL_RATING_META', 'inventor_reviews_post_total_rating' );
		}

		/**
		 * Include classes
		 *
		 * @access public
		 * @return void
		 */
		public function includes() {
            require_once INVENTOR_REVIEWS_DIR . 'includes/class-inventor-reviews-customizations.php';
            require_once INVENTOR_REVIEWS_DIR . 'includes/class-inventor-reviews-logic.php';
		}

        public static function set_capabilities() {
            $role = get_role( 'subscriber' );
            if( $role ) {
                $role->add_cap( 'upload_files' );
            }
        }

        /**
         * Loads frontend files
         *
         * @access public
         * @return void
         */
        public static function enqueue_frontend() {
            wp_enqueue_script( 'inventor-reviews', plugins_url( '/inventor-reviews/assets/script.js' ), array( 'jquery' ), false, true );
            wp_enqueue_style( 'inventor-reviews', plugins_url( '/inventor-reviews/assets/style.css' ), array(), '20170214' );
            wp_enqueue_script( 'raty', plugins_url( '/inventor-reviews/libraries/raty/jquery.raty.js' ), array( 'jquery' ), false, true );
            wp_enqueue_media();
        }

        /**
         * Loads backend files
         *
         * @access public
         * @return void
         */
        public static function enqueue_backend() {
            wp_enqueue_style( 'inventor-reviews-style-admin', plugins_url( '/inventor-reviews/assets/style-admin.css' ), array(), '20160805' );
            wp_enqueue_script( 'raty-admin', plugins_url( '/inventor-reviews/libraries/raty/jquery.raty.js' ), array( 'jquery' ), false, true );
            wp_enqueue_script( 'inventor-reviews-script-admin', plugins_url( '/inventor-reviews/assets/script-admin.js' ), array( 'jquery' ), false, true );
        }
	}

	new Inventor_Reviews();
}