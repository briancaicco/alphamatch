<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://bravevisuals.com
 * @since      1.0.0
 *
 * @package    Alpha_Coupons
 * @subpackage Alpha_Coupons/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the admin menu for the plugin
 *
 * @package    Alpha_Coupons
 * @subpackage Alpha_Coupons/admin
 * @author     Brave Visuals <e@bravevisuals.com>
 */


// Register Custom Alpha Coupon
function alpha_coupons_post_type() {

	$labels = array(
		'name'                  => _x( 'Alpha Coupons', 'Alpha Coupon General Name', 'wordpress' ),
		'singular_name'         => _x( 'Alpha Coupon', 'Alpha Coupon Singular Name', 'wordpress' ),
		'menu_name'             => __( 'Alpha Coupons', 'wordpress' ),
		'name_admin_bar'        => __( 'Alpha Coupon', 'wordpress' ),
		'archives'              => __( 'Coupon Archives', 'wordpress' ),
		'attributes'            => __( 'Coupon Attributes', 'wordpress' ),
		'parent_item_colon'     => __( 'Parent Item:', 'wordpress' ),
		'all_items'             => __( 'All Coupons', 'wordpress' ),
		'add_new_item'          => __( 'Add New Coupon', 'wordpress' ),
		'add_new'               => __( 'Add New', 'wordpress' ),
		'new_item'              => __( 'New Coupon', 'wordpress' ),
		'edit_item'             => __( 'Edit Coupon', 'wordpress' ),
		'update_item'           => __( 'Update Coupon', 'wordpress' ),
		'view_item'             => __( 'View Coupon', 'wordpress' ),
		'view_items'            => __( 'View Coupons', 'wordpress' ),
		'search_items'          => __( 'Search Coupons', 'wordpress' ),
		'not_found'             => __( 'Not found', 'wordpress' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wordpress' ),
		'featured_image'        => __( 'Featured Image', 'wordpress' ),
		'set_featured_image'    => __( 'Set featured image', 'wordpress' ),
		'remove_featured_image' => __( 'Remove featured image', 'wordpress' ),
		'use_featured_image'    => __( 'Use as featured image', 'wordpress' ),
		'insert_into_item'      => __( 'Insert into item', 'wordpress' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'wordpress' ),
		'items_list'            => __( 'Coupons list', 'wordpress' ),
		'items_list_navigation' => __( 'Coupons list navigation', 'wordpress' ),
		'filter_items_list'     => __( 'Filter coupons list', 'wordpress' ),
	);
	$args = array(
		'label'                 => __( 'Alpha Coupon', 'wordpress' ),
		'description'           => __( 'Alpha Coupon Description', 'wordpress' ),
		'labels'                => $labels,
		'supports'              => '',
		'taxonomies'            => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 58,
		'menu_icon'				=> 'dashicons-tickets-alt',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'alpha_coupons', $args );

}
add_action( 'init', 'alpha_coupons_post_type', 0 );



// func that is going to set our title of our customer magically
function alpha_coupons_set_title( $data , $postarr ) {

    // We only care if it's our customer
    if( $data[ 'post_type' ] === 'alpha_coupons' ) {

        // get the customer name from _POST or from post_meta
        $alpha_coupon_code = ( ! empty( $_POST[ 'alpha_coupon_code' ] ) ) ? $_POST[ 'alpha_coupon_code' ] : get_post_meta( $postarr[ 'ID' ], 'alpha_coupon_code', true );

        // if the name is not empty, we want to set the title
        if( $alpha_coupon_code !== '' ) {

            // sanitize name for title
            $data[ 'post_title' ] = $alpha_coupon_code;
            // sanitize the name for the slug
            $data[ 'post_name' ]  = sanitize_title( sanitize_title_with_dashes( $alpha_coupon_code, '', 'save' ) );
        }
    }
    return $data;
}
add_filter( 'wp_insert_post_data' , 'alpha_coupons_set_title' , '99', 2 );


// Add the custom columns to the coupons post type:
add_filter( 'manage_alpha_coupons_posts_columns', 'set_custom_edit_alpha_coupons_columns' );
function set_custom_edit_alpha_coupons_columns($columns) {
    unset( $columns['date'] );
    $columns['alpha_coupon_expiry_date'] = __( 'Expires', 'wordpress' );
    $columns['alpha_coupon_type'] = __( 'Type', 'wordpress' );

    return $columns;
}

// Add the data to the custom columns for the alpha_coupons post type:
add_action( 'manage_alpha_coupons_posts_custom_column' , 'custom_alpha_coupons_column', 10, 2 );
function custom_alpha_coupons_column( $column, $post_id ) {
    switch ( $column ) {

        case 'alpha_coupon_expiry_date' :
            echo get_post_meta( $post_id , 'alpha_coupon_expiry_date' , true ); 
        break;
        case 'alpha_coupon_type' :
            echo get_post_meta( $post_id , 'alpha_coupon_type' , true ); 
        break;

    }
}




// /** Step 1. */
// function alpha_coupons_plugin_menu() {
// 	add_options_page( 'Alpha Coupons Options', 'Alpha Coupons', 'manage_options', 'alpha-coupons-options', 'alpha_coupons_options' );
// 	add_menu_page( 'Alpha Coupons Options', 'Alpha Coupons', 'manage_options', 'alpha-coupons-options', 'alpha_coupons_options', $icon_url, 'app' );
// }
// /** Step 2 (from text above). */
// add_action( 'admin_menu', 'alpha_coupons_plugin_menu' );
// /** Step 3. */
// function alpha_coupons_options() {
// 	if ( !current_user_can( 'manage_options' ) )  {
// 		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
// 	}
// 	echo '<div class="wrap">';
// 	echo '<p>Options will show up here.</p>';
// 	echo '</div>';
// }