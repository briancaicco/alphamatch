<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Compare_Scripts
 *
 * @class Inventor_Compare_Scripts
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Compare_Scripts {
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
        wp_register_script( 'inventor-compare', plugins_url( '/inventor-compare/assets/js/inventor-compare.js' ), array( 'jquery' ), '20170107', true );
        wp_enqueue_script( 'inventor-compare' );
    }
}

Inventor_Compare_Scripts::init();