<?php if ( ! $is_verified && $author_id != get_current_user_id() ): ?>
    <?php if ( Inventor_Claims_Logic::user_already_claimed( $listing_id, get_current_user_id() ) ) : ?>
        <a href="<?php echo get_permalink( $claim_page ); ?>?id=<?php echo $listing_id ?>" class="claim-listing inventor-claims-btn marked" data-listing-id="<?php echo $listing_id; ?>" data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>">
            <?php echo __( 'I claimed it', 'inventor-claims' ); ?>
        </a><!-- /.inventor-claims-btn -->
    <?php else: ?>
        <a href="<?php echo get_permalink( $claim_page ); ?>?id=<?php echo $listing_id ?>" class="claim-listing inventor-claims-btn" data-listing-id="<?php echo $listing_id; ?>" data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>">
            <?php echo __( 'Claim', 'inventor-claims' ); ?>
        </a><!-- /.inventor-claims-btn -->
    <?php endif ; ?>
<?php endif ; ?>