<?php
/*
Plugin Name: Recipes
Description: Custom Recipes
*/

// add recipe custom post type
function wp_create_recipes() {
	register_post_type( 
		'recipes',
		array(
			'labels' => array(
				'name' => __( 'Recipes' ),
				'singular_name' => __( 'Recipe' ),
				'add_new_item' => __('Add New Recipe'),
				'all_items' => __('All Recipes'),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5, 
	        //'taxonomies' => array('category', 'post_tag','wp_ingredients'),
        	'rewrite' => array('slug' => 'recipes'),
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats'),        	
		)
	);
}
add_action( 'init', 'wp_create_recipes' );


// create an ingredient taxonomy for recipe post type
function wp_ingredients_init() {
	register_taxonomy(
		'ingredients',
		'recipes',
		array(
			'label' => __( 'Ingredients' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => 'ingredients')
		)
	);
}

?>