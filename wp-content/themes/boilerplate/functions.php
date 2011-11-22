<?php

// add recipe custom post type
function wp_create_post_type() {
	register_post_type( 
		'wp_recipes',
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
        	'rewrite' => array('slug' => 'recipes')
		)
	);
}
add_action( 'init', 'wp_create_post_type' );

// create a ingredient taxonomy for tt_recipe post type
function wp_ingredients_init() {
	register_taxonomy(
		'wp_ingredients',
		'wp_recipes',
		array(
			'label' => __( 'Ingredients' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => 'ingredients')
		)
	);
}
add_action( 'init', 'wp_ingredients_init' );





//#############

function register_custom_menus() {
	register_nav_menus(
		array(
			'primary-nav' => 'Primary Nav',
			'secondary-nav' => 'Secondary Nav'
		)
	); 
}
add_action( 'init', 'register_custom_menus' );


?>