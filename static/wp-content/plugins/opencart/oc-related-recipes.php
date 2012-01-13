<?php

//get recipes related to product
function get_related_recipes(){
	global $post;
	//$term = $post->post_name;
	$term='red-beans';
	//echo $term;
	$args = array(
		'tax_query'=> array(
			array(
				'taxonomy'=>'ingredients',
				'field'=>'slug',
				'terms'=> $term
			)
		)
	);
	$args = array('ingredients'=>$term);
	//print_r($args);
	$query = new WP_Query($args);
	if($query->have_posts()){
		while ($query->have_posts()) {
			$query->the_post();
			echo '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
		} 	
	} else {
		echo 'whups';
	}
}
