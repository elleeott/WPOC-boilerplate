<?php get_header(); ?>
<div id="content-container" class="clearfix">
<div class="container">
	<section id="main-content" class="main-content-left">
	
		<?php 
		//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		//query_posts(array('post_type'=> array('post','wp_recipes'))); 
		
		?>

		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			DEPRECATE ME?
			<?php include('archive.php'); ?>
				
		<?php endwhile; endif;?>
		<?php wp_pagenavi(); ?>
	</section>
	
	<?php get_sidebar(); ?>
</div>
	
</div><!--end content container -->

<?php get_footer(); ?>