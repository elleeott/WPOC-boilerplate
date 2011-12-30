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
add_action( 'init', 'wp_ingredients_init' );


//define the custom fields
global $custom_comment_fields;
$custom_comment_fields = array(
	'hidden_field1' => '<input id="type" name="type" type="hidden" value="rating"/>',
	'field1' => '<p class="comment-form">
	<label for="rating">Rating</label>
	<input id="rating-1" name="rating" type="radio" value="1"/>
	<input id="rating-2" name="rating" type="radio" value="2"/>
	<input id="rating-3" name="rating" type="radio" value="3"/>
	<input id="rating-4" name="rating" type="radio" value="4"/>
	<input id="rating-4" name="rating" type="radio" value="5"/>
	</p>'
);


//create additional form fields for recipe post types
function recipe_review_fields($fields) {
	if(is_singular( 'recipes' )){
		global $custom_comment_fields;
		foreach($custom_comment_fields as $custom_comment_fields_key => $custom_comment_fields_html){
			$fields[$custom_comment_fields_key] = $custom_comment_fields_html;
		}
	}
	return $fields;
}
add_filter( 'comment_form_default_fields', 'recipe_review_fields' );


//show additional fields for logged in users
function recipe_review_fields_logged_in($commenter,$user_identity){
	global $custom_comment_fields;
	foreach($custom_comment_fields as $recipe_review_fields){
		echo $recipe_review_fields."\n";
	}
}
add_action( 'comment_form_logged_in_after', 'recipe_review_fields_logged_in',10,2);


//save rating to the db
function add_recipe_comment_meta($comment_id) {
	if(isset($_POST['type'])){
		$type = wp_filter_nohtml_kses($_POST['type']);
		add_comment_meta($comment_id, 'comment-type', $type, false);
	}
	if(isset($_POST['rating'])){
		$rating = wp_filter_nohtml_kses($_POST['rating']);
		add_comment_meta($comment_id, 'rating', $rating, false);
	}

}
add_action ('comment_post', 'add_recipe_comment_meta', 1);


//get average recipe rating

function average_rating($post_id) {
	$all_comments = get_comment_count($post_id);
	$comment_count = $all_comments['approved'];
	$comment_array = get_approved_comments($post_id); 
	$rating = 0;
	if($comment_array) {
		foreach($comment_array as $comment){
			$rating = $rating + get_comment_meta($comment->comment_ID,'rating',true);
		}
		return round(($rating/$comment_count)*2,0)/2;
	} else {
		return NULL;
	}
}

//add avg value meta field on comment status change
function add_rating_meta($comment){
	$post_id=$comment->comment_post_ID;
	$avg_rating = average_rating($post_id);
	update_post_meta($post_id, '_avg_rating', $avg_rating );			
}
add_action('comment_unapproved_to_approved', 'add_rating_meta');
add_action('comment_approved_to_unapproved', 'add_rating_meta');
add_action('trashed_comment', 'add_rating_meta');
add_action('deleted_comment', 'add_rating_meta');


//highest ranked recipe sidebar widget
class Ranked_Recipes_Widget extends WP_Widget {

	function Ranked_Recipes_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'ranked-recipes', 'description' => 'Displays the highest ranked recipes.');

		/* Create the widget. */
		$this->WP_Widget( 'ranked-recipes', 'Ranked Recipes', $widget_ops);
	}
	function widget($args){
		extract( $args );
		global $post;
		$query = new WP_Query( array( 
			'post_type' => 'recipes',			
			'orderby' => 'meta_value',
			'meta_key' => '_avg_rating',
			'showposts' => 5,
			'order' => 'DESC'
		 ));
		 
		if($query->have_posts()):
			echo $before_widget . $before_title . 'Top Recipes' . $after_title;
			echo '<ul>';
			while ($query->have_posts()):$query->the_post();
				if(get_post_meta($post->ID,'_avg_rating',true)) : 
					echo '<li>';
					echo '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a> ';
					echo get_post_meta($post->ID,'_avg_rating',true);
					echo '</li>';
				endif; 
			endwhile;
			echo '</ul>';
			echo $after_widget;
		endif; 
	}	
}

function load_ranked_recipe_widgets() {
	register_widget( 'Ranked_Recipes_Widget' );
}
add_action( 'widgets_init', 'load_ranked_recipe_widgets' );

