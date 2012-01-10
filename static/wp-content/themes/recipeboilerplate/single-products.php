<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<div class="container">
		<section id="main-content" class="main-content-left">	
			<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
				<article>
					<div class="product-img">
						<div class="large-img">
							<?php get_custom_gallery('product-med'); ?>
						</div>
						<div class="small-img">
							<?php get_custom_gallery('product-thumb'); ?>
						</div>
					</div>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>

					<div>
						<strong>Price:</strong>
						<?php echo money_format('$%i',get_post_meta(get_the_id(),'_oc_product_price',true)); ?>
					</div>
					<div>
						<strong>Item#:</strong>
						<?php echo get_post_meta(get_the_id(),'_oc_product_sku',true); ?>
					</div>
					<form class="add-to-cart-form" action="/store/index.php?route=checkout/cart/update" method="post">
						<input type="hidden" name="product_id" value="<?php echo get_post_meta(get_the_id(),'_oc_product_id',true); ?>"/>
						<input type="text" name="quantity" value="1"/>				
						<button class="button button-primary add-to-cart">add to cart</button>
						<!--<a href="#" class="button button-primary add-to-cart">add to cart</a>-->
					</form>


				</article>
			<?php endwhile; endif;?>
	
		</section>
		<?php get_sidebar(); ?>
	</div><!-- end container -->
</div><!--end content container -->

<?php get_footer(); ?>