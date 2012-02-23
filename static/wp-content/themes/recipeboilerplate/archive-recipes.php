<?php get_header(); ?>
<div id="content-container" class="products-category clearfix">
	<div class="container">
		<div id="main-content" class="main-content-left">
			<h1><?php echo $wp_query->queried_object->name; ?></h1>

			<?php get_template_part('_recipes-category'); ?>

		</div><!--main-content-->
		<?php get_sidebar(); ?>
	</div><!--container-->
</div><!--end content container -->

<?php get_footer(); ?>



