<?php
/*
Plugin Name: C-Metric Product Slider & Carousel for WooCommerce
Description: C-Metric Product Slider & Carousel for WooCommerce is a user-friendly plugin to display your WooCommerce products in a responsive, customizable slider or carousel. Showcase featured products, best sellers, or specific categories with ease, enhancing product visibility and boosting customer engagement. Perfect for creating dynamic product displays with minimal setup.
Version: 1.0
Author: cmetric
Author URI: https://www.c-metric.com/
* License: GPL-2.0-or-later
* License URI: https://opensource.org/licenses/GPL-2.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'CMWPS_VERSION', '1.0' );
define( 'CMWPS_URL', plugin_dir_url( __FILE__ ) );
define( 'CMWPS_PATH', dirname( __FILE__ ) );
define( 'CMWPS_TEXT_DOMAIN', 'cmetric-product-slider-carousel-for-woocommerce' );
define( 'CMWPS_DIR_PATH', plugin_dir_path( __FILE__ ) );

include __DIR__ . '/includes/cm-woo-product-slider-class.php';	
