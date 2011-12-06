<?php
/*
Template Name: Fancy Template
*/

?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/lib/cacheBuster.php');?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<link rel="stylesheet" media="screen" href="<?php autoVer('/static/css/fancy.css'); ?>" />	
	</head>
	<body>
		<article>
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
			
		<?php endwhile; endif;?>
			
		</article>
	</body>
</html>
