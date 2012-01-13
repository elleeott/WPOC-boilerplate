<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<div class="container">

		<section id="main-content" class="main-content-left">
			<h1>Ingredients</h1>
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<article>
				<?php the_time('F j, Y'); ?>
				<h1>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?> - <?php the_ID(); ?></a> this is a recipe
				</h1>
				<?php the_content(); ?>
				<div>Rating: 
				<?php  
					if(get_post_meta($post->ID,'_avg_rating',true)) {
						echo get_post_meta($post->ID,'_avg_rating',true);
					} else {
						echo 'not yet rated';
					} 
				?>
				</div>
				 
			</article>
	
			<?php endwhile; endif;?>
			<?php wp_pagenavi(); ?>
		</section>
		
		<?php get_sidebar(); ?>
	</div>		
</div><!--end content container -->

<?php get_footer(); ?>