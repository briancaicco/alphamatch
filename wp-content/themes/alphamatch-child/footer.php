<?php
/**
 * The template for displaying the footer
 *
 * @package Superlist
 * @since Superlist 1.0.0
 */

?>

</div><!-- /.content -->
</div><!-- /.main-inner -->
</div><!-- /.main -->

<footer class="footer">
	<?php dynamic_sidebar( 'bottom' ); ?>

	<section class="footer-nav">
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-8">       
					<?php wp_nav_menu( array(
						'menu'           => 'Footer Menu',
						'theme_location' => '__no_such_location',
						'container_class'			 => 'widget widget_nav_menu',
						'fallback_cb'    => false) ); 
						?>
						<div class="clearfix"></div>
					</div>
					<div class="col-12 col-md-4">
						<div class="widget widget_nav_menu text-right">
							<ul id="menu-footer-meta" class="menu">
								<?php if(!is_user_logged_in()) { ?><li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8105"><a href="<?php bloginfo( 'url' );?>/login"><i class="fa fa-user-o"></i>Login</a></li><?php } ?>
								<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8105"><a data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch"><i class="fa fa-search"></i>Search</a></li>
							</ul>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="footer-cta">
			<div class="container">
				<div class="row">
					<div class="col-12 col-md-8">
						<ul class="footer-social-links">
							<li><a href="https://www.facebook.com/alphamatch/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/facebook.svg"></a></li>
							<li><a href="https://www.youtube.com/channel/UCnlXymnG7mZxYxVqwsNofqg"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/youtube.svg"></a></li>
							<!-- <li><a href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/instagram.svg"></a></li> -->
							<li><a href="https://twitter.com/alphamatch"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/twitter.svg"></a></li>
							<li><a href="https://www.pinterest.ca/alpha0837/?eq=alp&etslf=4738"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/pinterest.svg"></a></li>

						</ul>
						<div class="clearfix"></div>
						<span class="small footer-credits">&copy; <?php echo date('Y'); ?> AlphaMatch. All rights reserved.</span>
					</div>
					<div class="col-12 col-md-4">
						<?php if(!is_user_logged_in()) { ?><a href="<?php bloginfo( 'url' );?>/register" class="btn btn-primary footer-signup">Sign Up Now</a><?php } ?>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</section>

		<?php if ( is_active_sidebar( 'footer-first' ) || is_active_sidebar( 'footer-second' ) || is_active_sidebar( 'footer-top-first' ) || is_active_sidebar( 'footer-top-second' ) || is_active_sidebar( 'footer-top-third' ) || is_active_sidebar( 'footer-top-fourth' ) ) : ?>
			<div class="footer-top">
				<?php if ( is_active_sidebar( 'footer-top-first' ) || is_active_sidebar( 'footer-top-second' ) || is_active_sidebar( 'footer-top-third' ) || is_active_sidebar( 'footer-top-fourth' ) ) : ?>
					<div class="footer-area">
						<div class="container">
							<div class="row">
								<div class="col-sm-3">
									<?php dynamic_sidebar( 'footer-top-first' ); ?>
								</div><!-- /.cols-sm-3 -->

								<div class="col-sm-3">
									<?php dynamic_sidebar( 'footer-top-second' ); ?>
								</div><!-- /.cols-sm-3 -->

								<div class="col-sm-3">
									<?php dynamic_sidebar( 'footer-top-third' ); ?>
								</div><!-- /.cols-sm-3 -->

								<div class="col-sm-3">
									<?php dynamic_sidebar( 'footer-top-fourth' ); ?>
								</div><!-- /.cols-sm-3 -->
							</div><!-- /.row -->
						</div><!-- /.container -->
					</div><!-- .footer-area -->
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-first' ) || is_active_sidebar( 'footer-second' ) ) : ?>
					<div class="container">
						<div class="footer-top-inner">
							<?php if ( is_active_sidebar( 'footer-first' ) ) : ?>
								<div class="footer-first">
									<?php dynamic_sidebar( 'footer-first' ); ?>
								</div><!-- /.footer-first -->
							<?php endif; ?>

							<?php if ( is_active_sidebar( 'footer-second' ) ) : ?>
								<div class="footer-second">
									<?php dynamic_sidebar( 'footer-second' ); ?>
								</div><!-- /.footer-second -->
							<?php endif; ?>
						</div><!-- /.footer-top-inner -->
					</div><!-- /.container -->
				<?php endif; ?>
			</div><!-- /.footer-top -->
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'footer-bottom-first' ) || is_active_sidebar( 'footer-bottom-second' ) ) : ?>
			<div class="footer-bottom">
				<div class="container">
					<div class="footer-bottom-inner">
						<div class="footer-bottom-first">
							<?php dynamic_sidebar( 'footer-bottom-first' ); ?>
						</div><!-- /.footer-bottom-first -->

						<div class="footer-bottom-second">
							<?php dynamic_sidebar( 'footer-bottom-second' ); ?>
						</div><!-- /.footer-bottom-second -->
					</div><!-- /.footer-bottom-inner -->
				</div><!-- /.container -->
			</div><!-- /.footer-bottom -->
		<?php endif; ?>

	</footer><!-- /.footer -->
</div><!-- /.page-wrapper -->

<?php get_template_part( 'templates/modal' ); ?>

<?php $customizer = get_theme_mod( 'superlist_general_customizer', false ); ?>
<?php if ( ! empty( $customizer ) ) : ?>
	<?php get_template_part( 'templates/action-bar' ); ?>
<?php endif; ?>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5ad27eca8e28fb04"></script>
<script type="text/javascript"> jQuery(document).ready(function($) { $('.alert-dismissible').delay( 5000 ).fadeOut( 400 ); });</script>
<?php wp_footer(); ?>

<?php if ( is_page( 70 )){ ?>
<script type="text/javascript">

	$( "form" ).submit(function( event ) {

		var str = $("#listing_featured_image").val();

		if ( str.length > 0 ) {

		  	var pattern = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

  			if(!pattern.test(str)) {

  				$('.alert').remove().fadeOut(500);
				$('#listing_featured_image').css('border', '1px solid rgba(236,173,32,.8)');
				$('<div class="alert alert-warning">Please enter a valid url</div>').appendTo('.cmb2-id-listing-featured-image').show().fadeIn(2000);

  				event.preventDefault();

		  	} else {

  				return;
  			}

  	return;

  }else{

  		$('.alert').remove().fadeOut(500);
  		$('#listing_featured_image').css('border', '1px solid rgba(236,173,32,.8)');
		$('<div class="alert alert-warning">Please add an image to continue</div>').appendTo('.cmb2-id-listing-featured-image').show().fadeIn(2000);
		// $('<div class="alert alert-warning">Please add an image to continue</div>').appendTo('.submission-form').show().fadeIn(2000);

	  	event.preventDefault();
	}

});
</script>
<?php } ?>

</body>
</html>
