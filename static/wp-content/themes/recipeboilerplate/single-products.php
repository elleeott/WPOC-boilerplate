<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">	
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<div class="product-img">
						<div class="large-img flexslider">
							<?php get_custom_gallery(); ?>
						</div>
						<?php /*
						<div class="small-img">
							<?php get_custom_gallery(); ?>
						</div>
						*/ ?>
					</div>
					<h1><?php the_title(); ?></h1>
					<div class="product-info">
						<?php get_oc_options(); ?>				
					</div>
					<h2>Description</h2>
					<?php the_content(); ?>
				</article>
				<div class="related-recipes">
					<?php get_related_recipes(); ?>
				</div>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div><!-- end container -->
</div><!--end content container -->

<?php get_footer(); ?>