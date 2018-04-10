<div class="form-group review-form-comment">
    <label><?php echo __( 'comment', 'inventor-reviews' ); ?></label>

    <textarea id="comment"
              name="comment"
              rows="4">REVIEW on "<?php the_title(); ?>" on <?php echo current_time( 'mysql' ); ?></textarea>
</div><!-- /.form-group -->