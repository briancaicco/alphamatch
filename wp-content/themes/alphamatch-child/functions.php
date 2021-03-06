<?php
/**
 * Superlist child functions and definitions
 *
 * @package Superlist Child
 * @since Superlist Child 1.0.0
 */


/**
 * Enqueue scripts & styles
 *
 * @action wp_enqueue_scripts
 * @return void
 */
function superlist_child_enqueue_files() {
	# Remove original style
   // wp_dequeue_style( 'superlist' );

	# Register style for custom appearance
	wp_register_style( 'superlist-custom', get_stylesheet_directory_uri() . '/assets/css/superlist-custom.css' );

	# Include new styles
	wp_enqueue_style( 'superlist-custom' ); 
	//wp_enqueue_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/assets/libraries/bootstrap-sass/javascripts/bootstrap.min.js', '', '', true );
	wp_enqueue_script( 'validate-js', get_stylesheet_directory_uri() . '/assets/js/validate-min.js', '', '', true );

}

add_action( 'wp_enqueue_scripts', 'superlist_child_enqueue_files' );



// Remove Taxonomy
remove_action( 'init', array( 'Inventor_Taxonomy_Colors', 'definition' ) );
remove_action( 'init', array( 'Inventor_Taxonomy_Locations', 'definition' ) );
remove_action( 'init', array( 'Inventor_Taxonomy_Pet_Animals', 'definition' ) );



/**
 * Dynamic Menu Shortcode
 *
 * @action menu_function
 * @return void
 */
function menu_function($atts, $content = null) {
	extract(
		shortcode_atts(
			array( 'name' => null, ),
			$atts
		)
	);
	return wp_nav_menu(
		array(
			'menu' => $name,
			'echo' => false
		)
	);
}
add_shortcode('menu', 'menu_function');


/**
 * Removes the archive lable 
 *
 * @action post
 * @return void
 */
function alpha_theme_archive_title( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	}

	return $title;
}

add_filter( 'get_the_archive_title', 'alpha_theme_archive_title' );


/**
 * Changes the default order of the listing details page
 *
 * @action post
 * @return void
 */

add_filter( 'inventor_listing_detail_sections', function( $sections, $post_type ) {
	return array(
		'gallery'           => __( 'Gallery', 'inventor' ),
		'overview'          => __( 'Details', 'inventor' ),
		'description'       => __( 'Description', 'inventor' ),
		'contact'           => __( 'Contact', 'inventor' ),
		'location'          => __( 'Location', 'inventor' ),
		'opening_hours'     => __( 'Opening Hours', 'inventor' ),
		'social'            => __( 'Social connections', 'inventor' ),
		'video'             => __( 'Video', 'inventor' ),
		//'food_menu'         => __( 'Meals And Drinks', 'inventor' ),
		//'faq'               => __( 'FAQ', 'inventor' ),
		'children-listings' => __( 'Related listings', 'inventor' ),
		'comments'          => null,
		'report'            => null
	);
}, 10, 2 );




	// Remove CSS version Parameter (messes with cacheing in chrome)
	//////////////////////////////////////////////////////////////////////
function remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );



/**
 * Removes post type Icon for pet post type
 *
 * @action post
 * @return void
 */
add_action( 'inventor_listing_type_icon', function ( $icon, $post_type ) {
	if ( $post_type == 'pet' ) {
		return '';
	}

	return $icon;
}, 10, 2 );


/**
 * Removes custom meta boxes from pet post type
 *
 * @action post
 * @return void
 */
// add_action( 'cmb2_init', 'custom_fields', 11 );

// function custom_fields( ) {
// 	$contact_metabox = CMB2_Boxes::get( INVENTOR_LISTING_PREFIX . 'pet_details' );

// 	if ( ! empty( $contact_metabox ) ) {
// 		$contact_metabox->add_field( array(
// 			'id'         => INVENTOR_LISTING_PREFIX . 'pet_animal',
// 			'taxonomy'       => ''
// 		) );

