<?php if ( Inventor_Compare_Logic::is_in_comparison( $listing_id ) ) : ?>
    <a href="#" class="inventor-compare-btn-toggle marked" data-listing-id="<?php echo $listing_id; ?>" data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>" title="<?php echo __( 'Remove from comparison', 'inventor-compare' ); ?>" data-toggle="tooltip" data-placement="top">
        <i class="fa fa-exchange"></i> <span data-toggle="<?php echo __( 'Compare', 'inventor-compare' ); ?>"><?php echo __( 'Remove from comparison', 'inventor-compare' ); ?></span>
    </a><!-- /.inventor-compare-btn-toggle -->
<?php else: ?>
    <a href="#" class="inventor-compare-btn-toggle" data-listing-id="<?php echo $listing_id; ?>" data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>" title="<?php echo __( 'Compare', 'inventor-compare' ); ?>" data-toggle="tooltip" data-placement="top">
        <i class="fa fa-exchange"></i> <span data-toggle="<?php echo __( 'Remove from comparison', 'inventor-compare' ); ?>"><?php echo __( 'Compare', 'inventor-compare' ); ?></span>
    </a><!-- /.inventor-compare-btn-toggle -->
<?php endif ; ?>