<?php
/**
 * This file is used to markup the filter form.
 *
 * @since      1.0
 *
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$display_filter_heading = ifwoopf_get_option('display_filter_heading');

if( $display_filter_heading === 'yes' ){
	$filter_heading = trim( ifwoopf_get_option('filter_heading') );
}
?>
<?php if (isset($fields) && !empty( $fields ) ) { ?>
	<div class="ifwoopf-wrap">
		<div class="ifwoopf-mobile-overlay"></div>
		<div class="ifwoopf-mobile-wrap">
			<button class="ifwoopf-mobile-btn" type="button"><?php echo !empty( $filter_heading ) ? esc_html($filter_heading) : esc_html__('Filter','iflair-woo-product-filters'); ?></button>
		</div>
		<div class="ifwoopf-form-wrap">
			<div class="ifwoopf-form-header">
				<?php if( $display_filter_heading === 'yes' ) { ?>
					<h2 class="ifwoopf-form-heading"><?php echo !empty( $filter_heading ) ? esc_html($filter_heading) : esc_html__('Filter', 'iflair-woo-product-filters'); ?></h2>
				<?php } ?>
				<a href="#" class="ifwoopf-mobile-close"><?php echo esc_html__('x','iflair-woo-product-filters');?></a>
			</div>
			<form class="ifwoopf-from" method="post">
				<?php foreach ($fields as $f_key => $field) { ?>
					<?php
					$filter_type = $field['type'];
					$file_name = sprintf("ifwoopf-type-%s.php", $filter_type);

					if( isset( $field['value'] ) && !empty( $field['value'] ) ){
						$value = $field['value'];
						$types_agrgs = array($filter_type=>$value);
					}else{
						$types_agrgs = array();
					}
					
					if( $filter_type === 'buttons' ) {
						$types_agrgs['fields'] = $fields;
					}

					?>
					
					<?php ifwoopf_get_template('filter-types/'.$file_name, $types_agrgs); ?>
					
				<?php } ?>
			</form>
		</div>
	</div>
	<p class="woocommerce-result-count-display"></p>
<?php } ?>