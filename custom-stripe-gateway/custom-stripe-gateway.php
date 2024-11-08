<?php
/**
 * Plugin Name: Stripe WooCommerce Gateway
 * Description: Custom Stripe Payment Gateway for WooCommerce.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
define( 'WPSTP_PAY_VERSION', '1.0' );
define( 'WPSTP_PAY_URL', plugin_dir_url( __FILE__ ) );
define( 'WPSTP_PAY_PATH', dirname( __FILE__ ) );
define( 'WPSTP_PAY_TEXT_DOMAIN', 'woo-strip-payment-gateway' );
define( 'WPSTP_PAY_DIR_PATH', plugin_dir_path( __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . '/includes/strip-main-class.php';