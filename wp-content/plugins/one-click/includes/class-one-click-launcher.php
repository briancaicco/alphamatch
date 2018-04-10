<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class One_Click_Launcher
 *
 * @class One_Click_Launcher
 * @package One_Click/Classes
 * @author Pragmatic Mates
 */
class One_Click_Launcher {
	/**
	 * Initialize scripts
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'process' ) );
	}

	/**
	 * Display one click template template
	 *
	 * @access public
	 * @return void
	 */
	public static function template() {
		include ONE_CLICK_DIR . 'templates/launcher.php';
	}

	/**
	 * Process all import steps
	 *
	 * @access public
	 * @return void
	 */
	public static function process() {
		if ( ! empty( $_GET['one-click-step'] ) ) {
			switch ( $_GET['one-click-step'] ) {
				case 'content':
					self::process_content();
					break;
				case 'theme-options':
					self::process_theme_options();
					break;
				case 'widgets-content':
					self::process_widgets_content();
					break;
				case 'widget-logic':
					self::process_widget_logic();
					break;
				case 'custom-options':
					self::process_custom_options();
					break;
				default:
					exit();
					break;
			}

			update_option( 'one_click_process_step_' . $_GET['one-click-step'], true );
			exit();
		}
	}

	/**
	 * Import main content
	 *
	 * @access public
	 * @return void
	 */
	public static function process_content() {
		$import = new WP_Import();
		$import->fetch_attachments = true;
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . ONE_CLICK_CONTENT_FILE;
		$import->import( $filepath );
	}

	/**
	 * Import theme options from customizer
	 *
	 * @access public
	 * @return void
	 */
	public static function process_theme_options() {
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . ONE_CLICK_THEME_OPTIONS_FILE;
		$data = unserialize( file_get_contents( $filepath ) );

		if ( isset( $data['options'] ) ) {
			foreach ( $data['options'] as $option_key => $option_value ) {
				update_option( $option_key, $option_value );
			}
		}

		foreach ( $data['mods'] as $key => $val ) {
			set_theme_mod( $key, $val );
		}
	}

	/**
	 * Import widget content
	 *
	 * @access public
	 * @return void
	 */
	public static function process_widgets_content() {
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . ONE_CLICK_WIDGETS_CONTENT_FILE;
		$json_array = json_decode( file_get_contents( $filepath ), true );

		list( $sidebars, $widgets ) = ( $json_array );

		update_option( 'sidebars_widgets', $sidebars );
		foreach( $widgets as $widgetID => $widgetOptions ) {
			update_option( 'widget_' . $widgetID, $widgetOptions );
		}

		foreach ( $sidebars as $sidebar_name=>$sidebar_content ) {
			$output[] = $sidebar_name . ': ' . implode( ", ", $sidebar_content );
		}
	}

	/**
	 * Import widget logic options
	 *
	 * @access public
	 * @return void
	 */
	public static function process_widget_logic() {
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . ONE_CLICK_WIDGET_LOGIC_FILE;
		$options = json_decode( file_get_contents( $filepath ), true );
		update_option( 'widget_logic', $options );
	}

	/**
	 * Import custom options
	 *
	 * @access public
	 * @return void
	 */
	public static function process_custom_options() {
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . ONE_CLICK_CUSTOM_FILE;
		$options = json_decode( file_get_contents( $filepath ), true );

		foreach ( $options as $key => $value ) {
			if ( 'locations' != $key ) {
				update_option( $key, $value );
			} else {
				$locations = array();

				foreach ( $value as $menu_location => $menu_name ) {
					$menu = wp_get_nav_menu_object( $menu_name );
					$locations[ $menu_location ] = $menu->term_id;
				}

				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}

		flush_rewrite_rules( true );
	}

	/**
	 * Checks if import file exists
	 *
	 * @access public
	 * @param $file string
	 * @return bool
	 */
	public static function import_found( $file ) {
		$filepath = get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR . '/' . $file;
		return file_exists( $filepath );
	}

	/**
	 * Checks if all required plugins are installed
	 *
	 * @access public
	 * @return bool|array
	 */
	public static function get_missing_plugins() {
		$plugins = One_Click::$required_plugins;
		$missing = array();

		foreach ( $plugins as $plugin ) {
			if ( ! is_plugin_active( $plugin['path'] ) ) {
				$missing[] = $plugin;
			}
		}

		return $missing;
	}

	/**
	 * Checks if all available steps has been processed
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_completed() {
		$steps = One_Click::get_imports();

		foreach( $steps as $step ) {
			$completion_sign = get_option( 'one_click_process_step_' . $step['id'], false );

			if ( self::import_found( $step['file'] ) && '1' !==  $completion_sign ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if some steps are missing
	 *
	 * @access public
	 * @param bool $return_missing_count
	 * @return bool
	 */
	public static function is_missing( $return_missing_count = False ) {
		$steps = One_Click::get_imports();
		$count_completed = 0;
		$count_available = 0;

		foreach( $steps as $step ) {
			$completion_sign = get_option( 'one_click_process_step_' . $step['id'], false );

			if ( self::import_found( $step['file'] ) ) {
				$count_available++;

				if ( '1' === $completion_sign ) {
					$count_completed++;
				}
			}
		}

		$count_missing = $count_available - $count_completed;

		if ( 0 != $count_completed && $count_completed != $count_available ) {
			return $return_missing_count ? $count_missing : true;
		}

		return $return_missing_count ? $count_missing : false;
	}

	/**
	 * Checks if PHP configuration is correct
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_correct_configuration() {
		foreach ( One_Click::$required_settings as $setting => $info ) {
			if ( ! self::is_setting_fulfilled( $setting ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns PHP setting value
	 *
	 * @access public
	 * @return bool
	 */
	public static function get_setting_value( $setting ) {
		return $setting == 'phpversion' ? phpversion() : ini_get( $setting );
	}

	/**
	 * Checks if specific PHP setting is fulfilled
	 *
	 * @access public
	 * @param $setting
	 * @return bool
	 */
	public static function is_setting_fulfilled( $setting ) {
		$setting_info = One_Click::$required_settings[ $setting ];
		$current_value = One_Click_Launcher::get_setting_value( $setting );
		$required_value = $setting_info['min'];

		return $setting == 'phpversion' ? version_compare( $current_value, $required_value, '>=' ) : One_Click::return_bytes( $current_value ) >= One_Click::return_bytes( $required_value );
	}

	/**
	 * Checks if exports folder exists
	 *
	 * @access public
	 * @return bool
	 */
	public static function exports_exists() {
		if ( ! file_exists( get_template_directory() . '/' . ONE_CLICK_EXPORTS_DIR ) ) {
			return false;
		}

		return true;
	}
}

One_Click_Launcher::init();