<?php 
// page template: Login Test

wp_head(); ?>

<?php
    // Define custom redirect
    $custom_redirect = "REDIRECT_URL";

    // If user is logged out
    if ( !is_user_logged_in() ) {
        $args = array(
            'redirect' => $custom_redirect
        );

        // Output the login form with arguments
        wp_login_form( $args );
    } 
    // Display below if user is logged in
    else {
        wp_loginout( home_url() ); // Display "Log Out" link.
        echo " | ";
        wp_register('', ''); // Display "Site Admin" link.
    }
?>

<?php wp_footer(); ?>