<?php

/**
 * This class manage filter query.
 *
 * @since 1.0 
 * 
 * @package iFlair_Woo_Product_Filters
 * @subpackage iFlair_Woo_Product_Filters/includes 
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Iflair_Woo_Product_Filters_Query {

	/**
	 * The wp query args for filter products
	 *
	 * @since 1.0
	 * 
	 * @var array $args wp query args
	 */
	public $args;

	/**
	 * The filter post data on submit filter form
	 *
	 * @since 1.0
	 * 
	 * @var array $filter_data post data
	 */
	public $filter_data;

	/**
	 * The filter wp query
	 *
	 * @since 1.0
	 * 
	 * @var object $filter_query wp query
	 */
	public $filter_query;

	/**
	 * The WC query
	 *
	 * @since 1.0
	 * 
	 * @var object $WC_Query WC query
	 */
	public $WC_Query;

	/**
	 * Check for is wc query is init
	 *
	 * @since 1.0
	 * 
	 * @var boolen $is_WC_Query_init 
	 */
	public $is_WC_Query_init;

	public function __construct(){
		
		$this->args = array();
		$this->filter_data = array();
		$this->filter_query = array();
		$this->WC_Query = array();
		$this->is_WC_Query_init = false;
	}

	/**
	 * This will parse qurey for filter products
	 * 
	 * @since 1.0
	 * 
	 * @param  array  $data filter data
	 * @param  object $query Product shop page query
	 * 
	 * @return array  $args WP Query args
	 */
	public function ifwoopf_parse_query_args($data=array(), $query=null){

		global $woocommerce;

		$WC_Query = null;

		if( empty( $query ) ){
			$pro_query = new WP_Query(array('post_type'=>'product'));
		}else{
			$pro_query = $query;
		}

		if ( isset( $woocommerce->query ) && is_a($woocommerce->query, 'WC_Query') ){
			$WC_Query = $woocommerce->query;
		}

		if( empty( $query ) && !empty( $WC_Query ) && method_exists($WC_Query, 'product_query') && method_exists($WC_Query, 'get_main_query') ){

			$WC_Query->product_query($pro_query);
			$pro_query = $WC_Query::get_main_query();
			$this->is_WC_Query_init = true;

		}

		$args = $pro_query->query_vars;
		$this->filter_data = $data;
		$this->args = $args;
		$this->filter_query = $pro_query;
		$this->WC_Query = $WC_Query;
		
		$this->ifwoopf_parse_query_taxonomy();
		$this->ifwoopf_parse_query_orderby();
		$this->ifwoopf_parse_query_paged();
		$this->ifwoopf_parse_query_search();

		$args['fields'] = 'ids';
		
		return $this->args;

	}

	/**
	 * Parse taxonomy query depending on filter data
	 *
	 * @since 1.0
	 * 
	 * @return array $args wp query args
	 */
	public function ifwoopf_parse_query_taxonomy(){

		if ( isset( $this->filter_data['taxonomy'] ) && !empty( $this->filter_data['taxonomy'] ) ) {

			$tax_arr = array();
			$tax_arr['relation'] = 'AND';
			foreach ($this->filter_data['taxonomy'] as $tax_name => $terms) {

				if( is_array( $terms ) ){
					$terms = array_filter($terms);
				}
				
				if ( !empty( $terms ) ){

					$tax_arr[] = array(
						'taxonomy' => $tax_name,
			            'field'    => 'term_id',
			            'terms'    => $terms,
			            'operator' => 'IN',
					);

				}

			}

			if ( !empty( $tax_arr ) ) {
				
				if ( isset( $this->args['tax_query'] ) && !empty( $this->args['tax_query'] ) ) {
					$this->args['tax_query'] = array_merge($this->args['tax_query'], $tax_arr);
				}else{
					$this->args['tax_query'] = $tax_arr;
				}

			}

		}

	}

	/**
	 * Parse order and orderby query depending on filter data
	 *
	 * @since 1.0
	 * 
	 * @return array $args wp query args
	 */
	public function ifwoopf_parse_query_orderby(){

		if( isset( $this->filter_data['sorting'] ) && !empty( $this->filter_data['sorting'] ) ){
			$sorting = sanitize_text_field($this->filter_data['sorting']);
		}else{
			$default_sorting = ifwoopf_get_sorting_types(true);
			$sorting = array_keys($default_sorting)[0];
		}

		$sorting_arr = explode("-", $sorting);
		$sorting_str = $sorting_arr[0];
		$order = ( isset( $sorting_arr[1] ) && !empty( $sorting_arr[1] ) ) ? 'DESC' : 'ASC';

		if( !empty( $this->WC_Query ) && method_exists($this->WC_Query, 'get_catalog_ordering_args') ){

			$f_args = $this->WC_Query->get_catalog_ordering_args($sorting_str, $order);
		}else{
			$f_args = array();
		}

		$this->args = wp_parse_args($f_args, $this->args);

	}

	/**
	 * Parse paged query depending on filter data
	 *
	 * @since 1.0
	 * 
	 * @return array $args wp query args
	 */
	public function ifwoopf_parse_query_paged(){

		if( !empty( $this->WC_Query ) && method_exists($this->WC_Query, 'get_main_query') ){
			$product_query = $this->WC_Query::get_main_query();
		}else{
			$product_query = $this->filter_query;
		}

		if ( isset( $this->filter_data['page'] ) && !empty( $this->filter_data['page'] ) ){
			$get_paged = intval( $this->filter_data['page'] );
			$paged = intval( $this->filter_data['page'] );
		}else{	
			$get_paged = intval( $product_query->get('paged') );
			$paged = intval( $product_query->get('paged') );
		}

		if ( empty( $paged ) ){
			$paged = 1;
		}

		if ( isset( $this->filter_data['ifwoopf_load_more'] ) ){
			$paged++;
		}

		$num_pro = ifwoopf_get_option('number_products_on_load_more', true);
		$posts_per_page = ifwoopf_get_option('number_products_on_load', true);
		$woo_per_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();
		$per_page = ( $posts_per_page === false ) ? $woo_per_page : intval( $posts_per_page );
		
		if ( $num_pro && isset( $this->filter_data['ifwoopf_load_more'] ) ){

			$num_pro = intval( $num_pro );
			$get_offset = ( !empty( $this->filter_data['offset'] ) ) ? $this->filter_data['offset'] : $per_page;
			$offset = ( $get_paged > 1 ) ? intval( $get_offset ) + $num_pro : intval( $get_offset );
			$per_page = $num_pro;
			$this->args['offset'] = $offset;

		}

		if ( $per_page === -1 ){
			$this->args['nopaging'] = true;
		}

		$this->args['posts_per_page'] = $per_page;
		$this->args['paged'] = $paged;

	}

	/**
	 * Parse search query depending on filter data
	 *
	 * @since 1.0
	 * 
	 * @return array $args wp query args
	 */
	public function ifwoopf_parse_query_search(){

		if ( isset( $this->filter_data['ifwoopf_search'] ) && !empty( $this->filter_data['ifwoopf_search'] ) ){
			$this->args['s'] = sanitize_text_field($this->filter_data['ifwoopf_search']);
		}

	}

}