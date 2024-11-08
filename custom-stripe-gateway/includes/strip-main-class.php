<?php

/**
 * This defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       c-metric.com
 *
 * @package    wpstp-stripe-payment-gateway
 * @subpackage wpstp-stripe-payment-gateway/includes
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class for registering a new settings page under Settings.
 */
class WPSTP_Stripe_Gateway {
    /**
     * Constructor.
     */
    public $id = 'wpstp_stripe_payment_gateway';

    function __construct() {
        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_wpstp_stripe_gateway' ));
        add_action( 'plugins_loaded', array( $this, 'init_wpstp_stripe_gateway' ));
    }

    /**
     * Add WPSTP Stripe Gateway to available WooCommerce payment gateways.
     *
     * @param array $gateways Existing payment gateways.
     * @return array Updated list of payment gateways.
     */
    public function add_wpstp_stripe_gateway( $gateways ) {
        $gateways[] = 'WC_WPSTP_Stripe_Payment_Gateway';
        return $gateways;
    }

    /**
     * Initialize the WPSTP Stripe Payment Gateway.
     */
    public function init_wpstp_stripe_gateway() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        require_once plugin_dir_path( __FILE__ ) . '/class-wc-gateway-stripe.php';
    }
}

// Initialize the main class.
return new WPSTP_Stripe_Gateway();
