<?php

/**
 * This class manage all base functionality.
 *
 * @since 1.0 
 * 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/includes 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Iflair_Woo_Product_Filters {

	/**
	 * The instance of query class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$query 	instance of query class
	 */
	public $query;

	/**
	 * The instance of model class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$model 	instance of model class
	 */
	public $model;

	/**
	 * The instance of public class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$public 	instance of public class
	 */
	public $public;

	/**
	 * The instance of admin class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$admin 	instance of admin class
	 */
	public $admin;

	/**
	 * The instance of widgets class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$widgets 	instance of widgets class
	 */
	public $widgets;

	/**
	 * The instance of main class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$instance 	instance of main class
	 */
	private static  $instance;

	/**
	 * This return instance of main class
	 * 
	 * @since 1.0
	 * 
	 * @return instance of Iflair_Woo_Product_Filters
	 */
	public static function instance(){
        
        if ( !isset( self::$instance ) ) {
            self::$instance = new Iflair_Woo_Product_Filters();
        }
        
        return self::$instance;
    }

    /**
     * This code will runs whole plugin
     *
     * @since 1.0
     */
    public function __construct(){

    }

    /**
	 * Runs all code
	 *
	 * @since 1.0
	 */
	public function run(){
		$this->ifwoopf_set_locale();
		$this->ifwoopf_load_dependencies();
		$this->ifwoopf_define_hooks();
	}

	/**
	 * Loades all files which are used by this plugin
	 * 
	 * @since 1.0
	 */
	private function ifwoopf_load_dependencies(){
		
		global $ifwoopf_settings,$ifwoopf_default_settings,$ifwoopf_query,$ifwoopf_model,$ifwoopf_admin,$ifwoopf_public;

		/**
		 * This file is responsible for defining all core functions. 
		 */
		require_once( IFWOOPF_INC_DIR_PATH.'ifwoopf-functions.php' );

		$ifwoopf_settings = ifwoopf_get_option();
		$ifwoopf_default_settings = ifwoopf_default_settings();


		/**
		 * This class is responsible for defining all methods filter query
		 */
		require_once( IFWOOPF_INC_DIR_PATH.'class-ifwoopf-query.php' );
		$ifwoopf_query = new Iflair_Woo_Product_Filters_Query();
		$this->query = $ifwoopf_query;

		/**
		 * This class is responsible for defining all methods for logic
		 */
		require_once( IFWOOPF_INC_DIR_PATH.'class-ifwoopf-model.php' );
		$ifwoopf_model = new Iflair_Woo_Product_Filters_Model();
		$this->model = $ifwoopf_model;

		/**
		 * This class is responsible for defining all functionality of admin
		 */
		
		if( is_admin() ){

			require_once( IFWOOPF_ADMIN_DIR_PATH.'class-ifwoopf-admin.php' );
			$ifwoopf_admin = new Iflair_Woo_Product_Filters_Admin();
			$this->admin = $ifwoopf_admin;

		}

		/**
		 * This class is responsible for defining all functionality of front
		 */
		require_once( IFWOOPF_PUBLIC_DIR_PATH.'class-ifwoopf-public.php' );
		$ifwoopf_public = new Iflair_Woo_Product_Filters_Public();
		$this->public = $ifwoopf_public;

		/**
		 * This class is responsible for defining all functionality of Widgets
		 */
		require_once( IFWOOPF_PUBLIC_DIR_PATH.'class-ifwoopf-widgets.php' );
		$ifwoopf_widgets = new Iflair_Woo_Product_Filters_Widgets();
		$this->widgets = $ifwoopf_widgets;

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since 1.0
	 */
	private function ifwoopf_set_locale() {

		load_plugin_textdomain(
			'iflair-woo-product-filters',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Deactivate this plugin if woocommerce is not activate.
	 *
	 * @since 1.0
	 */
	public function ifwoopf_deactivate() {

		if ( !function_exists('deactivate_plugins') ) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		add_action('admin_notices', array($this, 'ifwoopf_woocommerce_check_notice'));
		if( isset( $_GET['activate'] ) ){
			unset( $_GET['activate'] );
			$_GET['deactivate'] = true;
		}
		deactivate_plugins( IFWOOPF_FILE, true );

	}

	/**
	 * Display notice if woocommerce is not activate.
	 *
	 * @since 1.0
	 */
	public function ifwoopf_woocommerce_check_notice() {

		$woourl = 'https://wordpress.org/plugins/woocommerce/';
			
		$plugin_name = '<strong>'.IFWOOPF_PLUGIN_NAME.'</strong>';

		?>
		<div class="error notice is-dismissible"> 
			<p><?php printf( wp_kses_post($plugin_name) . ' ' . esc_html__( 'plugin requires %sWooCommerce%s plugin in order to work. So please ensure that WooCommerce is installed and activated.', 'iflair-woo-product-filters' ),'<a target="_blank" href="' . esc_url($woourl) . '">','</a>'); ?>				
			</p>
		</div>
	    <?php
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @since 1.0
	 * 
	 * @param mixed $links Plugin Action links.
	 * @return array
	 */
	public function ifwoopf_plugin_action_links( $links ) {

		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=ifwoopf-settings' ) . '" aria-label="' . sprintf( esc_attr( esc_html__('View %s settings', 'iflair-woo-product-filters' ) ), IFWOOPF_PLUGIN_NAME ) . '">' . esc_html__( 'Settings', 'iflair-woo-product-filters' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Define all hooks of plugins
	 *
	 * @since 1.0
	 */
	private function ifwoopf_define_hooks(){

		add_filter( 'plugin_action_links_' . plugin_basename( IFWOOPF_FILE ), array( $this, 'ifwoopf_plugin_action_links' ) );

		if( is_admin() ){
			$this->admin->add_hooks();
		}
		$this->public->add_hooks();
		$this->widgets->add_hooks();
		
	}
}