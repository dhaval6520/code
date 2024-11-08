<?php
/**
 * The plugin is Used For Orser Tracking
 *
 * @link              https://www.c-metric.com/
 * @package           give-dpo-pay-add-on 
 *
 * @wordpress-plugin
 * Plugin Name:       Give - DPO Pay Add-on 
 * Plugin URI:        https://www.c-metric.com/
 * Description:       This plugin used to Add DPO payment gateway to the GiveWP plugin
 * Version:           1.0.0
 * Author:            cmetric
 * Author URI:        https://www.c-metric.com/
 * License:           GPL-2.0+
 */
 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
if ( ! defined( 'give_dpo_pay_addon_VERSION' ) ) {
	define( 'give_dpo_pay_addon_VERSION', '0.0.1' );
}
if ( ! defined( 'give_dpo_pay_addon_PLUGIN_URL' ) ) {
	define( 'give_dpo_pay_addon_PLUGIN_URL', __FILE__ );
}
if ( ! defined( 'give_dpo_pay_addon_DIR' ) ) {
	define( 'give_dpo_pay_addon_DIR', plugin_dir_path( give_dpo_pay_addon_PLUGIN_URL ) );
}
if ( ! defined( 'give_dpo_pay_addon_URL' ) ) {
	define( 'give_dpo_pay_addon_URL', plugin_dir_url( give_dpo_pay_addon_PLUGIN_URL ) );
}
if ( ! defined( 'give_dpo_pay_addon_BASENAME' ) ) {
	define( 'give_dpo_pay_addon_BASENAME', plugin_basename( give_dpo_pay_addon_PLUGIN_URL ) );
}
if ( ! defined( 'give_dpo_pay_addon_TEXT_DOMAIN' ) ) {
	define( 'give_dpo_pay_addon_TEXT_DOMAIN', 'give_dpo_pay_addon_management' );
}
if ( ! defined( 'give_dpo_pay_addon_SLUG' ) ) {
	define( 'give_dpo_pay_addon_SLUG', 'give-dpo-pay-add-on' );
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require give_dpo_pay_addon_DIR . 'includes/give-dpo-pay-add-on-common-class.php';

// Activation hook for Page template
register_activation_hook( __file__, 'dpo_custom_page_create' );
function dpo_custom_page_create(){
	//create the thankyou page fro paylater payment gateway
	 $page = get_page_by_path( 'give-dpo-thank-you', OBJECT );
    if ( !isset($page) ){
		$new_page_title = 'Give DPO Thank You';
		$new_page_content = '';
		$new_page_template = plugin_dir_path( __FILE__ ) .'template/dpo-thankyou-template.php'; //ex. template-custom.php. Leave blank if you don't want a custom page template.
		//don't change the code bellow, unless you know what you're doing
		$page_check = get_page_by_title($new_page_title);
		$new_page = array(
			'post_type' => 'page',
			'post_title' => $new_page_title,
			'post_content' => $new_page_content,
			'post_status' => 'publish',
			'post_author' => 1,
		);
		if(!isset($page_check->ID)){
			$new_page_id = wp_insert_post($new_page);
			if(!empty($new_page_template)){
				update_post_meta($new_page_id, '_dpo_custom_template', $new_page_template);
			}
		}
	}
	$page2 = get_page_by_path( 'give-dpo-error', OBJECT );
    if ( !isset($page2) ){
		$new_page_title = 'Give DPO Error';
		$new_page_content = '';
		$new_page_template = plugin_dir_path( __FILE__ ) .'template/error-template.php'; //ex. template-custom.php. Leave blank if you don't want a custom page template.
		//don't change the code bellow, unless you know what you're doing
		$page_check = get_page_by_title($new_page_title);
		$new_page = array(
			'post_type' => 'page',
			'post_title' => $new_page_title,
			'post_content' => $new_page_content,
			'post_status' => 'publish',
			'post_author' => 1,
		);
		if(!isset($page_check->ID)){
			$new_page_id = wp_insert_post($new_page);
			if(!empty($new_page_template)){
				update_post_meta($new_page_id, '_dpo_custom_template', $new_page_template);
			}
		}
	}
}







