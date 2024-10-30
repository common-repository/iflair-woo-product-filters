<?php
/**
 * This class manage filter form widget.
 *
 * @since 1.1 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Iflair_Woo_Product_Filters_Form_widget extends WP_Widget {
 	
 	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.1
	 */
	public function __construct() {

		parent::__construct(
		// Base ID of your widget
		'ifwoopf_form', 
		// Widget name will appear in UI
		esc_html__('Ifwoopf Form Widget', 'iflair-woo-product-filters'), 
		// Widget description
		array( 'description' => esc_html__( 'iFlair WooCommerce Filter.', 'iflair-woo-product-filters' ), )
		);

	}
 
	/**
	 * Create widget front part
	 *
	 * @since 1.1
	 */
	public function widget( $args, $instance ) {
		global $ifwoopf_public;
		$position = ifwoopf_get_option('display_filter_position', true);
		$is_load_this = $ifwoopf_public->ifwoopf_enqueue_conditions();
		?>
		<?php if ( $is_load_this && $position !== 'from_hook' ) { ?>
			<?php echo esc_html($args['before_widget']); ?>
				<div class="ifwoopf-widget ifwoopf-widget-form">
					<?php $ifwoopf_public->ifwoopf_render_filter_form(); ?>
				</div>
			<?php echo esc_html($args['after_widget']); ?>
		<?php } ?>
		<?php
	}	

}