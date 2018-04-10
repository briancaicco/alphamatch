<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Customizations_Bookings
 *
 * @class Inventor_Bookings_Customizations_Bookings
 * @package Inventor_Bookings/Classes/Customizations
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Customizations_Bookings {
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

        // Booking detail page
        $wp_customize->add_setting( 'inventor_bookings_detail_page', array(
            'default' => null,
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_detail_page', array(
            'type' => 'select',
            'label' => __( 'Booking detail', 'inventor-bookings' ),
            'section' => 'inventor_pages',
            'settings' => 'inventor_bookings_detail_page',
            'choices' => $pages,
        ) );

        // My bookings page
        $wp_customize->add_setting( 'inventor_bookings_page', array(
            'default' => null,
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_page', array(
            'type' => 'select',
            'label' => __( 'My bookings', 'inventor-bookings' ),
            'section' => 'inventor_pages',
            'settings' => 'inventor_bookings_page',
            'choices' => $pages,
        ) );

        $wp_customize->add_section( 'inventor_bookings', array(
            'title'     => __( 'Inventor Bookings', 'inventor-bookings' ),
            'priority'  => 1,
        ) );

        // Min persons
        $wp_customize->add_setting( 'inventor_bookings_min_persons', array(
            'default'           => 1,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_min_persons', array(
            'type'        => 'number',
            'label'       => __( 'Minimum persons', 'inventor-bookings' ),
            'section'     => 'inventor_bookings',
            'settings'    => 'inventor_bookings_min_persons',
            'description' => __( 'Limitation of minimum persons needed for each booking.', 'inventor-bookings' ),
        ) );

        // Max persons
        $wp_customize->add_setting( 'inventor_bookings_max_persons', array(
            'default'           => 10,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_max_persons', array(
            'type'        => 'number',
            'label'       => __( 'Maximum persons', 'inventor-bookings' ),
            'section'     => 'inventor_bookings',
            'settings'    => 'inventor_bookings_max_persons',
            'description' => __( 'Limitation of maximum persons allowed for each booking.', 'inventor-bookings' ),
        ) );

        // Default booking status
        $wp_customize->add_setting( 'inventor_bookings_default_status', array(
            'default'           => 'awaiting_approval',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_default_status', array(
            'type'          => 'select',
            'label'         => __( 'Default ordering for listing archive', 'inventor-bookings' ),
            'section'       => 'inventor_bookings',
            'settings'      => 'inventor_bookings_default_status',
            'choices'		=> Inventor_Bookings_Logic::booking_statuses()
        ) );

        // Booking has to be prepaid
        $wp_customize->add_setting( 'inventor_bookings_prepaid', array(
            'default'           => false,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        $wp_customize->add_control( 'inventor_bookings_prepaid', array(
            'type'      	=> 'checkbox',
            'label'     	=> __( 'Prepaid bookings', 'inventor' ),
            'description'	=> __( 'Check if the approved booking has to be paid by customer.', 'inventor-bookings' ),
            'section'   	=> 'inventor_bookings',
            'settings'  	=> 'inventor_bookings_prepaid',
        ) );
    }
}

Inventor_Bookings_Customizations_Bookings::init();
