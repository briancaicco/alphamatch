<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://bravevisuals.com
 * @since             1.0.0
 * @package           Alpha_Coupons
 *
 * @wordpress-plugin
 * Plugin Name:       Alpha Coupons
 * Plugin URI:        http://alpamatch.com
 * Description:       
 * Version:           1.0.0
 * Author:            Brave Visuals
 * Author URI:        http://bravevisuals.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       alpha-coupons
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );



//////////////////////////////////////////////////////////////////////////////////////////////
// Create Admin Menu Item
//////////////////////////////////////////////////////////////////////////////////////////////

function alpha_coupons_menu() { 
	add_menu_page('Alpha Coupons', 'Alpha Coupons', 'read', 'manage-alpha-coupons', '', 'dashicons-tickets-alt', 58);
}
add_action('admin_menu', 'alpha_coupons_menu'); 


//////////////////////////////////////////////////////////////////////////////////////////////
// Create Custom Post Types
//////////////////////////////////////////////////////////////////////////////////////////////
function alpha_custom_post_types() {

	$types = array(

			// Coupons
		array('the_type' => 'alpha_coupons',
			'single' => 'All Coupons',
			'plural' => 'All Coupons'),

			// Redeemed
		array('the_type' => 'alpha_redeemed',
			'single' => 'Redeemed Coupon',
			'plural' => 'Redeemed Coupons'),

			// // Derp
			// array('the_type' => 'derp',
			//          'single' => 'Derp',
			//          'plural' => 'Derp')

		);

	foreach ($types as $type) {

		$the_type = $type['the_type'];
		  $single = $type['single'];
		  $plural = $type['plural'];

		  $labels = array(
			'name' => _x($plural, 'post type general name'),
			'singular_name' => _x($single, 'post type singular name'),
			'add_new' => _x('Add New', $single),
			'add_new_item' => __('Add New '. $single),
			'edit_item' => __('Edit '.$single),
			'new_item' => __('New '.$single),
			'view_item' => __('View '.$single),
			'search_items' => __('Search '.$plural),
			'not_found' =>  __('No '.$plural.' found'),
			'not_found_in_trash' => __('No '.$plural.' found in Trash'),
			'parent_item_colon' => ''
		  );

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'show_in_menu' => 'manage-alpha-coupons',
			'supports' => array('title')
				  );

		  register_post_type($the_type, $args);

	}

}
add_action('init', 'alpha_custom_post_types');




//////////////////////////////////////////////////////////////////////////////////////////////
// Add the custom columns to the coupons post type
//////////////////////////////////////////////////////////////////////////////////////////////

function set_custom_edit_alpha_coupons_columns($columns) {
	unset( $columns['date'] );
	$columns['alpha_coupon_expiry_date'] = __( 'Expires', 'wordpress' );
	$columns['alpha_coupon_type'] = __( 'Type', 'wordpress' );

	return $columns;
}
add_filter( 'manage_alpha_coupons_posts_columns', 'set_custom_edit_alpha_coupons_columns' );



// Add the data to the custom columns for the alpha_coupons post type:
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
add_action( 'manage_alpha_coupons_posts_custom_column' , 'custom_alpha_coupons_column', 10, 2 );


//////////////////////////////////////////////////////////////////////////////////////////////
// func that is going to set our Coupon post titles magically
//////////////////////////////////////////////////////////////////////////////////////////////
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




//////////////////////////////////////////////////////////////////////////////////////////////
// Create Form on Paymemnt page to accept coupon code
//////////////////////////////////////////////////////////////////////////////////////////////

function alpha_create_coupon_form() {
		global $current_user;

		ob_start(); ?>
		<h2>Discount</h2>
		<div class="form-group">
			<label for="alpha_trial_coupon">Coupon Code</label>
			<input type="text" class="form-control js-validate js-validate-coupon js-validate-coupon1 js-validate-coupon2" name="alpha_submitted_code" id="alpha_submitted_code" />
			<p class="err"></p>
		</div>

		<?php 

		$content = ob_get_clean();

		$code = 'ALPHA2018';

		$post_information = array(
		    'post_title' => wp_strip_all_tags( $_POST['alpha_submitted_code'] ),
		    'post_content' => '',
		    'post_type' => 'alpha_redeemed',
		    'post_status' => 'publish',
		    'post_author' => $current_user->ID
		);

		wp_insert_post( $post_information );

		echo $content;
}
add_action( 'inventor_payment_form_fields', 'alpha_create_coupon_form' );



//////////////////////////////////////////////////////////////////////////////////////////////
// Create the coupons status
//////////////////////////////////////////////////////////////////////////////////////////////
function alpha_check_coupon_status() {

		global $current_user;

		// Get the users coupons
		$args = array(
		'author' 		=> $current_user->ID, 
		'post_type' 	=> 'alpha_redeemed', 
		'post_status'	=> 'publish',
		);

		$posts_array = get_posts($args);

		if(in_array('ALPHA2018', $posts_array)){
			foreach ( $posts_array as $post ) : setup_postdata( $post );
				$coupon_redeemed_date = get_the_date();
			endforeach;
			wp_reset_postdata();
		} else {
			return false;
		}

		// set current date
		$then = $coupon_redeemed_date;
		$now = new DateTime();

		// get the difference between today and the coupon applied date
		$diff = date_diff( $then, $now );

		//now convert the $diff object to type integer
		$intDiff = $diff->format( "%R%a" );
		$intDiff = intval( $intDiff );

		// Calculate time left until coupon expires
		// TODO: make expiary time dynamic (currently set for 30 days)
		$timeleft = 30 - $intDiff;


		// Display coupon status
		if ($timeleft < 0 ){
			
			$coupon_status = 'expired';
			if( current_user_can( 'manage_options' ) ){}
			else{	
				$current_user->remove_role('alpha');
			}

		} else{

			$coupon_status = 'active';
			$current_user->add_role('alpha');

		}

	}

add_action( 'login_init', 'alpha_check_coupon_status');

