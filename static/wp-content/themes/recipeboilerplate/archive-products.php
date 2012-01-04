<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
			<article>
				<div class="product-img">
					<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'product-thumb' ); } ?>
				</div>
				<h1>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h1>
				<?php the_content(); ?>
			</article>
			<?php comments_template(); ?>
	
			<?php endwhile; endif;?>
		</section>
		
		<?php get_sidebar(); ?>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>