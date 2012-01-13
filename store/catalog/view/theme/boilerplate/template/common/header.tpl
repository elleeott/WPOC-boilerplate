<?php 
global $page_title;
$page_title=$title;
function assignPageTitle(){
	global $page_title;
	
	return $page_title;
}
add_filter('wp_title', 'assignPageTitle');
?>
<?php get_header(); ?>
