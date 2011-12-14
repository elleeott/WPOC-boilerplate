<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<article>
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
	</section>
	
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>