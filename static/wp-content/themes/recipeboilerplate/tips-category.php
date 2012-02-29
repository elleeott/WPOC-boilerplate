<?php get_header(); ?>
<div id="content-container" class="blog-category clearfix">
<div class="container">
	<div id="main-content" class="main-content-left">
		<hgroup>
			<h1><?php echo $wp_query->queried_object->name; ?></h1>
		</hgroup>
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<article>
			<div>
				<?php the_content(); ?>
			</div>
		</article>
				
		<?php endwhile; endif;?>
		<?php wp_pagenavi(); ?>
	</div>
	
	<?php get_sidebar(); ?>
</div>
	
</div><!--end content container -->

<?php get_footer(); ?>
	
	
