<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Compare_Shortcodes
 *
 * @class Inventor_Compare_Shortcodes
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Compare_Shortcodes {
    /**
     * Initialize shortcodes
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_shortcode( 'inventor_compare', array( __CLASS__, 'compare' ) );
    }

    /**
     * Compare
     *
     * @access public
     * @param $atts
     * @return void
     */
    public static function compare( $atts ) {
        if ( empty( $atts ) && ! is_array( $atts ) ) {
            $atts = array();
        }

        $common_fields = Inventor_Compare_Logic::get_common_fields();
        $attributes = array();
        $compare_data = array();

        $listings_ids = Inventor_Compare_Logic::get_comparison_list();

        foreach( $listings_ids as $listing_id ) {
            $attributes[ $listing_id ] = Inventor_Post_Types::get_attributes( $listing_id, true );
        }

        $fields = apply_filters( 'inventor_compare_fields', $common_fields );

        foreach ( $fields as $field_id => $field_name ) {
            foreach ( $listings_ids as $listing_id ) {
                $attribute = isset( $attributes[ $listing_id ][ $field_id ] ) ? $attributes[ $listing_id ][ $field_id ] : null;
                $value = isset( $attribute ) ? $attribute['value'] : '-';
                $compare_data[ $field_name ][ $listing_id ] = $value;
            }
        }

        $atts['compare_data'] = apply_filters( 'inventor_compare_data', $compare_data, $listings_ids );

        $comparison_query = Inventor_Compare_Logic::get_comparison_query();
        query_posts( $comparison_query->query );
        echo Inventor_Template_Loader::load( 'compare', $atts, $plugin_dir = INVENTOR_COMPARE_DIR );
        wp_reset_query();
    }
}

Inventor_Compare_Shortcodes::init();