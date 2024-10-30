<?php

/**
 * Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @since 1.0
 * 
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This include template by name.
 *
 * @since 1.0
 * 
 * @param string $name Name of template
 * @param array  $args Arguments
 */
function ifwoopf_get_template($name='', $args = array()){
	
	$template_path = IFWOOPF_PUBLIC_DIR_PATH.'partials/';
	$file_path = $template_path.$name;

	if ( file_exists($file_path) ) {
		
		if ( !empty( $args ) && is_array( $args ) ) {
			extract($args);
		}

		include $file_path;
	}

}

/**
 * Display field html by it's type
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 */
function ifwoopf_get_front_field_markup($field=array()){

	$name = sprintf('fields/ifwoopf-%s.php', $field['type']);
	$field = ifwoopf_get_final_front_field($field);
	$args['field'] = $field;

	do_action('ifwoopf_before_front_field_markup', $field);

	ifwoopf_get_template($name, $args);

	do_action('ifwoopf_after_front_field_markup', $field);

}

/**
 * Get final field attributes
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 * @return array  $field field attributes
 */
function ifwoopf_get_final_front_field($field=array()){

	if ( empty( $field ) ){
		return array();
	}

	$defaults = ifwoopf_get_filter_fields_defaults();
	$default = $defaults[$field['type']];
	$field = wp_parse_args( $field, $default );
	$field_type = $field['type'];
	
	$field = ifwoopf_set_value_to_field($field);

	switch ( $field_type ) {
		case 'checkbox':
		case 'radio':
			
			if( isset( $field['value'] ) && isset( $field['value_attribute'] ) && $field['value'] == $field['value_attribute'] ){
				$field['checked'] = true;
			}

			break;
	}

	return $field;

}

/**
 * Get default filter field values
 *
 * @since 1.0
 * 
 * @return array $defaults The default field type values array
 */
function ifwoopf_get_filter_fields_defaults(){
	
	$defaults = array(
		'select' => array(
			'id' => '',
			'title' => '',
			'css' => '',
			'class' => '',
			'custom_attributes' => array(),
			'options' => array(),
			'description' => '',
			'value' => '',
			'placeholder' => esc_html__('Select Option', 'iflair-woo-product-filters'),
		),
		'checkbox' => array(
			'id' => '',
			'title' => '',
			'label' => '',
			'css' => '',
			'class' => '',
			'custom_attributes' => array(),
			'description' => '',
			'value' => '',
			'value_attribute' => '',
		),
		'text' => array(
			'id' => '',
			'title' => '',
			'label' => '',
			'css' => '',
			'class' => '',
			'placeholder' => '',
			'custom_attributes' => array(),
			'description' => '',
			'value' => '',
		),
		'radio' => array(
			'id' => '',
			'title' => '',
			'label' => '',
			'css' => '',
			'class' => '',
			'custom_attributes' => array(),
			'description' => '',
			'value' => '',
			'value_attribute' => '',
		),
	);
	
	return $defaults;

}

/**
 * Get default sorting types
 * 
 * @since 1.0
 *
 * @param  boolean $is_default is return default
 * 
 * @return array $sorting_types Default types of sorting
 */

function ifwoopf_get_sorting_types($is_default=false){
	
	if( $is_default ){
		return  array('date-desc' => esc_html__('Newest Product', 'iflair-woo-product-filters'));
	}

	$sorting_types = array(
		'title' => esc_html__('Name - (A-Z)', 'iflair-woo-product-filters'),
		'title-desc' => esc_html__('Name - (Z-A)', 'iflair-woo-product-filters'),
		'price' => esc_html__('Price - Low to High', 'iflair-woo-product-filters'),
		'price-desc' => esc_html__('Price - High to Low', 'iflair-woo-product-filters'),
		'popularity' => esc_html__('Popularity', 'iflair-woo-product-filters'),
		'rating' => esc_html__('Rating', 'iflair-woo-product-filters'),
		'date-desc' => esc_html__('Newest Product', 'iflair-woo-product-filters'),
		'date' => esc_html__('Oldest Product', 'iflair-woo-product-filters'),
	);

	return $sorting_types;

}

