<?php if ( is_user_logged_in() ) : ?>
    <div class="form-group review-form-file">
        <label>
            <?php echo __( 'Attach image', 'inventor-reviews' ); ?>
        </label>

        <input id="image-url" type="text" name="image" />
        <button id="upload-button" type="button" class="btn-primary"><?php echo __( 'Upload Image', 'inventor-reviews' ); ?></button>

    </div><!-- /.form-group -->
<?php endif; ?>
