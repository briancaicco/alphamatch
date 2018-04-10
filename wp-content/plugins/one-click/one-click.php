<?php
/**
 * Plugin Name: One Click
 * Version: 1.0.0
 * Description: Plugin for importing content and widget settings into theme.
 * Author: Pragmatic Mates
 * Author URI: http://pragmaticmates.com
 * Text Domain: one-click
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package One_Click
 */

if ( ! class_exists( 'One_Click' ) ) {
	/**
	 * Class One_Click
	 *
	 * @class One_Click
	 * @package One_Click
	 * @author Pragmatic Mates
	 */
	final class One_Click {
		/**
		 * List of required plugins
		 *
		 * @var array
		 */
		public static $required_plugins = array(
			array (
				'name'      => 'Wordpress Importer',
				'slug'      => 'wordpress-importer',
				'path'      => 'wordpress-importer/wordpress-importer.php',
				'required'  => true,
			),
			array (
				'name'      => 'Widget Logic',
				'slug'      => 'widget-logic',
				'path'      => 'widget-logic/widget_logic.php',
				'required'  => true,
			),
		);

		/**
		 * List of required settings
		 *
		 * @var array
		 */
		public static $required_settings = array(
			'phpversion' => array (
				'docs'      => 'https://secure.php.net/releases/',
				'min'      	=> '5.3.4',
			),
			'upload_max_filesize' => array (
				'docs'      => 'http://php.net/manual/en/ini.core.php#ini.upload-max-filesize',
				'min'      	=> '32M',
			),
			'post_max_size' => array (
				'docs'      => 'http://php.net/manual/en/ini.core.php#ini.post-max-size',
				'min'      	=> '64M',
			),
			'memory_limit' => array (
				'docs'      => 'http://php.net/manual/en/ini.core.php#ini.memory-limit',
				'min'      	=> '512M',
			),
			'max_input_time' => array (
				'docs'      => 'http://php.net/manual/en/info.configuration.php#ini.max-input-time',
				'min'      	=> '1000',
			),
			'max_execution_time' => array (
				'docs'      => 'http://php.net/manual/en/info.configuration.php#ini.max-execution-time',
				'min'      	=> '1000',
			),
		);

		/**
		 * Initialize plugin
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
			$this->libraries();
			$this->load_plugin_textdomain();

			add_action( 'tgmpa_register', array( __CLASS__, 'register_plugins' ) );
		}

		/**
		 * Get list of all available steps
		 *
		 * @access public
		 * @return array
		 */
		public static function get_imports() {
			return array(
				array(
					'id'            => 'content',
					'file'          => ONE_CLICK_CONTENT_FILE,
					'title'         => __( 'WordPress Content', 'one-click' ),
					'description'   => __( 'Images will be downloaded from external source so please be patient. Required plugin <a href="https://wordpress.org/plugins/wordpress-importer/">WordPress Importer</a>.', 'one-click' ),
					'action'        => esc_url( wp_nonce_url( add_query_arg( array( 'one-click-step' =>  'content', 'import' => 'true' ) ), 'one_click_step_content_nonce', '_one_click_step_content_nonce' ) ),
				),
				array(
					'id'            => 'theme-options',
					'file'          => ONE_CLICK_THEME_OPTIONS_FILE,
					'title'         => __( 'Customizer Theme Options', 'one-click'),
					'description'   => __( 'Options imported from customizer. Plugin which was used to export options <a href="https://wordpress.org/plugins/customizer-export-import/">Customizer Export/Import</a>.', 'one-click' ),
					'action'        => esc_url( wp_nonce_url( add_query_arg( 'one-click-step', 'theme-options' ), 'one_click_step_theme_options_nonce', '_one_click_step_theme_options_nonce' ) ),
				),
				array(
					'id'            => 'widgets-content',
					'file'          => ONE_CLICK_WIDGETS_CONTENT_FILE,
					'title'         => __( 'Widgets Content &amp; Options', 'one-click'),
					'description'   => __( 'Widget content and sidebar positions. Required plugin <a href="https://wordpress.org/plugins/widget-settings-importexport/">Widget Settings Importer/Exporter</a>.', 'one-click' ),
					'action'        => esc_url( wp_nonce_url( add_query_arg( 'one-click-step', 'widgets-content' ), 'one_click_step_widgets_content_nonce', '_one_click_step_widgets_content_nonce' ) ),
				),
				array(
					'id'            => 'widget-logic',
					'file'          => ONE_CLICK_WIDGET_LOGIC_FILE,
					'title'         => __( 'Widget Logic', 'one-click'),
					'description'   => __( 'Widget Logic conditional tags import for better widget management. Required plugin <a href="https://wordpress.org/plugins/widget-logic/">Widget Logic</a>.', 'one-click' ),
					'action'        => esc_url( wp_nonce_url( add_query_arg( 'one-click-step', 'widget-logic' ), 'one_click_step_widget_logic_nonce', '_one_click_step_widget_logic_nonce' ) ),
				),
				array(
					'id'            => 'custom-options',
					'file'          => ONE_CLICK_CUSTOM_FILE,
					'title'         => __( 'Custom Options', 'one-click'),
					'description'   => __( 'Custom options import functionality to fix same basic variables or proper menu locations.', 'one-click' ),
					'action'        => esc_url( wp_nonce_url( add_query_arg( 'one-click-step', 'custom-options' ), 'one_click_step_custom_options_nonce', '_one_click_step_custom_options_nonce' ) ),
				),
			);
		}

		/**
		 * Defines constants
		 *
		 * @access public
		 * @return void
		 */
		public function constants() {
			define( 'ONE_CLICK_DIR', plugin_dir_path( __FILE__ ) );
			define( 'ONE_CLICK_EXPORTS_DIR', 'exports' );

			define( 'ONE_CLICK_CONTENT_FILE', 'demo-content.xml' );
			define( 'ONE_CLICK_THEME_OPTIONS_FILE', 'theme-options.dat' );
			define( 'ONE_CLICK_WIDGETS_CONTENT_FILE', 'widgets.json' );
			define( 'ONE_CLICK_WIDGET_LOGIC_FILE', 'widget-logic.json' );
			define( 'ONE_CLICK_CUSTOM_FILE', 'custom.json' );
		}

		/**
		 * Include classes
		 *
		 * @access public
		 * @return void
		 */
		public function includes() {
			require_once ONE_CLICK_DIR . 'includes/class-one-click-scripts.php';
			require_once ONE_CLICK_DIR . 'includes/class-one-click-menu.php';
			require_once ONE_CLICK_DIR . 'includes/class-one-click-launcher.php';
		}

		/**
		 * Loads third party libraries
		 *
		 * @access public
		 * @return void
		 */
		public static function libraries() {
			require_once ONE_CLICK_DIR . 'libraries/class-tgm-plugin-activation.php';
		}

		/**
		 * Loads localization files
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'one-click', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Install plugins
		 *
		 * @access public
		 * @return void
		 */
		public static function register_plugins() {
			tgmpa( self::$required_plugins );
		}

		/**
		 * Returns bytes from given string
		 *
		 * @access public
		 * @return int
		 */
		public static function return_bytes( $val ) {
			$val = trim( $val );
			$last = strtolower( $val[ strlen($val) - 1 ] );

			switch( $last ) {
				// The 'G' modifier is available since PHP 5.1.0
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}

			return $val;
		}
	}

	new One_Click();
}