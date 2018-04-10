<?php
/**
 * The template for displaying archive
 *
 * @package Superlist
 * @since Superlist 1.0.0
 */

get_header(); ?>

<?php $display_as_grid = ! empty( $_GET['listing-display'] ) && 'grid' == $_GET['listing-display'] || empty( $_GET['listing-display'] ) && get_theme_mod( 'inventor_general_show_listing_archive_as_grid', true ); ?>

<div class="row">
    <div class="col-sm-12">

            <?php get_template_part( 'templates/document-title' ); ?>

            <?php do_action( 'inventor_before_filter_form', $args ); ?>
                <div class="widget widget_filter">
                    <div class="widget-inner widget-pb">
                        <form method="get"
                              action="<?php echo esc_attr( Inventor_Filter::get_filter_action() ); ?>"
                              class="filter-form <?php if ( ! empty( $instance['live_filtering'] ) ) : ?>live <?php endif; ?><?php if ( ! empty( $instance['auto_submit_filter'] ) || empty( $instance['button_text'] ) ) : ?>auto-submit-filter <?php endif; ?><?php if ( ! empty( $input_titles ) && 'labels' == $input_titles ) : ?>has-labels<?php endif; ?>">
                                <?php include Inventor_Template_Loader::locate( 'widgets/filter-form-sorting-options' ); ?>
                        </form>
                    </div>
                </div>

            <?php do_action( 'inventor_after_filter_form', $args ); ?>

    </div>
</div>
<div class="row">
    <?php get_sidebar() ?>
    <div class="<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>col-sm-8 col-lg-9<?php else : ?>col-sm-12<?php endif; ?>">
        <div id="primary">

            <?php dynamic_sidebar( 'content-top' ); ?>

            <?php do_action( 'inventor_before_listing_archive' ); ?>

            <?php if ( have_posts() ) : ?>
                <?php if ( $display_as_grid ) : ?>
                    <div class="listing-box-archive type-box items-per-row-3">
                <?php endif; ?>

                <div class="listings-row">

                    <?php while ( have_posts() ) : the_post(); ?>
                        <div class="listing-container">
                            <?php if ( $display_as_grid ) : ?>
                                <?php include Inventor_Template_Loader::locate( 'listings/column' ); ?>
                            <?php else : ?>
                                <?php include Inventor_Template_Loader::locate( 'listings/row' ); ?>
                            <?php endif; ?>
                        </div><!-- /.listing-container -->
                    <?php endwhile; ?>

                </div><!-- /.listings-row -->

                <?php if ( $display_as_grid ) : ?>
                    </div><!-- /.listing-box-archive -->
                <?php endif; ?>

                <?php do_action( 'inventor_after_listing_archive' ); ?>

                <?php the_posts_pagination( array(
                    'prev_text'             => __( 'Previous', 'inventor' ),
                    'next_text'             => __( 'Next', 'inventor' ),
                    'mid_size'              => 2,
                ) ); ?>
            <?php else : ?>
                <?php get_template_part( 'templates/content', 'none' ); ?>
            <?php endif; ?>
        </div><!-- /#primary -->

        <?php dynamic_sidebar( 'content-bottom' ); ?>
    </div><!-- /.col-* -->

</div><!-- /.row -->

<?php get_footer(); ?>
