			<div class="product-grid clearfix">
			
				<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
					<div class="product-cell">
						<div class="product-img">
							<?php if ( has_post_thumbnail()) { 
								echo '<a href="';
								the_permalink();
								echo '">';
								the_post_thumbnail( 'product-thumb' );
								echo '</a>';
							} ?>
						</div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	
						
						<div>
							<strong>Price:</strong>
							<?php echo money_format('$%i',get_post_meta(get_the_id(),'_oc_product_price',true)); ?>
						</div>
						<div>
							<strong>Item#:</strong>
							<?php echo get_post_meta(get_the_id(),'_oc_product_sku',true); ?>
						</div>
						<?php /*
						<form class="add-to-cart-form" action="/store/index.php?route=checkout/cart/update" method="post">
							<input type="hidden" name="product_id" value="<?php echo get_post_meta(get_the_id(),'_oc_product_id',true); ?>"/>
							<input type="hidden" name="quantity" value="1"/>				
							<a href="#" class="button button-primary add-to-cart">add to cart</a>
						</form>
						*/ ?>
					</div>
				<?php endwhile; endif;?>
			</div>
			<?php wp_pagenavi(); ?>
