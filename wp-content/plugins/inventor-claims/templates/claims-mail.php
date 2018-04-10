<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php if ( ! empty( $name ) ) : ?>
    <strong><?php echo __( 'Name', 'inventor-claims' ); ?>: </strong> <?php echo esc_attr( $name ); ?><br><br>
<?php endif; ?>

<?php if ( ! empty( $email ) ) : ?>
    <strong><?php echo __( 'E-mail', 'inventor-claims' ); ?>: </strong> <?php echo esc_attr( $email ); ?><br><br>
<?php endif; ?>

<?php if ( ! empty( $phone ) ) : ?>
    <strong><?php echo __( 'Phone', 'inventor-claims' ); ?>: </strong> <?php echo esc_attr( $phone ); ?><br><br>
<?php endif; ?>

<?php $permalink = get_permalink( $listing->ID ); ?>
<?php if ( ! empty( $permalink ) ) : ?>
    <strong><?php echo __( 'URL', 'inventor-claims' ); ?>: </strong> <?php echo esc_attr( $permalink ); ?><br><br>
<?php endif; ?>

<?php if ( ! empty( $message ) ) : ?>
    <?php echo esc_html( $message ); ?>
<?php endif; ?>