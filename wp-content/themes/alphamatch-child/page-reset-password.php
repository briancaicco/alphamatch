<?php 
/*
 * Template Name: Forgot password page
 * Description: Page template for custom forgot password page

 */


global $wpdb, $user_ID;

function tg_validate_url() {
	global $post;
	$page_url = esc_url(get_permalink( $post->ID ));
	$urlget = strpos($page_url, "?");
	if ($urlget === false) {
		$concate = "?";
	} else {
		$concate = "&";
	}
	return $page_url.$concate;
}

if (!$user_ID) { //block logged in users
	if(isset($_GET['key']) && $_GET['action'] == "reset_pwd") {
		$reset_key = $_GET['key'];
		$user_login = $_GET['login'];
		$user_data = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login));
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		if(!empty($reset_key) && !empty($user_data)) {

			$new_password = wp_generate_password( 10, true, true );
	  

	  		//echo $new_password; exit();
		
			wp_set_password( $new_password, $user_data->ID );
	  
	  		//mailing reset details to the user
			$message = __('<p>Your new password for the account at:') . get_option('siteurl') . '</p>' . "\r\n\r\n";
			$message .= sprintf(__('<p>Username: %s</p>'), $user_login) . "\r\n\r\n";
			$message .= sprintf(__('<p>Password: %s</p>'), $new_password) . "\r\n\r\n";
			$message .= __('<p>You can now login with your new password at: ') . get_option('siteurl')."/login" .'</p>'. "\r\n\r\n";
			
		if ( $message && !wp_mail($user_email, 'Password Reset Request', $message) ) {
				exit();
			}
			else {
				$redirect_to = get_bloginfo('url')."/login?action=reset_success";
				wp_safe_redirect($redirect_to);
				exit();
			}
		} else {
			$redirect_to = get_bloginfo('url')."/login?action=no_valid_key";
			wp_safe_redirect($redirect_to);
			exit();
		}
	}
  //exit();
	if($_POST['action'] == "tg_pwd_reset"){
		if ( !wp_verify_nonce( $_POST['tg_pwd_nonce'], "tg_pwd_nonce")) {
			exit("Nice try!");
		}
		if(empty($_POST['user_input'])) {
			echo "<div class='alert alert-warning'>Please enter your Username or E-mail address</div>";
			exit();
		}
	//We shall SQL escape the input
		$user_input = $wpdb->escape(trim($_POST['user_input']));
		if ( strpos($user_input, '@') ) {
			$user_data = get_user_by_email($user_input);
	  if(empty($user_data)) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
	  	echo "<div class='alert alert-warning'>Please enter a valid E-mail address.</div>";
	  	exit();
	  }
	}
	else {
		$user_data = get_userdatabylogin($user_input);
	  if(empty($user_data) || $user_data->caps[administrator] == 1) { //delete the condition $user_data->caps[administrator] == 1, if you want to allow password reset for admins also
	  	echo "<div class='alert alert-warning'>Please enter a valid Username.</div>";
	  	exit();
	  }
	}
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	if(empty($key)) {
	  //generate reset key
		$key = wp_generate_password(20, false);
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}
	//mailing reset details to the user
	$message = __('<p>Someone requested that the password be reset for the following account:') . get_option('siteurl') . '</p>' . "\r\n\r\n";
	$message .= sprintf(__('<p>Username: %s</p>'), $user_login) . "\r\n\r\n";
	$message .= __('<p>If this was a mistake, just ignore this email and nothing will happen.</p>') . "\r\n\r\n";
	$message .= __('<p>To reset your password, visit the following address:</p>') . "\r\n\r\n";
	$message .= tg_validate_url() . "action=reset_pwd&key=$key&login=" . rawurlencode($user_login) . "\r\n";
	if ( $message && !wp_mail($user_email, 'Password Reset Request', $message) ) {
		echo "<div class='alert alert-warning'>Email failed to send. Please contact us for assistance.</div>";
		exit();
	}
	else {
		echo "<div class='alert alert-warning'>We have just sent you an email with Password reset instructions.</div>";
		exit();
	}
} else { ?>


<? get_header(); ?>

<div class="row">
	<div class="col-sm-12">
        <div id="primary">
			<?php dynamic_sidebar( 'content-top' ); ?>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); $post_meta = get_post_meta($post->ID); ?>

					<?php the_content(); ?>

					<form class="user_form login-form" id="loginform" action="" method="post">

						<div id="result"></div> <!-- To hold validation results -->
						<br/>
						<div class="form-group">
							<label for="name_input">Username or Email Address</label>
							<input type="text" id="name_input" class="text" name="user_input" value="" />
						</div>
						<input type="hidden" name="action" value="tg_pwd_reset" />
						<input type="hidden" name="tg_pwd_nonce" value="<?php echo wp_create_nonce("tg_pwd_nonce"); ?>" />

						<button type="submit" id="submitbtn" class="reset_password button" name="submit" >Rest Password</button>
						<br/>
					</form>

					<script type="text/javascript">
						jQuery("#loginform").submit(function() {
							jQuery('#result').html('<div class="loading alert alert-warning">Validating...</div>').fadeIn();
							var input_data = jQuery('#loginform').serialize();
							jQuery.ajax({
								type: "POST",
								url:  "<?php echo get_permalink( $post->ID ); ?>",
								data: input_data,
								success: function(msg){
									jQuery('.loading').remove();
									jQuery('<div>').html(msg).appendTo('div#result').hide().fadeIn('slow');
								}
							});
							return false;
						});
					</script>

			<?php endwhile; endif; ?>



			<?php dynamic_sidebar( 'content-bottom' ); ?>

        </div><!-- #primary -->
	</div><!-- /.col-* -->
</div><!-- /.row -->

<?php get_footer(); ?>

	<?php

}
} else {
	wp_redirect( home_url() ); exit;
}
?>







