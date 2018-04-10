<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class One_Click_Menu
 *
 * @class One_Click_Menu
 * @package One_Click/Classes
 * @author Pragmatic Mates
 */
class One_Click_Menu {
	/**
	 * Initialize scripts
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}

	/**
	 * Add one click installation menu link
	 *
	 * @access public
	 * @return void
	 */
	public static function admin_menu() {
		add_submenu_page( 'tools.php', __( 'One Click Installation', 'one-click'), __( 'One Click Installation', 'one-click' ), 'manage_options', 'one-click', array( 'One_Click_Launcher', 'template' ) );
	}
}

One_Click_Menu::init();