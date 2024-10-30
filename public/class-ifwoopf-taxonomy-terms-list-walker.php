<?php
/**
 * This class manage listing of taxonomy terms.
 *
 * @since 1.0 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class IFwoopf_Taxonomy_Terms_List_Walker extends Walker {

	/**
	 * What the class handles.
	 * 
	 * @since 1.0
	 * 
	 * @var string $tree_type
	 */
	public $tree_type = 'product_cat';

	/**
	 * DB fields to use.
	 *
	 * @since 1.0
	 * 
	 * @var array $db_fields
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 1.0
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$field_type = $args['field_type'];

		if( $field_type !== 'dropdown' ){
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent<ul class='ifwoopf-term-children'>\n";
		}

		
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 1.0
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$field_type = $args['field_type'];

		if( $field_type !== 'dropdown' ){
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}
	}

	/**
	 * Start the element output.
	 *
	 * @since 1.0
	 * @see Walker::start_el()
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

		$cat_id = intval( $cat->term_id );
		$cat_slug = $cat->slug;
		$field_args = $args['field_args'];
		$field_type = $args['field_type'];
		$taxonomy_name = $field_args['taxonomy_name'];
		$all_parents = ( isset( $args['all_parents'] ) ) ? $args['all_parents'] : array();
		$is_current_que_tax = false;
		$tax_query = ( isset( $args['tax_query'] ) && !empty( $args['tax_query'] ) ) ? $args['tax_query'] : array();

		if( !empty( $tax_query ) ){

			$get_all_children = get_term_children($cat_id, $taxonomy_name);
			$match = array_intersect($tax_query,$get_all_children);

			if( ( in_array($cat_id, $tax_query) || !empty( $match ) ) && $args['has_children'] ){
				$is_current_que_tax = true;
			}

		}

		$args['field_args']['is_current_que_tax'] = $is_current_que_tax;

		$li_toggled_class = ( isset($field_args['toggle']) && $field_args['toggle'] && ( in_array($cat_slug, $all_parents) || $is_current_que_tax ) ) ? 'ifwoopf-toggled' : '';


		if( !empty( $li_toggled_class ) && $cat->parent === 0 ){
			$li_toggled_class = $li_toggled_class . ' ifwoopf-current-toggle active-ifwoopf-term-parent';
		} 
		
		if( $field_type !== 'dropdown' ){
			
			$output .= sprintf('<li class="ifwoopf-term-item ifwoopf-term-item-%1$s %2$s', $cat_id, $li_toggled_class);

			if ( $args['current_category'] === $cat_id ) {
				$output .= ' current-ifwoopf-term';
			}

			if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
				$output .= ' ifwoopf-term-parent';
			}

			if (isset($args['current_category_ancestors']) && isset($args['current_category']) && in_array($cat_id, $args['current_category_ancestors'], true)) {
				$output .= ' current-ifwoopf-term-parent';
			}

			$field_markup = $this->ifwoopf_get_field_markup($output, $cat, $depth, $args, $current_object_id);
			$output .= sprintf('">%s', $field_markup);

			if ( $args['show_count'] ) {
				$output .= sprintf(' <span class="ifwoopf-term-count">(%s)</span>', $cat->count);
			}

			$output = apply_filters('ifwoopf_after_taxonomy_field_markup', $output, $cat, $depth, $args, $current_object_id);

		}else if ( $field_type === 'dropdown' ) {

			$output .= '<option';

			$selected =  ( $args['current_category'] === $cat_id || $field_args['value'] == $cat_id ) ? 'selected="selected"' : '';

			$output .= sprintf(' value="%1$s" %2$s>', esc_attr($cat_id), esc_html($selected));

			if ( $cat->parent !== 0 ){
				$output .= str_repeat('- ', $depth);
			}

			$output .= $cat->name;

			if ( $args['show_count'] ) {
				$output .= sprintf(' (%s)', $cat->count);
			}

			$output .= '</option>';

		}	
	}

	/**
	 * Get term field output
	 * 
	 * @since 1.0
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 * @return html html of field.
	 */
	public function ifwoopf_get_field_markup($output, $cat, $depth, $args, $current_object_id){

		$field_type = $args['field_type'];
		$field_args = $args['field_args'];
		$field_args['label'] = $cat->name;
		$field_args['value_attribute'] = $cat->term_id;
		$field_args['type'] = $field_type;

		if( isset( $field_args['toggle'] ) && $field_args['toggle'] === true ){
			if( !isset( $args['has_children'] ) || !$args['has_children'] ){
				unset($field_args['toggle']);
			}
		}
		
		ob_start();
		?>
		<?php ifwoopf_get_front_field_markup($field_args); ?>
		<?php
		return ob_get_clean();

	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 1.0
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $cat    Category.
	 * @param int    $depth  Depth of category. Not used.
	 * @param array  $args   Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$field_type = $args['field_type'];

		if( $field_type !== 'dropdown' ){
			$output .= "</li>\n";
		}elseif ( $field_type === 'dropdown' ) {
			$output .= "</option>\n";
		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max.
	 * depth and no ignore elements under that depth. It is possible to set the.
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 1.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}