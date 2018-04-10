<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class One_Click_Scripts
 *
 * @class One_Click_Scripts
 * @package One_Click/Classes
 * @author Pragmatic Mates
 */
class One_Click_Scripts {
	/**
	 * Initialize scripts
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_backend' ) );
	}

	/**
	 * Loads backend files
	 *
	 * @access public
	 * @return void
	 */
	public static function enqueue_backend() {
		wp_enqueue_script( 'one-click', plugins_url( '/one-click/assets/js/one-click.js' ), array( 'jquery' ), false, true );
		wp_enqueue_style( 'one-click', plugins_url( '/one-click/assets/css/one-click.css' ), array(), '20161011' );
	}

}

One_Click_Scripts::init();
