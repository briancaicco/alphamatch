<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Compare_Customizations_Compare
 *
 * @class Inventor_Compare_Customizations_Compare
 * @package Inventor_Compare/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Compare_Customizations_Compare {
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

        // Compare
        $wp_customize->add_setting( 'inventor_compare_page', array(
            'default' => null,
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_compare_page', array(
            'type' => 'select',
            'label' => __( 'Compare listings', 'inventor-compare' ),
            'section' => 'inventor_pages',
            'settings' => 'inventor_compare_page',
            'choices' => $pages,
        ) );
    }
}

Inventor_Compare_Customizations_Compare::init();
