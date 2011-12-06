<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<article>
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> this is a product
			</h1>
			<?php the_content(); ?>
		</article>
		<?php comments_template(); ?>

		<?php endwhile; endif;?>
	</section>
	
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>