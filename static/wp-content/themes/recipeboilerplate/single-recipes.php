<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
	
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<article>
				<h1><?php the_title(); ?> - - This is a Product</h1>
			<div>Rating: 
			<?php  
				if(get_post_meta($post->ID,'_avg_rating',true)) {
					echo get_post_meta($post->ID,'_avg_rating',true);
				} else {
					echo 'not yet rated';
				} 

			?>
			</div>


				<?php the_content(); ?>
			</article>
		<?php comments_template('/comments-recipes.php'); ?>
		<?php endwhile; endif;?>

	</section>
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>