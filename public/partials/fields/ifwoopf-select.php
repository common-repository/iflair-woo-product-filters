<?php
/**
 * This file is used to markup the filter field select.
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
<select name="<?php echo esc_attr($field['name']); ?>"
	id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> <?php echo esc_html($disabeled); ?>>
	<option value="0" selected="selected">Choose Option</option>
	<?php if( isset( $field['select2'] ) && $field['select2'] === true && !empty( $field['placeholder'] ) ){ ?>
		<option></option>
	<?php }elseif ( !empty( $field['placeholder'] ) ) { ?>
		<option><?php echo esc_html($field['placeholder']); ?></option>
	<?php } ?>
	<?php
	foreach ( $field['options'] as $key => $val ) {
		?>
		<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr(( $field['value'] === $key ) ? 'selected="selected"' : ''); ?>><?php echo esc_html( $val ); ?></option>
		<?php
	}
	?>
</select> 