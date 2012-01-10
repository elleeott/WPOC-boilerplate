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
        	'rewrite' => array('slug' => 'recipes'),
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats'),        	
		)
	);
}
add_action( 'init', 'wp_create_recipes' );


//create an ingredient taxonomy for recipe post type
function wp_ingredients_init() {
	register_taxonomy(
		'ingredients',
		'recipes',
		array(
			'label' => __( 'Recipe Ingredients' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array( 'slug' => 'ingredients')
		)
	);
}
add_action( 'init', 'wp_ingredients_init' );

//create a recipe category taxonomy for recipe post type
function wp_recipe_categories_init() {
	register_taxonomy(
		'recipe-category',
		'recipes',
		array(
			'label' => __( 'Recipe Categories' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'recipe-category'),
			'hierarchical' => true
		)
	);
}
add_action( 'init', 'wp_recipe_categories_init' );

//create a recipe tags taxonomy for recipe post type
function wp_recipe_tags_init() {
	register_taxonomy(
		'recipe-tags',
		'recipes',
		array(
			'label' => __( 'Recipe Tags' ),
			'sort' => true,
			'args' => array( 'orderby' => 'term_order' ),
			'rewrite' => array( 'slug' => 'recipe-tag')
		)
	);
}
add_action( 'init', 'wp_recipe_tags_init' );


//add directions meta box to admin screen
function add_directions_meta_box() {
	add_meta_box(
		'directions_meta',
		'Directions',
		'recipe_directions_init',
		'recipes',
		'normal',
		'high'
	);
}

add_action('admin_init','add_directions_meta_box');


//add visual editor capabilites for recipe directions
function tinyMCE_directions_editor() {

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		tinyMCE.execCommand("mceAddControl", false, "recipe-directions");
	});
</script>
<?php
}

add_action('admin_head', 'tinyMCE_directions_editor');







//add custom tinymce button to tag recipe ingredients
function add_ingredient_button($buttons) {
    array_push($buttons,'ingredients');
    return $buttons;
}
add_filter('mce_buttons', 'add_ingredient_button');

function ingredients_register($plugin_array) {
    $plugin_array['ingredients'] = plugins_url('tiny-plugin/ingredients.js');
    return $plugin_array;
}
add_filter('mce_external_plugins', 'ingredients_register');








//render the directions field
function recipe_directions_init($post) {
?>
<style>
	#recipe-directions {
		width:98%;
		height:10em;
	}
	#directions_meta {
		background:#fff;
	}
	#directions_meta .inside {
		margin:0;
		padding:0;
	}
</style>
<?php
    wp_nonce_field( 'recipe_directions_nonce_action', 'recipe_directions_nonce' ); 
	$values = get_post_custom($post->ID);
	if(isset($values['_recipe-directions'])){
		$directions = esc_attr($values['_recipe-directions'][0]);
	} else {
		$directions = '';
	}
	echo '<textarea type="text" id="recipe-directions" class="mceEditor" name="_recipe-directions" cols="40" rows="4">'.$directions.'</textarea>';
}

//save additional content to the db
function save_recipe_postdata($post_id) {
	
	//exit function if doing autosave or security doesn't pass muster
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if(!isset($_POST['recipe_directions_nonce']) || !wp_verify_nonce( $_POST['recipe_directions_nonce'], 'recipe_directions_nonce_action' )) return;  
    if(!current_user_can('edit_post' )) return;
    
    //define allowed html elements
    $allowed = array(
    	'a'=> array(
    		'href'=>array(),
    		'title'=>array(),
    		'rel'=>array()    		
    	),
    	'ol'=> array(
    		'class'=> array(),
    		'id'=> array()
    	),
    	'li'=>array(
    		'class'=>array(),
    		'id'=>array()
    	),
    	'br'=>array(),
    	'p'=>array(),
    	'strong'=>array(),
    	'em'=>array(),
    	'span'=>array(
    		'class'=>array(),
    		'id'=>array()
    	),    	
    	'div'=>array(
    		'class'=>array(),
    		'id'=>array()
    	)   	
    );
	//insert to the db
	if(isset($_POST['_recipe-directions'])) {
		update_post_meta($post_id,'_recipe-directions',wp_kses($_POST['_recipe-directions'],$allowed),$directions);
	}	
}

add_action( 'save_post', 'save_recipe_postdata' );



//define the custom fields for ratings
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

