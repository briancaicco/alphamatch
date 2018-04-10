<?php echo wp_kses( $args['before_widget'], wp_kses_allowed_html( 'post' ) ); ?>

<div class="alpha-favorite-listings" data-toggle="collapse" data-target="#alpha_fav_side" aria-expanded="false" aria-controls="alpha_fav_side">
    <?php if ( ! empty( $instance['title'] ) ) : ?>
        <?php echo wp_kses( $args['before_title'], wp_kses_allowed_html( 'post' ) ); ?>
        <?php echo esc_attr( $instance['title'] ); ?>
        <?php echo wp_kses( $args['after_title'], wp_kses_allowed_html( 'post' ) ); ?>
    <?php endif; ?>
</div>

<?php if ( have_posts() ) : ?>
    <div class="collapse" id="alpha_fav_side">
        <div class="listings-row">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="listing-container">
                    <?php echo Inventor_Template_Loader::load( 'listings/small' ); ?>
                </div><!-- /.listing-container -->
            <?php endwhile; ?>
        </div><!-- /.listings-row -->
    </div>
<?php else : ?>
    <div class="alert alert-warning">
        <?php if ( is_user_logged_in() ): ?>
            <?php echo __( "You don't have any favorite listings, yet. Start by adding some.", 'inventor-favorites' ); ?>
        <?php else: ?>
            <?php echo __( 'Please login to view favorites.', 'inventor-favorites' ); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php echo wp_kses( $args['after_widget'], wp_kses_allowed_html( 'post' ) ); ?>