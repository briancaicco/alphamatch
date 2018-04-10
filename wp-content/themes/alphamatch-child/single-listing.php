<?php
/**
 * The template for displaying single listing
 *
 * @package Superlist
 * @since Superlist 1.0.0
 */

get_header(); ?>

<div class="row">

<!--   <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#msg-seller" aria-expanded="false" aria-controls="msg-seller">
    Message Seller
  </button>
    <div class="collapse" id="msg-seller">

        <?php// echo do_shortcode( '[seller-contact-form]', false ); ?>

    </div> -->



    <?php get_sidebar() ?>
    <div class="<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>col-sm-8 col-lg-9<?php else : ?>col-sm-12<?php endif; ?>">
        <div id="primary">

            <?php dynamic_sidebar( 'content-top' ); ?>

            <?php if ( have_posts() ) : ?>
                <div class="content">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php include Inventor_Template_Loader::locate( 'content-listing' ); ?>
                    <?php endwhile; ?>

                    <?php superlist_pagination(); ?>
                </div><!-- /.content -->
            <?php else : ?>
                <?php get_template_part( 'templates/content', 'none' ); ?>
            <?php endif; ?>

            <?php dynamic_sidebar( 'content-bottom' ); ?>
            <?php $author_nickname = get_the_author_meta( 'nickname' ); ?>
            <?php //echo do_shortcode( '[fep_shortcode_message_to to="{current-post-author}" subject="{current-post-title}"]', true ); ?>

    </div><!-- #primary -->
</div><!-- /.col-* -->
</div><!-- /.row -->

<?php get_footer(); ?>
