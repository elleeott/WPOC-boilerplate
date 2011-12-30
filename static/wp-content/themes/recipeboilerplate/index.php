<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
	
		<?php 
		//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		//query_posts(array('post_type'=> array('post','wp_recipes'))); 
		
		?>

		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>

		<?php include('archive.php'); ?>
				
		<?php endwhile; endif;?>
		
	</section>
	
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>