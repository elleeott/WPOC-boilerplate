<?php get_header(); ?>
<div id="content-container" class="page-default clearfix">
	<div class="container">
		<div id="main-content" class="main-content-left">	
		
		
		
		
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article class="primary">
				<hgroup>
					<h1><?php the_title(); ?></h1>
				</hgroup>
					<div>
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; endif;?>



	
		</div><!--end main-content-->
		<?php get_sidebar(); ?>
	</div><!-- end container -->
</div><!--end content container -->
<?php get_footer(); ?>