<?php
/*
Plugin Name: comments reviews
Description: enables reviews in comments
*/


//define the custom fields
global $custom_comment_fields;
$custom_comment_fields = array(
	'hidden_field1' => '<input id="type" name="type" type="hidden" value="rating"/>',
	'field1' => '<p class="comment-form">
	<label for="rating">Rating</label>
	<input id="rating-0" name="rating" type="radio" value="0" checked="yes"/>
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
		$species = wp_filter_nohtml_kses($_POST['rating']);
		add_comment_meta($comment_id, 'rating', $species, false);
	}

}
add_action ('comment_post', 'add_recipe_comment_meta', 1);

//get average recipe rating
function average_rating() {
	global $wpdb;
	$post_id = get_the_ID();
	$average_rating = 0;	
	$counter = 0;
	$ratings = $wpdb->get_results("select {$wpdb->prefix}commentmeta.meta_value from {$wpdb->prefix}commentmeta inner join {$wpdb->prefix}comments on {$wpdb->prefix}comments.comment_id={$wpdb->prefix}commentmeta.comment_id where {$wpdb->prefix}commentmeta.meta_key='rating' and {$wpdb->prefix}comments.comment_post_id=$post_id and {$wpdb->prefix}comments.comment_approved =1");
	if ($ratings) {
		foreach ($ratings as $rating) {
			$counter = $counter +1;
			$average_rating = $average_rating + $rating->meta_value;
		} 
		return round((($average_rating/$counter)*2),0)/2;
	} else {
		return $counter;
	}
}
?>