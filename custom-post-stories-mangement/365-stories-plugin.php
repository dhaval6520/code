<?php

/**
 * The plugin is Used For Mange Stories From backend
 *
 *
 * @link              https://www.c-metric.com/
 * @since             1.0.0
 * @package           365-stories
 *
 * @wordpress-plugin
 * Plugin Name:       365 Stories
 * Plugin URI:        https://www.c-metric.com/
 * Description:       This plugin is used for Stories Filter and search funationality.
 * Version:           1.0.0
 * Author:            cmetric
 * Author URI:        https://www.c-metric.com/
 * License:           GPL-2.0+
 * License URI:       https://www.c-metric.com/
 * Text Domain:       365-stories
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Stories_CPT_VERSION', '1.0.0' );
define( 'Stories_CPT_URL', plugin_dir_url( __FILE__ ) );
define( 'Stories_CPT_PATH', dirname( __FILE__ ) );
define( 'Stories_CPT_TEXT_DOMAIN', 'stories_textdomain' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-365-stories-post.php';
require plugin_dir_path( __FILE__ ) . 'includes/stories-funtion.php';





