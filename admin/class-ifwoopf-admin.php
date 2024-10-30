<?php
/**
 * This class manage all admin functionality.
 *
 * @since 1.0 
 * 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/admin 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}     

require_once( IFWOOPF_ADMIN_DIR_PATH . 'ifwoopf-admin-functions.php');

class Iflair_Woo_Product_Filters_Admin {

	/**
	 * Allowed sccren to load plugin admin scripts
	 *
	 * @since 1.0
	 * 
	 * @var array $allowed_screens
	 */
	public $allowed_screens;

	/**
	 * All modal settings
	 *
	 * @since 1.0
	 * 
	 * @var array $modal_popup_settings
	 */
	public $modal_popup_settings;

	public function __construct(){
		$this->allowed_screens = array(
			'toplevel_page_ifwoopf-settings'
		);
		$this->modal_popup_settings = array(); 
	}

	/**
	 * Add custom plugin setting page
	 * 
	 * @since 1.0
	 */
	public function ifwoopf_admin_page(){

		add_menu_page(
	        esc_html__( 'IFWOOPF Settings', 'iflair-woo-product-filters' ),
	        'Woo Product Filter Settings',
	        'manage_options',
	        'ifwoopf-settings',
	        array($this, 'ifwoopf_admin_callback'),
	        'dashicons-filter',
	    );

	}

	/**
	 * Register the style for the admin side.
	 * 
	 * @since 1.0
	 * 
	 * @param  string $hook_suffix Current screen id
	 */
	public function ifwoopf_enqueue_styles($hook_suffix) {

		$prefix = IFWOOPF_PREFIX;

		wp_register_style( $prefix.'-select2-style', IFWOOPF_ASSETS_DIR_URL . 'css/select2.min.css');
		wp_register_style( $prefix.'-jquery-ui-style', IFWOOPF_ASSETS_DIR_URL . 'css/jquery-ui.min.css');
		wp_register_style( $prefix.'-jquery-ui-theme-style', IFWOOPF_ASSETS_DIR_URL . 'css/jquery-ui.theme.min.css');
		wp_register_style( $prefix.'-admin-style', IFWOOPF_ADMIN_DIR_URL . 'css/ifwoopf-admin.css');

		if ( in_array($hook_suffix, $this->allowed_screens) ) {
			wp_enqueue_style($prefix.'-select2-style');
			wp_enqueue_style($prefix.'-jquery-ui-style');
			wp_enqueue_style($prefix.'-jquery-ui-theme-style');
			wp_enqueue_style($prefix.'-admin-style');
		}

	}

	/**
	 * Register the JavaScript for the admin side.
	 *
	 * @since 1.0
	 * 
	 * @param  string $hook_suffix Current screen id
	 */
	public function ifwoopf_enqueue_scripts($hook_suffix) {

		$prefix = IFWOOPF_PREFIX;

		$ifwoopf_settings = ifwoopf_get_option();
		$ifwoopf_default_settings = ifwoopf_default_settings();
		$select2_object = $this->ifwoopf_get_global_select2_settings();
		$select2_objects = $this->ifwoopf_get_select2_settings();
		$default_sorting_option = ifwoopf_get_sorting_types(true);

		$ifwoopf_admin_obj = array(
			'ifwoopf_default_settings' => $ifwoopf_default_settings,
			'select2_object' => $select2_object,
			'select2_objects' => $select2_objects,
			'select2_placeholder' => esc_html__('Select Option', 'iflair-woo-product-filters'),
			'default_sorting_option' => $default_sorting_option,
		);

		wp_register_script( $prefix.'-select2-script', IFWOOPF_ASSETS_DIR_URL . 'js/select2.min.js', array( 'jquery' ), IFWOOPF_VERSION );
		wp_register_script( $prefix.'-admin-script', IFWOOPF_ADMIN_DIR_URL . 'js/ifwoopf-admin.js', array( 'jquery' ), IFWOOPF_VERSION );

		if ( in_array($hook_suffix, $this->allowed_screens) ) {

			wp_enqueue_script($prefix.'-select2-script');
			wp_enqueue_script($prefix.'-admin-script');
			wp_localize_script($prefix.'-admin-script', 'ifwoopf_admin_obj',$ifwoopf_admin_obj);
			wp_enqueue_script('jquery-ui-sortable');
			
		}


	}

	/**
	 * Callback funtion for admin page
	 *
	 * @since 1.0
	 */
	public function ifwoopf_admin_callback(){

		global $defaults;
		$defaults = $this->ifwoopf_get_settings_fields_defaults();
		$ifwoops_tabs = $this->ifwoopf_get_settings_tabs();

		require_once( IFWOOPF_ADMIN_DIR_PATH . 'partials/ifwoopf-settings.php' );

	}

	/**
	 * Get all modal settings fields
	 *
	 * @since 1.0
	 * 
	 * @return   array $settings The settings array
	 */
	public function ifwoopf_get_modal_popup_settings(){

		global $ifwoopf_default_settings;

		$ifwoopf_settings = ifwoopf_get_option();
		$sorting_multiple_fields = $this->ifwoopf_get_sorting_setting_fields();
		$default_sorting_options = ifwoopf_get_enabled_sorting_types();
		$disabled_default_sorting = ( empty( $default_sorting_options ) ) ? true : false;
		if( empty( $default_sorting_options ) ){
			$default_sorting_options = ifwoopf_get_sorting_types(true);
		}
		
		$drag_settings = array(
			array(
				'id' => 'sorting_display',
				'field' => array(
					'id' => 'sorting_display',
					'type' => 'checkbox',
					'format' => 'simple',
				),
				'fields' => array(
					'display_sorting_heading' => array(
						'title' => esc_html__('Show heading', 'iflair-woo-product-filters'),
						'id' => 'display_sorting_heading',
						'name' => 'display_sorting_heading',
						'custom_attributes' => array(
							'data-name="display_sorting_heading"',
							'data-text-class="ifwoopf-sorting-heading"'
						),
						'type' => 'checkbox',
						'class' => 'ifwoopf-display-heading-check ifwoopf-display-sorting-heading ifwoopf-display-heading',
					),
					'sorting_heading' => array(
						'title' => esc_html__('Heading text', 'iflair-woo-product-filters'),
						'id' => 'sorting_heading',
						'type' => 'text',
						'class' => 'ifwoopf-sorting-heading',
						'placeholder' => esc_html__('Sorting', 'iflair-woo-product-filters'),
					),
					array(
						'title' => esc_html__('Section expand/collapse', 'iflair-woo-product-filters'),
						'id' => 'toggle_btn_sorting',
						'name' => 'toggle_btn_sorting',
						'class' => 'ifwoopf-field-toggle-check',
						'type' => 'checkbox',
					),
					array(
						'title' => esc_html__('Defalut Toggle', 'iflair-woo-product-filters'),
						'id' => 'default_toggle_sorting',
						'name' => 'default_toggle_sorting',
						'class' => 'ifwoopf-select2 ifwoopf-field-defalut-toggle',
						'type' => 'select',
						'options' => array(
							'show' => esc_html__('Show', 'iflair-woo-product-filters'),
							'hide' => esc_html__('Hide', 'iflair-woo-product-filters'),
						),
						'placeholder' => '',
					),
					'default_sorting' => array(
						'id' => 'default_sorting',
						'title' => esc_html__('Default sorting', 'iflair-woo-product-filters'),
						'type' => 'select',
						'class' => 'ifwoopf-default-sorting',
						'options' => $default_sorting_options,
						'placeholder' => '',
						'disabled' => $disabled_default_sorting,
					),
					'sorting_types' => array(
						'id' => 'sorting_types',
						'format' => 'simple',
						'title' => esc_html__('Sorting options', 'iflair-woo-product-filters'),
						'type' => 'sortingfield',
						'over_table' => true,
						'fields' => $sorting_multiple_fields,
					),
					'reset_sorting_order' => array(
						'id' => 'reset_sorting_order',
						'format' => 'simple',
						'label' => esc_html__('Reset Sorting Order', 'iflair-woo-product-filters'),
						'type' => 'button',
						'class' => 'button-primary ifwoopf-reset-sorting',
					),
				),
				'name' => esc_html__('Sorting dropdown', 'iflair-woo-product-filters'),
				'type' => esc_html__('Products order', 'iflair-woo-product-filters'),
				'popup' => true,
				'sortable' => true,
			),
			array(
				'id' => 'search_display',
				'field' => array(
					'id' => 'search_display',
					'type' => 'checkbox',
					'format' => 'simple',
					'class' => 'ifwoopf-check-search-display '
				),
				'name' => esc_html__('Search box', 'iflair-woo-product-filters'),
				'type' => esc_html__('Search', 'iflair-woo-product-filters'),
				'popup' => true,
				'sortable' => true,
				'fields' => array(
					'display_search_heading' => array(
						'title' => esc_html__('Show heading', 'iflair-woo-product-filters'),
						'id' => 'display_search_heading',
						'name' => 'display_search_heading',
						'custom_attributes' => array(
							'data-name="display_search_heading"',
							'data-text-class="ifwoopf-search-heading"'
						),
						'type' => 'checkbox',
						'class' => 'ifwoopf-display-heading-check ifwoopf-display-search-heading ifwoopf-display-heading',
					),
					'search_heading' => array(
						'title' => esc_html__('Heading text', 'iflair-woo-product-filters'),
						'id' => 'search_heading',
						'type' => 'text',
						'format' => 'tr',
						'class' => 'ifwoopf-search-heading ',
						'placeholder' => esc_html__('Search', 'iflair-woo-product-filters'),
					),
					array(
						'title' => esc_html__('Section expand/collapse', 'iflair-woo-product-filters'),
						'id' => 'toggle_btn_search',
						'name' => 'toggle_btn_search',
						'class' => 'ifwoopf-field-toggle-check',
						'type' => 'checkbox',
					),
					array(
						'title' => esc_html__('Default Toggle', 'iflair-woo-product-filters'),
						'id' => 'default_toggle_search',
						'name' => 'default_toggle_search',
						'class' => 'ifwoopf-select2 ifwoopf-field-defalut-toggle',
						'type' => 'select',
						'options' => array(
							'show' => esc_html__('Show', 'iflair-woo-product-filters'),
							'hide' => esc_html__('Hide', 'iflair-woo-product-filters'),
						),
						'placeholder' => '',
					),
					'search_placeholder' => array(
						'title' => esc_html__('Search placeholder text', 'iflair-woo-product-filters'),
						'id' => 'search_placeholder',
						'type' => 'text',
						'format' => 'tr',
					),
					'search_min_input' => array(
						'title' => esc_html__('Search min input', 'iflair-woo-product-filters'),
						'id' => 'search_min_input',
						'type' => 'number',
						'min' => '1',
						'placeholder' => esc_attr($ifwoopf_default_settings['search_min_input']),
					),
					'search_in_title' => array(
						'title' => esc_html__('Search in Title', 'iflair-woo-product-filters'),
						'id' => 'search_in_title',
						'type' => 'checkbox',
						'class' => 'ifwoopf-check-search-in-title ',
						'custom_attributes' => array(
							'data-name="search_in_title"',
						),
						'hide' => true,
						'format' => 'tr',
						'disabled' => true,
					),
				),
			),
		);

		$taxonomy_settings = $this->ifwoopf_get_taxonomies_settings();

		$last_drag_settings = array(
			array(
				'id' => 'filter_buttons_display',
				'field' => array(
					'id' => 'filter_buttons_display',
					'type' => 'checkbox',
					'format' => 'simple',
				),
				'name' => esc_html__('Buttons', 'iflair-woo-product-filters'),
				'type' => esc_html__('Filter', 'iflair-woo-product-filters'),
				'popup' => true,
				'sortable' => true,
				'fields' => array(
					'filter_button_display' => array(
						'title' => esc_html__('Show filter button', 'iflair-woo-product-filters'),
						'id' => 'filter_button_display',
						'name' => 'filter_button_display',
						'type' => 'checkbox',
						'custom_attributes' => array(
							'data-btn-text-field="filter_button_text"',
						),
						'class' => 'ifwoopf-display-button-check',
					),
					'filter_button_text' => array(
						'title' => esc_html__('Filter button text', 'iflair-woo-product-filters'),
						'id' => 'filter_button_text',
						'type' => 'text',
						'format' => 'tr',
						'placeholder' => esc_html__('Filter', 'iflair-woo-product-filters'),
					),
					'reset_filter_button_display' => array(
						'title' => esc_html__('Show reset filter button', 'iflair-woo-product-filters'),
						'id' => 'reset_filter_button_display',
						'name' => 'reset_filter_button_display',
						'type' => 'checkbox',
						'custom_attributes' => array(
							'data-btn-text-field="reset_filter_button_text"',
						),
						'class' => 'ifwoopf-display-button-check',
					),
					'reset_filter_button_text' => array(
						'title' => esc_html__('Reset filter button text', 'iflair-woo-product-filters'),
						'id' => 'reset_filter_button_text',
						'type' => 'text',
						'format' => 'tr',
						'placeholder' => esc_html__('Reset', 'iflair-woo-product-filters'),
					),
				),
			),
		);

		$settings = array_merge($drag_settings, $taxonomy_settings, $last_drag_settings);
	
		$settings = $this->ifwoopf_get_sorted_settings($settings);

		$this->modal_popup_settings = $settings;

		return $settings;

	}

	/**
	 * Get all general settings fields
	 *
	 * @since 1.0
	 * 
	 * @return array $general_settings The general settings array
	 */
	public function ifwoopf_get_general_settings(){

		global $ifwoopf_default_settings;

		$columns_4_shop_page = array(
			'1' => esc_html__('1', 'iflair-woo-product-filters'),
			'2' => esc_html__('2', 'iflair-woo-product-filters'),
			'3' => esc_html__('3', 'iflair-woo-product-filters'),
			'4' => esc_html__('4', 'iflair-woo-product-filters'),
			'5' => esc_html__('5', 'iflair-woo-product-filters'),
			'6' => esc_html__('6', 'iflair-woo-product-filters')
		);

		$general_settings = array(
			'enable_filter_on_shop_page' => array(
				'title' => esc_html__('Would you like to show filter at shop page?', 'iflair-woo-product-filters'),
				'id' => 'enable_filter_on_shop_page',
				'name' => 'enable_filter_on_shop_page',
				'type' => 'checkbox',
			),
			'display_filter_heading' => array(
				'title' => esc_html__('Show filter heading', 'iflair-woo-product-filters'),
				'id' => 'display_filter_heading',
				'name' => 'display_filter_heading',
				'custom_attributes' => array(
					'data-name="display_filter_heading"',
					'data-text-class="ifwoopf-filter-heading"'
				),
				'type' => 'checkbox',
				'class' => 'ifwoopf-display-heading-check ifwoopf-display-filter-heading ',
			),
			'filter_heading' => array(
				'title' => esc_html__('Heading text', 'iflair-woo-product-filters'),
				'id' => 'filter_heading',
				'type' => 'text',
				'format' => 'tr',
				'class' => 'ifwoopf-filter-heading ',
				'placeholder' => esc_html__('Filter', 'iflair-woo-product-filters'),
			),
			'display_filter_position' => array(
				'title' => esc_html__('Display filter', 'iflair-woo-product-filters'),
				'id' => 'display_filter_position',
				'name' => 'display_filter_position',
				'type' => 'select',
				'options' => array(
					'from_hook' => esc_html__('Left sidebar', 'iflair-woo-product-filters')
				),
				'placeholder' => '',
			),
			'numbr_of_products_to_display' => array(
				'heading' => esc_html__('Number of products to display', 'iflair-woo-product-filters'),
				'type' => 'heading',
				'id' => 'number_products_4_display',
			),
			// 'number_of_columns_selection' => array(
			// 	'title' => esc_html__('Select columns for shop page', 'iflair-woo-product-filters'),
			// 	'id' => 'number_of_columns_selection_4_shop',
			// 	'name' => 'number_of_columns_selection_4_shop',
			// 	'type' => 'select',
			// 	'options' => $columns_4_shop_page,
			// 	'placeholder' => '',
			// ),
			'number_products_on_load' => array(
				'title' => esc_html__('On page load', 'iflair-woo-product-filters'),
				'id' => 'number_products_on_load',
				'type' => 'number',
				'min' => '-1',
				'placeholder' => esc_attr($ifwoopf_default_settings['number_products_on_load']),
			),
			'number_products_on_load_more' => array(
				'title' => esc_html__('On load more', 'iflair-woo-product-filters'),
				'id' => 'number_products_on_load_more',
				'type' => 'number',
				'min' => '1',
				'placeholder' => esc_attr($ifwoopf_default_settings['number_products_on_load_more']),
			),
			'store_filter_in_url' => array(
				'title' => esc_html__('Store filter data in url', 'iflair-woo-product-filters'),
				'id' => 'store_filter_in_url',
				'name' => 'store_filter_in_url',
				'type' => 'checkbox',
			),
		);

		return $general_settings;

	}

	/**
	 * Get all taxonomies settings fields
	 *
	 * @since 1.0
	 * 
	 * @return   array $taxonomy_settings The settings array
	 */
	public function ifwoopf_get_taxonomies_settings(){

		$taxonomy_settings = array();
		$ifwoopf_settings = ifwoopf_get_option();
		$the_product_taxonomies = ifwoopf_get_taxonomies("all", false, true);
		$tax_fields = array();
		$tax_order_field = array(
			'name' => esc_html__('Name', 'iflair-woo-product-filters'),
			'hierarchical' => esc_html__('Display in Hierarchical view', 'iflair-woo-product-filters'),
		);
		$fornt_field_typs = array(
			'checkbox' => esc_html__('Checkbox', 'iflair-woo-product-filters'),
			'radio' => esc_html__('Radio button', 'iflair-woo-product-filters'),
			'dropdown' => esc_html__('Dropdown', 'iflair-woo-product-filters'),
		);

		if ( !empty( $the_product_taxonomies ) ){
			foreach ($the_product_taxonomies as $tax_key => $tax) {

				$taxonomy_details = get_taxonomy( $tax_key );
				$hierarchical = ( isset( $taxonomy_details->hierarchical ) ) ? $taxonomy_details->hierarchical : false;
				$final_tax_order_field = $tax_order_field;
				if( !$hierarchical ){
					unset($final_tax_order_field['hierarchical']);
				}
				
				$singular_name = ( is_object( $taxonomy_details ) && isset( $taxonomy_details->labels->singular_name ) && !empty( $taxonomy_details->labels->singular_name ) ) ? sprintf( esc_html__("Select %s", 'iflair-woo-product-filters'), $taxonomy_details->labels->singular_name ) : "";
				$tax_label = ( is_object( $taxonomy_details ) && isset( $taxonomy_details->label ) && !empty( $taxonomy_details->label ) ) ? $taxonomy_details->label : "";
				$dropdown_placeholder = ( !empty( $singular_name ) ) ? $singular_name : esc_html__('Select Option', 'iflair-woo-product-filters');

				$field = array(
					'id' => $tax_key,
					'name' => $tax_key,
					'type' => 'checkbox',
					'format' => 'simple',
				);
				$get_terms = get_terms( array(
					'taxonomy'     => $tax_key,
					'hide_empty'   => false,
					'fields' 	   => 'id=>name',
				) );
				$tax_fields = array(
					array(
						'title' => esc_html__('Show heading', 'iflair-woo-product-filters'),
						'id' => sprintf('display_heading_%s', $tax_key),
						'name' => 'display_heading',
						'custom_attributes' => array(
							'data-name="display_heading"',
						),
						'type' => 'checkbox',
						'class' => 'ifwoopf-display-heading',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Heading text', 'iflair-woo-product-filters'),
						'id' => sprintf('heading_%s', $tax_key),
						'name' => 'heading',
						'type' => 'text',
						'class' => 'ifwoopf-heading-text ',
						'placeholder' => esc_html($tax),
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Section expand/collapse', 'iflair-woo-product-filters'),
						'id' => sprintf('toggle_btn_%s', $tax_key),
						'name' => 'toggle_btn',
						'class' => 'ifwoopf-field-toggle-check',
						'type' => 'checkbox',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Default action', 'iflair-woo-product-filters'),
						'id' => sprintf('default_toggle_%s', $tax_key),
						'name' => 'default_toggle',
						'class' => 'ifwoopf-select2 ifwoopf-field-defalut-toggle',
						'type' => 'select',
						'options' => array(
							'show' => esc_html__('Show', 'iflair-woo-product-filters'),
							'hide' => esc_html__('Hide', 'iflair-woo-product-filters'),
						),
						'placeholder' => '',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Selection type', 'iflair-woo-product-filters'),
						'id' => sprintf('selection_type_%s', $tax_key),
						'name' => 'selection_type',
						'type' => 'select',
						'class' => 'ifwoopf-select2 selection-type',
						'multidimensional' => array('terms_display_settings',$tax_key),
						'options' => $fornt_field_typs,
						'placeholder' => '',
					),
					array(
						'title' => esc_html__('Select option text', 'iflair-woo-product-filters'),
						'id' => sprintf('dropdown_placeholder_%s', $tax_key),
						'name' => 'dropdown_placeholder',
						'type' => 'text',
						'class' => 'ifwoopf-dropdown-placeholder',
						'placeholder' => esc_attr($dropdown_placeholder),
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Display order', 'iflair-woo-product-filters'),
						'id' => sprintf('display_order_%s', $tax_key),
						'name' => 'display_order',
						'type' => 'select',
						'class' => 'ifwoopf-select2 ifwoopf-display-order',
						'multidimensional' => array('terms_display_settings',$tax_key),
						'options' => $final_tax_order_field,
						'placeholder' => '',
					),
					array(
						'title' => sprintf(
							esc_html__('Toggle parent/child %s', 'iflair-woo-product-filters'),
							$tax_label,
						),
						'id' => sprintf('toggle_%s', $tax_key),
						'name' => 'toggle',
						'class' => 'ifwoopf-toggle-check',
						'type' => 'checkbox',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Hide empty', 'iflair-woo-product-filters'),
						'id' => sprintf('hide_empty_%s', $tax_key),
						'name' => 'hide_empty',
						'type' => 'checkbox',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => sprintf( 
							esc_html__('Exclude %s', 'iflair-woo-product-filters'),
							$tax
						),
						'id' => sprintf('exclude_terms_%s', $tax_key),
						'name' => 'exclude_terms',
						'type' => 'multiselect',
						'class' => 'ifwoopf-select2 ifwoopf-exclude-terms',
						'custom_attributes' => array(
							sprintf('data-tax="%s"', $tax_key)
						),
						'multidimensional' => array('terms_display_settings',$tax_key),
						'options' => $get_terms,
					),
					array(
						'title' => sprintf( 
							esc_html__('Include %s', 'iflair-woo-product-filters'),
							$tax
						),
						'id' => sprintf('include_terms_%s', $tax_key),
						'name' => 'include_terms',
						'type' => 'multiselect',
						'class' => 'ifwoopf-select2 ifwoopf-include-terms',
						'custom_attributes' => array(
							sprintf('data-tax="%s"', $tax_key),
						),
						'multidimensional' => array('terms_display_settings',$tax_key),
						'options' => $get_terms,
					),
					array(
						'title' => esc_html__('Display product count', 'iflair-woo-product-filters'),
						'id' => sprintf('product_count_%s', $tax_key),
						'name' => 'product_count',
						'type' => 'checkbox',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
					array(
						'title' => esc_html__('Add class for additonal css', 'iflair-woo-product-filters'),
						'id' => sprintf('classes_%s', $tax_key),
						'name' => 'classes',
						'type' => 'text',
						'multidimensional' => array('terms_display_settings',$tax_key),
					),
				);
				$taxonomy_settings[] = array(
					'id' => $tax_key,
					'field' => $field,
					'name' => $tax,
					'class' => 'ifwoopf-tax-popup-field ',
					'type' => esc_html__('Taxonomy', 'iflair-woo-product-filters'),
					'popup' => true,
					'sortable' => true,
					'fields' => $tax_fields,
				);
			}
		}

		return $taxonomy_settings;

	}

	/**
	 * Get all sorting settings fields
	 *
	 * @since 1.0
	 * 
	 * @return   array $sorting_multiple_fields The settings array
	 */
	public function ifwoopf_get_sorting_setting_fields($is_defalut=false){

		$sorting_types = ifwoopf_get_sorting_types();
		$sorting_types_keys = array_keys($sorting_types);
		$saved_sorting_types = array();
		$get_saved_sorting_types = ifwoopf_get_option('sorting_display_settings');
		if( !$is_defalut && !empty( $get_saved_sorting_types ) ){
			foreach ($get_saved_sorting_types as $sd_key => $sd) {
				$saved_sorting_types[$sd_key] = $sorting_types[$sd_key];
			}
			$sorting_types = $saved_sorting_types;
		}
		
		$sorting_multiple_fields = array();
		foreach ($sorting_types as $st_key => $st) {

			$default_sorting_num = array_search($st_key, $sorting_types_keys);
			$custom_attributes = array();
			if( $default_sorting_num !== false ){
				$custom_attributes[] = sprintf('data-default-sorting="%s"', $default_sorting_num);
			}

			$custom_attributes[] = sprintf('data-sorting-key="%s"', $st_key);
			$custom_attributes[] = sprintf('data-sorting-label="%s"', $st);
			$checkbox_custom_attributes = $custom_attributes;
			$checkbox_custom_attributes[] = sprintf('data-name="sorting_display_settings=>%s=>enable"', $st_key);

			$st_bunch = array(
				array(
					'label' => esc_html($st),
					'id' => sprintf('enable_sorting_%s', $st_key),
					'name' => 'enable',
					'type' => 'checkbox',
					'class' => 'ifwoopf-enable-sorting',
					'format' => 'simple',
					'custom_attributes' => $checkbox_custom_attributes,
					'multidimensional' => array('sorting_display_settings',$st_key),
				),
				array(
					'title' => esc_html__('Sorting Label', 'iflair-woo-product-filters'),
					'id' => sprintf('label_sorting_%s', $st_key),
					'name' => 'label',
					'type' => 'text',
					'placeholder' => esc_attr($st),
					'format' => 'simple',
					'custom_attributes' => $custom_attributes,
					'multidimensional' => array('sorting_display_settings',$st_key),
				),
			);
			$sorting_multiple_fields[] = $st_bunch;
		}

		return $sorting_multiple_fields;

	}

	/**
	 * Get select2 objects for settings fields
	 *
	 * @since 1.0
	 * 
	 * @return   array $objects The objects of select2 settings
	 */
	public function ifwoopf_get_select2_settings(){
		
		$objects = array();
		$global_object = $this->ifwoopf_get_global_select2_settings();
		$all_taxs = ifwoopf_get_taxonomies("all", false, true);
		$remove_placeholder = array('default_toggle_search', 'default_toggle_sorting');
		$remove_placeholder_tax = array('display_order_','selection_type_', 'default_toggle_');

		if( !empty( $all_taxs ) ){

			foreach ($all_taxs as $tax_key => $tax) {
				
				foreach ($remove_placeholder_tax as $rp_key => $rp) {
					
					$field_id = $rp.$tax_key;
					$obj = $global_object;
					unset($obj['placeholder']);
					unset($obj['allowClear']);
					$objects[$field_id] = $obj;

				}

			}

		}

		foreach ($remove_placeholder as $rp_key => $rp) {
					
			$field_id = $rp;
			$obj = $global_object;
			unset($obj['placeholder']);
			unset($obj['allowClear']);
			$objects[$field_id] = $obj;

		}

		return $objects;

	}

	/**
	 * Get select2 global object for settings fields
	 *
	 * @since 1.0
	 * 
	 * @return   array $object The object of select2 settings
	 */
	public function ifwoopf_get_global_select2_settings(){
		
		$object = array();
		
		$object['placeholder'] = esc_html__('Select Option', 'iflair-woo-product-filters');
		$object['allowClear'] = true;		

		return $object;

	}

	/**
	 * Get default setting field values
	 *
	 * @since 1.0
	 * 
	 * @return   array $defaults The default field type values array
	 */
	public function ifwoopf_get_settings_fields_defaults(){
		
		$defaults = array(
			'multiselect' => array(
				'id' => '',
				'title' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'options' => array(),
				'description' => '',
				'value' => array(),
			),
			'select' => array(
				'id' => '',
				'title' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'options' => array(),
				'description' => '',
				'value' => '',
				'placeholder' => esc_html__('Select Option', 'iflair-woo-product-filters'),
				'select2' => true,
			),
			'checkbox' => array(
				'id' => '',
				'title' => '',
				'label' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'description' => '',
				'value' => '',
				'value_attribute' => '',
				'is_array' => false,
				'disabled' => false,
				'hidden_value' => 'no',
				'hide' => false,
			),
			'number' => array(
				'id' => '',
				'title' => '',
				'label' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'placeholder' => '',
				'custom_attributes' => array(),
				'description' => '',
				'value' => '',
			),
			'text' => array(
				'id' => '',
				'title' => '',
				'label' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'placeholder' => '',
				'custom_attributes' => array(),
				'description' => '',
				'value' => '',
			),
			'textarea' => array(
				'id' => '',
				'title' => '',
				'label' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'placeholder' => '',
				'custom_attributes' => array(),
				'description' => '',
				'value' => '',
			),
			'sortingfield' => array(
				'id' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'fields' => array(),
				'description' => '',
				'reset_html' => array(),
			),
			'button' => array(
				'id' => '',
				'title' => '',
				'label' => '',
				'css' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'description' => '',
				'button_type' => 'button',
				'disabled' => false,
			),
			'code-editor' => array(
				'id' => '',
				'css' => '',
				'editor_language' => '',
				'format' => 'tr',
				'class' => '',
				'custom_attributes' => array(),
				'description' => '',
				'value' => '',
			),
			'heading' => array(
				'id' => '',
				'css' => '',
				'format' => 'simple',
				'over_table' => true,
				'class' => '',
				'custom_attributes' => array(),
			),
		);
		
		return $defaults;

	}

	/**
	 * Save plugin settings
	 * 
	 * @since 1.0
	 * @since 1.1 set ifwoopf currnet tab to session
	 */
	public function ifwoopf_save_settings(){
		
		if ( isset( $_POST['ifwoopf_save_settings'] ) && !empty( $_POST['ifwoopf_save_settings'] ) ) {
			// verify nonce
			if(wp_verify_nonce($_POST['ifwoo_nonce'], 'ifwoo_action_nonce')) {
				
				$post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				$post_ifwoopf_settings = $post_data['ifwoopf_settings'];
				$modal_popup_settings = $this->ifwoopf_get_modal_popup_settings();
				
				if ( isset( $post_ifwoopf_settings['search_display'] ) && $post_ifwoopf_settings['search_display'] === 'yes' ){
					$post_ifwoopf_settings['search_in_title'] = 'yes';
				}

				if ( isset( $post_ifwoopf_settings['terms_display_settings'] ) && !empty( $post_ifwoopf_settings['terms_display_settings'] ) ){
					foreach ($post_ifwoopf_settings['terms_display_settings'] as $ts_key => $terms_display_settings) {
						
						if ( !empty( $terms_display_settings ) ){
							
								$tds = $post_ifwoopf_settings['terms_display_settings'][$ts_key];
								if( isset( $tds['exclude_terms'] ) && !empty( $tds['exclude_terms'] ) && isset( $tds['include_terms'] ) && !empty( $tds['include_terms'] ) ){

									//unset($post_ifwoopf_settings['terms_display_settings'][$ts_key]['exclude_terms']);
									//unset($post_ifwoopf_settings['terms_display_settings'][$ts_key]['include_terms']);
							
								}
							
						}

					}
				}

				$updated = update_option(IFWOOPF_PREFIX.'_settings', $post_ifwoopf_settings);

				if (!session_id()) {
				    session_start();
				}

				if ( $updated === false ){
					global $ifwoopf_settings;
					if ( $post_ifwoopf_settings === $ifwoopf_settings ){
						$updated = true;
					}
				}

				$_SESSION['ifwoopf_settings_updated'] = sanitize_text_field($updated);

				if( isset( $post_data['ifwoopf_currnet_tab'] ) && !empty( $post_data['ifwoopf_currnet_tab'] ) ){
					$_SESSION['ifwoopf_currnet_tab'] = sanitize_text_field($post_data['ifwoopf_currnet_tab']);
				}

			} else {
				wp_die('invalid nonce');
			}
		} 

	}

	/**
	 * Add tr structure before field
	 * 
	 * @since 1.0
	 * 
	 * @param  array $field field attributes
	 */
	public function ifwoopf_before_admin_field_table_markup($field){

		if ( !isset( $field['format'] ) || ( isset( $field['format'] ) && $field['format'] !== 'simple' ) ){
			$hidden_class = ( isset( $field['hide'] ) && $field['hide'] ) ? 'hidden' : '';
			?>
			<tr class="<?php echo esc_attr($hidden_class); ?>" valign="top">
				<th scope="row" class="ifwoopf-field-heading">
					<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				</th>
				<td class="ifwoopf-field ifwoopf-field-<?php echo esc_attr($field['type']) ?>">
			<?php
		}

		if( isset( $field['over_table'] ) && $field['over_table'] === true ){

			?>
				</tbody>
			</table>
				
			<?php
		}

	}

	/**
	 * Add tr structure before field
	 * 
	 * @since 1.0
	 * 
	 * @param  array $field field attributes
	 */
	public function ifwoopf_after_admin_field_table_markup($field){

		if ( !isset( $field['format'] ) || ( isset( $field['format'] ) && $field['format'] !== 'simple' ) ){
			?>
				</td>
			</tr>
			<?php
		}

		if( isset( $field['over_table'] ) && $field['over_table'] === true ){

			?>
			<table class="form-table" role="presentation">
				<tbody>
			<?php
		}

	}

	/**
	 * Get sortable settings fields
	 * 
	 * @since 1.0
	 * 
	 * @param  array $fields fields attributes
	 * @param  string $return return type
	 *
	 * @return array $sortable_fields list of sortable fields
	 */
	public function ifwoopf_get_sortable_fields($fields=array(), $return='key'){

		$sortable_fields = array();
		if( empty( $fields ) || !is_array($fields) ){
			return array();
		}

		foreach ($fields as $key => $field) {
			
			if( isset( $field['sortable'] ) && $field['sortable'] ){

				$get_field = ifwoopf_get_final_field($field['field']);
				$key = ( isset( $get_field['name'] ) && !empty( $get_field['name'] ) ) ? $get_field['name'] : $get_field['id'];

				if( $return === 'key' ){

					$sortable_fields[] = $key;

				}else{

					$sortable_fields[$key] = $field;

				}

			}

		}

		return $sortable_fields;

	}

	/**
	 * Get sorted settings
	 * 
	 * @since 1.0
	 * 
	 * @param  array  $settings Settings fields
	 * 
	 * @return array $sorted_settings Sorted settings fields
	 */
	public function ifwoopf_get_sorted_settings($settings=array()){

		$ifwoopf_settings = ifwoopf_get_option();
		
		if( empty( $ifwoopf_settings ) || empty( $settings ) || !is_array($settings) ){
			return $settings;
		}

		$sorted_settings = array();
		$settings_with_id = $this->ifwoopf_get_sortable_fields($settings, "field");
		$sortable_fields = array_keys($settings_with_id);
		$temp_sortable_fields = $sortable_fields;

		foreach ($ifwoopf_settings as $s_key => $setting) {

			if( in_array($s_key, $sortable_fields) ){	
				$sorted_settings[] = $settings_with_id[$s_key];
				$get_field_key = array_search($s_key, $temp_sortable_fields);
				if( $get_field_key !== -1 ){
					unset($temp_sortable_fields[$get_field_key]);
				}
			}

		}
	
		foreach ($settings as $s_key => $setting) {
			
			$s_field = ( isset( $setting['field'] ) ) ? $setting['field'] : $setting;
			$s_field_name = ( isset( $s_field['name'] ) && !empty( $s_field['name'] ) ) ? $s_field['name'] : $s_field['id'];

			if( ( !isset( $setting['sortable'] ) || ( isset( $setting['sortable'] ) && $setting['sortable'] === false  ) ) || in_array($s_field_name, $temp_sortable_fields) ){
				$sorted_settings[] = $setting; 
			}
		}

		return $sorted_settings;

	}

	/**
	 * Get default setting tabs
	 *
	 * @since 1.1
	 * 
	 * @return   array $tabs The default settings tabs
	 */
	public function ifwoopf_get_settings_tabs(){

		$tabs = array(
			'general_settings' => array(
				'id' => 'general_settings',
				'label' => esc_html__('General Settings', 'iflair-woo-product-filters'),
				'heading' => esc_html__('General Settings', 'iflair-woo-product-filters'),
				'callback' => array($this, 'ifwoopf_tab_general_settings_callback'),
			),
			'filter_settings' => array(
				'id' => 'filter_settings',
				'label' => esc_html__('Filter', 'iflair-woo-product-filters'),
				'callback' => array($this, 'ifwoopf_tab_filter_settings_callback'),
			),
		);

		if( isset( $_SESSION['ifwoopf_currnet_tab'] ) && !empty( $_SESSION['ifwoopf_currnet_tab'] ) ){
			$tabs[$_SESSION['ifwoopf_currnet_tab']]['default'] = true;
			unset($_SESSION['ifwoopf_currnet_tab']);
		}else{
			$tabs['general_settings']['default'] = true;
		}

		return $tabs;

	}

	/**
	 * Display general setting tab markup
	 *
	 * @since 1.1
	 * 
	 */
	public function ifwoopf_tab_general_settings_callback(){

		$general_settings = $this->ifwoopf_get_general_settings();
		$args = array('settings' => $general_settings);
		ifwoopf_get_admin_template('ifwoopf-settings-table.php', $args);

	}

	/**
	 * Display filter setting tab markup
	 *
	 * @since 1.1
	 * 
	 */
	public function ifwoopf_tab_filter_settings_callback(){

		$modal_popup_settings = $this->ifwoopf_get_modal_popup_settings();
		$args = array('modal_popup_settings' => $modal_popup_settings);
		ifwoopf_get_admin_template('ifwoopf-settings-table-modal-popup.php', $args);

	}

	/**
	 * Define all hooks of admin side
	 *
	 * @since 1.0
	 */
	public function add_hooks(){
		add_action('admin_menu', array($this, 'ifwoopf_admin_page'));
		add_action('admin_enqueue_scripts', array($this, 'ifwoopf_enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'ifwoopf_enqueue_scripts'));
		add_action('admin_init', array($this, 'ifwoopf_save_settings'));
		add_action('ifwoopf_before_admin_field_markup', array($this, 'ifwoopf_before_admin_field_table_markup'));
		add_action('ifwoopf_after_admin_field_markup', array($this, 'ifwoopf_after_admin_field_table_markup'));	
	}
}