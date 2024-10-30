<?php
/**
 * This file is used to markup the filter type search.
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

<?php
$display_search_heading = ifwoopf_get_option('display_search_heading');
if( $display_search_heading === 'yes' ){
	$heading = trim(ifwoopf_get_option('search_heading'));
}
$placeholder = ifwoopf_get_option('search_placeholder');

$feld_args = array(
	'type' => 'text',
	'value' => '',
	'name' => 'ifwoopf_search',
	'placeholder' => $placeholder,
);

$toggle_btn = ifwoopf_get_option('toggle_btn_search');
$field_toggle = ( $toggle_btn === 'yes' ) ? true : false;
$default_toggle_search = ifwoopf_get_option('default_toggle_search');

$toggled_btn_class = ( $default_toggle_search === 'show' ) ? 'ifwoopf-type-btn-toggled' : '';
$toggled_type_class = ( $display_search_heading === 'yes' && $toggle_btn === 'yes' ) ? 'ifwoopf-toggle-enabeled' : '';
$toggled_type_class = ( $default_toggle_search === 'show' ) ? $toggled_type_class . ' ifwoopf-type-toggled' : $toggled_type_class;
?>
<div class="ifwoopf-type ifwoopf-type-search <?php echo esc_attr( $toggled_type_class ); ?>">
	<?php if( $display_search_heading === 'yes' ) { ?>
		<div class="ifwoopf-type-heading">
			<label><?php echo ( !empty( $heading ) ) ? esc_html($heading) : esc_html__('Search', 'iflair-woo-product-filters'); ?></label>
			<?php if(!empty($field_toggle)) { ?>
					<i class="ifwoopf-toggle-field-btn fa-solid fa-circle-plus <?php echo esc_attr( $toggled_btn_class ); ?>"></i>
				<?php } ?> 
		</div>
	<?php } ?>
	<div class="ifwoopf-field-wrap">
		<?php ifwoopf_get_front_field_markup($feld_args); ?>
	</div>
</div>