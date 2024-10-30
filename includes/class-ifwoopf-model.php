<?php

/**
 * This class manage all lgical functionality.
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

class Iflair_Woo_Product_Filters_Model {

	/**
	 * The instance of query class
	 * 
	 * @since 1.0
	 * 
	 * @var instance 	$query 	instance of query class
	 */
	public $query;

	public function __construct(){

		global $ifwoopf_query;
		$this->query = $ifwoopf_query;

	}

	/**
	 * This will parse qurey for filter products
	 * 
	 * @since 1.0
	 * 
	 * @param  array  $data filter data
	 * @return array  $args WP Query args
	 */
	public function ifwoopf_parse_query_args($data=array()){

		$args = $this->query->ifwoopf_parse_query_args($data);
		return $args;

	}

	/**
	 * This will return html of product loop
	 * 
	 * @since 1.0
	 * 
	 * @param  array  $args WP Query args
	 * @return array  $return return html of products
	 */
	public function ifwoopf_markup_products($args=array(), $filter_data = array()){
		
		if ( empty( $args ) ) {
			return array();
		}

		$return = array();
		$products = new WP_Query($args);

		$get_count = $this->ifwoopf_markup_result_count($products);

		ob_start();

		if ( $products->have_posts() ) {
			while ( $products->have_posts() ) {
				$products->the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();

		} else {

			if ( !isset( $filter_data['ifwoopf_load_more'] ) ){
				ifwoopf_get_template('ifwoopf-no-data-found.php');
			}
			
		}

		$html = ob_get_clean();

		$return['html'] = $html;
		$return['result_count'] = $get_count;
		$return['query'] = $products;

		return $return;

	}

	/**
	 * Get markup html of result count on product loop
	 * 
	 * @since 1.0
	 * 
	 * @param object $query WP Query object
	 * @return html of woo product loop count
	 */
	public function ifwoopf_markup_result_count($query){
		
		ob_start();
		
		$args = array(
			'total'    => $query->found_posts,
			'per_page' => $query->get( 'posts_per_page' ),
			'current'  => $query->get( 'paged' ),
		);

		wc_get_template( 'loop/result-count.php', $args );
		return ob_get_clean();

	}

}