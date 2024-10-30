<?php

/**
 * This class includes and manage all widgets functionality.
 *
 * @since 1.1 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Iflair_Woo_Product_Filters_Widgets {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.1
	 */
	public function __construct(){

		$this->ifwoopf_include_widgets();

	}

	/**
	 * Require all widgets class files
	 *
	 * @since 1.1
	 */
	public function ifwoopf_include_widgets(){
		
		require_once( IFWOOPF_PUBLIC_DIR_PATH.'widgets/class-ifwoopf-form-widget.php' );

	}

	/**
	 * Register all widgets
	 *
	 * @since 1.1
	 */
	public function ifwoopf_register_widgets(){
		
		register_widget( 'Iflair_Woo_Product_Filters_Form_widget' );

	}

	/**
	 * Define all hooks
	 *
	 * @since 1.1
	 */
	public function add_hooks(){

		add_action( 'widgets_init', array($this, 'ifwoopf_register_widgets') );

	}

}