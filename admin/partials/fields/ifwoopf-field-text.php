<?php
/**
 * This file is used to markup the filter field text.
 *
 * @since      1.0
 *
 * @package    iFlair_WooCommerce_Product_Filters
 * @subpackage iFlair_WooCommerce_Product_Filters/admin/partials/fields
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input type="text" name="<?php echo esc_attr($field['name_attribute']);?>"
	id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($field['field_value']); ?>">
<?php echo esc_html($field['description']); ?>