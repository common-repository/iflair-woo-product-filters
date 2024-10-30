<?php
/**
 * This file is used to markup the filter field text.
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
<?php if ( !empty( $field['name'] ) ) { ?>
	<?php
	$field_id = ( !empty( $field['id'] ) ) ? $field['id'] : "text_".$field['name'];
	?>
	<?php if ( isset( $field['label'] ) && !empty( $field['label'] ) ) { ?>
		<label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field['label']); ?></label>
	<?php } ?>
	<input type="text" name="<?php echo esc_attr($field['name']); ?>"
		id="<?php echo esc_attr( $field_id ); ?>"
		style="<?php echo esc_attr( $field['css'] ); ?>"
		class="<?php echo esc_attr( $field['class'] ); ?>"
		placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
		<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($field['value']); ?>">
<?php } ?>