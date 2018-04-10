<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Claims_Scripts
 *
 * @class Inventor_Claims_Scripts
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Claims_Scripts {
    /**
     * Initialize scripts
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ) );
    }

    /**
     * Loads frontend files
     *
     * @access public
     * @return void
     */
    public static function enqueue_frontend() {
        wp_register_script( 'inventor-claims', plugins_url( '/inventor-claims/assets/js/inventor-claims.js' ), array( 'jquery' ), false, true );
        wp_enqueue_script( 'inventor-claims' );
    }
}

Inventor_Claims_Scripts::init();