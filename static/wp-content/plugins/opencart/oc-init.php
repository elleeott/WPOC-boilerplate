<?php

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


// create a product category taxonomy for products post type
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


// create n product tags taxonomy for products post type
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
