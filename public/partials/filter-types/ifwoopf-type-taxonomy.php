<?php
/**
 * This file is used to markup the filter type taxonomy.
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
?>
<?php if ( isset( $taxonomy ) && !empty( $taxonomy ) ) { ?>	
	<?php
		$taxonomy_name = $taxonomy;
		$tax_settings = ifwoopf_get_option("", false,array("terms_display_settings", $taxonomy_name));
		$taxonomy_details = get_taxonomy( $taxonomy_name );
		$singular_name = ( is_object( $taxonomy_details ) && isset( $taxonomy_details->labels->singular_name ) && !empty( $taxonomy_details->labels->singular_name ) ) ? esc_html__("Select ", 'iflair-woo-product-filters') . $taxonomy_details->labels->singular_name : "";
		$taxonomy_label = ( is_object( $taxonomy_details ) && isset( $taxonomy_details->label ) ) ? $taxonomy_details->label : "";
		$taxonomy_hierarchical = ( isset( $taxonomy_details->hierarchical ) ) ? $taxonomy_details->hierarchical : false;

		$display_heading = ( isset( $tax_settings['display_heading'] ) && !empty( $tax_settings['display_heading'] ) ) ? $tax_settings['display_heading'] : "";
		$tax_heading_text = ( isset( $tax_settings['heading'] ) && !empty( trim($tax_settings['heading']) ) ) ? $tax_settings['heading'] : $taxonomy_label;
		$taxonomy_field_toggle = ( isset( $tax_settings['toggle_btn'] ) && $tax_settings['toggle_btn'] === 'yes' ) ? true : false;
		$display_order = ( isset( $tax_settings['display_order'] ) && !empty( $tax_settings['display_order'] ) ) ? $tax_settings['display_order'] : "";
		$show_count = ( isset( $tax_settings['product_count'] ) && $tax_settings['product_count'] == 'yes' ) ? true : false;
		$hierarchical = ( $display_order === 'hierarchical' ) ? true : false;
		$hide_empty = ( isset( $tax_settings['hide_empty'] ) && $tax_settings['hide_empty'] == 'yes' ) ? true : false;
		$field_type = ( isset( $tax_settings['selection_type'] ) && !empty( $tax_settings['selection_type'] ) ) ? $tax_settings['selection_type'] : "checkbox";
		$classes = ( isset( $tax_settings['classes'] ) && !empty( $tax_settings['classes'] ) ) ? $tax_settings['classes'] : "";
		$dropdown_placeholder = ( isset( $tax_settings['dropdown_placeholder'] ) && !empty( trim($tax_settings['dropdown_placeholder']) ) ) ? $tax_settings['dropdown_placeholder'] : "";
		$toggle = ( isset( $tax_settings['toggle'] ) && !empty( $tax_settings['toggle'] ) ) ? $tax_settings['toggle'] : "";
		$default_toggle = ( isset( $tax_settings['default_toggle'] ) && !empty( $tax_settings['default_toggle'] ) ) ? $tax_settings['default_toggle'] : "show";

		$query_args = ifwoopf_pars_query_string_args();
		$arg_key = "taxonomy_".$taxonomy_name;
		$tax_query = array();

		$list_args          = array(
			'show_count'   => $show_count,
			'hierarchical' => $hierarchical,
			'taxonomy'     => $taxonomy_name,
			'hide_empty'   => $hide_empty,
		);

		if( isset( $query_args[$arg_key] ) ){
			$tax_query = explode(",", $query_args[$arg_key]);
			$list_args['tax_query'] = $tax_query;
		}

		if( isset( $tax_settings['include_terms'] ) && !empty( $tax_settings['include_terms'] ) ){

			$list_args['include'] = $tax_settings['include_terms'];

		}elseif ( isset( $tax_settings['exclude_terms'] ) && !empty( $tax_settings['exclude_terms'] ) ) {

			$list_args['exclude'] = $tax_settings['exclude_terms'];

		}

		if ( $display_order === 'name' ){
			$list_args['orderby'] = 'name';
		}
		
		$terms = get_terms( $list_args );
		
		if( $hierarchical && !empty( $terms ) ){
			$term_ids = wp_list_pluck($terms, "term_id");
			foreach ($terms as $t_key => $term) {
				if( $term->parent !== 0 && !in_array($term->parent, $term_ids) ){
					unset($terms[$t_key]);
				}
			}
		}
		
		$field_name = sprintf('taxonomy[%s][]', $taxonomy_name);
		$field_args = array(
			'name' => $field_name,
			'taxonomy_name' => $taxonomy_name,
		);

		if( $toggle === 'yes' && $display_order == 'hierarchical' && $field_type !== 'dropdown' ){
			$field_args['toggle'] = true;
			$field_args['default_toggle'] = $default_toggle;
		}

		if( is_tax() ){
			$term = get_queried_object();
			$term_id = $term->term_id;
			$current_taxonomy = $term->taxonomy;
			$list_args['current_term_id'] = $term_id;
			$get_parents_str = get_term_parents_list($term_id, $current_taxonomy , array('format'=> 'slug', 'link' => false, 'separator' => ','));
			$all_parents = explode(",", $get_parents_str);
			$all_parents = array_filter($all_parents);
			$list_args['all_parents'] = $all_parents;
			$field_args['value'] = $term_id;
		}

		$div_id = sprintf('ifwoopf_type_taxonomy_%s', $taxonomy_name);
		$ul_id = sprintf('ifwoopf_taxonomy_%s', $taxonomy_name);

		if( $taxonomy_field_toggle && $display_heading == 'yes' ){
			$classes = $classes . ' ifwoopf-toggle-enabeled';
		}

		$is_field_toggled = ( $taxonomy_field_toggle && ( $default_toggle === 'show' || ( is_tax() && isset( $current_taxonomy ) && $taxonomy_name === $current_taxonomy ) || !empty( $tax_query ) ) );
		
		if( $is_field_toggled ){
			$classes = $classes . ' ifwoopf-type-toggled';
		}

		$toggled_btn_class = ( $is_field_toggled ) ? "ifwoopf-type-btn-toggled" : "";

		?>
		<?php if ( isset($terms) && !empty( $terms ) ) { ?>
			<div class="ifwoopf-type ifwoopf-type-taxonomy <?php echo esc_attr($div_id); ?> <?php echo esc_attr($classes); ?>" id="<?php echo esc_attr($div_id); ?>">
				<?php if( $display_heading == 'yes' && !empty( $tax_heading_text ) ){ ?>
					<div class="ifwoopf-type-heading">
						<label><?php echo esc_html($tax_heading_text); ?></label>
						<?php if( !empty($taxonomy_field_toggle) ) { ?>
							<i class="ifwoopf-toggle-field-btn fa-solid fa-circle-plus <?php echo esc_attr($toggled_btn_class); ?>"></i>
						<?php } ?> 
					</div>
				<?php } ?>
				<div class="ifwoopf-field-wrap">
					<?php 					
					include_once IFWOOPF_PUBLIC_DIR_PATH . 'class-ifwoopf-taxonomy-terms-list-walker.php';

					$list_args['walker']                     = new IFwoopf_Taxonomy_Terms_List_Walker();
					$list_args['title_li']                   = '';
					$list_args['show_option_none']           = sprintf(
						esc_html__( 'No %s exist.', 'iflair-woo-product-filters' ),
						$taxonomy_label,
					);
					$list_args['field_type'] = $field_type;
					$list_args['field_args'] = $field_args;
					$toggle_class = ( isset( $field_args['toggle'] ) && $field_args['toggle'] === true ) ? " ifwoopf-toggle-enabeled" : "";
					
					if( is_tax() ){
						$toggle_class = $toggle_class . ' taxonomy-page';
					}

					if ( $field_type !== 'dropdown' ){
						printf('<ul class="ifwoopf-taxonomy %1$s%2$s">', $ul_id, $toggle_class);
					}elseif ( $field_type === 'dropdown' ) {
						$option_placeholder = ( !empty( $singular_name ) ) ? $singular_name : esc_html__('Select Option', 'iflair-woo-product-filters');
						if( !empty( $dropdown_placeholder ) ){
							$option_placeholder = $dropdown_placeholder;
						}
						$field_args['type'] = 'select';
						$field_args = ifwoopf_set_value_to_field($field_args);
						$list_args['field_args'] = $field_args;
						?>
						<div class="ifwoopf-field ifwoopf-field-select">
							<select name="<?php echo esc_attr($field_args['name']); ?>">
								<option value=""><?php echo esc_html($option_placeholder); ?></option>
						<?php
					}

						wp_list_categories( $list_args );

					if ( $field_type !== 'dropdown' ){
						echo '</ul>';
					}elseif ( $field_type === 'dropdown' ) {
						?>
							</select>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		<?php } ?>	
<?php } ?>