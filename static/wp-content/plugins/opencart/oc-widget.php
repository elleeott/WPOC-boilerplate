<?php 

//product sidebar widget
class OC_Products_Widget extends WP_Widget {

	function OC_Products_Widget() {
		$widget_ops = array( 'classname' => 'oc-products', 'description' => 'Displays products listed in the OpenCart database');

		$this->WP_Widget( 'oc-products', 'OpenCart Products', $widget_ops);
	}
	function widget($args){
		extract( $args );
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_product_description
			ORDER BY name
		");

		if($results):
			echo $before_widget . $before_title . 'Products' . $after_title;
			echo '<ul>';
			foreach ($results as $result) {
				echo '<li><a href="/store/?route=product/product&product_id='.$result->product_id.'">'.$result->name.'</a></li>';				
			}
			echo '</ul>';
			echo $after_widget;
		endif; 
	}	
}

function load_oc_products_widgets() {
	register_widget( 'OC_Products_Widget' );
}
add_action( 'widgets_init', 'load_oc_products_widgets' );


//list product tag widget
class Product_Tag_Widget extends WP_Widget {

	function Product_Tag_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'product-tag', 'description' => 'Displays a list of all products by tag.');

		/* Create the widget. */
		$this->WP_Widget( 'product-tag', 'Product Tags', $widget_ops);
	}
	function widget($args){
		extract( $args );
		$terms = get_terms('product-tags');
		$count = count($terms);
		if($terms){
			echo $before_widget . $before_title . 'Products by Tags' . $after_title;
			echo '<ul>';
			foreach ($terms as $term) {
				echo '<li><a href="'.get_term_link($term,'product-tags').'">'.$term->name.'</a> '.$term->count.'</li>';
			}
			echo '</ul>';
			echo $after_widget;
		}
	}	
}

function product_tag_widgets() {
	register_widget( 'Product_Tag_Widget' );
}
add_action( 'widgets_init', 'product_tag_widgets' );



//list product categories widget
class Product_Category_Widget extends WP_Widget {

	function Product_Category_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'product-category', 'description' => 'Displays a list of all products by category.');

		/* Create the widget. */
		$this->WP_Widget( 'product-category', 'Product Categories', $widget_ops);
	}
	function widget($args){
		extract( $args );
		$terms = get_terms('product-category');
		$count = count($terms);
		if($terms){
			echo $before_widget . $before_title . 'Products by Categories' . $after_title;
			echo '<ul>';
			foreach ($terms as $term) {
				echo '<li><a href="'.get_term_link($term,'product-category').'">'.$term->name.'</a> '.$term->count.'</li>';
			}
			echo '</ul>';
			echo $after_widget;
		}
	}	
}

function product_category_widgets() {
	register_widget( 'Product_Category_Widget' );
}
add_action( 'widgets_init', 'product_category_widgets' );