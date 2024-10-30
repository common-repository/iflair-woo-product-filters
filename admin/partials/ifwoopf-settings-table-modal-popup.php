<?php
/**
 * This file is used to markup the plugin settings in table with modal popup.
 *
 * @since      1.0
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( !empty( $modal_popup_settings ) ) { ?>
<table class="ifwoopf-table-modal ifwoopf-sortable-table">
	<thead>
		<th><?php echo esc_html__('Position', 'iflair-woo-product-filters'); ?></th>
		<th><?php echo esc_html__('Hide/Show', 'iflair-woo-product-filters'); ?></th>
		<th><?php echo esc_html__('Name', 'iflair-woo-product-filters'); ?></th>
		<th><?php echo esc_html__('Type', 'iflair-woo-product-filters'); ?></th>
		<th><?php echo esc_html__('View/Edit', 'iflair-woo-product-filters'); ?></th>
	</thead>
	<tbody>
		<?php foreach ($modal_popup_settings as $se_key => $setting) { ?>
			<?php
			$classes = isset( $setting['class'] ) ? $setting['class'] . ' ' : '';
			$sortable = isset( $setting['sortable'] ) ? $setting['sortable'] : false;
			$sortable_class = $sortable ? 'ifwoopf-row-sortable' : '';
			$setting_id = $setting['id'];
			$fields = isset( $setting['fields'] ) ? $setting['fields'] : array();
			$name = isset( $setting['name'] ) && !empty( $setting['name'] ) ? $setting['name'] : "-";
			$type = isset( $setting['type'] ) && !empty( $setting['type'] ) ? $setting['type'] : "-";

			if ( isset( $setting['field'] ) && !empty( $setting['field'] ) ) {
				$classes .= 'ifwoopf-check-popup-field ';
			}

			?>
			<tr class="<?php echo esc_attr( $classes . $sortable_class ); ?>">
				<td>
					<?php
					if ( !empty($sortable) ) {
						echo wp_kses_post('<span class="ui-icon ui-icon-arrowthick-2-n-s sorting-handle"></span>');
					} else {
						echo esc_html__('-', 'iflair-woo-product-filters');
					}
					?>
				</td>
				<td>
					<?php
					if ( isset( $setting['field'] ) && !empty( $setting['field'] ) ) {
						$field = $setting['field'];
						$field['class'] = isset( $field['class'] ) ? $field['class'] . 'ifwoopf-enable-popup ' : 'ifwoopf-enable-popup ';
						ifwoopf_get_admin_field_markup( $field );
					} else {
						echo esc_html__('-', 'iflair-woo-product-filters');
					}
					?>
				</td>
				<td><?php echo esc_html( $name ); ?></td>
				<td><?php echo esc_html( $type ); ?></td>
				<td>
					<?php 
					if (!isset($defaults)) { $defaults = array(); }

					if ( isset( $setting['popup'] ) && $setting['popup'] === true ) { ?>
						<a href="#" class="ifwoopf-table-modal-open" data-id="<?php echo esc_attr( $setting_id ); ?>"><?php echo esc_html__('View/Edit', 'iflair-woo-product-filters'); ?></a>
						<?php if ( !empty( $fields ) ) { ?>
							<?php 
							$popup_args = array(
								'setting' => $setting,
								'fields' => $fields,
								'popup_heading' => $name,
								'defaults' => $defaults,
							);
							ifwoopf_get_admin_template('ifwoopf-settings-popup.php', $popup_args);
							?>
						<?php } ?>
					<?php } else { ?>
						<?php echo esc_html__('-', 'iflair-woo-product-filters'); ?>	
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>