/**
 * Get enabled sorting types
 * 
 * @since 1.0
 * 
 * @return array $sorting_types enabled types of sorting
 */
function ifwoopf_get_enabled_sorting_types(){
	
	$enabled_sorting_types = array();
	$sorting_types = ifwoopf_get_sorting_types();
	
	$get_saved_sorting_types = ifwoopf_get_option('sorting_display_settings');
	
	if( !empty( $get_saved_sorting_types ) ){
		foreach ($get_saved_sorting_types as $sd_key => $sd) {
			if( $sd['enable'] == 'yes' ){
				$enabled_sorting_types[$sd_key] = $sorting_types[$sd_key];
			}
		}
	}
	
	return $enabled_sorting_types;

}

/**
 * Get all fields for filter form by settings
 *	
 * @since 1.0
 * 
 * @return array $fields
 */
function ifwoopf_get_filters_by_settings(){

	$ifwoopf_settings = ifwoopf_get_option();

	if( empty( $ifwoopf_settings ) ){
		$ifwoopf_settings = ifwoopf_get_option('', true);
	}

	$fields = array();

	$taxonomies = ifwoopf_get_taxonomies('all');
	
	if( !empty( $ifwoopf_settings ) ){

		foreach ($ifwoopf_settings as $s_key => $s_v) {
			
			$field = array();

			if ( $s_key == 'sorting_display' && $s_v === 'yes' ) {

				$sorting_types = ifwoopf_get_sorting_types();

				if( isset( $ifwoopf_settings['sorting_display_settings'] ) && !empty( $ifwoopf_settings['sorting_display_settings'] ) ){

					$sorting_settings = $ifwoopf_settings['sorting_display_settings'];
					foreach ($sorting_settings as $sts_key => $sts) {
						if( $sts['enable'] === 'yes' ){
							$sorting_label = ( !empty( trim($sts['label']) ) ) ? $sts['label'] : $sorting_types[$sts_key];
							$enabled_srotings[$sts_key] = $sorting_label;
						}
					}

					$field = array(
						'type' => 'sorting',
						'value' => $enabled_srotings,
					);
					
				}	

			}else if ( $s_key == 'search_display' && $s_v === 'yes' ) {

				$field = array(
					'type' => 'search',
				);

			}else if ( in_array($s_key, $taxonomies) && $s_v === 'yes' ){

				$field = array(
					'type' => 'taxonomy',
					'value' => $s_key,
				);

			}else if ( $s_key === 'filter_buttons_display' && $s_v === 'yes' ){

				$field = array(
					'type' => 'buttons',
				);

			}

			if( !empty( $field ) ){
				$fields[] = $field;
			}

		}

	}	

	return $fields;

}

/**
 * Get all registerd taxonomies of product post type
 *
 * @since 1.0
 * 
 * @return array $taxonomies
 */
function ifwoopf_get_product_taxonomies(){
	
	$taxonomies = array();
	$product_taxonomies = get_taxonomies(
		array(
			'public'=>true,
			'object_type'=>array(
				'product'
			),
		)
	);
	
	if ( !empty( $product_taxonomies ) ){
		foreach ($product_taxonomies as $tx_key => $tax) {
			
			$get_tax = get_taxonomy( $tax );
			
			if ( !is_wp_error($get_tax) && isset( $get_tax->label ) && strpos($tax, 'pa_') === false ) {
				$taxonomies[$tax] = $get_tax->label;
			}

		}
	}

	return $taxonomies;

}

/**
 * Get all attributes of product post type
 *
 * @since 1.0
 * 
 * @return array $attributes
 */
