<?php
/**
 * This file is used to markup the setting field button.
 *
 * @since      1.0
 *
 * @package    iFlair_WooCommerce_Product_Filters
 * @subpackage iFlair_WooCommerce_Product_Filters/admin/partials/fields
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

$disabeled = ( isset( $field['disabled'] ) && $field['disabled'] === true ) ? 'disabled="disabled"' : "";
?>

<?php if ( isset( $field['button_type'] ) && $field['button_type'] === 'button' ) { ?>
<button type="button"
	id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data( implode( ' ', $field['custom_attributes'] ) ); ?> value="<?php echo esc_attr($value); ?>" <?php echo esc_attr($disabeled); ?>><?php echo esc_html($field['label']); ?></button>
<?php } else { ?>
	<a href="<?php echo esc_url( $field['href'] ); ?>"
	id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="<?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data( implode( ' ', $field['custom_attributes'] ) ); ?> <?php echo esc_attr($disabeled); ?>><?php echo esc_html($field['label']); ?></a>
<?php } ?>