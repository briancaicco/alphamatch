<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Customizations_Reviews
 *
 * @class Inventor_Reviews_Customizations_Reviews
 * @package Inventor_Reviews/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Reviews_Customizations_Reviews {
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
        $wp_customize->add_section( 'inventor_reviews', array(
            'title'     => __( 'Inventor Reviews', 'inventor-reviews' ),
            'priority'  => 1,
        ) );

        // Pros and Cons
        $wp_customize->add_setting( 'inventor_reviews_pros_and_cons_enabled', array(
            'default'           => true,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_reviews_pros_and_cons_enabled', array(
            'type'          => 'checkbox',
            'label'         => __( 'Enable Pros and Cons fields', 'inventor-reviews' ),
            'description'   => __( 'If disabled, single textarea will be shown.', 'inventor-reviews' ),
            'section'       => 'inventor_reviews',
            'settings'      => 'inventor_reviews_pros_and_cons_enabled',
        ) );

        // File field
        $wp_customize->add_setting( 'inventor_reviews_media_upload_enabled', array(
            'default'           => true,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_reviews_media_upload_enabled', array(
            'type'     => 'checkbox',
            'label'    => __( 'Enable media upload', 'inventor-reviews' ),
            'section'  => 'inventor_reviews',
            'settings' => 'inventor_reviews_media_upload_enabled',
        ) );
    }
}

Inventor_Reviews_Customizations_Reviews::init();
