<?php if ( have_posts() ) : ?>
    <table class="bookings">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $listing_id = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'listing_id', true );
            $fullname = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'name', true );
            $book_from = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_from', true );
            $book_to = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'book_to', true );
            $persons = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'number_of_persons', true );
            $booking_status = get_post_meta( get_the_ID(), INVENTOR_BOOKING_PREFIX . 'status', true );
            $listing = get_post( $listing_id );
            ?>

            <tr>
                <td class="booking-info">
                    <span class="listing-title"><?php echo get_the_title( $listing ); ?></span>
                    <span class="fullname"><?php echo $fullname; ?></span>
                    <span class="datetime"><?php echo $book_from; ?></span>
                    <span class="persons"><?php echo $persons; ?></span>
                </td>
                <td class="booking-id">
                    <i class="fa fa-globe"></i>
                    <?php echo get_the_ID(); ?>
                </td>
                <td class="gift">
                    <i class="fa fa-gift"></i>
                    <?php echo 'TODO: PRICE'; ?>
                </td>
                <td class="actions">
                    <a href="#cancel"><i class="fa fa-times"></i></a>
                </td>
                <td class="status">
                    <span class="status"><?php echo Inventor_Bookings_Post_Type_Booking::get_status_display( $booking_status ); ?></span>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php the_posts_pagination( array(
        'prev_text' => __( 'Previous', 'inventor-bookings' ),
        'next_text' => __( 'Next', 'inventor-bookings' ),
        'mid_size'  => 2,
    ) ); ?>
<?php else : ?>
    <div class="alert alert-warning">
        <?php if ( is_user_logged_in() ): ?>
            <?php echo __( "You don't have any bookings, yet.", 'inventor-bookings' ); ?>
        <?php else: ?>
            <?php echo __( 'You need to log in at first.', 'inventor-bookings' ); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>