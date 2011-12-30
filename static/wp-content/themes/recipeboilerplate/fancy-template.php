<?php
/*
Template Name: Fancy Template
*/

?>
<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<?php wp_head(); ?>
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