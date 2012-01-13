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