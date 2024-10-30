<?php
/**
 * This file is used to markup the plugin settings modal popup.
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
$popup_id = "ifwoopf_popup_".$setting['id'];
?>
<div id="<?php echo esc_attr($popup_id); ?>" class="ifwoopf-popup-main ifwoopf-popup-overlay">
	<div class="ifwoopf-popup">
		<div class="ifwoopf-popup-header">
			<?php if(!empty($popup_heading)){ ?>
				<h2><?php echo esc_html($popup_heading); ?></h2>
			<?php } ?>
			<span class="ifwoopf-popup-close dashicons dashicons-dismiss"></span>
		</div>
		<div class="ifwoopf-popup-content">
			<table class="form-table" role="presentation">
				<tbody>
					<?php if( !empty( $fields ) ) { ?>
						<?php foreach ($fields as $f_key => $field) { ?>
							<?php
							ifwoopf_get_admin_field_markup($field);
							?>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="ifwoopf-popup-footer">
			<button type="button" class="button-primary ifwoopf-popup-close"><?php echo esc_html__('Save Changes', 'iflair-woo-product-filters'); ?></button>
			<button type="button" class="button-primary ifwoopf-popup-reset"><?php echo esc_html__('Reset', 'iflair-woo-product-filters'); ?></button>
		</div>
	</div>
</div>