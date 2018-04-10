<?php if ( ! empty( $object_id ) ) : ?>
    <?php $price = get_post_meta( $object_id, INVENTOR_BOOKING_PREFIX . 'price', true ); ?>
    <?php $listing = Inventor_Bookings_Logic::get_booking_listing( $object_id ); ?>

    <?php if ( empty( $price ) || $price == 0 ) : ?>
        <div class="alert alert-danger">
            <?php echo __( "Price is not set.", 'inventor-bookings' ); ?>
        </div><!-- /.payment-info -->
    <?php else : ?>
        <div class="payment-info">
        <?php if ( 'booking' == $payment_type ) : ?>
            <?php echo sprintf( __( 'You are going to pay <strong>%s</strong> for booking of <strong>"%s"</strong>.', 'inventor-bookings' ), Inventor_Price::format_price( $price ), $listing->post_title ); ?>
        <?php endif; ?>
        </div><!-- /.payment-info -->
    <?php endif; ?>

<?php else : ?>
    <div class="payment-info">
        <?php echo __( 'Missing listing.', 'inventor-bookings' ); ?>
    </div><!-- /.payment-info -->
<?php endif; ?>