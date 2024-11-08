<?php
/*
Plugin Name: Page Comment Add on For WPBakery Page Builder
Plugin URI: https://www.c-metric.com/
Version:  1.0
Author: cmetric
Description: Using This Plugin Addon You can add Comment Section in page/post.
*/

namespace PageCommentAddonForWPBakeryPageBuilder;

if ( ! defined( 'ABSPATH' ) ) {
    die( "Please don't try to access this file directly." );
}
define( 'PCAFWPB_VERSION', '0.0.1' );
define( 'PCAFWPB_URL', plugin_dir_url( __FILE__ ) );
define( 'PCAFWPB_PATH', dirname( __FILE__ ) );
define( 'PCAFWPB_TEXT_DOMAIN', 'pcafwpb_textdomain' );


// Before VC Init
add_action( 'vc_before_init', __namespace__.'\pcafwpb_vc_before_init_actions' );
function pcafwpb_vc_before_init_actions() {
    include( plugin_dir_path( __FILE__ ) . 'includes/pcafwpb-setting.php');
}

include( plugin_dir_path( __FILE__ ) . 'includes/pcafwpb-funtion.php');


