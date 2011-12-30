<?php
/*
Plugin Name: Products
Description: Custom Products
*/

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
	        //'taxonomies' => array('category', 'post_tag','wp_ingredients'),
        	'rewrite' => array('slug' => 'products'),
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats')        	
		)
	);
}
add_action( 'init', 'wp_create_products' );

// create a ingredient taxonomy for product post type
/*
function wp_ingredients_init() {
	register_taxonomy(
		'ingredients',
		'products',
		array(
			'label' => __( 'Ingredients' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => 'ingredients')
		)
	);
}
*/

?>