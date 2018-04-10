<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php echo wp_kses( $args['before_widget'], wp_kses_allowed_html( 'post' ) ); ?>

<div class="widget-inner
 <?php if ( ! empty( $instance['classes'] ) ) : ?><?php echo esc_attr( $instance['classes'] ); ?><?php endif; ?>
 <?php echo ( empty( $instance['padding_top'] ) ) ? '' : 'widget-pt' ; ?>
 <?php echo ( empty( $instance['padding_bottom'] ) ) ? '' : 'widget-pb' ; ?>"
    <?php if ( ! empty( $instance['background_color'] ) || ! empty( $instance['background_image'] ) ) : ?>
        style="
        <?php if ( ! empty( $instance['background_color'] ) ) : ?>
            background-color: <?php echo esc_attr( $instance['background_color'] ); ?>;
    <?php endif; ?>
        <?php if ( ! empty( $instance['background_image'] ) ) : ?>
            background-image: url('<?php echo esc_attr( $instance['background_image'] ); ?>');
        <?php endif; ?>"
    <?php endif; ?>>

    <?php if ( ! empty( $instance['title'] ) ) : ?>
        <?php echo wp_kses( $args['before_title'], wp_kses_allowed_html( 'post' ) ); ?>
        <?php echo wp_kses( $instance['title'], wp_kses_allowed_html( 'post' ) ); ?>
        <?php echo wp_kses( $args['after_title'], wp_kses_allowed_html( 'post' ) ); ?>
    <?php endif; ?>

    <div class="widget-content">
        <form method="post" action="<?php the_permalink(); ?>" class="booking-form">
            <input type="hidden" name="listing_id" value="<?php the_ID(); ?>">

            <?php if ( ! empty( $instance['receive_admin'] ) ) : ?>
                <input type="hidden" name="receive_admin" value="1">
            <?php endif; ?>

            <?php if ( ! empty( $instance['receive_author'] ) ) : ?>
                <input type="hidden" name="receive_author" value="1">
            <?php endif; ?>

            <?php if ( ! empty( $instance['receive_listing_email'] ) ) : ?>
                <input type="hidden" name="receive_listing_email" value="1">
            <?php endif; ?>

            <div class="form-group">
                <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_book_from"><?php echo __( 'From', 'inventor-bookings' ); ?></label>
                <input
                    class="form-control"
                    name="book_from"
                    type="date"
                    id="<?php echo ! empty( $field_id_prefix ) ? $field_id_prefix : ''; ?><?php echo esc_attr( $args['widget_id'] ); ?>_book_from"
                    placeholder="<?php echo __( 'From', 'inventor-bookings' ); ?>"
                    value="<?php echo empty( $_POST['book_from'] ) ? '' : esc_attr( $_POST['book_from'] ); ?>"
                    required="required"
                >
            </div><!-- /.form-group -->

            <div class="form-group">
                <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_book_to"><?php echo __( 'To', 'inventor-bookings' ); ?></label>
                <input
                    class="form-control"
                    name="book_to"
                    type="date"
                    id="<?php echo ! empty( $field_id_prefix ) ? $field_id_prefix : ''; ?><?php echo esc_attr( $args['widget_id'] ); ?>_book_to"
                    placeholder="<?php echo __( 'From', 'inventor-bookings' ); ?>"
                    value="<?php echo empty( $_POST['book_to'] ) ? '' : esc_attr( $_POST['book_to'] ); ?>"
                    required="required"
                    >
            </div><!-- /.form-group -->

            <div class="form-group">
                <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_number_of_persons"><?php echo __( 'Persons', 'inventor-bookings' ); ?></label>
                <input
                    class="form-control"
                    name="number_of_persons"
                    type="number"
                    step="1"
                    min="1"
                    id="<?php echo ! empty( $field_id_prefix ) ? $field_id_prefix : ''; ?><?php echo esc_attr( $args['widget_id'] ); ?>_number_of_persons"
                    placeholder="<?php echo __( 'Number of persons', 'inventor-bookings' ); ?>"
                    value="<?php echo empty( $_POST['number_of_persons'] ) ? '' : esc_attr( $_POST['number_of_persons'] ); ?>"
                    required="required"
                    >
            </div><!-- /.form-group -->

            <div class="booking-restrictions">
                <?php $period_unit = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'period_unit', true ); ?>
                <?php $min_period = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'min_period', true ); ?>
                <?php $max_period = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'max_period', true ); ?>
                <dl>
                    <?php if ( ! empty( $min_period ) ) : ?><dt><?php echo __( 'Min: ', 'inventor-bookings' ); ?></dt><dd><?php echo esc_attr( $min_period ) . ' ' . esc_attr( $period_unit ) ; ?></dd><?php endif; ?>
                    <?php if ( ! empty( $max_period ) ) : ?><dt><?php echo __( 'Max: ', 'inventor-bookings' ); ?></dt><dd><?php echo esc_attr( $max_period ) . ' ' . esc_attr( $period_unit ) ; ?></dd><?php endif; ?>
                </dl>

                <?php $min_persons = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'min_persons', true ); ?>
                <?php $max_persons = get_post_meta( get_the_ID(), INVENTOR_BOOKINGS_PREFIX . 'max_persons', true ); ?>
                <dl>
                    <?php if ( ! empty( $min_persons ) ) : ?><dt><?php echo __( 'Min: ', 'inventor-bookings' ); ?></dt><dd><?php echo $min_persons . ' ' . _n( 'person', 'persons', $min_persons, 'inventor-bookings' ); ?></dd><?php endif; ?>
                    <?php if ( ! empty( $max_persons ) ) : ?><dt><?php echo __( 'Max: ', 'inventor-bookings' ); ?></dt><dd><?php echo $max_persons . ' ' . _n( 'person', 'persons', $max_persons, 'inventor-bookings' ); ?></dd><?php endif; ?>
                </dl>
            </div>

            <?php if ( ! empty( $instance['show_phone'] ) || ! empty( $instance['show_name'] ) ) : ?>
                <p>
                    <?php echo __( 'Contact information', 'inventor-bookings' ); ?>
                </p>
            <?php endif; ?>

            <?php if ( ! empty( $instance['show_name'] ) ) : ?>
                <div class="form-group">
                    <input class="form-control" name="fullname" type="text" placeholder="<?php echo __( 'Name', 'inventor-bookings' ); ?>" value="<?php echo empty( $_POST['fullname'] ) ? '' : esc_attr( $_POST['fullname'] ); ?>" required="required">
                </div><!-- /.form-group -->
            <?php endif; ?>

                <div class="form-group">
                    <input class="form-control" name="email" type="email" placeholder="<?php echo __( 'E-mail', 'inventor-bookings' ); ?>" value="<?php echo empty( $_POST['email'] ) ? '' : esc_attr( $_POST['email'] ); ?>" required="required">
                </div><!-- /.form-group -->

            <?php if ( ! empty( $instance['show_phone'] ) ) : ?>
                <div class="form-group">
                    <input class="form-control" name="phone" type="text" placeholder="<?php echo __( 'Phone', 'inventor-bookings' ); ?>" value="<?php echo empty( $_POST['phone'] ) ? '' : esc_attr( $_POST['phone'] ); ?>" required="required">
                </div><!-- /.form-group -->
            <?php endif; ?>

            <?php if ( ! empty( $instance['show_message'] ) ) : ?>
                <div class="form-group">
                    <textarea class="form-control" name="message" required="required" placeholder="<?php echo __( 'Message', 'inventor-bookings' ); ?>" rows="4"><?php echo empty( $_POST['message'] ) ? '' : esc_attr( $_POST['message'] ); ?></textarea>
                </div><!-- /.form-group -->
            <?php endif; ?>

            <?php if ( ! empty( $instance['show_recaptcha'] ) && class_exists( 'Inventor_Recaptcha' ) ) : ?>
                <?php if ( Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) : ?>
                    <div id="recaptcha-<?php echo esc_attr( $this->id ); ?>" class="g-recaptcha" data-sitekey="<?php echo get_theme_mod( 'inventor_recaptcha_site_key' ); ?>"></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="form-group form-group-button">
                <button class="button" type="submit" name="booking_form"><?php echo __( 'Request', 'inventor-bookings' ); ?></button>
            </div><!-- /.form-group -->
        </form>
    </div><!-- /.widget-content -->
</div><!-- /.widget-inner -->

<?php echo wp_kses( $args['after_widget'], wp_kses_allowed_html( 'post' ) ); ?>
