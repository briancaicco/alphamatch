<?php if ( class_exists( 'Inventor_Recaptcha' ) ) : ?>
    <?php if ( Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) : ?>
        <div id="recaptcha-post-<?php echo get_the_ID(); ?>" class="g-recaptcha" data-sitekey="<?php echo get_theme_mod( 'inventor_recaptcha_site_key' ); ?>"></div>
    <?php endif; ?>
<?php endif; ?>