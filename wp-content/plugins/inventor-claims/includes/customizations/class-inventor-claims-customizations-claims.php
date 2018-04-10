<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Claims_Customizations_Claims
 *
 * @class Inventor_Claims_Customizations_Claims
 * @package Inventor_Claims/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Claims_Customizations_Claims {
    /**
     * Initialize customization type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'customize_register', array( __CLASS__, 'customizations' ) );
    }

    /**
     * Customizations
     *
     * @access public
     * @param object $wp_customize
     * @return void
     */
    public static function customizations( $wp_customize ) {
        $pages = Inventor_Utilities::get_pages();

        // Claim page
        $wp_customize->add_setting( 'inventor_claims_page', array(
            'default' => null,
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_claims_page', array(
            'type' => 'select',
            'label' => __( 'Claim listing', 'inventor-claims' ),
            'section' => 'inventor_pages',
            'settings' => 'inventor_claims_page',
            'choices' => $pages,
        ) );
    }
}

Inventor_Claims_Customizations_Claims::init();
