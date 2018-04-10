<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php echo __( 'Your following booking was approved:' ); ?><br><br>

<?php $listing = Inventor_Bookings_Logic::get_booking_listing( $booking->ID ); ?>

<?php if ( ! empty( $listing ) ) : ?>
    <?php $permalink = get_permalink( $listing->ID ); ?>

    <strong><?php echo __( 'Listing', 'inventor-bookings' ); ?>:</strong>

    <a href="<?php echo esc_attr( $permalink ); ?>">
        <?php echo get_the_title( $listing ); ?>
    </a>
    <br><br>
<?php endif; ?>

<?php $book_from = date( "Y-m-d", get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'book_from', true ) ); ?>
<?php if ( ! empty( $book_from ) ) : ?>
    <strong><?php echo __( 'Book from', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $book_from ); ?><br><br>
<?php endif; ?>

<?php $book_to = date( "Y-m-d", get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'book_to', true ) ); ?>
<?php if ( ! empty( $book_to ) ) : ?>
    <strong><?php echo __( 'Book to', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $book_to ); ?><br><br>
<?php endif; ?>

<?php $number_of_persons = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'number_of_persons', true ); ?>
<?php if ( ! empty( $number_of_persons ) ) : ?>
    <strong><?php echo __( 'Number of persons', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $number_of_persons ); ?><br><br>
<?php endif; ?>

<?php $price = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'price', true ); ?>
<?php if ( ! empty( $price ) ) : ?>
    <strong><?php echo __( 'Price', 'inventor-bookings' ); ?>:</strong> <?php echo Inventor_Price::format_price( $price ); ?><br><br>
<?php endif; ?>

<?php $name = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'name', true ); ?>
<?php if ( ! empty( $name ) ) : ?>
    <strong><?php echo __( 'Name', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $name ); ?><br><br>
<?php endif; ?>

<?php $email = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'email', true ); ?>
<?php if ( ! empty( $email ) ) : ?>
    <strong><?php echo __( 'E-mail', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $email ); ?><br><br>
<?php endif; ?>

<?php $phone = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'phone', true ); ?>
<?php if ( ! empty( $phone ) ) : ?>
    <strong><?php echo __( 'Phone', 'inventor-bookings' ); ?>:</strong> <?php echo esc_attr( $phone ); ?><br><br>
<?php endif; ?>

<?php $message = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'message', true ); ?>
<?php if ( ! empty( $message ) ) : ?>
    <strong><?php echo __( 'Message', 'inventor-bookings' ); ?>:</strong> <?php echo esc_html( $message ); ?><br><br>
<?php endif; ?>

<?php $booking_detail_page_id = get_theme_mod( 'inventor_bookings_detail_page', false ); ?>

<?php if ( ! empty( $booking_detail_page_id ) ): ?>
    <?php $hash = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'hash', true ); ?>
    <?php $url = get_permalink( $booking_detail_page_id ); ?>
    <?php $url .= '?hash=' . $hash ; ?>
    <strong><?php echo __( 'Please confirm your booking at this URL:', 'inventor-bookings' ); ?> <?php echo $url; ?></strong>
<?php endif; ?>