// 		return '';
// 	}
// }



// /**
//  * Removes custom meta boxes
//  *
//  * @action post
//  * @return void
//  */
// 

// add_action( 'cmb2_init', 'remove_metabox', 10, 0 );

// function remove_metabox() {
//     Inventor_Post_Types::remove_metabox( 
//     	'dog_details', array(
// 		'video',
// 		'listing_street_view',
// 		'listing_street_view_location',
// 		'listing_inside_view',
// 		'listing_inside_view_location',

//     ) 
//    );
// }


// /**
//  * Removes custom meta boxes from pet post type
//  *
//  * @action post
//  * @return void
//  */
// add_action( 'cmb2_init', 'custom_fields', 11 );

// function custom_fields( ) {
// 	$details_metabox = CMB2_Boxes::get( INVENTOR_LISTING_PREFIX . 'pet_details' );

// 	if ( ! empty( $details_metabox ) ) {
// 		$details_metabox->add_field( 
// 			array(
// 				'id'         => INVENTOR_LISTING_PREFIX . 'pet_age',
// 				'name'       => __( 'Age', 'domain' ),
// 				'type'       => 'number'
// 			),
// 			array(
// 				'id'         => INVENTOR_LISTING_PREFIX . 'pet_weight',
// 				'name'       => __( 'Weight', 'domain' ),
// 				'type'       => 'number'
// 			),
// 		);
// 	}
// }


/**
 * Sets cookie to hide login modal
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */
add_action( 'wp_footer', 'alpha_popup_cookie_setter', 500 );

function alpha_popup_cookie_setter() { ?>
<script type="text/javascript">
	(function ($, document, undefined) {
		
		jQuery('.popmake-8188').click('pumSetCookie');
		
		  // setTimeout(function () {
		  //     jQuery('#pum-8188').popmake('close');
		  // }, 5000); // 5 seconds

		}(jQuery, document))
	</script>
	<?php
}

/**
 * Displays a login form.
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */
add_shortcode( 'login-form', 'alpha_loginform_shortcode' );
function alpha_loginform_shortcode( $atts, $content = null ) {

	$defaults = array(      "redirect"              =>  site_url( $_SERVER['REQUEST_URI'] )
);

	extract(shortcode_atts($defaults, $atts));
	if (!is_user_logged_in()) {
		$content = wp_login_form( array( 'echo' => false, 'redirect' => $redirect ) );
	}
	return $content;
}


/**
 * Displays a author contact form
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */
add_shortcode( 'seller-contact-form', 'alpha_auth_contact_form_shortcode' );
function alpha_auth_contact_form_shortcode( $atts, $content = null ) {

	if ( ! fep_current_user_can( 'send_new_message') ) {
		$form = "<div class='fep-error'>".__("<a href='/register' style='color: #666 !important;' ><u><b>Login</b></u></a> or <a href='/register' style='color: #666 !important;' ><u><b>Register</b></u></a> to message this seller.", 'front-end-pm')."</div>";
	} elseif( !empty($_POST['fep_action']) && 'newmessage' == $_POST['fep_action'] ) {
		if( fep_errors()->get_error_messages() ) {
			$form = Fep_Form::init()->form_field_output('newmessage', fep_errors() );
		} else {
			$form = fep_info_output();
		}
	} else {
		$form =  Fep_Form::init()->form_field_output( 'newmessage' );
	}

	return $form;

}



/**
 * Contact Author Ajax Submit Form
 *
 * @since 0.1.0
 * @uses 
 */
add_action("wp_ajax_auth_contact_form", "alpha_author_contact_form_process");

function alpha_author_contact_form_process() {

	wp_localize_script( "the_unique_name_for_your_js",
		'theUniqueNameForYourJSObject',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ), //url for php file that process ajax request to WP
			'nonce' => wp_create_nonce( "unique_id_nonce" ),// this is a unique token to prevent form hijacking
			'someData' => 'extra data you want  available to JS'
		)
	);

}



