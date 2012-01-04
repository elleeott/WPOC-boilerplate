<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">
		
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<div class="product-img">
						<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'product-med' ); } ?>
					</div>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>