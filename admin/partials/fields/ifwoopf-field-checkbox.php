<?php
/**
 * This file is used to markup the setting field checkbox.
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

$value_attribute = ( isset( $field['value_attribute'] ) ) ? $field['value_attribute'] : "";
$disabeled = ( isset( $field['disabled'] ) && $field['disabled'] === true ) ? 'disabled="disabled"' : "";
$hidden_parent_class = ( isset( $field['hidden_value'] ) && !empty( $field['hidden_value'] ) ) ? ' ifwoopf-checkbox-has-hidden' : '';

if ( !empty( $value_attribute ) ){
	$value = $value_attribute;
}else{
	$value = "yes";
} 

?>
<input type="checkbox" name="<?php echo esc_attr($field['name_attribute']); ?>" id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); echo esc_html($hidden_parent_class); ?>"
	<?php echo wp_kses_data(implode(' ',$field['custom_attributes']));?> value="<?php echo esc_attr($value); ?>" <?php if ($field['checked'] === true) echo esc_html__("checked=checked",'iflair-woo-product-filters'); ?> <?php echo esc_html($disabeled); ?>>
<?php
$hidden_disabeled = ( empty( $disabeled ) && $field['checked'] === true ) ? 'disabled="disabled"' : $disabeled;
?>
<input type="hidden" name="<?php echo esc_attr($field['name_attribute']); ?>" id="<?php echo esc_attr( $field['id'] ).'_hidden'; ?>"
<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> value="<?php echo esc_attr($field['hidden_value']); ?>" <?php echo esc_html($hidden_disabeled); ?>>
<?php if ( !empty( $field['label'] ) ) { ?>
	<label class="ifwoopf-checkbox-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html($field['label']) ?></label>
<?php } ?>
<?php echo esc_html($field['description']); ?>