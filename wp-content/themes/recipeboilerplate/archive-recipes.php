<?php get_header(); ?>

<div id="content-container" class="clearfix">
	<section id="main-content" class="main-content-left">
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<article>
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?> - <?php the_ID(); ?></a> this is a recipe
			</h1>
			<?php the_content(); ?>
			<?php echo average_rating(); ?>
		</article>

		<?php endwhile; endif;?>
	</section>
	
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>