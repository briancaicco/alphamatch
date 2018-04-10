<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Widget_Booking_Form
 *
 * @class Inventor_Bookings_Widget_Booking_Form
 * @package Inventor_Bookings/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Widget_Booking_Form extends WP_Widget {
    /**
     * Initialize widget
     *
     * @access public
     */
    function __construct() {
        parent::__construct(
            'booking_form',
            __( 'Booking Form', 'inventor-bookings' ),
            array(
                'description' => __( 'Booking form.', 'inventor-bookings' ),
            )
        );
    }

    /**
     * Frontend
     *
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget( $args, $instance ) {
        if ( is_singular() && class_exists( 'Inventor_Template_Loader' ) ) {
            $booking_enabled = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'enabled', true );

            // TODO: create booking price field
            $price = Inventor_Price::get_listing_price( get_the_ID() );

            $can_be_booked = $booking_enabled && ! empty( $price );
            $can_be_booked = apply_filters( 'inventor_bookings_listing_can_be_booked', $can_be_booked, get_the_ID(), $booking_enabled, $price );

            if ( $can_be_booked ) {
                include Inventor_Template_Loader::locate( 'widgets/booking-form', INVENTOR_BOOKINGS_DIR );
            }
        }
    }

    /**
     * Update
     *
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    /**
     * Backend
     *
     * @access public
     * @param array $instance
     * @return void
     */
    function form( $instance ) {
        if ( class_exists( 'Inventor_Template_Loader' ) ) {
            include Inventor_Template_Loader::locate( 'widgets/booking-form-admin', INVENTOR_BOOKINGS_DIR );
            include Inventor_Template_Loader::locate( 'widgets/appearance-admin' );
            include Inventor_Template_Loader::locate( 'widgets/visibility-admin' );
        }
    }
}