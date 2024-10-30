<?php 

/**
 * This class manage all public functionality.
 *
 * @since 1.0 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/public 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Iflair_Woo_Product_Filters_Public {

	/**
	 * The instance of model class
	 * 
	 * @since 1.0
	 *
	 * @var instance 	$model 	instance of model class
	 */
	public $model;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 */
	public function __construct(){

		global $ifwoopf_model;
		$this->model = $ifwoopf_model;

	}

	/**
	 * Register the style for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function ifwoopf_enqueue_styles() {

		$prefix = IFWOOPF_PREFIX;
		$ifwoopf_settings = ifwoopf_get_option();
		$is_load_scripts = $this->ifwoopf_enqueue_conditions();

		wp_register_style( $prefix.'-fontawesome-style', IFWOOPF_ASSETS_DIR_URL . 'css/fontawesome-all.min.css');
		wp_register_style( $prefix.'-public-style', IFWOOPF_PUBLIC_DIR_URL . 'css/ifwoopf-public.css');

		if ( $is_load_scripts ){

			wp_enqueue_style($prefix.'-fontawesome-style');
			wp_enqueue_style($prefix.'-public-style');

			$extra_css = ( isset( $ifwoopf_settings['additional_css'] ) ) ? $ifwoopf_settings['additional_css'] : "";
			$extra_css = stripcslashes( esc_html( trim( $extra_css ) ) );

			if ( $extra_css !== '' ) {
	            wp_add_inline_style($prefix.'-public-style' , $extra_css);
	        }

		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function ifwoopf_enqueue_scripts() {

		$prefix = IFWOOPF_PREFIX;
		$ifwoopf_settings = ifwoopf_get_option();
		$is_load_scripts = $this->ifwoopf_enqueue_conditions();

		wp_register_script( $prefix.'-public-script-functions', IFWOOPF_PUBLIC_DIR_URL . 'js/ifwoopf-public-functions.js', array( 'jquery' ), IFWOOPF_VERSION, false );
		wp_register_script( $prefix.'-public-script', IFWOOPF_PUBLIC_DIR_URL . 'js/ifwoopf-public.js', array( 'jquery' ), IFWOOPF_VERSION, false );

		if ( $is_load_scripts ){

			$is_enabled_buttons = ifwoopf_get_option('filter_buttons_display');
			$is_enabled_button = ifwoopf_get_option('filter_button_display');
			$is_enabled_button = ( $is_enabled_buttons === 'yes' ) ? $is_enabled_button : "no";
			$search_min_input = ifwoopf_get_option('search_min_input', true);
			$enabled_filters_names = ifwoopf_get_enabled_filters_names();
			$posts_per_page_load_more = ifwoopf_get_option('number_products_on_load_more', true);
			$store_filter_in_url = ifwoopf_get_option('store_filter_in_url');
			
			$commn_obj = array( 
	            'ajaxurl' => admin_url( 'admin-ajax.php' ),
	            'prefix' => $prefix,
	            'is_enabled_button' => $is_enabled_button,
	            'search_min_input' => $search_min_input,
	            'enabled_filters_names' => $enabled_filters_names,
	            'posts_per_page_load_more' => $posts_per_page_load_more,
	            'store_filter_in_url' => $store_filter_in_url,
	            'nonce'          => wp_create_nonce('ajax-nonce'), 
	        );

			wp_enqueue_script($prefix.'-public-script');
			wp_enqueue_script($prefix.'-public-script-functions');
			wp_localize_script($prefix.'-public-script-functions', 'ifwoopf_public_object',$commn_obj);

			$javascript_after_ajax = ( isset( $ifwoopf_settings['javascript_after_ajax'] ) ) ? $ifwoopf_settings['javascript_after_ajax'] : "";
			$javascript_after_ajax = stripcslashes( trim( $javascript_after_ajax ) );
			
	        wp_add_inline_script($prefix.'-public-script' , 'window.ifwoopf_javascript_after_ajax = function(){'.$javascript_after_ajax.'}');	        

		}

	}

	/**
	 * This will add custom body classes to body tag
	 *
	 * @since 1.0.0
	 * 
	 * @param array $classes list of body classes
	 */
	public function ifwoopf_add_body_classes($classes){

		$classes[] = 'ifwoopf-body';

		return $classes;

	}

	/**
	 * This will return need to load scripts or not
	 *
	 * @since 1.0
	 * 
	 * @return boolean $return value of load or not
	 */
	public function ifwoopf_enqueue_conditions(){

		$return = false;
		$taxonomies = ifwoopf_get_taxonomies();

		if ( is_shop() || is_post_type_archive('product') || is_tax($taxonomies) ){
			$return = true;
		}

		return $return;

	}

	/**
	 * Render filter form to the shop page
	 *
	 * @since 1.0
	 */
	public function ifwoopf_render_filter_form(){

		$fields = ifwoopf_get_filters_by_settings();

		$args = array(
			'fields' => $fields,
		);

		ifwoopf_get_template('ifwoopf-filter-form.php', $args);

	}

	/**
	 * This will return filterd data of product
	 *
	 * @since 1.0
	 * 
	 * @return json $response The content
	 */
	public function ifwoopf_filter_products_callback(){
		// Check for nonce security  
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
	         
	         die ('InValid Nonce');

	    } else {
			
			$response = array();
			$data = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		    
			if ( !empty( $data ) ) {

				$args = $this->model->ifwoopf_parse_query_args($data);
				$get_products = $this->model->ifwoopf_markup_products($args, $data);

				$products_html = $get_products['html'];
				$product_query = $get_products['query'];
				$get_count = $get_products['result_count'];

				$max_page = $product_query->max_num_pages;
				$page = $product_query->get('paged');
				$found_posts = $product_query->found_posts;
				$num_pro = ifwoopf_get_option('number_products_on_load_more', true);
				$offset = $product_query->get('offset');

				if ( $num_pro && !empty( $offset ) ){
					$posts_per_page = ifwoopf_get_option('number_products_on_load', true);
					$woo_per_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();
					$per_page = ( $posts_per_page === false ) ? $woo_per_page : intval( $posts_per_page );
					$get_max_page = $found_posts / intval($num_pro);
					$max_page = ceil( $get_max_page );
					$response['offset'] = $product_query->get('offset');
				}

				$response['products_html'] = $products_html;
				$response['max_page'] = $max_page;
				$response['result_count'] = $get_count;
				$response['page'] = $page;
				$response['total_found_posts'] = $found_posts;

			}

			wp_send_json($response);

		}
	}


	/**
	 * This will display hidde field for pagination 
	 *
	 * @since 1.0
	 * 
	 * @return string $content html of woo product loop end
	 */
	public function ifwoopf_add_custom_input_pagination(){

		global $wp_query;
		$max_page = $wp_query->max_num_pages;
		$posts_per_page = ifwoopf_get_option('number_products_on_load', true);
		$number_of_columns_selection_4_shop = ifwoopf_get_option('number_of_columns_selection_4_shop');
		$woo_per_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();
		$per_page = ( $posts_per_page === false ) ? $woo_per_page : intval( $posts_per_page );
		$pagination_query = ifwoopf_pars_pagination_query_string_args();
		$paged = get_query_var( 'paged' ) ? get_query_var('paged') : 1;
		if( isset( $pagination_query['page'] ) ){
			$paged = $pagination_query['page'];
		}
		if( isset( $pagination_query['max_page'] ) ){
			$max_page = $pagination_query['max_page'];
		}
		if( isset( $pagination_query['offset'] ) ){
			$per_page = $pagination_query['offset'];
		}
		
		
		$input_html = '<input type="hidden" name="ifwoopf_page" id="ifwoopf_page" value="'.esc_attr($paged).'">';
		$max_input_html = '<input type="hidden" name="ifwoopf_max_page" id="ifwoopf_max_page" value="'.esc_attr($max_page).'">';
		$offset = '<input type="hidden" name="ifwoopf_offset" id="ifwoopf_offset" value="'.esc_attr($per_page).'">';
		$shop_columns = '<input type="hidden" name="ifwoopf_shop_columns" id="ifwoopf_shop_columns" value="'.esc_attr($number_of_columns_selection_4_shop).'">';
		
		$woo_allowed_html = array(
			'input' => array(
				'type'  => array(),
				'id'    => array(), 
				'name'  => array(),
				'value' => array(),
			),
		);

		echo wp_kses($input_html ,$woo_allowed_html );
		echo wp_kses($max_input_html ,$woo_allowed_html );
		echo wp_kses($offset ,$woo_allowed_html );
		echo wp_kses($shop_columns ,$woo_allowed_html );

	}

	/**
	 * Set numbers of products on load on shop page
	 *
	 * @since 1.0
	 * 
	 * @param object $query WP Query object
	 */
	public function ifwoopf_add_custom_products_per_page($query){

		if ( !is_admin() && $query->is_main_query() ){

			$taxonomies = ifwoopf_get_taxonomies();

			if( is_shop() || is_post_type_archive('product') || is_tax($taxonomies) ){
				
				global $woocommerce, $ifwoopf_query;

				$WC_Query = null;

				if ( isset( $woocommerce->query ) && is_a($woocommerce->query, 'WC_Query') ){
					$WC_Query = $woocommerce->query;
				}

				$pagination_query = ifwoopf_pars_pagination_query_string_args();

				if ( empty( $pagination_query ) ){

				    $posts_per_page = get_option( 'posts_per_page' ); // Define or initialize $posts_per_page

				    if ( $posts_per_page ){

				        $query->set( 'posts_per_page', $posts_per_page ); // Set posts_per_page parameter

				        if ( $posts_per_page === -1 ){
				            $query->set( 'nopaging', true );
				        }

				    }

				}

				$default_sorting = ifwoopf_get_option('default_sorting');
				if( empty( $default_sorting ) ){
					$default_sorting = ifwoopf_get_sorting_types(true);
					$default_sorting = array_keys($default_sorting)[0];
				}

				if( !empty( $default_sorting ) ){

					$sorting_arr = explode("-", $default_sorting);
					$sorting_str = $sorting_arr[0];
					$order = ( isset( $sorting_arr[1] ) && !empty( $sorting_arr[1] ) ) ? 'DESC' : 'ASC';

					if( !empty( $WC_Query ) && method_exists($WC_Query, 'get_catalog_ordering_args') ){
						$f_args = $WC_Query->get_catalog_ordering_args($sorting_str, $order);
					}else{
						$f_args = array();
					}

					$query->query_vars = wp_parse_args($f_args, $query->query_vars);

				}

				$filter_data = ifwoopf_wp_query_args();
				$filter_data = array_merge($filter_data, $pagination_query);
				
				if( !empty( $filter_data ) ){

					$parse_args = $ifwoopf_query->ifwoopf_parse_query_args($filter_data, $query);

					if ( isset( $filter_data['offset'] ) && !empty( $filter_data['offset'] ) && isset( $filter_data['page_limit'] ) && !empty( $filter_data['page_limit'] ) ) {

						$parse_args['posts_per_page'] = intval( $filter_data['offset'] ) + intval($filter_data['page_limit']);

					}

					unset($parse_args['paged']);

					$query->query_vars = $parse_args;

				}

			}

		}

	}

	/**
	 * Remove woocommerce default hooks
	 *
	 * @since 1.0
	 */
	public function ifwoopf_remove_woocommerce_default_hooks(){
	
		/* Remove woocommerce default hooks */
		remove_action('woocommerce_before_shop_loop','woocommerce_catalog_ordering', 30);
		remove_action('woocommerce_before_shop_loop','woocommerce_result_count', 20);
		remove_action('woocommerce_after_shop_loop','woocommerce_pagination', 10);		

	}

	/**
	 * This will hide woocommerce default sorting and pagination
	 *
	 * @since  1.0
	 * 
	 * @param  string $template The absolute path.
	 * @param  string $template_name Template name.
	 * @param  array  $args          Arguments. (default: array).
	 * @param  string $template_path Template path. (default: '').
	 * @param  string $default_path  Default path. (default: '').
	 * 
	 * @return string $template The absolute path.
	 */
	public function ifwoopf_hide_woo_default_shop_extras($template, $template_name, $args, $template_path, $default_path){

		$is_load_ifwoopf = $this->ifwoopf_enqueue_conditions();
		$to_hide = array('loop/orderby.php','loop/pagination.php', 'loop/result-count.php');

		if ( $is_load_ifwoopf && in_array($template_name, $to_hide) ){

			return "";

		}

		return $template;

	}

	/**
	 * Start parent for filter wraper and product listing
	 *
	 * @since 1.1
	 */
	public function ifwoopf_start_main_wraper(){

		?>
		<div class="ifwoopf-main-wrap">
		<?php

	}

	/**
	 * Over parent for filter wraper and product listing
	 *
	 * @since 1.1
	 */
	public function ifwoopf_over_main_wraper(){

		?>
		</div>
		<?php

	}

	/**
	 * Start parent for field
	 *
	 * @since 1.1
	 *
	 * @param array $field Field attributes
	 */
	public function ifwoopf_before_front_field_markup($field){

		$type = $field['type'];
		?>
		<div class="ifwoopf-field ifwoopf-field-<?php echo esc_attr($type); ?>">
		<?php

	}

	/**
	 * Over parent for field
	 *
	 * @since 1.1
	 *
	 * @param array $field Field attributes
	 */
	public function ifwoopf_after_front_field_markup($field){

		?>
		</div>
		<?php

	}

	/**
	 * Over parent for field
	 *
	 * @since 1.1
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 * @return html $output html of field.
	 */
	public function ifwoopf_after_taxonomy_front_field_markup($output, $cat, $depth, $args, $current_object_id){

		$field = $args['field_args']; 
		$cat_slug = $cat->slug;
		$all_parents = ( isset( $args['all_parents'] ) ) ? $args['all_parents'] : array();
		$btn_toggled_class = ( in_array($cat_slug, $all_parents) || $field['is_current_que_tax'] ) ? 'ifwoopf-toggled' : '';

		ob_start();
		?>
		<?php if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) { ?>
			<?php if( isset( $field['toggle'] ) && $field['toggle'] === true ){ ?>
				<i class="ifwoopf-toggle-btn fa-solid fa-circle-plus <?php echo esc_attr($btn_toggled_class); ?>"></i>
			<?php } ?>
		<?php } ?>
		<?php

		$output = $output .= ob_get_clean();

		return $output;
	}

	/**
	 * Define all hooks of front side
	 *
	 * @since 1.0
	 */
	public function add_hooks(){
		
		add_action( 'wp_enqueue_scripts', array( $this, 'ifwoopf_enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ifwoopf_enqueue_scripts' ) );
		add_filter('body_class', array($this, 'ifwoopf_add_body_classes'));

		$position = ifwoopf_get_option('display_filter_position', true);
		$enable_filter_on_shop_page = ifwoopf_get_option('enable_filter_on_shop_page', true);
		if($enable_filter_on_shop_page == 'yes') {
			if( $position === 'from_hook' ){
				add_action('woocommerce_before_shop_loop', array($this, 'ifwoopf_start_main_wraper'), 1);
				add_action('woocommerce_after_shop_loop', array($this, 'ifwoopf_over_main_wraper'), 999);
				add_action('woocommerce_before_shop_loop', array($this, 'ifwoopf_render_filter_form'));
			}
		}

		add_action('after_setup_theme', array($this, 'ifwoopf_remove_woocommerce_default_hooks'), 999);
		add_filter('wc_get_template', array( $this, 'ifwoopf_hide_woo_default_shop_extras' ), 999, 5);

		add_action('ifwoopf_before_front_field_markup', array($this, 'ifwoopf_before_front_field_markup') );
		add_action('ifwoopf_after_front_field_markup', array($this, 'ifwoopf_after_front_field_markup') );

		add_filter('ifwoopf_after_taxonomy_field_markup', array($this, 'ifwoopf_after_taxonomy_front_field_markup'), 10, 5);

		add_action('wp_ajax_ifwoopf_filter_products', array($this, 'ifwoopf_filter_products_callback'));
		add_action('wp_ajax_nopriv_ifwoopf_filter_products', array($this, 'ifwoopf_filter_products_callback'));
		add_action('woocommerce_after_shop_loop', array($this, 'ifwoopf_add_custom_input_pagination'));

		add_action('pre_get_posts', array($this, 'ifwoopf_add_custom_products_per_page'));

	}
}