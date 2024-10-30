<?php
/**
 * This file is used to markup the filter type sorting.
 *
 * @since      1.0
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public/partials/filter-types
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( isset( $sorting ) && !empty( $sorting ) && is_array( $sorting ) ) { ?>
	<?php
	$default_sorting = ifwoopf_get_option('default_sorting');
	if( empty( $default_sorting ) ){
		$default_sorting = ifwoopf_get_sorting_types(true);
		$default_sorting = array_keys($default_sorting)[0];
	}

	$field_name = 'sorting';
	$feld_args = array(
		'type' => 'select',
		'options' => $sorting,
		'name' => $field_name,
		'placeholder' => '',
		'value' => $default_sorting
	);

	$display_sorting_heading = ifwoopf_get_option('display_sorting_heading');
	if( $display_sorting_heading === 'yes' ){
		$heading = trim(ifwoopf_get_option('sorting_heading'));
	}

	$toggle_btn = ifwoopf_get_option('toggle_btn_sorting');
	$field_toggle = ( $toggle_btn === 'yes' ) ? true : false;
	$default_toggle_sorting = ifwoopf_get_option('default_toggle_sorting');

	$toggled_btn_class = ( $default_toggle_sorting === 'show' ) ? 'ifwoopf-type-btn-toggled' : '';
	$toggled_type_class = ( $display_sorting_heading === 'yes' && $toggle_btn === 'yes' ) ? 'ifwoopf-toggle-enabeled' : '';
	$toggled_type_class = ( $default_toggle_sorting === 'show' ) ? $toggled_type_class . ' ifwoopf-type-toggled' : $toggled_type_class;
	?>
	<div class="ifwoopf-type ifwoopf-type-sorting <?php echo esc_attr( $toggled_type_class ); ?>">
		<?php if ( $display_sorting_heading === 'yes' ) { ?> 
			<div class="ifwoopf-type-heading">
				<label><?php echo !empty( $heading ) ? esc_html($heading) : esc_html__('Sorting', 'iflair-woo-product-filters'); ?></label>
				<?php if( !empty($field_toggle) ) { ?>
					<i class="ifwoopf-toggle-field-btn fa-solid fa-circle-plus <?php echo esc_attr( $toggled_btn_class ); ?>"></i>
				<?php } ?> 
			</div>
		<?php } ?>
		<div class="ifwoopf-field-wrap">
			<?php ifwoopf_get_front_field_markup($feld_args); ?>
		</div>
	</div>	
<?php } ?>