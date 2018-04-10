<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Google_Map_Customizations_Google_Map
 *
 * @class Inventor_Google_Map_Customizations_Google_Map
 * @package Inventor_Google_Map/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Google_Map_Customizations_Google_Map {
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
        $wp_customize->add_section( 'inventor_google_map', array(
            'title' 	=> __( 'Inventor Google Map', 'inventor-google-map' ),
            'priority' 	=> 1,
        ) );

        // Google Map Marker Icon
        $wp_customize->add_setting( 'inventor_google_map_marker_icon_by_category', array(
            'default'           => false,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_google_map_marker_icon_by_category', array(
            'label'             => __( 'Marker icon by category', 'inventor-google-map' ),
            'description'       => __( 'Default: icon by listing type', 'inventor-google-map' ),
            'section'           => 'inventor_google_map',
            'settings'          => 'inventor_google_map_marker_icon_by_category',
            'type'              => 'checkbox',
        ) );
    }
}

Inventor_Google_Map_Customizations_Google_Map::init();
