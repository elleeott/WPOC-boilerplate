<?php

//additional meta boxes for admin screen
function additional_meta_boxes() {
	add_meta_box(
		'directions_meta',
		'Recipe Information',
		'recipe_directions_init',
		'recipes',
		'normal',
		'high'
	);
}

add_action('admin_init','additional_meta_boxes');


//render the directions field
function recipe_directions_init($post) {
?>
<style>
	#recipe-directions, #recipe-ingredients {
		width:98%;
		height:10em;
	}
	#directions_meta {
	}
	#directions_meta .inside {

	}
	#directions_meta .mceEditor {
		border:1px solid #ccc;
		display:block;
		background:#fff;
	}
	.additional-meta-fields {
		margin-top:20px;
	}
	.additional-meta-fields label{
		display:inline-block;
		width:150px;
	}
</style>
<?php
    wp_nonce_field( 'recipe_directions_nonce_action', 'recipe_directions_nonce' ); 
	$values = get_post_custom($post->ID);
	if(isset($values['_recipe_directions'])){
		$directions = esc_attr($values['_recipe_directions'][0]);
	} else {
		$directions = '';
	}
	if(isset($values['_recipe_ingredients'])){
		$ingredients = esc_attr($values['_recipe_ingredients'][0]);
	} else {
		$ingredients = '';
	}
	if(isset($values['_prep_time'])){
		$prep_time = esc_attr($values['_prep_time'][0]);
	} else {
		$prep_time = '';
	}
	if(isset($values['_cook_time'])){
		$cook_time = esc_attr($values['_cook_time'][0]);
	} else {
		$cook_time = '';
	}
	if(isset($values['_yield'])){
		$yield = esc_attr($values['_yield'][0]);
	} else {
		$yield = '';
	}
	if(isset($values['_serving_size'])){
		$serving_size = esc_attr($values['_serving_size'][0]);
	} else {
		$serving_size = '';
	}
	if(isset($values['_calories'])){
		$calories = esc_attr($values['_calories'][0]);
	} else {
		$calories = '';
	}
	if(isset($values['_fat'])){
		$fat = esc_attr($values['_fat'][0]);
	} else {
		$fat = '';
	}
	echo '<p><strong>Ingredients</strong></p>';
	echo '<div><textarea id="recipe-ingredients" class="mceEditor" name="_recipe_ingredients" cols="40" rows="4">'.$ingredients.'</textarea></div>';
	echo '<p><strong>Directions</strong></p>';
	echo '<div><textarea id="recipe-directions" class="mceEditor" name="_recipe_directions" cols="40" rows="4">'.$directions.'</textarea></div>';
	echo '<div class="additional-meta-fields">';
	echo '<div><label for="prep-time">Prep Time:</label><input type="text" id="prep-time" name="_prep_time" value="'.$prep_time.'"/></div>';
	echo '<div><label for="cook-time">Cook Time:</label><input type="text" id="cook-time" name="_cook_time" value="'.$cook_time.'"/></div>';
	echo '<div><label for="yield">Yield:</label><input type="text" id="yield" name="_yield" value="'.$yield.'"/></div>';
	echo '<div><label for="serving-size">Serving Size:</label><input type="text" id="serving-size" name="_serving_size" value="'.$serving_size.'"/></div>';
	echo '<div><label for="calories">Calories per Serving:</label><input type="text" id="calories" name="_calories" value="'.$calories.'"/></div>';
	echo '<div><label for="fat">Fat per Serving:</label><input type="text" id="fat" name="_fat" value="'.$fat.'"/></div>';
	echo '</div>';
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
    	'ul'=> array(
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
	if(isset($_POST['_recipe_directions'])) {
		update_post_meta($post_id,'_recipe_directions',wp_kses($_POST['_recipe_directions'],$allowed));
	}
	if(isset($_POST['_cook_time'])) {
		update_post_meta($post_id,'_cook_time',wp_kses($_POST['_cook_time'],$allowed));
	}
	if(isset($_POST['_prep_time'])) {
		update_post_meta($post_id,'_prep_time',wp_kses($_POST['_prep_time'],$allowed));
	}
	if(isset($_POST['_yield'])) {
		update_post_meta($post_id,'_yield',wp_kses($_POST['_yield'],$allowed));
	}
	if(isset($_POST['_serving_size'])) {
		update_post_meta($post_id,'_serving_size',wp_kses($_POST['_serving_size'],$allowed));
	}
	if(isset($_POST['_calories'])) {
		update_post_meta($post_id,'_calories',wp_kses($_POST['_calories'],$allowed));
	}
	if(isset($_POST['_fat'])) {
		update_post_meta($post_id,'_fat',wp_kses($_POST['_fat'],$allowed));
	}
	if(isset($_POST['_recipe_ingredients'])) {
		update_post_meta($post_id,'_recipe_ingredients',wp_kses($_POST['_recipe_ingredients'],$allowed));
		$pattern = '#\[ingredient_name\](.+)\[\/ingredient_name\]#';
		preg_match_all($pattern,$_POST['_recipe_ingredients'],$matches);
		wp_set_post_terms($post_id,$matches[1],'ingredients');
	}
}
add_action( 'save_post', 'save_recipe_postdata' );


//add visual editor capabilites for recipe directions
function tinyMCE_directions_editor() {

?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		/*tinyMCE.execCommand("mceAddControl", false, "recipe-directions");
		tinyMCE.execCommand("mceAddControl", false, "recipe-ingredients");
		*/
	});
</script>
<?php
}

add_action('admin_head', 'tinyMCE_directions_editor');


//add custom tinymce button to tag recipe ingredients
function add_ingredient_buttons($buttons) {
    array_push($buttons,'ingredient_name','ingredient_amount');
    return $buttons;
}
add_filter('mce_buttons', 'add_ingredient_buttons');

function ingredients_register($plugin_array) {
    $plugin_array['ingredient_name'] = plugins_url('recipes/tiny-plugins/ingredient_name.js');
    $plugin_array['ingredient_amount'] = plugins_url('recipes/tiny-plugins/ingredient_amount.js');
    return $plugin_array;
}
add_filter('mce_external_plugins', 'ingredients_register');
