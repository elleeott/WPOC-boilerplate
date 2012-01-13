<?php get_header(); ?>
<style>
	dl {
		margin:20px 0;
	}
	dt {
		clear:left;
		float:left;
		width:150px;
		font-weight:bold;
	}
	dd {
		width:220px;
		float:left;
		margin-left:10px;
	}
</style>
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
						<dl class="clearfix">
							<?php if(get_post_meta($post->ID,'_prep_time',true)): ?>
								<dt>Prep time:</dt> <dd class="preptime"><?php create_time('_prep_time'); ?><span class="value-title" title="<?php create_hrecipe_time('_prep_time');?>"></span></dd>
							<?php endif; ?>
							<dt>Cook time:</dt> <dd class="cooktime"><?php create_time('_cook_time'); ?><span class="value-title" title="<?php create_hrecipe_time('_cook_time');?>"></span></dd>
							<dt>Total time:</dt> <dd class="duration"><?php create_time('total'); ?><span class="value-title" title="<?php create_hrecipe_time('total');?>"></span></dd>
							<dt>Yield:</dt> <dd class="yield">1 9" pie</dd>
						</dl>
						<dl class="nutrition clearfix">
							<dt>Serving Size:</dt> <dd class="servingsize">1 medium slice</dd>
							<dt>Calories per serving:</dt> <dd class="servingsize">250</dd>
							<dt>Fat per Serving:</dt> <dd class="fat">12g</dd>
						</dl>
						<?php if(get_post_meta($post->ID,'_recipe_ingredients',true)) : ?>
							<div class="ingredients">
								<h3>Ingredients:</h3>
								<?php get_ingredients();?>
							</div>
						<?php endif; ?>
						<?php /*
						<ul class="ingredients">
							<li class="ingredient">Thinly-sliced <span class="name"><a href="#">apples</a></span>: <span class="amount">6 cups</span></li>
							<li class="ingredient"><span class="name"><a href="#">White sugar</a></span>: <span class="amount">3/4 cup</span></li>
							<li class="ingredient"><span class="amount">3/4 cup</span> <span class="name"><a href="#">White sugar</a></span></li>
						</ul>
						*/ ?>
						<?php if(get_post_meta($post->ID,'_recipe_directions',true)) : ?>
						<div class="instructions">
							<h3>Directions:</h3>
							<?php echo get_post_meta($post->ID,'_recipe_directions',true); ?>
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