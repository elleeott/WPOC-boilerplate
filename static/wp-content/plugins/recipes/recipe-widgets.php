<?php

//list ingredients widget
class List_Ingredients_Widget extends WP_Widget {

	function List_Ingredients_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'list-ingredients', 'description' => 'Displays a list of all ingredients used in recipes.');

		/* Create the widget. */
		$this->WP_Widget( 'list-ingredients', 'List Ingredients', $widget_ops);
	}
	function widget($args){
		extract( $args );
		$terms = get_terms('ingredients');
		$count = count($terms);
		if($terms){
			echo $before_widget . $before_title . 'Recipes by Ingredients' . $after_title;
			echo '<ul>';
			foreach ($terms as $term) {
				echo '<li><a href="'.get_term_link($term,'ingredients').'">'.$term->name.'</a> '.$term->count.'</li>';
			}
			echo '</ul>';
			echo $after_widget;
		}
	}	
}

function load_ingredients_list_widgets() {
	register_widget( 'List_Ingredients_Widget' );
}
add_action( 'widgets_init', 'load_ingredients_list_widgets' );



//recipe category widget
class Recipe_Category_Widget extends WP_Widget {

	function Recipe_Category_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'recipe-category', 'description' => 'Recipes organized by category.');

		/* Create the widget. */
		$this->WP_Widget( 'recipe-category', 'Recipe Categories', $widget_ops);
	}
	function widget($args){
		extract($args);
		$terms= get_terms('recipe-category');
		if($terms){
			echo $before_widget . $before_title . 'Recipe Categories' . $after_title;
			echo '<ul>';
			//print_r($terms);
			foreach($terms as $term) {
				//echo '<li><a href="/recipe-category/'.$term->slug.'">'.$term->name.'</a> '.$term->count.'</li>';
				echo '<li><a href="'.get_term_link($term,'recipe-category').'">'.$term->name.'</a> '.$term->count.'</li>';
			}
			echo '</ul>';
			echo $after_widget;
		}
	}	

}

function recipe_category_widgets() {
	register_widget( 'Recipe_Category_Widget' );
}
add_action( 'widgets_init', 'recipe_category_widgets' );


//recipe category widget
class Recipe_Tag_Widget extends WP_Widget {

	function Recipe_Tag_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'recipe-tag', 'description' => 'Recipes organized by tag.');

		/* Create the widget. */
		$this->WP_Widget( 'recipe-tag', 'Recipe Tags', $widget_ops);
	}
	function widget($args){
		extract($args);
		$terms= get_terms('recipe-tags');
		if($terms){
			echo $before_widget . $before_title . 'Recipe Tags' . $after_title;
			echo '<ul>';
			foreach($terms as $term) {
				echo '<li><a href="'.get_term_link($term,'recipe-tags').'">'.$term->name.'</a> '.$term->count.'</li>';
			}
			echo '</ul>';
			echo $after_widget;
		}
	}	
}

function recipe_tag_widgets() {
	register_widget( 'Recipe_Tag_Widget' );
}
add_action( 'widgets_init', 'recipe_tag_widgets' );



//recent recipes widget
class Recent_Recipes_Widget extends WP_Widget {

	function Recent_Recipes_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'recent-recipes', 'description' => '5 most recent recipes.');

		/* Create the widget. */
		$this->WP_Widget( 'recent-recipes', 'Recent Recipes', $widget_ops);
	}
	function widget($args){
		extract($args);
		global $post;
		$query = new WP_Query( array( 
			'post_type' => 'recipes',			
			'showposts' => 5,
			'orderby' => 'date',
			'order' => 'DESC'
		 ));
		 
		if($query->have_posts()):
			echo $before_widget . $before_title . 'Recent Recipes' . $after_title;
			echo '<ul>';
			while ($query->have_posts()):$query->the_post();
					echo '<li>';
					echo '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a> ';
					echo '</li>';
			endwhile;
			echo '</ul>';
			echo $after_widget;
		endif; 
	}	

}

function recent_recipes_widgets() {
	register_widget( 'Recent_Recipes_Widget' );
}
add_action( 'widgets_init', 'recent_recipes_widgets' );
