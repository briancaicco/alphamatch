<div class="detail-banner" data-background-image="<?php echo esc_attr( get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'banner_image', true ) ); ?>">
    <?php include Inventor_Template_Loader::locate( 'content-listing-banner-info' ); ?>
</div><!-- /.detail-banner -->