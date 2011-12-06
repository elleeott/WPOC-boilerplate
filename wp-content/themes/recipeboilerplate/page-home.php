<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<section>
		<div id="hero">
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			<?php endwhile; endif;?>
		</div>
		<div id="promo-tiles" class="clearfix">
			<div></div>
			<div></div>
			<div class="last"></div>
		</div>
	</section>
	
</div><!--end content container -->

<?php get_footer(); ?>
		