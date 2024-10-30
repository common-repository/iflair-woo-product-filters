<?php
/**
 * Admin setting Functions
 *
 * General setting functions.
 *
 * @since 1.0
 * 
 * @package    iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/admin
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
function ifwoopf_get_admin_template($name='', $args = array()){
	
	$template_path = IFWOOPF_ADMIN_DIR_PATH.'partials/';
	$file_path = $template_path.$name;

	if ( file_exists($file_path) ) {
		
		if ( !empty( $args ) && is_array( $args ) ) {
			extract($args);
		}

		include $file_path;
	}

}

/**
 * This will print message on save settings.
 *
 * @since 1.0
 */
function ifwoopf_settings_notice(){

	if (!session_id()) {
	    session_start();
	}

	if ( isset( $_SESSION['ifwoopf_settings_updated'] ) && !empty($_SESSION['ifwoopf_settings_updated']) ){

		$updated = sanitize_text_field($_SESSION['ifwoopf_settings_updated']);

		?>
		<?php if ( isset($updated) && !empty($updated) ){ ?>
			<div class="notice notice-success is-dismissible">
		        <p><?php echo esc_html__( 'Settings updated successfully.', 'iflair-woo-product-filters' ); ?></p>
		    </div>
		<?php }else{ ?>
			<div class="notice notice-error is-dismissible">
		        <p><?php echo esc_html__( 'Settings not updated successfully.', 'iflair-woo-product-filters' ); ?></p>
		    </div>
		<?php } ?>
		<?php
		unset($_SESSION['ifwoopf_settings_updated']);
	}

}

/**
 * Display field html by it's type
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 */
function ifwoopf_get_admin_field_markup($field=array()){

	$name = sprintf('fields/ifwoopf-field-%s.php', $field['type']);
	$field = ifwoopf_get_final_field($field);
	$args = array(
		'field' => $field
	);

	do_action('ifwoopf_before_admin_field_markup', $field);

	ifwoopf_get_admin_template($name, $args);

	do_action('ifwoopf_after_admin_field_markup', $field);

}

/**
 * Get final field attributes
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 * @return array  $field field attributes
 */
function ifwoopf_get_final_field($field=array()){

	if ( empty( $field ) ){
		return array();
	}

	global $defaults;

	$ifwoopf_settings = ifwoopf_get_option();
	$default_settings = ifwoopf_default_settings();
	$default_settings_keys = array_keys($default_settings);
	$default = isset($defaults[$field['type']]) ? $defaults[$field['type']] : null;
	$field = wp_parse_args( $field, $default );

	$field_type = $field['type'];
	$field_name = isset($field['name']) ? $field['name'] : '';
	$field_id = $field['id'];
	$name_attribute = ifwoopf_get_field_name_attribute($field);
	$field['name_attribute'] = $name_attribute;

	$option_value = ifwoopf_get_field_value($field);
	$field['field_value'] = $option_value;

	switch ($field_type) {

		case 'checkbox':
			if ( isset( $field['value_attribute'] ) && !empty( $field['value_attribute'] ) ){
				$checked = ( isset( $ifwoopf_settings[$field_name] ) && in_array($field['value_attribute'], $option_value) ) ? true : false;
			}else{
				$checked = ( $field['field_value'] == 'yes' || ( empty( $ifwoopf_settings ) && ( in_array($field['id'], $default_settings_keys) || in_array($field['name'], $default_settings_keys) ) ) ) ? true : false;
			}

			$field['checked'] = $checked;
			break;

	}

	$field = ifwoopf_check_field_for_disable($field);

	return $field;

}

/**
 * Get field name attribute
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 * @return mix  $option_value field value
 */
function ifwoopf_get_field_value($field=array()){

	if ( empty( $field ) ){
		return array();
	}

	$ifwoopf_settings = ifwoopf_get_option();
	if( empty( $ifwoopf_settings ) ){
		$ifwoopf_settings = ifwoopf_default_settings();
	}
	$option_value = isset($field['value']) ? $field['value'] : '';
	$field_type = $field['type'];
	$field_name = isset($field['name']) ? $field['name'] : '';
	$field_id = $field['id'];

	if ( empty( $option_value ) && !isset( $field['multidimensional'] ) ){
		
		if ( isset( $ifwoopf_settings[$field_id] ) ) {

			$option_value = $ifwoopf_settings[$field_id];

		}else if ( isset( $ifwoopf_settings[$field_name] ) ){
			
			$option_value = $ifwoopf_settings[$field_name];
			
		}

	}elseif ( isset( $field['multidimensional'] ) && !empty( isset( $field['multidimensional'] ) ) ) {
		$option_value = ifwoopf_get_value_of_multidimensional_field($field);
	}

	return $option_value;

}

/**
 * Get field name attribute
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 * @return string  $field_name field name attribute
 */
function ifwoopf_get_field_name_attribute($field=array()){

	if ( empty( $field ) ){
		return "";
	}

	$field_type = $field['type'];
	$field_prefix = 'ifwoopf_settings';
	$field_name = $field_prefix.'['.esc_attr( $field['id'] ).']';
	$multi_value_field_types = array('multiselect');

	if( isset( $field['name'] ) && !empty( $field['name'] ) && !isset( $field['multidimensional'] ) ){
		$field_name = $field_prefix.'['.esc_attr( $field['name'] ).']';
	}

	if ( isset( $field['multidimensional'] ) && !empty( $field['multidimensional'] ) ){

		$field_name = $field_prefix;

		foreach ($field['multidimensional'] as $mkey => $mvalue) {
			$field_name = $field_name.'['.esc_attr($mvalue).']';
		}

		$field_name = $field_name.'['.esc_attr( $field['name'] ).']';
		
	}

	if ( isset( $field['is_array'] ) && $field['is_array'] === true && !in_array($field_type, $multi_value_field_types) ){
		$field_name = $field_name . '[]';
	}

	if ( in_array($field_type, $multi_value_field_types) ){
		$field_name = $field_name . '[]';
	}

	return $field_name;

}

/**
 * Check if field is need to be disabled or not.
 *
 * @since 1.0
 * 
 * @param  array  $field field attributes
 * @return array  $field field attributes
 */
function ifwoopf_check_field_for_disable($field=array()){

	if ( empty( $field ) ){
		return $field;
	}

	$ifwoopf_settings = ifwoopf_get_option();
	$field_name = isset($field['name']) ? $field['name'] : '';

	switch ($field_name) {

		case 'exclude_terms':
			$fi_arr = $field['multidimensional'];
			$fi_arr[] = "include_terms";
			$value = ifwoopf_get_value_of_multidimensional_field(array(), $fi_arr);
			if ( !empty( $value ) ){
				$field['disabled'] = true;
			}
			break;

		case 'include_terms':
			$fi_arr = $field['multidimensional'];
			$fi_arr[] = "exclude_terms";
			$value = ifwoopf_get_value_of_multidimensional_field(array(), $fi_arr);
			if ( !empty( $value ) ){
				$field['disabled'] = true;
			}
			break;

	}

	return $field;

}