function ifwoopf_get_product_attributes(){

	global $wpdb;
	$attributes = array();

	if ( function_exists('wc_get_attribute_taxonomies') && function_exists('wc_attribute_taxonomy_name') ) {
		
		$get_attributes = wc_get_attribute_taxonomies();

		if ( !empty( $get_attributes ) ) {
			
			foreach ($get_attributes as $at_key => $attribute) {
				
				$att_name = $attribute->attribute_name;
				$att_label = $attribute->attribute_label;
				$att_name = wc_attribute_taxonomy_name( $att_name );
				$attributes[$att_name] = $att_label;

			}

		}

	}else{

		$get_taxonomies = get_object_taxonomies( 'product', 'objects' );
		
		if ( !empty( $get_taxonomies ) ) {
			
			foreach ($get_taxonomies as $tax_key => $taxonomy) {
				
				$att_name = $taxonomy->name;
				$att_label = $taxonomy->label;

				if ( strpos($att_name, 'pa_') !== false ) {
					$attributes[$att_name] = $att_label;
				}
				

			}

		}

	}

	return $attributes;

}

/**
 * Get setting option value by name
 *
 * @since 1.0
 * 
 * @param  string $key option name
 * @param  array $keys_arr option keys
 * @return mix $value option value
 */
function ifwoopf_get_option($key = '', $is_default=false, $keys_arr = array()){

	if( !empty( $keys_arr ) ){
		return ifwoopf_get_value_of_multidimensional_field(array(), $keys_arr);
	}

	$ifwoopf_default_settings = ifwoopf_default_settings();
	$ifwoopf_settings = get_option(IFWOOPF_PREFIX.'_settings'); 
	$value = false;
	if ( !empty( $key ) && isset( $ifwoopf_settings[$key] ) && !empty( $ifwoopf_settings[$key] ) ){
		$value = $ifwoopf_settings[$key];
	}else if ( $is_default && !empty( $key ) && isset( $ifwoopf_default_settings[$key] ) && !empty( $ifwoopf_default_settings[$key] ) ){
		$value = $ifwoopf_default_settings[$key];
	}else if ( !empty( $key ) && empty( $ifwoopf_settings ) && isset( $ifwoopf_default_settings[$key] ) && !empty( $ifwoopf_default_settings[$key] ) ) {
		$value = $ifwoopf_default_settings[$key];
	}else if ( empty( $key ) ) {
		if( $is_default ){
			$value = $ifwoopf_default_settings;
		}else{
			$value = $ifwoopf_settings;
		}
	}

	return $value;
}

/**
 * Get value of multidimensional field
 *
 * @since 1.0
 * 
 * @param  array  $field    field attributes
 * @param  array  $keys_arr arrya of keys
 * @return mix    $value    field value
 */
function ifwoopf_get_value_of_multidimensional_field($field=array(), $keys_arr = array()){

	if ( empty( $field ) && empty( $keys_arr ) ){
		return '';
	}

	$ifwoopf_default_settings  = ifwoopf_default_settings();
	$ifwoopf_settings = get_option(IFWOOPF_PREFIX.'_settings');
	if( empty( $ifwoopf_settings ) ){
		$ifwoopf_settings = $ifwoopf_default_settings;
	}
	$value = "";
	
	$elemets_arr = ( !empty( $field ) && isset( $field['multidimensional'] ) && !empty( $field['multidimensional'] ) ) ? $field['multidimensional'] : $keys_arr;

	foreach ($elemets_arr as $mkey => $mvalue) {
		
		if ( empty( $value ) && isset( $ifwoopf_settings[$mvalue] ) ){
			$value = $ifwoopf_settings[$mvalue];
		}elseif ( !empty( $value ) && isset( $value[$mvalue] ) ) {
			$value = $value[$mvalue];
		}elseif ( !empty( $keys_arr ) ) {
			return "";
		}

	}

	if ( !empty( $field ) && empty( $keys_arr ) && !empty( $value ) ){

		$field_name = $field['name'];

		if( isset( $value[$field_name] ) ){
			$value = $value[$field_name];
		}else{
			return "";
		}

	}

	return $value;

}

/**
 * Get enabled taxonomies name
 *
 * @since 1.0
 * 
 * @param  string  $taxonomy_type The type of taxonomy
 * @param  boolean $enabled       Is check for enabled
 * @return array   $enabled_tax   Names of taxonomy
 */
