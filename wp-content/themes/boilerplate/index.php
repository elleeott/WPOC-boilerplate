<?php get_header(); ?>

<div id="content-container" class="clearfix">

	<section id="main-content">
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<?php if(is_single() || is_page()) : ;?>
					<?php include('article.php'); ?>
				<?php else : ; ?>
					<?php include('article-list.php'); ?>
				<?php endif; ?>	
			<?php endwhile; endif;?>
	</section>
	
	<?php get_sidebar(); ?>
	
</div><!--end content container -->

<?php get_footer(); ?>