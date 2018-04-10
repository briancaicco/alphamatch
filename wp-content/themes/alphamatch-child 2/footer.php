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
					<span class="small footer-credits">&copy; <?php the_date('Y'); ?> AlphaMatch. All rights reserved.</span>
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

<?php alpha_change_user_role(); ?>

<?php wp_footer(); ?>

</body>
</html>
