<?php
/*
Plugin Name: opencart integratation
Description: This makes opencart session data available to wordpress.
*/


// Get Session Data from OpenCart
function cart_items() {
	if(!isset($_SESSION)) {
		session_start();
	}	
	$cart_items=0;
	if(isset($_SESSION['cart'])){
		foreach ($_SESSION['cart'] as $item) {
			$cart_items = $cart_items + $item;	
		}
	}
	return $cart_items;

}
add_action('init', 'cart_items');

//this function checks if we have set the $isapage variable, and if so prevents WP from sending a 404
//required to make wordpress work with OpenCart
function ssp_status_filter($s)
{
	global $isapage;
	if($isapage && strpos($s, "404"))
		return false;	//don't send the 404
	else
		return $s;
}
add_filter('status_header', 'ssp_status_filter');


?>