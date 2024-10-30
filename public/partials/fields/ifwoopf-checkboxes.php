<?php
/**
 * This file is used to markup the filter field checkboxes.
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
<?php if ( isset( $field['values'] ) && !empty( $field['values'] ) && is_array( $field['values'] ) ) { ?>
	<?php foreach ($values as $value => $label) { ?>
		<?php
		$field_id = ( !empty( $field['id'] ) ) ? $field['id'] : "checkbox_".$field['value'];
		?>
		<input type="checkbox" name="<?php echo esc_attr($field['name']); ?>"
		id="<?php echo esc_attr( $field_id ); ?>"
		style="<?php echo esc_attr( $field['css'] ); ?>"
		class="<?php echo esc_attr( $field['class'] ); ?>"
		<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($value); ?>">
		<label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
	<?php } ?>
<?php } ?>