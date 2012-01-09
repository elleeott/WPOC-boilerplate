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

/*
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


//add products to menu from opencart
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
*/

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


// add product custom post type
function wp_create_products() {
	register_post_type( 
		'products',
		array(
			'labels' => array(
				'name' => __( 'Products' ),
				'singular_name' => __( 'Product' ),
				'add_new_item' => __('Add New Product'),
				'all_items' => __('All Products'),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5, 
        	'rewrite' => array('slug' => 'products'),
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats')        	
		)
	);
}
add_action( 'init', 'wp_create_products' );

// create an product category taxonomy for products post type
function wp_product_categories_init() {
	register_taxonomy(
		'product-category',
		'products',
		array(
			'label' => __( 'Product Categories' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'product-category'),
			'hierarchical' => true
		)
	);
}
add_action( 'init', 'wp_product_categories_init' );

// create an product category taxonomy for products post type
function wp_product_tags_init() {
	register_taxonomy(
		'product-tags',
		'products',
		array(
			'label' => __( 'Product Tags' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'product-tag')
		)
	);
}
add_action( 'init', 'wp_product_tags_init' );

//sync oc products with WP db - create product custom post type for each OC product.
//TODO: move this to an options page so it doesn't run on every admin request

function load_oc_products() {
	global $wpdb;
	
	//get product list from OC
	$products = $wpdb->get_results("
		SELECT *
		FROM oc_product
		INNER JOIN oc_product_description
		ON oc_product.product_id=oc_product_description.product_id
	");
	
	//loop through results
	foreach ($products as $product) {
	
		//query to see if product is already in WP db
		$wp_products = $wpdb->get_row("
			SELECT *
			FROM $wpdb->postmeta
			WHERE meta_value = $product->product_id
			AND meta_key =  '_oc_product_id'
		");
		
		//if product doesn't exist in WP DB:
		if(empty($wp_products->meta_value)){
			$the_post = array(
				'post_title'=>$product->name,
				'post_type'=>'products',
				'post_status'=>'publish'
			);
			$oc_post_id = wp_insert_post($the_post);
			add_post_meta($oc_post_id, '_oc_product_id', $product->product_id);						
		} else {
		//if product DOES exist in the DB:
			$the_post = array(
				'post_title'=>$product->name,
				'ID'=>$wp_products->post_id
			);
			wp_update_post($the_post);
		}
		update_post_meta($wp_products->post_id, '_oc_product_price', $product->price);				
		update_post_meta($wp_products->post_id, '_oc_product_sku', $product->sku);				
	}
}
add_action( 'admin_init', 'load_oc_products' );




