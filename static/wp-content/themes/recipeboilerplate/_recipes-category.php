		<div class="recipe-grid clearfix">
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<div class="recipe-cell">
				<div class="recipe-img">
					<?php if(has_post_thumbnail()) : ?>
						<?php echo get_the_post_thumbnail($post->ID,'recipe-med'); ?>	
					<?php else : ?>
						<img src="<?php echo STATIC_SUBDIR.'/img/interface/no-image.jpg'; ?>" />
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
		</div>
		<?php wp_pagenavi(); ?>
