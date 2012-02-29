<?php
/*
Plugin Name: Recipe Tips
Description: Tips custom post type
*/

// add tips custom post type
function wp_create_tips() {
	register_post_type( 
		'tips',
		array(
			'labels' => array(
				'name' => __( 'Tips' ),
				'singular_name' => __( 'Tip' ),
				'add_new_item' => __('Add New Tip'),
				'all_items' => __('All Tips'),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5, 
        	'rewrite' => array('slug' => 'tips'),
		    //'supports' => array( 'title', /*'editor',*/ 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats'),        	
		    'supports' => array('title', 'author', 'editor', 'revisions', 'page-attributes', 'post-formats'),        	
		)
	);
}
add_action( 'init', 'wp_create_tips' );

//create a tips category taxonomy for tips post type
function wp_tips_categories_init() {
	register_taxonomy(
		'tip-category',
		'tips',
		array(
			'label' => __( 'Tip Categories' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'tip-category'),
			'hierarchical' => true
		)
	);
}
add_action( 'init', 'wp_tips_categories_init' );

//create a tips tags taxonomy for recipe post type
function wp_tips_tags_init() {
	register_taxonomy(
		'tip-tags',
		'tips',
		array(
			'label' => __( 'Tip Tags' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => 'rtip-tag')
		)
	);
}
add_action( 'init', 'wp_tips_tags_init' );



//list tips widget
class List_Tips_Widget extends WP_Widget {

	function List_Tips_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'list-tips', 'description' => 'outputs all recipe tips.');

		/* Create the widget. */
		$this->WP_Widget( 'list-tips', 'List Tips', $widget_ops);
	}
	function widget($args){
		extract($args);
		global $post;
		$query = new WP_Query( array( 
			'post_type' => 'tips',			
			//'showposts' => 5,
			'orderby' => 'date',
			'order' => 'DESC'
		 ));
		 
		if($query->have_posts()):
			echo $before_widget . $before_title . 'Recipes Tips' . $after_title;
			echo '<ul>';
			while ($query->have_posts()):$query->the_post();
					echo '<li><p>';
					echo $post->post_content;
					echo '</p></li>';
			endwhile;
			echo '</ul>';
			echo $after_widget;
		endif; 
	}	

}

function load_tips_list_widgets() {
	register_widget( 'List_Tips_Widget' );
}
add_action( 'widgets_init', 'load_tips_list_widgets' );