<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Claims_Shortcodes
 *
 * @class Inventor_Claims_Shortcodes
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Claims_Shortcodes {
    /**
     * Initialize shortcodes
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_shortcode( 'inventor_claim_form', array( __CLASS__, 'claim_form' ) );
    }

    /**
     * Claim form
     *
     * @access public
     * @param $atts
     * @return void
     */
    public static function claim_form( $atts ) {
        if ( ! is_user_logged_in() ) {
            echo Inventor_Template_Loader::load( 'misc/not-allowed' );
            return;
        }

        if ( ! empty( $_GET['id'] ) ) {
            $atts = array(
                'listing' => Inventor_Post_Types::get_listing( $_GET['id'] )
            );
        }

        echo Inventor_Template_Loader::load( 'claims-form', $atts, $plugin_dir = INVENTOR_CLAIMS_DIR );
    }
}

Inventor_Claims_Shortcodes::init();