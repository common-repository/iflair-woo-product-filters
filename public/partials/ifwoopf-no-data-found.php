<?php

/**
 * This file is used to markup the no products found.
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
?>
<li class="product">
	<p class="ifwoopf-no-found"><?php echo esc_html__('No products founds', 'iflair-woo-product-filters'); ?></p>
</li>