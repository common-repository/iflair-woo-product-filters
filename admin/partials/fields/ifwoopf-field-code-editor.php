<?php
/**
 * This file is used to markup the setting code editor.
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
<div class="ifwoopf-code-editor-wrap">
	<pre id="<?php echo esc_attr( $field['id'] ); ?>"
		style="<?php echo esc_attr( $field['css'] ); ?>"
		class="ifwoopf-code-editor <?php echo esc_attr( $field['class'] ); ?>" data-editor-language="<?php echo esc_attr( $field['editor_language'] ); ?>" <?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?>><?php echo esc_html(stripcslashes( $field['field_value'] )); ?></pre>
		<input type="hidden" name="<?php echo esc_attr($field['name_attribute']); ?>" id="hidden_input_<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( stripcslashes( $field['field_value'] ) ); ?>">
</div>