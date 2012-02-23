<?php get_header(); ?>
<div id="content-container" class="clearfix">
<div class="container">
	<div id="main-content" class="main-content-left">
	<?php if ($wp_query->found_posts > 0 ) : ?>
		<h1><?php echo $wp_query->found_posts; ?> results for &ldquo;<?php echo $s; ?>&rdquo;</h1>
		<?php if (have_posts()) : ?>
			<ul>
				<?php while (have_posts()) : the_post(); ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>		
				<?php endwhile; ?>
			</ul>
			<?php wp_pagenavi(); ?>
		<?php endif;?>
		
	<?php else : ?>
		<h1>Oh pooh, we couldn&rsquo;t find what you were looking for</h1>
		<p>Perhaps our site map could be of some use.</p>
		<?php get_template_part('sitemap'); ?>
	<?php endif; ?>
	
	</div>
	<?php get_sidebar(); ?>
</div>
	
</div><!--end content container -->

<?php get_footer(); ?>