function ifwoopf_get_taxonomies($taxonomy_type='all', $enabled=false, $with_label=false){

	if( $enabled ){
		if( $taxonomy_type == 'all' ){
			$ifwoopf_settings = ifwoopf_get_option();
			$taxonomies = ifwoopf_get_taxonomies($taxonomy_type, false, $with_label);
		}else{
			$taxonomies = ifwoopf_get_taxonomies($taxonomy_type, false, $with_label);
		}
	}else{
		if( $taxonomy_type == 'all' ){
			$tax = ifwoopf_get_product_taxonomies();
			$attr = ifwoopf_get_product_attributes();
			$taxonomies = array_merge($tax, $attr);
		}elseif( $taxonomy_type == 'taxonomies' ) {
			$taxonomies = ifwoopf_get_product_taxonomies();
		}else{
			$taxonomies = ifwoopf_get_product_attributes();
		}
	}

	if( !$enabled ){
		if( !$with_label ){
			$taxonomies = array_keys($taxonomies);
		}
	}

	return $taxonomies;

}

/**
 * Get plugin's default settings
 * 
 * @since 1.0
 * @return array
 */
function ifwoopf_default_settings(){

	$default_sorting_types = ifwoopf_get_sorting_types();
	$default_enabled_sorting_types = array();

	$uncategorized_term_id = get_option( 'default_product_cat' );
	
	foreach ($default_sorting_types as $ds_key => $ds) {
		$default_enabled_sorting_types[$ds_key] = array('enable'=>'yes');
	}

	$default = array(
		'display_filter_position' => 'from_hook',
		'number_of_columns_selection_4_shop' => '3',
		'sorting_display' => 'yes',
		'search_display' => 'yes',
		'product_cat' => 'yes',
		'product_tag' => 'yes',
		'pa_color' => 'yes',
		'pa_size' => 'yes',
		'toggle_btn_sorting' => 'yes',
		'toggle_btn_search' => 'yes',
		'search_placeholder' => 'Search product by name',
		'number_products_on_load' => 12,
		'number_products_on_load_more' => 6,
		'search_min_input' => 3,
		'display_heading' => 'yes',
		'search_in_title' => 'yes',
		'display_sorting_heading' => 'yes',
		'display_search_heading' => 'yes',
		'display_filter_heading' => 'yes',
		'terms_display_settings' => array(
			'product_cat' => array(
				'display_heading' => 'yes',
				'exclude_terms' => $uncategorized_term_id,
				'product_count' => 'yes',
				'toggle_btn' => 'yes',
				'display_order' => 'hierarchical',
				'toggle' => 'yes',
			),
			'product_tag' => array(
				'display_heading' => 'yes',
				'product_count' => 'yes',
				'toggle_btn' => 'yes',
			),
			'pa_color' => array(
				'display_heading' => 'yes',
				'product_count' => 'yes',
				'toggle_btn' => 'yes',
			),
			'pa_size' => array(
				'display_heading' => 'yes',
				'product_count' => 'yes',
				'toggle_btn' => 'yes',
			),
		),
		'sorting_display_settings' => $default_enabled_sorting_types,
		'product_count' => 'yes',
		'filter_buttons_display' => 'yes',
	);

	return $default;

}

/**
 * Get query string args
 * 
 * @since 1.1
 * @return array $enabled_filters The query string args
 */
function ifwoopf_get_enabled_filters_names(){

    $get_filters = ifwoopf_get_filters_by_settings();
    $enabled_filters = array();

    foreach ($get_filters as $f_key => $f_field) {
        
        // Check if 'value' key exists before accessing it
        $filter_value = isset($f_field['value']) ? $f_field['value'] : '';

        // Proceed only if 'type' key exists
        if (isset($f_field['type'])) {
            $filter_key = $f_field['type'];

            if( $filter_key == 'taxonomy' ){
                $filter_key = sprintf('%1$s_%2$s', $filter_key, $filter_value);
            }

            $filter_key = sprintf('%1$s_%2$s', IFWOOPF_PREFIX, $filter_key);

            $enabled_filters[$filter_key] = $filter_value;
        }
    }

    return $enabled_filters;
}


