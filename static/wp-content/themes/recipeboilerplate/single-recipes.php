<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<div class="hrecipe">
						<div class="published"><?php the_time('F j, Y'); ?></div>
						<div class="recipe-img">
							<div class="large-img">
								<?php get_custom_gallery('product-med'); ?>
							</div>
							<div class="small-img">
								<?php get_custom_gallery('product-thumb'); ?>
							</div>
						</div>
						<div class="item">
							<h1 clas="fn"><?php the_title(); ?> - - This is a Recipe</h1>
						</div>
						
						<div class="review hreview-aggregate">
							<span class="rating">
								<div class="average">Rating: 
									<?php  
										if(get_post_meta($post->ID,'_avg_rating',true)) {
											echo get_post_meta($post->ID,'_avg_rating',true) . ' stars';
										} else {
											echo 'not yet rated';
										} 
									?>
								</div>
								<div class="count">
									<?php  
										if(get_post_meta($post->ID,'_sum_rating',true)) {
											echo 'based on '.get_post_meta($post->ID,'_sum_rating',true).' reviews';
										} 
									?>
								</div>
							</span>
						</div>
						<dl>
							<dt>Prep time:</dt> <dd class="preptime">30 min<span class="value-title" title="PT30M"></span></dd>
							<dt>Cook time:</dt> <dd class="cooktime">1 hour<span class="value-title" title="PT1H"></span></dd>
							<dt>Total time:</dt> <dd class="duration">1 hour, 30 min<span class="value-title" title="PT1H30M"></span></dd>
							<dt>Yield:</dt> <dd class="yield">1 9" pie</dd>
						</dl>
						<dl class="nutrition">
							<dt>Serving Size:</dt> <dd class="servingsize">1 medium slice</dd>
							<dt>Calories per serving:</dt> <dd class="servingsize">250</dd>
							<dt>Fat per Serving:</dt> <dd class="fat">12g</dd>
						</dl>
						<h3>Ingredients:</h3>
						<ul class="ingredients">
							<li class="ingredient">Thinly-sliced <span class="name"><a href="#">apples</a></span>: <span class="amount">6 cups</span></li>
							<li class="ingredient"><span class="name"><a href="#">White sugar</a></span>: <span class="amount">3/4 cup</span></li>
							<li class="ingredient"><span class="amount">3/4 cup</span> <span class="name"><a href="#">White sugar</a></span></li>
						</ul>
						<?php if(get_post_meta($post->ID,'_recipe-directions',true)) : ?>
						<div class="instructions">
							<h3>Directions:</h3>
							<?php echo get_post_meta($post->ID,'_recipe-directions',true); ?>
						</div>
						<?php endif; ?>
						<div class="summary">
							<?php the_excerpt(); ?>
						</div>
						<?php the_content(); ?>
					</div>
				</article>
			<?php comments_template('/comments-recipes.php'); ?>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>