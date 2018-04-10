<?php $children_listings_query = Inventor_Query::get_children_listings( get_the_ID(), 'publish' ); ?>

<?php if ( $children_listings_query->have_posts() ) : ?>
    <div class="listing-detail-section" id="listing-detail-section-user-listings">
        <h2 class="page-header"><?php echo $section_title; ?></h2>

        <div class="row items-per-row-3">
            <?php while ( $children_listings_query->have_posts() ) : $children_listings_query->the_post(); ?>

                <div class="listing-container">
                    <?php include Inventor_Template_Loader::locate( 'listings/small' ); ?>
                </div><!-- /.listing-container -->
            <?php endwhile; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    </div><!-- /.listing-detail-section -->
<?php endif; ?>