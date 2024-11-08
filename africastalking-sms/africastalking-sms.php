<?php
/*
Plugin Name: Bible - Africa's Talking SMS 
Description: Easily send single or bulk SMS messages from your WordPress site using the Bible - Africa's Talking SMS. Manage recipients, customize messages, and track SMS history with a secure, user-friendly interface.
Version: 1.0
Author: Rupesh Jorkar (RJ)
Author URI: https://www.c-metric.com/
* License: GPL-2.0-or-later
* License URI: https://opensource.org/licenses/GPL-2.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
define( 'BASMS_BATCH_VERSION', '1.0' );
define( 'BASMS_BATCH_URL', plugin_dir_url( __FILE__ ) );
define( 'BASMS_BATCH_PATH', dirname( __FILE__ ) );
define( 'BASMS_BATCH_TEXT_DOMAIN', 'BASMS_plugin' );
define( 'BASMS_BATCH_DIR_PATH', plugin_dir_path( __FILE__ ) );

include __DIR__ . '/includes/africastalking-sms-class.php';	
include __DIR__ . '/includes/africastalking-send-form-data-class.php';	
include __DIR__ . '/includes/africastalking-send-sms-class.php';	
include __DIR__ . '/includes/africastalking-sms-history.php';	


register_activation_hook( __FILE__, 'AfricasTalkingSmsActivated' );

function AfricasTalkingSmsActivated() {
	ob_start();
	\AfricasTalkingSmsActivated::africastalking_sms_log();
	ob_end_clean();
}

