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


//add custom tinymce button to tag recipe ingredients

function ingredients_register($plugin_array) {
    $plugin_array['ingredient_name'] = plugins_url('recipes/tiny-plugins/ingredient_name.js');
    $plugin_array['ingredient_amount'] = plugins_url('recipes/tiny-plugins/ingredient_amount.js');
    return $plugin_array;
}

function tiny_addbuttons() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		return;
	}
	if ( get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'ingredients_register');
	}
}

add_action('init', 'tiny_addbuttons');


//render the directions field
function recipe_directions_init($post) {
?>
<style>

	#directions_meta .mceEditor {
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
	.additional-meta-fields span{
		color:#666;
	}
	.recipe-meta-info {
		float:left;
	}
	.recipe-nutritional-info {
		float:right;
	}
</style>
<?php
    wp_nonce_field( 'recipe_directions_nonce_action', 'recipe_directions_nonce' ); 
	$values = get_post_custom($post->ID);
	if(isset($values['_recipe_directions'])){
		$directions = html_entity_decode($values['_recipe_directions'][0]);
	} else {
		$directions = '';
	}
	if(isset($values['_recipe_ingredients'])){
		$ingredients = html_entity_decode($values['_recipe_ingredients'][0]);
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
	if(isset($values['_sat_fat'])){
		$sat_fat = esc_attr($values['_sat_fat'][0]);
	} else {
		$sat_fat = '';
	}
	if(isset($values['_trans_fat'])){
		$trans_fat = esc_attr($values['_trans_fat'][0]);
	} else {
		$trans_fat = '';
	}
	if(isset($values['_cholesterol'])){
		$cholesterol = esc_attr($values['_cholesterol'][0]);
	} else {
		$cholesterol = '';
	}
	if(isset($values['_sodium'])){
		$sodium = esc_attr($values['_sodium'][0]);
	} else {
		$sodium = '';
	}
	if(isset($values['_carbs'])){
		$carbs = esc_attr($values['_carbs'][0]);
	} else {
		$carbs = '';
	}
	if(isset($values['_dietary_fiber'])){
		$dietary_fiber = esc_attr($values['_dietary_fiber'][0]);
	} else {
		$dietary_fiber = '';
	}
	if(isset($values['_sugars'])){
		$sugars = esc_attr($values['_sugars'][0]);
	} else {
		$sugars = '';
	}
	if(isset($values['_protein'])){
		$protein = esc_attr($values['_protein'][0]);
	} else {
		$protein = '';
	}

	echo '<strong>Ingredients</strong><br/>';
	//echo '<div><textarea id="recipe-ingredients" class="mceEditor" name="_recipe_ingredients" cols="40" rows="4">'.$ingredients.'</textarea></div>';
	$ing_settings = array(
		'wpautop' => false,
		'media_buttons' => false,
		'textarea_name' => '_recipe_ingredients',
		'tinymce' => array(
			'theme_advanced_buttons1'=>'undo,redo,bullist,ingredient_amount,ingredient_name,link,unlink'
		)
	);
	wp_editor($ingredients,'recipeingredients',$ing_settings);
	echo '<p></p><strong>Directions</strong><br/>';
	//echo '<div><textarea id="recipe-directions" class="mceEditor" name="_recipe_directions" cols="40" rows="4">'.$directions.'</textarea></div>';
	$dir_settings = array(
		'wpautop' => false,
		'media_buttons' => false,
		'textarea_name' => '_recipe_directions',
		'tinymce' => array(
			'theme_advanced_buttons1'=>'numlist,bold,italic,link,unlink,sub,sup,undo,redo'
		)
	);
	wp_editor($directions,'recipedirections',$dir_settings);
	echo '<div class="additional-meta-fields">';
	echo '<div class="recipe-meta-info">';
	echo '<strong>Recipe Meta Information</strong><br/>';
	echo '<div><label for="prep-time">Prep Time:</label><input type="text" id="prep-time" name="_prep_time" value="'.$prep_time.'"/> <span>minutes</span></div>';
	echo '<div><label for="cook-time">Cook Time:</label><input type="text" id="cook-time" name="_cook_time" value="'.$cook_time.'"/> <span>minutes</span></div>';
	echo '<div><label for="yield">Yield:</label><input type="text" id="yield" name="_yield" value="'.$yield.'"/> <span>servings</span></div>';
	echo '<div><label for="serving-size">Serving Size:</label><input type="text" id="serving-size" name="_serving_size" value="'.$serving_size.'"/></div>';
	echo '</div>';

	echo '<div class="recipe-nutritional-info">';
	echo '<strong>Nutritional Information</strong><br/>';
	echo '<div><label for="calories">Calories per Serving:</label><input type="text" id="calories" name="_calories" value="'.$calories.'"/> <span>calories</span></div>';
	echo '<div><label for="fat">Total Fat:</label><input type="text" id="fat" name="_fat" value="'.$fat.'"/> <span>grams</span></div>';
	echo '<div><label for="sat-fat">Saturated Fat:</label><input type="text" id="sat-fat" name="_sat_fat" value="'.$sat_fat.'"/> <span>grams</span></div>';
	echo '<div><label for="trans-fat">Trans Fat:</label><input type="text" id="trans-fat" name="_trans_fat" value="'.$trans_fat.'"/> <span>grams</span></div>';
	echo '<div><label for="cholesterol">Cholesterol:</label><input type="text" id="cholesterol" name="_cholesterol" value="'.$cholesterol.'"/> <span>milligrams</span></div>';
	echo '<div><label for="sodium">Sodium:</label><input type="text" id="sodium" name="_sodium" value="'.$sodium.'"/> <span>milligrams</span></div>';
	echo '<div><label for="carbs">Carbohydrates:</label><input type="text" id="carbs" name="_carbs" value="'.$carbs.'"/> <span>grams</span></div>';
	echo '<div><label for="dietary-fiber">Dietary Fiber:</label><input type="text" id="dietary-fiber" name="_dietary_fiber" value="'.$dietary_fiber.'"/> <span>grams</span></div>';
	echo '<div><label for="sugars">Sugars:</label><input type="text" id="sugars" name="_sugars" value="'.$sugars.'"/> <span>grams</span></div>';
	echo '<div><label for="protein">Protein:</label><input type="text" id="protein" name="_protein" value="'.$protein.'"/> <span>grams</span></div>';
	echo '</div>';
	echo '<div style="clear:both;"></div>';
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
	if(isset($_POST['_sat_fat'])) {
		update_post_meta($post_id,'_sat_fat',wp_kses($_POST['_sat_fat'],$allowed));
	}
	if(isset($_POST['_trans_fat'])) {
		update_post_meta($post_id,'_trans_fat',wp_kses($_POST['_trans_fat'],$allowed));
	}
	if(isset($_POST['_cholesterol'])) {
		update_post_meta($post_id,'_cholesterol',wp_kses($_POST['_cholesterol'],$allowed));
	}
	if(isset($_POST['_sodium'])) {
		update_post_meta($post_id,'_sodium',wp_kses($_POST['_sodium'],$allowed));
	}
	if(isset($_POST['_carbs'])) {
		update_post_meta($post_id,'_carbs',wp_kses($_POST['_carbs'],$allowed));
	}
	if(isset($_POST['_dietary_fiber'])) {
		update_post_meta($post_id,'_dietary_fiber',wp_kses($_POST['_dietary_fiber'],$allowed));
	}
	if(isset($_POST['_sugars'])) {
		update_post_meta($post_id,'_sugars',wp_kses($_POST['_sugars'],$allowed));
	}
	if(isset($_POST['_protein'])) {
		update_post_meta($post_id,'_protein',wp_kses($_POST['_protein'],$allowed));
	}
	
	if(isset($_POST['_recipe_ingredients'])) {
		update_post_meta($post_id,'_recipe_ingredients',wp_kses($_POST['_recipe_ingredients'],$allowed));
		
		//this seems too complicated.  let's just have users manually enter the relevant ingredients 'tags'.
		/*
		$pattern = '#\[ingredient_name\](.+)\[\/ingredient_name\]#';
		preg_match_all($pattern,$_POST['_recipe_ingredients'],$matches);
		wp_set_post_terms($post_id,$matches[1],'ingredients');
		*/
		
	}
}
add_action( 'save_post', 'save_recipe_postdata' );