/**
 * Get query string args
 * 
 * @since 1.1
 * @return array $query_args The query string args
 */
function ifwoopf_pars_query_string_args(){

	$get_filters = ifwoopf_get_filters_by_settings();
	$enabled_filters = ifwoopf_get_enabled_filters_names();
	$query_args = array();

	foreach ($enabled_filters as $key => $value) {
		
		if( isset( $_GET[$key] ) ){

			$to_key = substr($key, strlen(IFWOOPF_PREFIX)+1);
			$query_args[$to_key] = sanitize_text_field($_GET[$key]);			

		}

	}

	return $query_args;

}

/**
 * Get query string args
 * 
 * @since 1.1
 * @return array $query_args The query string args
 */
function ifwoopf_pars_pagination_query_string_args(){

	$pagination_args = array(
		'ifwoopf_max_page',
		'ifwoopf_page',
		'ifwoopf_offset',
		'ifwoopf_page_limit',
	);
	$query_args = array();

	foreach ($pagination_args as $arr_key => $key) {
		
		if( isset( $_GET[$key] ) ){
			$to_key = substr($key, strlen(IFWOOPF_PREFIX)+1);
			$query_args[$to_key] = sanitize_text_field($_GET[$key]);
		}

	}

	return $query_args;

}

/**
 * Set value to field
 * 
 * @since 1.1
 * @return array $field field attributes 
 */
function ifwoopf_set_value_to_field($field=array()){

	if( !is_array( $field ) || empty( $field ) ){
		return $field;
	}

	$query_args = ifwoopf_pars_query_string_args();
	$value = '';

	$field_name = ( isset( $field['name'] ) ) ? $field['name'] : $field['id'];
	$field_type = $field['type'];

	if( strpos($field_name, IFWOOPF_PREFIX.'_') !== false ){
		$field_name = substr($field_name, strlen(IFWOOPF_PREFIX)+1);
	}

	if (str_ends_with($field_name, '[]')) {
	    $field_name = str_replace("][", "_", $field_name);
	    $field_name = str_replace("_]", "", $field_name);
	    $field_name = str_replace("[", "_", $field_name);
	}else{
		$field_name = str_replace("][", "_", $field_name);
	    $field_name = str_replace("[", "_", $field_name);
	    $field_name = str_replace("]", "", $field_name);
	}
	
	if( isset( $query_args[$field_name] ) ){

		$values_str = $query_args[$field_name];

		if ( strpos($field_name, "taxonomy") !== false ) {

			$values_arr = explode(",", $values_str);

			switch ( $field_type ) {
				case 'checkbox':
				case 'radio':
					
					$value_key = array_search($field['value_attribute'], $values_arr);

					if( $value_key !== -1 ){
						$value = $values_arr[$value_key];
					}

					break;

				case 'select':
					
					$value = ( isset( $values_arr[0] ) ) ? $values_arr[0] : '';

					break;
			}
			
		}

		$field['value'] = $value;

	}

	return $field;

}

/**
 * Get query vars
 * 
 * @since 1.1
 * @return array $filter_data The filter query data
 */
function ifwoopf_wp_query_args(){

	$filter_data = array();
	$taxonomies = array();
	$query_args = ifwoopf_pars_query_string_args();
	
	foreach ($query_args as $key => $value) {
		
		if( $key === 'search' ){

			$filter_data['ifwoopf_search'] = $value;

		}elseif ( strpos($key, "taxonomy") !== false ) {
			
			$tax_key = str_replace("taxonomy_", "", $key);
			$taxonomies[$tax_key] = explode(",", $value);

		}else{

			$filter_data[$key] = $value;

		}

	}

	if( !empty( $taxonomies ) ){
		$filter_data['taxonomy'] = $taxonomies;
	}

	return $filter_data;

}