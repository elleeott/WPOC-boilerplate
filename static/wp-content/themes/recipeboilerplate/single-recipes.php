<?php get_header(); ?>

<div id="content-container" class="recipe-single clearfix">
	<div class="container">
		<div id="main-content" class="main-content-left">
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article class="primary">
					<div class="hrecipe">
						<div class="recipe-img">
							<div class="large-img">
								<?php get_recipe_gallery(); ?>
							</div>
						</div>
						<div class="social-elements clearfix">
							<?php get_social_elements(); ?>
						</div>
						<div class="review hreview-aggregate">
							<?php get_ratings(); ?>
						</div>
						<div class="item">
							<h1 class="fn"><?php the_title(); ?></h1>
						</div>
							<time datetime="<?php the_time('c'); ?>"><?php the_time('F j, Y'); ?></time>
						
						<div class="summary">
							<?php the_excerpt(); ?>
						</div>
						<dl class="clearfix">
							<?php if(get_post_meta($post->ID,'_prep_time',true)): ?>
								<dt>Prep time:</dt> <dd class="preptime"><?php create_time('_prep_time'); ?><span class="value-title" title="<?php create_hrecipe_time('_prep_time');?>"></span></dd>
							<?php endif; ?>
							
							<?php if(get_post_meta($post->ID,'_cook_time',true)): ?>
								<dt>Cook time:</dt> <dd class="cooktime"><?php create_time('_cook_time'); ?><span class="value-title" title="<?php create_hrecipe_time('_cook_time');?>"></span></dd>
							<?php endif; ?>
							
							<?php if(get_post_meta($post->ID,'_cook_time',true) || get_post_meta($post->ID,'_prep_time',true)): ?>
								<dt>Total time:</dt> <dd class="duration"><?php create_time('total'); ?><span class="value-title" title="<?php create_hrecipe_time('total');?>"></span></dd>
							<?php endif; ?>
							<?php if(get_post_meta($post->ID,'_yield',true)): ?>
								<dt>Yield:</dt> <dd class="yield"><?php echo get_post_meta($post->ID,'_yield',true) ; ?></dd>
							<?php endif; ?>
						</dl>
						
						<dl class="nutrition clearfix">
							<?php if(get_post_meta($post->ID,'_serving_size',true)): ?>
								<dt>Serving Size:</dt> <dd class="servingsize"><?php echo get_post_meta($post->ID,'_serving_size',true); ?></dd>
							<?php endif; ?>
							<?php if(get_post_meta($post->ID,'_calories',true)): ?>
								<dt>Calories per serving:</dt> <dd class="servingsize"><?php echo get_post_meta($post->ID,'_calories',true); ?></dd>
							<?php endif; ?>
							<?php if(get_post_meta($post->ID,'_fat',true)): ?>
								<dt>Fat per Serving:</dt> <dd class="fat"><?php echo get_post_meta($post->ID,'_fat',true); ?></dd>
							<?php endif; ?>
						</dl>
						
						<?php get_nutrition_label(); ?>

						<?php if(get_post_meta($post->ID,'_recipe_ingredients',true)) : ?>
							<div class="ingredients">
								<h3>Ingredients:</h3>
								<?php get_ingredients();?>
							</div>
						<?php endif; ?>
						
						<?php if(get_post_meta($post->ID,'_recipe_directions',true)) : ?>
							<div class="instructions">
								<h3>Directions:</h3>
								<?php echo get_post_meta($post->ID,'_recipe_directions',true); ?>
							</div>
						<?php endif; ?>
											
					</div><!--close hrecipe -->
					
					<div id="recipe-tags">
						<h3>recipe tags</h3>
						<?php 
						$terms = get_the_terms($post->ID,'recipe-tags');
						if($terms){
							echo '<ul>';
							foreach($terms as $term) {
								echo '<li><a href="'.get_term_link($term->slug,'recipe-tags').'">'.$term->name.'</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
					<div id="ingredients">
						<h3>recipe ingredients</h3>
						<?php 
						$terms = get_the_terms($post->ID,'ingredients');
						if($terms){
							echo '<ul>';
							foreach($terms as $term) {
								echo '<li><a href="'.get_term_link($term->slug,'ingredients').'">'.$term->name.'</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
					<div id="recipe-categories">
						<h3>recipe categories</h3>
						<?php 
						$terms = get_the_terms($post->ID,'recipe-category');
						if($terms){
							echo '<ul>';
							foreach($terms as $term) {
								echo '<li><a href="'.get_term_link($term->slug,'recipe-category').'">'.$term->name.'</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>

				</article>
			<?php comments_template(); ?>
			<?php endwhile; endif;?>
	
		</div>
		<?php get_sidebar(); ?>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>