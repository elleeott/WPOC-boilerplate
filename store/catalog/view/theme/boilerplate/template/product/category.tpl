<?php echo $header; ?>

<div id="content-container" class="clearfix">
	<div class="container">
		<?php //echo $column_left; ?>
		<?php // echo $content_top; ?>
		<?php //echo $column_right; ?>
		<div id="main-content" class="main-content-right">
	
		
		  <div class="breadcrumb">
		    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
		    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		    <?php } ?>
		  </div>
			
	<div class="product-info">
		  <h1><?php echo $heading_title; ?></h1>
		 
		  
		  <?php if ($thumb || $description) { ?>
			  <div class="category-info clearfix">
				<?php if ($thumb) { ?>
					<div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
				<?php } ?>
				<?php if ($description) { ?>
					<?php echo $description; ?>
				<?php } ?>
			  </div>
		  <?php } ?>
		  
		  
			<?php if ($categories) { ?>
				<h2><?php echo $text_refine; ?></h2>
					<div class="category-list">
						<?php if (count($categories) <= 5) { ?>
							<ul>
							<?php foreach ($categories as $category) { ?>
								<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
							<?php } ?>
							</ul>
						<?php } else { ?>
					<?php for ($i = 0; $i < count($categories);) { ?>
						<ul>
						<?php $j = $i + ceil(count($categories) / 4); ?>
							<?php for (; $i < $j; $i++) { ?>
								<?php if (isset($categories[$i])) { ?>
									<li><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></li>
								<?php } ?>
							<?php } ?>
						</ul>
						<?php } ?>
					<?php } ?>
					</div>
			<?php } ?>
		  
			  
			<?php if ($products) { ?>
				<div class="product-filter clearfix">
					<div class="limit"><b><?php echo $text_limit; ?></b>
						<select onchange="location = this.value;">
							<?php foreach ($limits as $limits) { ?>
								<?php if ($limits['value'] == $limit) { ?>
									<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
								<?php } else { ?>
									<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
									<?php } ?>
							<?php } ?>
						</select>
					</div>
					<div class="sort"><b><?php echo $text_sort; ?></b>
						<select onchange="location = this.value;">
						<?php foreach ($sorts as $sorts) { ?>
							<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
								<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
							<?php } else { ?>
								<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
							<?php } ?>
						<?php } ?>
						</select>
					</div>
				</div><!-- close product-filter-->
				
				
				<div class="product-compare"><a href="<?php echo $compare; ?>" id="compare_total"><?php echo $text_compare; ?></a></div>
				
				<div class="product-list clearfix">
					<?php foreach ($products as $product) { ?>
						<div class="product-cell">
							<div class="image">
								<a href="<?php echo $product['href']; ?>">
								<?php if ($product['thumb']) { ?>
									<img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
								<?php } else { ?>
									no image
								<?php } ?>
								</a>
							</div>
							<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
							<div class="description"><?php echo $product['description']; ?></div>
							<?php if ($product['price']) { ?>
								<div class="price">
								<?php if (!$product['special']) { ?>
									<?php echo $product['price']; ?>
								<?php } else { ?>
									<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
								<?php } ?>
								<?php if ($product['tax']) { ?>
									<br />
									<span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
								<?php } ?>
								</div><!--close price-->
							<?php } ?>
							<?php if ($product['rating']) { ?>
								<div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
							<?php } ?>
							
							
							<?php // ajax add to cart ?>
							<div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button button-primary"><span><?php echo $button_cart; ?></span></a></div>
						
							<?php //non-ajax add to cart ?>
							<?php /* ?>
							<div class="cart"><a href="/store/index.php?route=checkout/cart&product_id=<?php echo $product['product_id'];?>" class="button button-primary"><span><?php echo $button_cart; ?></span></a></div>
							<?php */ ?>
							
							
							
							<div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
							<div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
						</div><!--close product-cell-->
					<?php } ?>
				</div>
				
				
				<div class="pagination"><?php echo $pagination; ?></div>
				
				
			<?php } //close if($products)?> 
		  
			<?php if (!$categories && !$products) { ?>
				<div class="content"><?php echo $text_empty; ?></div>
				<div class="buttons">
					<div class="right"><a href="<?php echo $continue; ?>" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
				</div>
			<?php } ?>
			</div><!--close product-info-->		  
	
			<?php echo $content_bottom; ?>
		  
		</div><!--main-content-->
	</div>	
</div><!--content-container-->
  
<?php echo $footer; ?>