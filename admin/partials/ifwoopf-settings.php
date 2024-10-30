<?php 
/**
 * This file is used to markup the plugin settings.
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
global $ifwoopf_settings;
$ifwoopf_settings = get_option(IFWOOPF_PREFIX.'_settings');
$default_tab = '';
?>
<div class="wrap">

	<h1><?php echo esc_html__('WooCommerce Product Filters Settings', 'iflair-woo-product-filters'); ?></h1>

	<?php ifwoopf_settings_notice(); ?>

	<form method="post" id="ifwoopf-settings">

		<div class="ifwoopf-tab-wrap">
			<ul class="ifwoopf-tabs-nav">
				<?php foreach ($ifwoops_tabs as $tab_key => $tab) { ?>
					<?php
					if( isset( $tab['default'] ) && $tab['default'] === true ){
						$default_tab = $tab_key;
					}

					$tab_li_class = ( isset( $tab['default'] ) && $tab['default'] === true ) ? sprintf("ifwoopf-tab-active-li ifwoopf-tab-%s", $tab['id']) : sprintf("ifwoopf-tab-%s", $tab['id']);
					?>
			        <li class="ifwoopf-tab <?php echo esc_attr($tab_li_class); ?>">
			        	<a href="javascript:void(0);" data-tag="<?php echo esc_attr($tab['id']); ?>" class="ifwoopf-tab-btn"><?php echo esc_html($tab['label']); ?></a>
			        </li>
			    <?php } ?>
		    </ul>    
		    <div style="clear: both;"></div>    
		    <div class="ifwoopf-tab-container">
		    	<?php foreach ($ifwoops_tabs as $tab_key => $tab) { ?>
					<?php
					$tab_content_class = ( isset( $tab['default'] ) && $tab['default'] === true ) ? sprintf("ifwoopf-tab-active-ifwoopf-tab-content ifwoopf-tab-%s", $tab['id']) : sprintf("ifwoopf-tab-content-hide ifwoopf-tab-%s", $tab['id']);
					?>
					<div class="ifwoopf-tab-content <?php echo esc_attr($tab_content_class); ?>" id="<?php echo esc_attr($tab['id']); ?>">					
						<?php if( isset( $tab['heading'] ) && !empty( $tab['heading'] ) ) { ?>
							<h2><?php echo esc_html($tab['heading']); ?></h2>
						<?php } ?>
						<?php call_user_func($tab['callback']); ?>				
					</div>
			    <?php } ?>
		    </div> 
		</div> 
		<?php $woo_save_changes_val = 'Save Changes'; ?>
		<input type="hidden" name="ifwoopf_currnet_tab" id="ifwoopf_currnet_tab" value="<?php echo esc_attr($default_tab);?>">
		<?php wp_nonce_field('ifwoo_action_nonce', 'ifwoo_nonce');?>
		<div class="submit">
			<button name="ifwoopf_save_settings" class="button-primary ifwoopf-save-button" type="submit" value="<?php echo esc_attr($woo_save_changes_val); ?>"><?php echo esc_html__('Apply Changes', 'iflair-woo-product-filters'); ?></button>
		</div>
	</form>
</div>