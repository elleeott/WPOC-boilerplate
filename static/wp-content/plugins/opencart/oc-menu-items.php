<?php

//get catalog data from opencart
function get_oc_categories($items, $args) {
    if( $args->theme_location == 'primary-nav' ){
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_category_description
			INNER JOIN oc_category on (oc_category.category_id = oc_category_description.category_id)
			WHERE oc_category.top = 1
			ORDER BY name
		");
		$cat_list = '<li><a href="/store/">Categories</a><ul>';
		foreach ($results as $result) {
			$cat_list .= '<li><a href="/store/?route=product/category&path='.$result->category_id.'">'.$result->name.'</a></li>';
		}
		$cat_list .='</ul></li>';
		return $items . $cat_list;
	} else {
		return $items;
	}
}
add_filter('wp_nav_menu_items','get_oc_categories',10,2);


//add products to menu from opencart
function get_oc_products($items, $args) {
    if( $args->theme_location == 'primary-nav' ){
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT *
			FROM oc_product_description
			ORDER BY name
		");
		$prod_list = '<li><a href="/store/">Products</a><ul>';
		foreach ($results as $result) {
			$prod_list .= '<li><a href="/store/?route=product/product&product_id='.$result->product_id.'">'.$result->name.'</a></li>';
		}
		$prod_list .='<ul></li>';
		return $items . $prod_list;
	} else {
		return $items;
	}
}
add_filter('wp_nav_menu_items','get_oc_products',10,2);
