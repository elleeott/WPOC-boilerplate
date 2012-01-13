<?php
//create admin page for oc plugin - syncs db tables
function oc_plugin_menu() {
	add_options_page('OpenCart Plugin Options', 'OpenCart', 'manage_options', 'oc-plugin-options', 'oc_plugin_options');
}

function oc_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	//sync oc products with WP db - create product custom post type for each OC product.
	global $wpdb;
	
	//get product list from OC
	$products = $wpdb->get_results("
		SELECT *
		FROM oc_product
		INNER JOIN oc_product_description
		ON oc_product.product_id=oc_product_description.product_id
	");
	
	//loop through results
	foreach ($products as $product) {

		//query to see if product is already in WP db
		$wp_products = $wpdb->get_row("
			SELECT *
			FROM $wpdb->postmeta
			WHERE meta_value = $product->product_id
			AND meta_key =  '_oc_product_id'
		");
		
		//if product doesn't exist in WP DB:
		if(empty($wp_products->meta_value)){
			$the_post = array(
				'post_title'=>$product->name,
				'post_type'=>'products',
				'post_status'=>'publish'
			);
			$oc_post_id = wp_insert_post($the_post);
			add_post_meta($oc_post_id, '_oc_product_id', $product->product_id);						
		} else {
		//if product DOES exist in the DB:
			$the_post = array(
				'post_title'=>$product->name,
				'ID'=>$wp_products->post_id
			);
			wp_update_post($the_post);
		}
		update_post_meta($wp_products->post_id, '_oc_product_price', $product->price);				
		update_post_meta($wp_products->post_id, '_oc_product_sku', $product->sku);				
	
		//get special price
		$special_price = $wpdb->get_row("
			SELECT *
			FROM oc_product_special
			WHERE product_id = $product->product_id
		");
		if(!empty($special_price->price)) {
			update_post_meta($wp_products->post_id, '_oc_product_price_special', $special_price->price);				
		}
	}
	
	echo '<div class="wrap">';
	echo '<h2>OpenCart Plugin Options</h2>';
	echo '<p>Product tables synced</p>';
	echo '</div>';	
	
}
add_action('admin_menu', 'oc_plugin_menu');
