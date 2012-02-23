<?php 

//parse ingredient shortcodes
function wrap_amount($atts, $content=NULL){
	return '<span class="amount">'.$content.'</span>';
}
add_shortcode('ingredient_amount','wrap_amount');

function wrap_ingredient_name($atts, $content=NULL){
	$terms = get_term_by('name',$content,'ingredients');
	//if ingredient matches ingredient tag, then convert this to link
	if ($terms) {
		return '<span class="name"><a href="/ingredients/'.$terms->slug.'">'.$content.'</a></span>';
	} else {
		return '<span class="name">'.$content.'</span>';
	}
}
add_shortcode('ingredient_name','wrap_ingredient_name');

//output ingredients
function get_ingredients() {
	global $post;
	$snippet = do_shortcode(get_post_meta($post->ID,'_recipe_ingredients',true));

	$dom = new DOMDocument();
	@$dom->loadHTML($snippet);
	$x = new DOMXPath($dom);
	foreach($x->query('//li') as $node) {
		$node->setAttribute('class','ingredient');
	}
	$output = $dom->saveHtml();
	echo $output;
}


// template tags for custom fields
function create_time($meta_field){
	global $post;
	$output='';
	if ($meta_field=='total'){
		$time = get_post_meta($post->ID,'_prep_time',true) + get_post_meta($post->ID,'_cook_time',true);
	} else {
		$time = get_post_meta($post->ID,$meta_field,true);
	}
	$h=(int)($time/60);
	$m=(int)($time - $h*60);
	
	if ($h>0) {
		$output.=$h.' hour';
		if ($h>1) {
			$output.= 's';
		}
		if ($m>0){
			$output.= ', '.$m.' minute';
			if ($m>1){
				$output.= 's';
			}
		}
	}
	else {
		$output.= $m.' minute';
		if ($m>1){
			$output.= 's';
		}
	}
	echo $output;
}

function create_hrecipe_time($meta_field){
	global $post;
	$output='PT';
	if ($meta_field=='total'){
		$time = get_post_meta($post->ID,'_prep_time',true) + get_post_meta($post->ID,'_cook_time',true);
	} else {
		$time = get_post_meta($post->ID,$meta_field,true);
	}
	$h=(int)($time/60);
	$m=(int)($time - $h*60);
	if ($h>0) {
		$output.=$h.'H';
	}
	if ($m>0){
		$output.= $m.'M';
	}
	echo $output;
}

//outputs recipe ratings if available
function get_ratings() {
	global $post;
	if(get_post_meta($post->ID,'_avg_rating',true) && get_post_meta($post->ID,'_sum_rating',true)) {
		$avg_rating = get_post_meta($post->ID,'_avg_rating',true);
		$sum_ratings = get_post_meta($post->ID,'_sum_rating',true);
		?>
		<span class="rating">
			<div class="average">Rating: <?php echo $avg_rating; ?> stars</div>
			<div class="count">Based on <?php echo $sum_ratings ; ?> reviews</div>
		</span>
		
<?php } else { ?>
		<span class="rating">
			not yet rated.
		</span>
	
<?php }
}

//get nutrition label 
function get_nutrition_label() {
	global $post;
	$serving_size = get_post_meta($post->ID,'_serving_size',true);
	$servings = get_post_meta($post->ID,'_yield',true);
	$calories = get_post_meta($post->ID,'_calories',true);
	$fat = get_post_meta($post->ID,'_fat',true);
	$sat_fat = get_post_meta($post->ID,'_sat_fat',true);
	$trans_fat = get_post_meta($post->ID,'_trans_fat',true);
	$cholesterol = get_post_meta($post->ID,'_cholesterol',true);
	$sodium = get_post_meta($post->ID,'_sodium',true);
	$carbs = get_post_meta($post->ID,'_carbs',true);
	$dietary_fiber = get_post_meta($post->ID,'_dietary_fiber',true);
	$sugars = get_post_meta($post->ID,'_sugars',true);
	$protein = get_post_meta($post->ID,'_protein',true);
	if($serving_size && $servings && $calories && $fat && $sat_fat && $trans_fat && $cholesterol && $sodium && $carbs && $dietary_fiber && $sugars && $protein){
		echo do_shortcode('[nutr-label servingsize="'.$serving_size.'" servings="'.$servings.'" calories="'.$calories.'" totalfat="'.$fat.'" satfat="'.$sat_fat.'" transfat="'.$trans_fat.'" cholesterol="'.$cholesterol.'" sodium="'.$sodium.'" carbohydrates="'.$carbs.'" fiber="'.$dietary_fiber.'" sugars="'.$sugars.'" protein="'.$protein.'"]'); 
	}
}
