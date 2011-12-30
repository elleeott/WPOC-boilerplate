<?php 
global $pageTitle;
$pageTitle=$title;
function assignPageTitle(){
	global $pageTitle;
	return $pageTitle;
}
add_filter('wp_title', 'assignPageTitle');
?>
<?php get_header(); ?>
