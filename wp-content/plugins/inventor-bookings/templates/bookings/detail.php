<?php if ( ! $booking ): ?>
    <p><?php echo __( 'Booking not found.', 'inventor-bookings' ); ?></p>
<?php else: ?>
    <div class="booking-info-wrapper">
        <table class="booking-info">
            <tr>
                <th><?php echo __( 'Booking ID', 'inventor-bookings' ); ?></th>
                <td><?php echo $booking->ID; ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Status', 'inventor-bookings' ); ?></th>
                <td>
                    <?php $booking_status = get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'status', true ); ?>
                    <?php echo Inventor_Bookings_Post_Type_Booking::get_status_display( $booking_status ); ?>
                </td>
            </tr>
            <tr>
                <th><?php echo __( 'Listing', 'inventor-bookings' ); ?></th>
                <td><?php echo get_the_title( $listing ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Book from', 'inventor-bookings' ); ?></th>
                <td><?php echo date( "Y-m-d", get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'book_from', true ) ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Book to', 'inventor-bookings' ); ?></th>
                <td><?php echo date( "Y-m-d", get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'book_to', true ) ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Number of persons', 'inventor-bookings' ); ?></th>
                <td><?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'number_of_persons', true ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Price', 'inventor-bookings' ); ?></th>
                <td><?php echo Inventor_Price::format_price( get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'price', true ) ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Name', 'inventor-bookings' ); ?></th>
                <td><?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'name', true ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'E-mail', 'inventor-bookings' ); ?></th>
                <td><?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'email', true ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Phone', 'inventor-bookings' ); ?></th>
                <td><?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'phone', true ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( 'Message', 'inventor-bookings' ); ?></th>
                <td><?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'message', true ); ?></td>
            </tr>
        </table>

        <?php if ( ( $listing->post_author == get_current_user_id() || is_super_admin() ) && $booking_status == 'awaiting_approval' ): ?>
            <form method="post" class="booking-action">
                <input type="hidden" name="hash" value="<?php echo get_post_meta( $booking->ID, INVENTOR_BOOKING_PREFIX . 'hash', true );  ?>">
                <button type="submit" name="cancel_booking" class="btn-danger"><?php echo __( 'Cancel booking', 'inventor-bookings' ); ?></button>
                <button type="submit" name="approve_booking" class="btn-success"><?php echo __( 'Approve booking', 'inventor-bookings' ); ?></button>
            </form>
        <?php endif; ?>

        <?php $payment_page_id = get_theme_mod( 'inventor_general_payment_page', false ); ?>

        <?php if ( ! empty( $payment_page_id ) && $booking_status == 'pending_payment' ): ?>
            <form method="post" action="<?php echo get_permalink( $payment_page_id ); ?>" class="booking-action">
                <input type="hidden" name="payment_type" value="booking">
                <input type="hidden" name="object_id" value="<?php echo esc_attr( $booking->ID ); ?>">

                <button type="submit"><?php echo __( 'Pay booking', 'inventor-bookings' ); ?></button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>