/**
 * Front End Private Messages
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */

add_filter( 'fep_form_fields', 'fep_cus_fep_form_fields' );

function fep_cus_fep_form_fields( $fields )
{

	unset( $fields['message_title']['minlength'] );
	unset( $fields['message_content']['minlength'] );
	return $fields;
}


/**
 * Front End Private Messages
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */

add_action( 'wp_head', 'fep_cus_fep_form_values' );

function fep_cus_fep_form_values()
{
	global $post;

	$seller_id = $post->post_author;
	$seller_name = get_the_author_meta( 'user_login', $seller_id );
	$post_title = $post->post_title;

	?>
	<script type="text/javascript">
		(function($) {
			$(document).ready(function() {
			// Form adjustments
			$('.fep-form-field-message_title, .fep-form-field-fep-message-to').css('display', 'none')
			$('#fep-message-to').css('display', 'none');
			$('#fep-message-top, #fep-message-to').attr({
				'class' : 'hidden',
				'value' : '<?php echo $seller_name; ?>'           
			}),
			$('#message_title').attr({
				'class' : 'hidden',
				'value' : '<?php echo $post_title; ?>'           
			}),
			// $('#token').attr({
			// 'value' : null          
			// }),
			$('.front-end-pm-form form').attr({
				'action' : null           
			})
			if( $('.fep-success').text().length != 0 ){
				$('#msg-seller-btn').html("<i class='fa fa-thumbs-up'></i>&nbsp;Message sent.");
				$('.fep-success').css('display', 'none');
			}else if ( $('.fep-error').text().length != 0 ){
				//$('.fep-error').clone().appendTo('#msg-seller-btn');
				$('#msg-seller-btn').html($('.fep-error').html());
				$('#msg-seller-btn').addClass('btn-danger');
				$('.fep-error').css('display', 'none');
			}
		});
			
		})(jQuery);


	</script>
	<?php
}



/**
 * Investor social boxes
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */

add_filter( 'inventor_metabox_social_networks', 'custom_social_networks', 10, 2 );

function custom_social_networks( $social_networks, $post_type ) {
	return array(
		'facebook'		=> 'Facebook',
		'twitter'		=> 'Twitter',
		'instagram'		=> 'Instagram',
		'google'		=> 'Google',
	);
}


// Remove Pets post type
function delete_post_type(){
unregister_post_type( 'pet' );
}
add_action('init','delete_post_type');

/**
 * Upgrade users role with purchase
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */

add_action( 'inventor_user_package_was_set', 'alpha_change_user_role', 10, 2);

function alpha_change_user_role() { 

	global $current_user; 

	//get_currentuserinfo();

	// Make sure the user is not an admin
	if ( $current_user && !current_user_can('activate_plugins') ) {

		// Get the package ID of the currently logged in user
		$package_level = get_user_meta( $current_user->ID, 'user_package' , true );

		// If package level is not "Free" do something...
		if ( $package_level != '8107' || $package_level != '697'  ) {
			
			// If package level is not "Premium" do something...
			if ( $package_level != '8108' || $package_level != '8156'  ) {
				
				$current_user->add_role('alpha');
				$current_user->remove_role('premimum');
				$current_user->remove_role('free');
			
			} else{
				
				$current_user->add_role('premimum');
				$current_user->remove_role('alpha');
				$current_user->remove_role('free');
			
			}

		} else{
			
			$current_user->add_role( 'free' );
			$current_user->add_role('alpha');
			$current_user->remove_role('premium');
			
		}

	}

}


/**
 * Time Helper functions
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */

function isPast($time)
{
    return (strtotime($time) < time());
}

function isFuture($time)
{
    return (strtotime($time) > time());

}



/**
 * Redirect non-admins to the homepage after logging into the site.
 *
 * @since 	1.0
 */
