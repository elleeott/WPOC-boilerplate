<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<div id="main-content" class="main-content-left">
		
		
		
		
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article class="primary">
					<hgroup>
						<h1><?php the_title(); ?></h1>
					</hgroup>
					<div class="social-elements">
						<?php get_social_elements(); ?>
					</div>
					<div class="date"><?php the_time('F j, Y'); ?></div>
				
					<?php the_content(); ?>
					<p>
						Categories: <?php the_category(', ') ?>
					</p>
					<p>
						<?php the_tags(); ?>
					</p>
				</article>
			<?php comments_template(); ?>
			<?php endwhile; endif;?>
	
	
	
	
	
		</div>
		<?php get_sidebar(); ?>
	</div>	
</div><!--end content container -->

<?php get_footer(); ?>