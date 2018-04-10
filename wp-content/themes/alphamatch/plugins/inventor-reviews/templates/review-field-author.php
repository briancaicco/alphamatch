<div class="form-group review-form-author">
    <label>
        <?php echo __( 'Name', 'inventor-reviews' ); ?> <span class="required">*</span>
    </label>

    <input id="author"
           name="author"
           type="text"
           value="<?php echo esc_attr( $commenter['comment_author'] ); ?>"
           size="30"
           required="required">
</div><!-- /.form-group -->