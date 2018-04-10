<div class="form-group review-form-rating" data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images">
    <label for="rating">
        <?php echo __( 'Rating', 'inventor-reviews' ); ?>
    </label>

    <div class="rating-wrapper">    
        <input class="hidden"
               type="number"
               id="rating"
               name="rating"
               value="<?php echo esc_attr( ! empty( $commenter['comment_rating'] ) ? $commenter['comment_rating'] : null ) ; ?>" size="30">
    </div>
</div><!-- /.form-group -->