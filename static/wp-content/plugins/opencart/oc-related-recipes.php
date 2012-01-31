<?php

//get recipes related to product
function get_related_recipes(){
	global $post;
	$terms = get_the_terms($post->ID,'product-tags');
	
	$result=array();
	foreach($terms as $term) {
		array_push($result,$term->name);
	}

	$args = array(
		'tax_query'=>array(
			array(
				'taxonomy' => 'ingredients',
				'field' => 'slug',
				'terms' => $result
			)
		)
	);
	
	$query = new WP_Query($args);
		if($query->have_posts()){
			echo '<ul>';
			while ($query->have_posts()) {
				$query->the_post();
				echo '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
			} 	
			echo '<ul>';
		} else {
			echo 'no related recipes';
		}
	wp_reset_postdata();	
}
