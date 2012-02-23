<?php get_header(); ?>
<div id="content-container" class="clearfix">
<div class="container">
	<div id="main-content" class="main-content-left">
		<hgroup>
			<h1><?php the_title(); ?></h1>
		</hgroup>
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<article>
			<?php the_time('F j, Y'); ?>
			<h1>
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h1>
			<div>
				<?php the_content(); ?>
				<p>
					Categories: <?php the_category(', ') ?>
				</p>
				<p>
					<?php the_tags(); ?>
				</p>
			</div>
		</article>
				
		<?php endwhile; endif;?>
		<?php wp_pagenavi(); ?>
	</div>
	
	<?php get_sidebar(); ?>
</div>
	
</div><!--end content container -->

<?php get_footer(); ?>
	
	
