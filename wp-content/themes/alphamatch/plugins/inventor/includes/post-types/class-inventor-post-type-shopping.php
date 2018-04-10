<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Post_Type_Shopping
 *
 * @class Inventor_Post_Type_Shopping
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Post_Type_Shopping {
	/**
	 * Initialize custom post type
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ) );
        add_action( 'cmb2_init', array( __CLASS__, 'fields' ) );
        add_filter( 'inventor_shop_allowed_listing_post_types', array( __CLASS__, 'allowed_purchasing' ) );
        add_filter( 'inventor_claims_allowed_listing_post_types', array( __CLASS__, 'allowed_claiming' ) );
    }

    /**
     * Defines if post type can be claimed
     *
     * @access public
     * @param array $post_types
     * @return array
     */
    public static function allowed_claiming( $post_types ) {
        $post_types[] = 'shopping';
        return $post_types;
    }

    /**
     * Defines if post type can be purchased
     *
     * @access public
     * @param array $post_types
     * @return array
     */
    public static function allowed_purchasing( $post_types ) {
        $post_types[] = 'shopping';
        return $post_types;
    }

	/**
	 * Custom post type definition
	 *
	 * @access public
	 * @return void
	 */
	public static function definition() {

		$labels = array(
			'name'                  => __( 'Shoppings', 'inventor' ),
			'singular_name'         => __( 'Shopping', 'inventor' ),
			'add_new'               => __( 'Add New Shopping', 'inventor' ),
			'add_new_item'          => __( 'Add New Shopping', 'inventor' ),
			'edit_item'             => __( 'Edit Shopping', 'inventor' ),
			'new_item'              => __( 'New Shopping', 'inventor' ),
			'all_items'             => __( 'Shoppings', 'inventor' ),
			'view_item'             => __( 'View Shopping', 'inventor' ),
			'search_items'          => __( 'Search Shopping', 'inventor' ),
			'not_found'             => __( 'No Shoppings found', 'inventor' ),
			'not_found_in_trash'    => __( 'No Shoppings Found in Trash', 'inventor' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Shoppings', 'inventor' ),
            'icon'                  => apply_filters( 'inventor_listing_type_icon', 'inventor-poi-cart', 'shopping' )
		);

		register_post_type( 'shopping',
			array(
				'labels'            => $labels,
				'show_in_menu'	    => 'listings',
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
				'has_archive'       => true,
				'rewrite'           => array( 'slug' => _x( 'shoppings', 'URL slug', 'inventor' ) ),
				'public'            => true,
				'show_ui'           => true,
                'show_in_rest'      => true,
				'categories'        => array(),
			)
		);
	}

    /**
     * Defines custom fields
     *
     * @access public
     * @return array
     */
    public static function fields() {
        $post_type = 'shopping';

        Inventor_Post_Types::add_metabox( $post_type, array( 'general' ) );

        $metabox_id = INVENTOR_LISTING_PREFIX . 'shopping_details';
        
        $cmb = new_cmb2_box( array(
            'id'            => $metabox_id,
            'title'         => __( 'Details', 'inventor' ),
            'object_types'  => array( 'shopping' ),
            'context'       => 'normal',
            'priority'      => 'high',
        ) );

        $field_name = __( 'Shopping category', 'inventor' );
        $field_id = INVENTOR_LISTING_PREFIX . 'shopping_category';
        $field_type = 'taxonomy_select';
        $cmb->add_field( array(
            'id'                => $field_id,
            'name'              => apply_filters( 'inventor_metabox_field_name', $field_name, $metabox_id, $field_id, $post_type ),
            'type'              => apply_filters( 'inventor_metabox_field_type', $field_type, $metabox_id, $field_id, $post_type ),
            'taxonomy'          => 'shopping_categories',
        ) );

        $field_name = __( 'Color', 'inventor' );
        $field_id = INVENTOR_LISTING_PREFIX . 'color';
        $field_type = 'taxonomy_multicheck_hierarchy';
        $cmb->add_field( array(
            'id'                => $field_id,
            'name'              => apply_filters( 'inventor_metabox_field_name', $field_name, $metabox_id, $field_id, $post_type ),
            'type'              => apply_filters( 'inventor_metabox_field_type', $field_type, $metabox_id, $field_id, $post_type ),
            'taxonomy'          => 'colors',
        ) );

        $field_name = __( 'Size', 'inventor' );
        $field_id = INVENTOR_LISTING_PREFIX . 'size';
        $field_type = 'text_small';
        $cmb->add_field( array(
            'id'                => $field_id,
            'name'              => apply_filters( 'inventor_metabox_field_name', $field_name, $metabox_id, $field_id, $post_type ),
            'type'              => apply_filters( 'inventor_metabox_field_type', $field_type, $metabox_id, $field_id, $post_type ),
            'description'       => __( 'For example M, 10cm, 47 ...', 'inventor' )
        ) );

        $field_name = __( 'Weight', 'inventor' );
        $field_id = INVENTOR_LISTING_PREFIX . 'weight';
        $field_type = 'text_small';
        $cmb->add_field( array(
            'id'                => $field_id,
            'name'              => apply_filters( 'inventor_metabox_field_name', $field_name, $metabox_id, $field_id, $post_type ),
            'type'              => apply_filters( 'inventor_metabox_field_type', $field_type, $metabox_id, $field_id, $post_type ),
            'attributes' => array(
                'type' => 'number',
                'pattern' => '\d*',
            ),
        ) );

        Inventor_Post_Types::add_metabox( 'shopping', array( 'gallery', 'video', 'price', 'location', 'flags', 'contact', 'social', 'listing_category' ) );
    }
}

Inventor_Post_Type_Shopping::init();