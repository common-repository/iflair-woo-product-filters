<?php
/**
 * This file is used to markup the setting field number.
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
$min = ( isset( $field['min'] ) && !empty( $field['min'] ) ) ? 'min="'.esc_attr( $field['min'] ).'"' : '';
$max = ( isset( $field['max'] ) && !empty( $field['max'] ) ) ? 'max="'.esc_attr( $field['max'] ).'"' : '';
$step = ( isset( $field['step'] ) && !empty( $field['step'] ) ) ? 'step="'.esc_attr( $field['step'] ).'"' : '';
?>
<input type="number" name="<?php echo esc_attr($field['name_attribute']); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" style="<?php echo esc_attr( $field['css'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" <?php echo esc_html($min); ?> <?php echo esc_html($max); ?> <?php echo esc_html($step); ?>
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($field['field_value']); ?>">
<?php echo esc_html($field['description']); ?>