function alpha_login_redirect( $redirect_to, $request, $user  ) {
	return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : site_url();
}
add_filter( 'login_redirect', 'alpha_login_redirect', 10, 3 );




// Reset Password Link missing from email fix.
/////////////////////////////////////////////////////////////////////////////////




add_filter ('retrieve_password_message', 'custom_retrieve_password_message', 99, 4);

function custom_retrieve_password_message($content, $key) {
global $wpdb;
if ( empty( $_POST['user_login'] ) ) {

     wp_die('<strong>ERROR</strong>: Enter a username or e-mail address.');

 } else if ( strpos( $_POST['user_login'], '@' ) ) {

     $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );

 }else if(!empty( $_POST['user_login'] )){

     $user_data = get_user_by('login', trim( $_POST['user_login']));

 }elseif ( empty( $user_data ) ){
     wp_die('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));

 }

$user_login_name = $user_data->user_login;


add_filter('wp_mail', function($args) {
$args = '<a href="<?php echo wp_login_url() ?>?action=rp&key=<?php echo $key ?>&login=<?php echo $user_login_name; ?>"><?php echo wp_login_url() ?>?action=rp&key=<?php echo $key ?>&login=<?php echo $user_login_name; ?></a>';
$args['alpa_pass_reset_link'] = do_shortcode($args['alpa_pass_reset_link']);
return $args;

}, 1, 1);


ob_start();

$email_subject = 'Your password has been changed';

?>

<p>A password reset request has been sent for your Alpha Match account.</p>
<p>If this was a mistake, just ignore this email and nothing will happen.</p>
<p>To initiate your password reset, click the link below:<p>
<p><a href="<?php echo wp_login_url() ?>?action=rp&key=<?php echo $key ?>&login=<?php echo $user_login_name; ?>"><?php echo wp_login_url() ?>?action=rp&key=<?php echo $key ?>&login=<?php echo $user_login_name; ?></a></p>


<?php

$message = ob_get_contents();

ob_end_clean();

return $message;

}


// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

        if ( empty($plaintext_pass) )
            return;

        $message  = __('Hi there,') . "\r\n\r\n";
        $message .= sprintf(__("Welcome to Alpha Match here's how to log in:"), get_option('blogname')) . "\r\n\r\n";
        $message .= home_url() . "login"  . "\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n";
        $message .= sprintf(__('If you have any problems, please contact us at %s.'), get_option('admin_email')) . "\r\n\r\n";

        wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);

    }
}


// Redirect failed login 
//////////////////////////////////////////////////////////////////////
add_action( 'wp_login_failed', 'my_front_end_login_fail' );
function my_front_end_login_fail( $username ) {
     $referrer = $_SERVER['HTTP_REFERER'];
     if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
          wp_redirect( $referrer . '?action=failed' );
          exit;
     }
}


// Set Wordpress Admin colour scheme
function additional_admin_color_schemes() {
  //Get the theme directory
  $theme_dir = get_template_directory_uri();
 
  //Ocean
  wp_admin_css_color( 'fresh', __( 'Fresh' ),
    $theme_dir . '/admin-colors/fresh/colors.min.css',
    array( '#ecad20', '#ecad20', '#ecad20', '#ecad20' )
  );
}
add_action('admin_init', 'additional_admin_color_schemes');
  
/**
 * Gets the request parameter.
 *
 * @param      string  $key      The query parameter
 * @param      string  $default  The default value to return if not found
 *
 * @return     string  The request parameter.
 */
function get_request_parameter( $key, $default = '' ) {
    // If not request set
    if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
        return $default;
    }
 
    // Set so process it
    return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
}

// WordPress Get menu from Shortcode
function print_menu_shortcode($atts, $content = null) {

extract(shortcode_atts(array( 'name' => null, 'class' => null ), $atts));
  return wp_nav_menu( array( 'menu' => $name, 'menu_class' => $class, 'echo' => false ) );
}

add_shortcode('menu', 'print_menu_shortcode');



