<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<div class="container">

		<section id="main-content" class="main-content-left clearfix">
			<h1>Recipes</h1>

			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<div class="recipe-cell">
				<div class="recipe-img">
					<?php if(has_post_thumbnail()) : ?>
						<?php echo get_the_post_thumbnail($post->ID,'recipe-med'); ?>	
					<?php else : ?>
						<img src="<?php echo $static_subdomain.'/img/interface/no-image.jpg'; ?>" />
					<?php endif; ?>			
				</div>
				<h2>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<div>Rating: 
				<?php  
					if(get_post_meta($post->ID,'_avg_rating',true)) {
						echo get_post_meta($post->ID,'_avg_rating',true);
					} else {
						echo 'not yet rated';
					} 
				?>
				</div>
			</div>
			<?php endwhile; endif;?>
			<?php wp_pagenavi(); ?>
		</section>
		
		<?php get_sidebar(); ?>
	</div>		
</div><!--end content container -->

<?php get_footer(); ?>