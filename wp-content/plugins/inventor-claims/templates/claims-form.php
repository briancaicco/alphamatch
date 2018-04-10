<?php if ( empty( $listing ) ): ?>
    <div class="alert alert-warning">
        <?php echo __( 'You have to specify listing!', 'inventor-claims' ) ?>
    </div>
<?php elseif ( Inventor_Claims_Logic::user_already_claimed( $listing->ID, get_current_user_id() ) ): ?>
    <div class="alert alert-info">
        <?php echo __( 'You have already claimed this listing. Please, wait for admin review.', 'inventor-claims' ) ?>
    </div>
<?php else: ?>
    <form method="post">
        <input type="hidden" name="listing_id" value="<?php echo $listing->ID; ?>">

        <div class="form-group">
            <input class="form-control" name="fullname" type="text" placeholder="<?php echo __( 'Name', 'inventor-claims' ); ?>" value="<?php echo empty( $_POST['fullname'] ) ? '' : esc_attr( $_POST['fullname'] ); ?>" required="required">
        </div><!-- /.form-group -->

        <div class="form-group">
            <input class="form-control" name="email" type="email" placeholder="<?php echo __( 'E-mail', 'inventor-claims' ); ?>" value="<?php echo empty( $_POST['email'] ) ? '' : esc_attr( $_POST['email'] ); ?>" required="required">
        </div><!-- /.form-group -->

        <div class="form-group">
            <input class="form-control" name="phone" type="text" placeholder="<?php echo __( 'Phone', 'inventor-claims' ); ?>" value="<?php echo empty( $_POST['phone'] ) ? '' : esc_attr( $_POST['phone'] ); ?>" required="required">
        </div><!-- /.form-group -->

        <div class="form-group">
            <textarea class="form-control" name="message" required="required" placeholder="<?php echo __( 'Message', 'inventor-claims' ); ?>" rows="4"><?php echo empty( $_POST['message'] ) ? '' : esc_attr( $_POST['message'] ); ?></textarea>
        </div><!-- /.form-group -->

        <?php if ( class_exists( 'Inventor_Recaptcha' ) ) : ?>
            <?php if ( Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) : ?>
                <div id="recaptcha-<?php echo esc_attr( get_the_ID() ); ?>" class="g-recaptcha" data-sitekey="<?php echo get_theme_mod( 'inventor_recaptcha_site_key' ); ?>"></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="button-wrapper">
            <button type="submit" class="btn btn-primary" name="claim_form"><?php echo sprintf( __( 'Claim %s', 'inventor-claims' ), get_the_title( $listing ) ); ?></button>
        </div><!-- /.button-wrapper -->
    </form>
<?php endif; ?>