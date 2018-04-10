<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Customizations_Stripe
 *
 * @class Inventor_Stripe_Customizations_Stripe
 * @package Inventor_Stripe/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Stripe_Customizations_Stripe {
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
        $wp_customize->add_section( 'inventor_stripe', array(
            'title'     => __( 'Inventor Stripe', 'inventor-stripe' ),
            'priority'  => 1,
        ) );

        // Stripe Secret Key
        $wp_customize->add_setting( 'inventor_stripe_secret_key', array(
            'default'           => null,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_stripe_secret_key', array(
            'label'         => __( 'Secret Key', 'inventor-stripe' ),
            'section'       => 'inventor_stripe',
            'settings'      => 'inventor_stripe_secret_key',
        ) );

        // Stripe Publishable Key
        $wp_customize->add_setting( 'inventor_stripe_publishable_key', array(
            'default'           => null,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_stripe_publishable_key', array(
            'label'         => __( 'Publishable Key', 'inventor-stripe' ),
            'section'       => 'inventor_stripe',
            'settings'      => 'inventor_stripe_publishable_key',
        ) );

        // Bitcoin support
        $wp_customize->add_setting( 'inventor_stripe_bitcoin_enabled', array(
            'default'           => null,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_stripe_bitcoin_enabled', array(
            'label'         => __( 'Enable bitcoin', 'inventor-stripe' ),
            'type'          => 'checkbox',
            'section'       => 'inventor_stripe',
            'settings'      => 'inventor_stripe_bitcoin_enabled',
        ) );
    }
}

Inventor_Stripe_Customizations_Stripe::init();
