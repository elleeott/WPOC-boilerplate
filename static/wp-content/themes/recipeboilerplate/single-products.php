<?php get_header(); ?>

<div id="content-container" class="products-detail clearfix">
	<div class="container">
		<div id="main-content" class="main-content-left">	
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article class="primary">
					<div class="hproduct">
						<div class="social-elements">
							<?php get_social_elements(); ?>
						</div>
						<div class="product-img">
							<div class="large-img flexslider">
								<?php get_product_gallery(); ?>
							</div>
							<?php /*
							<div class="small-img">
								<?php get_custom_gallery(); ?>
							</div>
							*/ ?>
						</div>
						<div class="item">
							<h1 class="fn"><?php the_title(); ?></h1>
						</div>
						<div class="product-info">
							<?php get_oc_options(); ?>				
						</div>
						<h2>Description</h2>
						<div class="description">
							<?php the_content(); ?>
						</div>
					</div>
					<div id="product-tags">
						<?php 
						$terms = get_the_terms($post->ID,'product-tags');
						if($terms){
							echo '<ul>';
							foreach($terms as $term) {
								echo '<li><a href="'.get_term_link($term->slug,'product-tags').'">'.$term->name.'</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
				</article>
				<div class="related-recipes">
					<?php get_related_recipes(); ?>
				</div>
			<?php endwhile; endif;?>
	
		</div>
		<?php get_sidebar(); ?>
	</div><!-- end container -->
</div><!--end content container -->

<?php get_footer(); ?>