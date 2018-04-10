<?php $pros_and_cons_enabled = get_theme_mod( 'inventor_reviews_pros_and_cons_enabled', true ); ?>
<?php $user_rated_already = Inventor_Reviews_Logic::current_user_has_rated( get_the_ID() ); ?>
<?php $class = $pros_and_cons_enabled && ! $user_rated_already ? 'review-form-comment' : 'review-form-review'; ?>
<?php $title = $user_rated_already ? __( 'Message', 'inventor-reviews' ) : __( 'Review', 'inventor-reviews' ); ?>

<div class="form-group <?php echo $class; ?>">
    <label><?php echo $title; ?></label>

    <?php $default_message = $pros_and_cons_enabled && ! $user_rated_already ? 'REVIEW on ' . get_the_title() . ' on ' . current_time( 'mysql' ) : ''; ?>

    <textarea id="comment"
              name="comment"
              rows="4"
              required="required"><?php echo $default_message; ?></textarea>
</div><!-- /.form-group -->