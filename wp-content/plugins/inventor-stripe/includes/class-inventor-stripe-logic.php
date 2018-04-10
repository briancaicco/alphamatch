<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once INVENTOR_STRIPE_DIR . 'libraries/stripe-php-4.0.0/init.php';

use Stripe\Stripe;

/**
 * Class Inventor_Stripe_Logic
 *
 * @class Inventor_Stripe_Logic
 * @package Inventor_Stripe/Classes
 * @author Pragmatic Mates
 */
class Inventor_Stripe_Logic {
    /**
     * Initialize Stripe functionality
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'process_payment' ), 9999 );

        add_filter( 'inventor_payment_gateways', array( __CLASS__, 'payment_gateways' ) );
    }

    /**
     * Adds payments gateways
     *
     * @access public
     * @param array $gateways
     * @return array
     */
    public static function payment_gateways( $gateways ) {
        if (  self::is_active() ) {
            $gateways['stripe-checkout'] = array(
                'id'      => 'stripe-checkout',
                'title'   => __( 'Stripe Checkout', 'inventor-stripe' ),
                'proceed' => false,
                'content' => Inventor_Template_Loader::load( 'stripe/checkout', array(), INVENTOR_STRIPE_DIR ),
            );
        }

        return $gateways;
    }

    /**
     * Gets Stripe config
     *
     * @access public
     * @return array|bool
     */
    public static function get_stripe_config() {
        $secret_key = get_theme_mod( 'inventor_stripe_secret_key', null );
        $publishable_key = get_theme_mod( 'inventor_stripe_publishable_key', null );
        $bitcoin_enabled = get_theme_mod( 'inventor_stripe_bitcoin_enabled', false );

        if ( empty( $secret_key ) || empty( $publishable_key ) ) {
            return false;
        }

        $stripe_config = array(
            "secret_key"        => $secret_key,
            "publishable_key"   => $publishable_key,
            "bitcoin_enabled"   => $bitcoin_enabled
        );

        return $stripe_config;
    }

    /**
     * Process payment form
     *
     * @access public
     * @return void
     */
    public static function process_payment() {
        $config = self::get_stripe_config();

        if ( ! isset( $_POST['stripeToken'] ) ) {
            return;
        }

        // set Stripe API key
        Stripe::setApiKey($config['secret_key']);

        $token = ! empty( $_POST['stripeToken'] ) ? $_POST['stripeToken'] : null;
        $token_type = ! empty( $_POST['stripeTokenType'] ) ? $_POST['stripeTokenType'] : null;
        $email = ! empty( $_POST['stripeEmail'] ) ? $_POST['stripeEmail'] : null;

        $settings = array(
            'payment_type'  => ! empty( $_POST['payment_type'] ) ? $_POST['payment_type'] : '',
            'object_id'  	=> ! empty( $_POST['object_id'] ) ? $_POST['object_id'] : '',
            'currency'  	=> ! empty( $_POST['currency'] ) ? $_POST['currency'] : '',
            'price'  	    => ! empty( $_POST['price'] ) ? $_POST['price'] : '',
        );

        // billing details
        $settings['billing_details'] = Inventor_Billing::get_billing_details_from_context( $_POST );

        try {
            // amount in cents
            $amount = Inventor_Price::get_price_in_cents( $settings["price"] );

            if ( $token_type == 'bitcoin_receiver' ) {
                // https://stripe.com/docs/bitcoin

                $receiver = \Stripe\BitcoinReceiver::create(array(
                    "amount" => $amount,
                    "currency" => $settings["currency"], // presently can only be USD
                    "email" => $email
                ));

                // sleep until bitcoin receiver gets filled
                // TODO: use Stripe webhook instead
                sleep(5);

                $customer = \Stripe\Customer::create(array(
                    "source" => $receiver->id
                ));

                $charge = \Stripe\Charge::create(array(
                    "amount" => $receiver->amount,
                    "currency" => $receiver->currency,
                    "customer" => $customer->id, // use either customer or source
//                    "source" => $receiver->id, // use either customer or source
//                    "description" => "Example charge" // TODO: add payment description
                ));

            } else {
                $customer = \Stripe\Customer::create( array(
                    'email' => $email,
                    'card'  => $token
                ));

                $charge = \Stripe\Charge::create( array(
                    'customer' => $customer->id,
                    'amount'   => $amount,
                    'currency' => $settings["currency"]
                ) );
            }

            // process successful result
            self::process_result( true, $settings, $charge->id, $token );

        } catch(\Stripe\Error\Card $e) {
            // The card has been declined
            Inventor_Utilities::show_message( 'danger', __( 'The card has been declined.', 'inventor-stripe' ) );
        } catch(\Stripe\Error\InvalidRequest $e) {
            // The card has been declined or token was used than once
            Inventor_Utilities::show_message( 'danger', $e->__toString() );
        }

        // process error result
        self::process_result( false, $settings, null, $token );
    }

