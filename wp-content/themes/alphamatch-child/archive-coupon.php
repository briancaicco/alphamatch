<?php
/**
 * The template for displaying archive
 *
 * @package Superlist
 * @since Superlist 1.0.0
 */

get_header(); ?>

<div class="row">
	<?php get_sidebar() ?>
	<div class="<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>col-sm-8 col-lg-9<?php else : ?>col-sm-12<?php endif; ?>">
		<div id="primary">
			<?php get_template_part( 'templates/document-title' ); ?>

			<?php dynamic_sidebar( 'content-top' ); ?>

			<?php if ( have_posts() ) : ?>

				<div class="content">
					<div class="row">
						<?php while ( have_posts() ) : the_post(); ?>
							<div class="col-sm-6">
								<?php if ( class_exists( 'Inventor_Coupons' ) ) : ?>
									<?php include Inventor_Template_Loader::locate( 'coupons/box', INVENTOR_COUPONS_DIR ); ?>
								<?php else : ?>
									<?php get_template_part( 'templates/content' ); ?>
								<?php endif;?>
							</div><!-- /.col-* -->
						<?php endwhile; ?>
					</div><!-- /.row -->
				</div><!-- /.content -->

				<?php superlist_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'templates/content', 'none' ); ?>
			<?php endif; ?>

			<?php dynamic_sidebar( 'content-bottom' ); ?>

		</div><!-- #primary -->
	</div><!-- /.col-* -->
</div><!-- /.row -->

<?php get_footer(); ?>