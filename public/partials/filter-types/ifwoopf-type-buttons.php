<?php
/**
 * This file is used to markup the filter type buttons.
 *
 * @since      1.0
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public/partials/filter-types
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_keys = wp_list_pluck($fields, 'type');
$is_enabled_buttons = ifwoopf_get_option('filter_buttons_display');
$is_enabled_filter_button = ifwoopf_get_option('filter_button_display');
$is_enabled_reset_button = ifwoopf_get_option('reset_filter_button_display');

if( $is_enabled_buttons && $is_enabled_filter_button ){
	$filter_button_text = esc_html( trim( ifwoopf_get_option('filter_button_text') ) );
	$submit_name = ( $filter_button_text ) ? sprintf( esc_html__('%s', 'iflair-woo-product-filters'), $filter_button_text): esc_html__('Filter', 'iflair-woo-product-filters');
}

if( $is_enabled_buttons && $is_enabled_reset_button ){
	$reset_filter_button_text = esc_html( trim( ifwoopf_get_option('reset_filter_button_text') ) );
	$reset_filter_button_text = ( $reset_filter_button_text ) ? sprintf( esc_html__('%s', 'iflair-woo-product-filters'), $reset_filter_button_text ) : esc_html__('Reset', 'iflair-woo-product-filters');
}

$not_allowed = array('sorting', 'search');

?>

<?php
$match = array_intersect($not_allowed, $field_keys);
?>
<?php if( $is_enabled_buttons === 'yes' ) { ?>
	<div class="ifwoopf-submit">
	<?php if ( $is_enabled_filter_button === 'yes' && ( ( ( count( $fields ) === 1 && empty( $match ) ) || ( count( $match ) < count( $fields ) ) ) || count( $fields ) > count( $not_allowed ) ) ) { ?>
			<button type="submit" name="ifwoopf_submit" class="ifwoopf-btn ifwoopf-submit" id="ifwoopf_submit"><?php echo esc_attr($submit_name); ?></button>
	<?php } ?>
	<?php if( $is_enabled_reset_button === 'yes' ) { ?>
		<button type="button" class="ifwoopf-btn ifwoopf-reset" id="ifwoopf_reset"><?php echo esc_attr($reset_filter_button_text); ?></button>
	<?php } ?>
	</div>
<?php } ?>