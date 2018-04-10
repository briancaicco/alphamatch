<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Packages_Migrations
 *
 * @class Inventor_Packages_Migrations
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Packages_Migrations {
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
        define( 'INVENTOR_PACKAGES_MIGRATION_KEY', 'migration_' . Inventor_Packages::DOMAIN );
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
        while ( get_option( INVENTOR_PACKAGES_MIGRATION_KEY ) < self::TOTAL_MIGRATIONS ) {
            $next_version = get_option( INVENTOR_PACKAGES_MIGRATION_KEY ) + 1;
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
        // change old metabox permission fields into options of single permissions field
        if ( $version == 1 ) {
            $packages = Inventor_Packages_Logic::get_packages( true, true, true );
            $metabox_permissions = apply_filters( 'inventor_packages_metabox_permissions', array(), 999 );

            foreach ( $packages as $package ) {
                // social metabox is a new permissions and allowed by default
                $package_allowed_metaboxes = array( 'social' );

                // check each existing package metabox permission
                foreach ( $metabox_permissions as $metabox => $label ) {
                    $field_id = INVENTOR_PACKAGE_PREFIX . 'metabox_' . $metabox . '_allowed';
                    $allowed_by_package = get_post_meta( $package->ID, $field_id, true );

                    // if package permission is allowed
                    if ( $allowed_by_package ) {
                        $package_allowed_metaboxes[] = $metabox;
                    }
                }

                // update package metabox permissions
                update_post_meta( $package->ID, INVENTOR_PACKAGE_PREFIX . 'metabox_permissions', $package_allowed_metaboxes );
            }
        }

        if ( $version == 2 ) {
            $packages = Inventor_Packages_Logic::get_packages( true, true, true );

            foreach ( $packages as $package ) {
                $max_listings = get_post_meta( $package->ID, INVENTOR_PACKAGE_PREFIX . 'max_listings', true );

                if ( $max_listings == -1 ) {
                    update_post_meta( $package->ID, INVENTOR_PACKAGE_PREFIX . 'max_listings', '' );
                }
            }
        }

        self::set_version( INVENTOR_PACKAGES_MIGRATION_KEY, $version );
    }
}

Inventor_Packages_Migrations::init();