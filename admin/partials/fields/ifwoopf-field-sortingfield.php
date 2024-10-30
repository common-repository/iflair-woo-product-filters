<?php
/**
 * This file is used to markup the setting field sorting fields.
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
<div id="<?php echo esc_attr( $field['id'] ); ?>"
	style="<?php echo esc_attr( $field['css'] ); ?>"
	class="ifwoopf-sorting-fields-wrap <?php echo esc_attr( $field['class'] ); ?>"
	<?php echo wp_kses_data(implode( ' ', $field['custom_attributes'] )); ?>>
	<?php if( isset( $field['title'] ) && !empty( $field['title'] ) ) { ?>
		<label class="ifwoopf-field-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
	<?php } ?>
	</th>
	<?php if ( isset( $field['fields'] ) && !empty( $field['fields'] ) ){ ?>
		<ul class="ifwoopf-sorting-field-parent">
		<?php foreach ($field['fields'] as $fkey => $field) { ?>
			<?php if( isset( $field[0]['type'] ) ) { ?>
				<li class="ui-state-default ifwoopf-multiple-field-child">
					<span class="ui-icon ui-icon-arrowthick-2-n-s sorting-handle"></span>
					<?php foreach ($field as $f_two_key => $field_two) { ?>
						<?php ifwoopf_get_admin_field_markup($field_two); ?>
					<?php } ?>
				</li>
			<?php }else{ ?>
				<?php ifwoopf_get_admin_field_markup($field); ?>
			<?php } ?>	
		<?php } ?>
		</ul>
	<?php } ?>
	<?php if ( isset( $field['description'] ) ) {
		echo esc_html( $field['description'] );
	} ?>
</div>