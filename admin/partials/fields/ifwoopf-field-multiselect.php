<?php
/**
 * This file is used to markup the setting field multiselect dropdown.
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
//$disabeled = ( isset( $field['disabled'] ) && $field['disabled'] === true ) ? 'disabled="disabled"' : "";
?>
<select name="<?php echo esc_attr($field['name_attribute']); ?>"
	id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?> multiple="multiple" <?php //echo esc_attr($disabeled); ?>>
	<?php
	foreach ( $field['options'] as $key => $val ) {
		?>
		<option value="<?php echo esc_attr( $key ); ?>"
			<?php
			if ( is_array( $field['field_value'] ) ) {
				selected( in_array( (string) $key, $field['field_value'], true ), true );
			} else {
				selected( $field['field_value'], (string) $key );
			}

			?>
		><?php echo esc_html( $val ); ?></option>
		<?php
	}
	?>
</select> 
<?php echo esc_html($field['description']); ?>	