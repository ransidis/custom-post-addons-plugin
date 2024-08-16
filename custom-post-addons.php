<?php
/*
Plugin Name: Custom Post Addons
Description: Adds custom text and a button above or below all posts on the site.
Version: 1.0
Author: Ransi Dissanayake
Author URI: https://ransi.me
Text Domain: custom-post-addons
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'CUSTOM_POST_ADDONS_VERSION', '1.1' );
define( 'CUSTOM_POST_ADDONS_TEXT_DOMAIN', 'custom-post-addons' );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-post-addons-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-post-addons-display.php';

// Initialize the admin settings page
if ( is_admin() ) {
	Custom_Post_Addons_Admin::get_instance();
}

// Initialize the frontend display
Custom_Post_Addons_Display::get_instance();

function custom_post_addons_enqueue_scripts() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'custom-post-addons-admin', plugins_url( '/assets/js/admin.js', __FILE__ ), array( 'wp-color-picker' ), CUSTOM_POST_ADDONS_VERSION, true );
	wp_enqueue_style( 'custom-post-addons-style', plugins_url( '/assets/css/styles.css', __FILE__ ), array(), CUSTOM_POST_ADDONS_VERSION );
}
add_action( 'admin_enqueue_scripts', 'custom_post_addons_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'custom_post_addons_enqueue_scripts' );
