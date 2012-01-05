<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">
		
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<div class="date"><?php the_time('F j, Y'); ?></div>
					<h1><?php the_title(); ?></h1>
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
			<?php comments_template(); ?>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div>	
</div><!--end content container -->

<?php get_footer(); ?>