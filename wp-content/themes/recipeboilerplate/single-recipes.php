<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
	
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<article>
				<h1><?php the_title(); ?> - - This is a Product</h1>
				<?php echo average_rating(); ?>

				<?php the_content(); ?>
			</article>
		<?php comments_template('/comments-recipes.php'); ?>
		<?php endwhile; endif;?>

	</section>
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>
		