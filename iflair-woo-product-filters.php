<?php

/**
 *
 * @since             	1.0
 * @package           	iFlair_Woo_Product_Filters
 *
 * Plugin Name:			WooCommerce Product Filters
 * Plugin URI: 		 	https://profiles.wordpress.org/iflairwebtechnologies
 * Description: 		Following plugin is used to apply filter on WooCommerce products.
 * Version:           	1.2
 * Author:            	iFlair Web Technologies Pvt. Ltd.
 * Author URI:        	https://www.iflair.com/
 * Text Domain:       	iflair-woo-product-filters
 * Domain Path:       	/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Defined all constant variables
 *
 * @since 1.0
 * 
 */
define( 'IFWOOPF_FILE', __FILE__);

define( 'IFWOOPF_PLUGIN_NAME', 'WooCommerce Product Filters');

define( 'IFWOOPF_PREFIX', 'ifwoopf');

define( 'IFWOOPF_VERSION', '1.2' );

define( 'IFWOOPF_DIR_PATH', plugin_dir_path( __FILE__ ));

define( 'IFWOOPF_DIR_URL', plugin_dir_url( __FILE__ ));

define( 'IFWOOPF_ASSETS_DIR_PATH', IFWOOPF_DIR_PATH.'assets/');

define( 'IFWOOPF_ASSETS_DIR_URL', IFWOOPF_DIR_URL.'assets/');

define( 'IFWOOPF_ADMIN_DIR_PATH', IFWOOPF_DIR_PATH.'admin/');

define( 'IFWOOPF_ADMIN_DIR_URL', IFWOOPF_DIR_URL.'admin/');

define( 'IFWOOPF_PUBLIC_DIR_PATH', IFWOOPF_DIR_PATH.'public/');

define( 'IFWOOPF_PUBLIC_DIR_URL', IFWOOPF_DIR_URL.'public/');

define( 'IFWOOPF_INC_DIR_PATH', IFWOOPF_DIR_PATH.'includes/');

/**
 * This code runs on plugin activation
 *
 * @since 1.0
 */
function ifwoopf_activate_plugin(){
	require_once( IFWOOPF_INC_DIR_PATH.'class-ifwoopf-activator.php' );
	Iflair_Woo_Product_Filters_Activator::activate();
}
register_activation_hook( __FILE__, 'ifwoopf_activate_plugin' );

/**
 * This code runs od plugin deactivation
 *
 * @since 1.0
 */
function ifwoopf_deactivate_plugin(){
	require_once( IFWOOPF_INC_DIR_PATH.'class-ifwoopf-deactivator.php' );
	Iflair_Woo_Product_Filters_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'ifwoopf_deactivate_plugin' );

/**
 * This code require the main file of plugin
 */
require( IFWOOPF_INC_DIR_PATH.'class-ifwoopf.php' );

/**
 * This code execution whole plugin's code 
 * 
 * @since 1.0
 * 
 * @return instance of core class
 */
function ifwoopf(){
	return Iflair_Woo_Product_Filters::instance();
}

/**
 * The code will runs on plugins loaded
 * 
 * @since 1.0
 */
function ifwoopf_run(){
	if ( class_exists('WooCommerce') ){
		ifwoopf()->run();
	}else{
		ifwoopf()->ifwoopf_deactivate();
	}
}
add_action('plugins_loaded', 'ifwoopf_run', 11);