    /**
     * Process payment result
     *
     * @access public
     * @return void
     */
    public static function process_result( $success, $settings, $payment_id, $token ) {
        $gateway = 'stripe-checkout';

        $post = get_post( $settings['object_id'] );
        $user_id = get_current_user_id();

        // validate payment
        $is_valid = true;

        if ( $success ) {
            $is_valid = ! Inventor_Post_Type_Transaction::does_transaction_exist( array( 'stripe-checkout' ), $payment_id );

            // if params are present, validate them
            if ( $is_valid ) {
                $is_valid = self::is_stripe_payment_valid( $payment_id );
            }

            // if payment is invalid, it is not successful transaction
            $success = $is_valid;
        }

        // prepare transaction data
        $data = array(
            'success'           => $success,
            'price'             => $settings["price"],
            'price_formatted'   => Inventor_Price::format_price( $settings["price"] ),
            'currency_code'     => $settings["currency"],
            'currency_sign'     => '',
            'token'             => $token,
            'paymentId'         => $payment_id
        );

        // create transaction
        Inventor_Post_Type_Transaction::create_transaction( $gateway, $success, $user_id, $settings['payment_type'], $payment_id, $settings['object_id'], $settings["price"], $settings["currency"], $data );

        // hook inventor action
        do_action( 'inventor_payment_processed', $success, $gateway, $settings['payment_type'], $payment_id, $settings['object_id'], $settings["price"], $settings["currency"], $user_id, $settings['billing_details'] );

        // handle payment
        if ( $success ) {
            if ( ! $is_valid ) {
                Inventor_Utilities::show_message( 'danger', __( 'Payment is invalid.', 'inventor-stripe' ) );
            } else if ( ! in_array( $settings['payment_type'], apply_filters( 'inventor_payment_types', array() ) ) ) {
                Inventor_Utilities::show_message( 'danger', __( 'Undefined payment type.', 'inventor-stripe' ) );
            } else {
                Inventor_Utilities::show_message( 'success', __( 'Payment has been successful.', 'inventor-stripe' ) );
            }
        } else {
            Inventor_Utilities::show_message( 'danger', __( 'Payment failed, canceled or is invalid.', 'inventor-stripe' ) );
        }

        // after payment page
        $redirect_url = Inventor_Utilities::get_after_payment_url( $settings['payment_type'], $settings['object_id'] );

        wp_redirect( $redirect_url );
        exit();
    }

    /**
     * Prepares payment data
     *
     * @access public
     * @param $payment_type
     * @param $object_id
     * @return array|bool
     */
    public static function get_data( $payment_type, $object_id ) {
        if ( empty( $payment_type ) || empty( $object_id ) ) {
            return false;
        }

        if ( ! in_array( $payment_type, apply_filters( 'inventor_payment_types', array() ) ) ) {
            return false;
        }

        $payment_data = apply_filters( 'inventor_prepare_payment', array(), $payment_type, $object_id );

        $config = Inventor_Stripe_Logic::get_stripe_config();
        $publishable_key = $config['publishable_key'];

        return array(
            'key'               => $publishable_key,
            'name'              => $payment_data['action_title'],
            'description'       => $payment_data['description'],
            'amount'            => Inventor_Price::get_price_in_cents( $payment_data['price'] ),
            'currency'          => Inventor_Price::default_currency_code(),
            'locale'            => 'auto'
        );
    }

    /**
     * Checks if Stripe payment is valid
     *
     * @access public
     * @param string $payment_id
     * @return bool
     */
    public static function is_stripe_payment_valid( $payment_id ) {
        $config = self::get_stripe_config();

        try {
            Stripe::setApiKey($config['secret_key']);

            $charge = \Stripe\Charge::retrieve( $payment_id );

            if ( $charge->id != $payment_id ) {
                return false;
            }

        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Checks if Stripe is active
     *
     * @access public
     * @return bool
     */
    public static function is_active() {
        $config = self::get_stripe_config();

        return ( ! empty( $config ) && is_array( $config ) );
    }

    /**
     * Returns supported currencies by Stripe listed here:
     *
     * @access public
     * @param string $payment
     * @see https://support.stripe.com/questions/which-currencies-does-stripe-support
     * @return array
     */
    public static function get_supported_currencies( $payment ) {
        $currency_group_1 = array(
            "AED", "ALL", "ANG", "ARS", "AUD", "AWG", "BBD", "BDT", "BIF", "BMD", "BND", "BOB", "BRL", "BSD", "BWP",
            "BZD", "CAD", "CHF", "CLP", "CNY", "COP", "CRC", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP", "ETB",
            "EUR", "FJD", "FKP", "GBP", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD", "HNL", "HRK", "HTG", "HUF", "IDR",
            "ILS", "INR", "ISK", "JMD", "JPY", "KES", "KHR", "KMF", "KRW", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD",
            "MAD", "MDL", "MNT", "MOP", "MRO", "MUR", "MVR", "MWK", "MXN", "MYR", "NAD", "NGN", "NIO", "NOK", "NPR",
            "NZD", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RUB", "SAR", "SBD", "SCR", "SEK", "SGD",
            "SHP", "SLL", "SOS", "STD", "SVC", "SZL", "THB", "TOP", "TTD", "TWD", "TZS", "UAH", "UGX", "USD", "UYU",
            "UZS", "VND", "VUV", "WST", "XAF", "XOF", "XPF", "YER", "ZAR"
        );

        $currency_group_2 = array(
            "AFN", "AMD", "AOA", "AZN", "BAM", "BGN", "GEL", "CDF", "KGS", "LSL", "MGA", "MKD", "MZN", "RON", "RSD",
            "SRD", "TJS", "TRY", "XCD", "ZMW"
        );

        return array_merge( $currency_group_1, $currency_group_2 );
    }
}

Inventor_Stripe_Logic::init();
