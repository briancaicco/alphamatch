<div class="form-group review-form-email">
    <label>
        <?php echo __( 'E-mail', 'inventor-reviews' ); ?> <span class="required">*</span>
    </label>

    <input id="email"
           name="email"
           type="text"
           value="<?php echo esc_attr( ! empty( $commenter['comment_author_email'] ) ? $commenter['comment_author_email'] : '' ); ?>"
           size="30"
           required="required">
</div><!-- /.form-group -->