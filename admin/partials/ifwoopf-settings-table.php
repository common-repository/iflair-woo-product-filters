<?php
/**
 * This file is used to markup the settings in table.
 *
 * @since      1.1
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if( isset($settings) && !empty( $settings ) ) { ?>
	<table class="form-table" role="presentation">
		<tbody>
			<?php foreach ($settings as $f_key => $field) { ?>
				<?php
				ifwoopf_get_admin_field_markup($field);
				?>
			<?php } ?>
		</tbody>
	</table>
<?php } ?>