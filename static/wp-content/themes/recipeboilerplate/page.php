<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">
		
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<h1><?php the_title(); ?></h1>
					<div>
						<?php the_content(); ?>

					</div>
				</article>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div>	
</div><!--end content container -->

<?php get_footer(); ?>