<?php
/*
Plugin Name: opencart integratation
Description: This makes opencart session data available to wordpress.
*/


// Get Session Data from OpenCart
function cart_items() {
	if(!isset($_SESSION)) {
		session_start();
	}	
	$cart_items=0;
	if(isset($_SESSION['cart'])){
		foreach ($_SESSION['cart'] as $item) {
			$cart_items = $cart_items + $item;	
		}
	}
	return $cart_items;

}
add_action('init', 'cart_items');

//this function checks if we have set the $isapage variable, and if so prevents WP from sending a 404
//required to make wordpress work with OpenCart
function ssp_status_filter($s) {
	global $isapage;
	if($isapage && strpos($s, "404"))
		return false;	//don't send the 404
	else
		return $s;
}
add_filter('status_header', 'ssp_status_filter');

//get catalog data from opencart
function get_oc_categories($items, $args) {
    if( $args->theme_location == 'primary-nav' ){
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_category_description
			INNER JOIN oc_category on (oc_category.category_id = oc_category_description.category_id)
			WHERE oc_category.top = 1
			ORDER BY name
		");
		$cat_list = '<li><a href="/store/">Categories</a><ul>';
		foreach ($results as $result) {
			$cat_list .= '<li><a href="/store/?route=product/category&path='.$result->category_id.'">'.$result->name.'</a></li>';
		}
		$cat_list .='</ul></li>';
		return $items . $cat_list;
	} else {
		return $items;
	}
}
add_filter('wp_nav_menu_items','get_oc_categories',10,2);


//get products menu from opencart
function get_oc_products($items, $args) {
    if( $args->theme_location == 'primary-nav' ){
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_product_description
			ORDER BY name
		");
		$prod_list = '<li><a href="/store/">Products</a><ul>';
		foreach ($results as $result) {
			$prod_list .= '<li><a href="/store/?route=product/product&product_id='.$result->product_id.'">'.$result->name.'</a></li>';
		}
		$prod_list .='<ul></li>';
		return $items . $prod_list;
	} else {
		return $items;
	}
}
add_filter('wp_nav_menu_items','get_oc_products',10,2);


//product sidebar widget

class OC_Products_Widget extends WP_Widget {

	function OC_Products_Widget() {
		$widget_ops = array( 'classname' => 'oc-products', 'description' => 'Displays products listed in the OpenCart database');

		$this->WP_Widget( 'oc-products', 'OpenCart Products', $widget_ops);
	}
	function widget($args){
		extract( $args );
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_product_description
			ORDER BY name
		");

		if($results):
			echo $before_widget . $before_title . 'Products' . $after_title;
			echo '<ul>';
			foreach ($results as $result) {
				echo '<li><a href="/store/?route=product/product&product_id='.$result->product_id.'">'.$result->name.'</a></li>';				
			}
			echo '</ul>';
			echo $after_widget;
		endif; 
	}	
}

function load_oc_products_widgets() {
	register_widget( 'OC_Products_Widget' );
}
add_action( 'widgets_init', 'load_oc_products_widgets' );