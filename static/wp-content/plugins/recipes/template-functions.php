<?php 

//parse ingredient shortcodes
function wrap_amount($atts, $content=NULL){
	return '<span class="amount">'.$content.'</span>';
}
add_shortcode('ingredient_amount','wrap_amount');

function wrap_ingredient_name($atts, $content=NULL){
	//global $post;
	$terms = get_term_by('name',$content,'ingredients');
	return '<span class="name"><a href="/ingredients/'.$terms->slug.'">'.$content.'</a></span>';
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
