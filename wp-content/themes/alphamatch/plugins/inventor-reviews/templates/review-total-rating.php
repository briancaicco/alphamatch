<div class="inventor-reviews-total-rating">
    <?php $total_rating = get_post_meta( $listing_id, INVENTOR_REVIEWS_TOTAL_RATING_META, true ); ?>
    <?php $total_rating = empty ( $total_rating ) ? 0 : $total_rating; ?>
    <?php $reviews_count = get_comment_count( $listing_id ); ?>

    <i class="fa fa-star"></i>
    <?php
    echo sprintf( _n(
        '<strong>%.2f / 5 </strong> from <a href="#listing-detail-section-reviews">%d review</a>',
        '<strong>%.2f / 5 </strong> from <a href="#listing-detail-section-reviews">%d reviews</a>',
        $reviews_count['approved'],
        'inventor-reviews'
    ), $total_rating, $reviews_count['approved'] );
    ?>

    <script type="application/ld+json">
{
    "@context" : "http://schema.org",
    "@type" : "<?php echo get_post_type() == 'event' ? 'Event' : 'Product'; ?>",
    "url":"<?php echo get_permalink(); ?>",
    "name":"<?php echo get_the_title(); ?>",
    "description":"<?php echo get_the_excerpt(); ?>",

    <?php if ( has_post_thumbnail() ) : ?>
        <?php $thumbnail = wp_get_attachment_url( get_post_thumbnail_id() ); ?>
        <?php $image = apply_filters( 'inventor_listing_featured_image', $thumbnail, get_the_ID() ); ?>
        "image":"<?php echo $thumbnail; ?>",
    <?php endif; ?>

    <?php if (get_post_type() == 'event'): ?>
        <?php $timestamps = Inventor_Post_Type_Event::get_timestamps( get_the_ID() ); ?>
        <?php if( ! empty( $timestamps['from'] ) ): ?>"startDate": "<?php echo date( DateTime::ATOM, $timestamps['from'] ); ?>",<?php endif; ?>
        <?php if( ! empty( $timestamps['to'] ) ): ?>"endDate": "<?php echo date( DateTime::ATOM, $timestamps['to'] ); ?>",<?php endif; ?>
    <?php endif; ?>

    "aggregateRating": {
        "@type":"AggregateRating",
        "worstRating":"0",
        "bestRating":"5",
        "ratingValue":"<?php echo $total_rating; ?>",
        "reviewCount":"<?php echo $reviews_count['approved']; ?>"
    },

    "location": {
        "@type": "Place",
        "name": "<?php echo __( 'Location', 'inventor' ); ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'address', true ); ?>"
        }
    }
}
</script>

</div><!-- /.inventor-reviews-total-rating -->