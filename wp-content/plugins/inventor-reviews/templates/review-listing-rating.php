<?php $rating = get_post_meta( $listing_id, INVENTOR_REVIEWS_TOTAL_RATING_META, true ); ?>
<?php $rating = empty ( $rating ) ? 0 : $rating; ?>

<?php if ( $display == null ): ?>
    <div class="inventor-reviews-rating <?php echo ( empty( $rating ) ) ? 'not-rated' : ''; ?>">
        <span class="review-rating-total" <?php do_action( 'inventor_review_rating_total_attrs', $listing_id ); ?> data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images" data-score="<?php echo esc_attr( $rating ); ?>"></span>
        <span class="review-rating-number"><?php echo esc_attr( $rating ); ?> <span class="separator">/</span> 5</span><!-- /.review-rating-number -->
    </div><!-- /.inventor-reviews-rating -->
<?php endif; ?>

<?php if ( $display == 'row' ): ?>
    <dt><?php echo __( 'Rating', 'inventor-reviews' ); ?></dt>
    <dd class="<?php echo ( empty( $rating ) ) ? 'not-rated' : ''; ?>">
        <span class="review-rating" <?php do_action( 'inventor_review_rating_total_attrs', $listing_id ); ?> data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images" data-score="<?php echo esc_attr( $rating ); ?>" ></span>
        <span class="review-rating-number"><?php echo esc_attr( $rating ); ?> <span class="separator">/</span> 5</span><!-- /.review-rating-number -->
    </dd>
<?php endif; ?>

<?php if ( $display == 'box' ): ?>
    <div class="listing-box-rating <?php echo ( empty( $rating ) ) ? 'not-rated' : ''; ?>">
        <span class="review-rating" <?php do_action( 'inventor_review_rating_total_attrs', $listing_id ); ?> data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images" data-score="<?php echo esc_attr( $rating ); ?>"></span>
        <span class="review-rating-number"><?php echo esc_attr( $rating ); ?> <span class="separator">/</span> 5</span><!-- /.review-rating-number -->
    </div><!-- /.listing-box-rating -->
<?php endif; ?>

<?php if ( $display == 'masonry' ): ?>
    <div class="listing-masonry-rating <?php echo ( empty( $rating ) ) ? 'not-rated' : ''; ?>">
        <span class="review-rating" <?php do_action( 'inventor_review_rating_total_attrs', $listing_id ); ?> data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images" data-score="<?php echo esc_attr( $rating ); ?>"></span>
        <span class="review-rating-number"><?php echo esc_attr( $rating ); ?> <span class="separator">/</span> 5</span><!-- /.review-rating-number -->
    </div><!-- /.listing-masonry-rating -->
<?php endif; ?>
