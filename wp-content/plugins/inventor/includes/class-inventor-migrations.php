<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Migrations
 *
 * @class Inventor_Migrations
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Migrations {
    const TOTAL_MIGRATIONS = 2;

    /**
     * Initialize scripts
     *
     * @access public
     * @return void
     */
    public static function init() {
        self::constants();

        add_action( 'cmb2_init', array( __CLASS__, 'migrate_all' ), 99 );
    }

    /**
     * Defines constants
     *
     * @access public
     * @return void
     */
    public static function constants() {
        define( 'INVENTOR_MIGRATION_KEY', 'migration_' . Inventor::DOMAIN );
    }

    /**
     * Gets migration version
     *
     * @access public
     * @param $key string
     * @return int
     */
    public static function get_version( $key ) {
        return get_option( $key, 0 );
    }

    /**
     * Sets migration version
     *
     * @access public
     * @param $key string
     * @param $version int
     * @return void
     */
    public static function set_version( $key, $version ) {
        update_option( $key, $version );
    }

    /**
     * Migrate all migrations
     *
     * @access public
     * @return void
     */
    public static function migrate_all() {
        while ( get_option( INVENTOR_MIGRATION_KEY ) < self::TOTAL_MIGRATIONS ) {
            $next_version = get_option( INVENTOR_MIGRATION_KEY ) + 1;
            self::migrate( $next_version );
        }
    }

    /**
     * Migrate single migration
     *
     * @access public
     * @param $version
     * @return void
     */
    public static function migrate( $version ) {
        global $wpdb;

        if ( $version == 1 ) {
            $sql = 'UPDATE ' . $wpdb->prefix . 'usermeta SET `meta_value` = 1 WHERE `meta_key` = "wp_user_level" AND `meta_value` = 0';
            $wpdb->query( $sql );
        }

        if ( $version == 2 ) {
            $post_types = Inventor_Post_Types::get_listing_post_types();

            $query = new WP_Query( array(
                'post_type'         => $post_types,
                'posts_per_page'    => -1,
                'post_status'       => 'any',
                'meta_key'       =>  INVENTOR_LISTING_PREFIX . 'featured',
                'meta_compare'   => 'NOT EXISTS',
            ) );

            foreach ( $query->posts as $listing ) {
                update_post_meta( $listing->ID, INVENTOR_LISTING_PREFIX . 'featured', 0 );
            }
        }

        self::set_version( INVENTOR_MIGRATION_KEY, $version );
    }
}

Inventor_Migrations::init();