<?php
/**
 * This file is used to markup the filter field checkbox.
 *
 * @since      1.0
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public/partials/fields
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( isset( $field['name'] ) && !empty( $field['name'] ) ) { ?>
	<?php
	$field_id = ( !empty( $field['id'] ) ) ? $field['id'] : "checkbox_".$field['value_attribute'];
	?>
	<input type="checkbox" name="<?php echo esc_attr($field['name']); ?>"
	id="<?php echo esc_attr( $field_id ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($field['value_attribute']); ?>" <?php if (isset($field['checked']) && $field['checked'] === true) echo esc_html__("checked='checked'"); ?>>
	<?php if ( !empty( $field['label'] ) ) { ?>
		<label class="ifwoopf-checkbox-label" for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field['label']) ?></label>
	<?php } ?>
<?php } ?>