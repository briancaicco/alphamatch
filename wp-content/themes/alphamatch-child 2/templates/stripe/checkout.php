<?php $data = Inventor_Stripe_Logic::get_data( $_POST['payment_type'], $_POST['object_id'] ); ?>
<?php $config = Inventor_Stripe_Logic::get_stripe_config(); ?>

<script
    src="https://checkout.stripe.com/checkout.js"
    class="stripe-button"
    data-key="<?php echo $data['key'] ?>"
    data-name="<?php echo $data['name'] ?>"
    data-description="<?php echo $data['description'] ?>"
    data-amount="<?php echo $data['amount'] ?>"
    data-locale="<?php echo $data['locale'] ?>"
    data-currency="<?php echo $data['currency'] ?>"
    <?php if ( $config['bitcoin_enabled'] ) echo 'data-bitcoin="true"'; ?>
    >
</script>