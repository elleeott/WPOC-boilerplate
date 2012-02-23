<?php 

//define the custom fields for ratings
global $custom_comment_fields;
$custom_comment_fields = array(
	'hidden_field1' => '<input id="type" name="type" type="hidden" value="rating"/>',
	'field1' => '
	<fieldset>
	<div class="comment-form-field comment-form-rating clearfix">
	<label for="rating">Rating</label>
	<span><input id="rating-1" name="rating" type="radio" value="1"/></span>
	<span><input id="rating-2" name="rating" type="radio" value="2"/></span>
	<span><input id="rating-3" name="rating" type="radio" value="3"/></span>
	<span><input id="rating-4" name="rating" type="radio" value="4"/></span>
	<span><input id="rating-4" name="rating" type="radio" value="5"/></span>
	</div>
	</fieldset>'
);


//create additional form fields for recipe post types
function recipe_review_fields($fields) {
	 if(is_singular('recipes')){ 
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
	if(is_singular('recipes')){ 
		global $custom_comment_fields;
		foreach($custom_comment_fields as $recipe_review_fields){
			echo $recipe_review_fields."\n";
		}
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
	$comment_array = get_approved_comments($post_id); 
	$rating = 0;
	$comment_count = 0;
	if($comment_array) {
		foreach($comment_array as $comment){
			if(get_comment_meta($comment->comment_ID,'rating',true)){
				$rating = $rating + get_comment_meta($comment->comment_ID,'rating',true);
				$comment_count ++;
			}
		}
		$avg_rating = round(($rating/$comment_count)*2,0)/2;
		return array ($avg_rating, $comment_count);
	} else {
		return NULL;
	}
}

//add avg and sum value meta field on comment status change
function add_rating_meta($comment){
	$post_id=$comment->comment_post_ID;
	$avg_rating = average_rating($post_id);
	if($avg_rating){
		update_post_meta($post_id, '_avg_rating', $avg_rating[0] );			
		update_post_meta($post_id, '_sum_rating', $avg_rating[1] );			
	}
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
					echo ' stars</li>';
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
