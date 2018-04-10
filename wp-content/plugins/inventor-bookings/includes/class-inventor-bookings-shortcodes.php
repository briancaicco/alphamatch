<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Bookings_Shortcodes
 *
 * @class Inventor_Bookings_Shortcodes
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Bookings_Shortcodes {
    /**
     * Initialize shortcodes
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_shortcode( 'inventor_booking_detail', array( __CLASS__, 'booking_detail' ) );
        add_shortcode( 'inventor_bookings', array( __CLASS__, 'bookings' ) );
        add_shortcode( 'inventor_bookings_manager', array( __CLASS__, 'bookings_manager' ) );
    }

    /**
     * User Bookings
     *
     * @access public
     * @param $atts
     * @return void
     */
    public static function bookings( $atts ) {
        if ( ! is_user_logged_in() ) {
            echo Inventor_Template_Loader::load( 'misc/not-allowed' );
            return;
        }

        $meta = array();
        $now = current_time( 'timestamp' );
        $order = 'asc';

        if ( is_array( $atts ) ) {
            if ( array_key_exists( 'time', $atts ) ) {
                if ( $atts['time'] == 'past' ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'book_from',
                        'value'     => $now,
                        'compare'   => '<',
                        'type'      => 'NUMERIC',
                    );
                }

                if( $atts['time'] == 'upcoming' ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'book_from',
                        'value'     => $now,
                        'compare'   => '>',
                        'type'      => 'NUMERIC',
                    );
                }
            }

            if ( array_key_exists( 'status', $atts ) ) {
                if ( strpos( $atts['status'], ',' ) === false ) {
                    $statuses = array( $atts['status'] );
                } else {
                    $statuses = explode( ',', $atts['status'] );
                }

                if ( ! empty( $statuses ) ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'status',
                        'value'     => $statuses,
                        'compare'   => 'IN',
                    );
                }
            }

            if ( array_key_exists( 'order', $atts ) ) {
                $order = $atts['order'];
            }
        }

        Inventor_Bookings_Logic::loop_my_bookings( $meta, $order );
        echo Inventor_Template_Loader::load( 'bookings/list', $atts, $plugin_dir = INVENTOR_BOOKINGS_DIR );
        wp_reset_query();
    }

    /**
     * Bookings manager
     *
     * @access public
     * @param $atts
     * @return void
     */
    public static function bookings_manager( $atts ) {
        if ( ! is_user_logged_in() ) {
            echo Inventor_Template_Loader::load( 'misc/not-allowed' );
            return;
        }

        $meta = array();
        $now = current_time( 'timestamp' );
        $order = 'asc';

        if ( is_array( $atts ) ) {
            if ( array_key_exists( 'time', $atts ) ) {
                if ( $atts['time'] == 'past' ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'book_from',
                        'value'     => $now,
                        'compare'   => '<',
                        'type'      => 'NUMERIC',
                    );
                }

                if( $atts['time'] == 'upcoming' ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'book_from',
                        'value'     => $now,
                        'compare'   => '>',
                        'type'      => 'NUMERIC',
                    );
                }
            }

            if ( array_key_exists( 'status', $atts ) ) {
                if ( strpos( $atts['status'], ',' ) === false ) {
                    $statuses = array( $atts['status'] );
                } else {
                    $statuses = explode( ',', $atts['status'] );
                }

                if ( ! empty( $statuses ) ) {
                    $meta[] = array(
                        'key'       => INVENTOR_BOOKING_PREFIX . 'status',
                        'value'     => $statuses,
                        'compare'   => 'IN',
                    );
                }
            }

            if ( array_key_exists( 'order', $atts ) ) {
                $order = $atts['order'];
            }
        }

        Inventor_Bookings_Logic::loop_bookings_of_my_listings( $meta, $order );
        echo Inventor_Template_Loader::load( 'bookings/list-manager', $atts, $plugin_dir = INVENTOR_BOOKINGS_DIR );
        wp_reset_query();
    }

    /**
     * Booking detail
     *
     * @param $atts
     * @param $atts|array
     * @return string
     */
    public static function booking_detail( $atts = array() ) {
        $hash = ! empty( $_GET['hash'] ) ? esc_attr( $_GET['hash'] ) : null;
        $booking = Inventor_Bookings_Logic::get_booking_by_hash( $hash );
        $booking_status = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'status', true );

        if ( $booking_status == 'pending_payment' && ! is_user_logged_in() ) {
            return Inventor_Template_Loader::load( 'misc/not-allowed' );
        }

        $args = array();

        if ( ! empty( $booking ) ) {
            $args = array(
                'booking'         => $booking,
                'listing'         => Inventor_Bookings_Logic::get_booking_listing( $booking->ID ),
            );
        }

        return Inventor_Template_Loader::load( 'bookings/detail', $args, INVENTOR_BOOKINGS_DIR );
    }
}

Inventor_Bookings_Shortcodes::init();
