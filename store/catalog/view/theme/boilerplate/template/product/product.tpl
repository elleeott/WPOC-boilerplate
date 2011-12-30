<?php echo $header; ?>
<div id="content-container" class="clearfix">
	<?php echo $column_left; ?>
	<?php echo $content_top; ?>
	<?php echo $column_right; ?>
	<div id="main-content" class="main-content-right">

	
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	
	<div class="product-info clearfix">
		<?php if ($thumb || $images) { ?>
			<div class="product-images">
				<?php if ($thumb) { ?>
					<div class="image"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="fancybox" rel="fancybox"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image" /></a></div>
				<?php } ?>
				<?php if ($images) { ?>
					<div class="image-additional">
					<?php foreach ($images as $image) { ?>
					<a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="fancybox" rel="fancybox"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
				<?php } ?>
			</div>
			<?php } ?>
			</div>
		<?php } ?>
   
    
	    <div class="product-description">
    		<h1><?php echo $heading_title; ?></h1>
			<?php if ($manufacturer) { ?>
				<span><?php echo $text_manufacturer; ?></span> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a>
			<?php } ?>
			<span><?php echo $text_model; ?></span> <?php echo $model; ?>
			<span><?php echo $text_reward; ?></span> <?php echo $reward; ?>
			<span><?php echo $text_stock; ?></span> <?php echo $stock; ?>
			
			
			<?php if ($price) { ?>
				<div class="price"><?php echo $text_price; ?>
					<?php if (!$special) { ?>
						<?php echo $price; ?>
					<?php } else { ?>
						<span class="price-old"><?php echo $price; ?></span> <span class="price-new"><?php echo $special; ?></span>
					<?php } ?>
					
					<?php if ($tax) { ?>
						<span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
					<?php } ?>
					<?php if ($points) { ?>
						<span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span>
					<?php } ?>
					<?php if ($discounts) { ?>
						<div class="discount">
							<?php foreach ($discounts as $discount) { ?>
								<?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?>
							<?php } ?>
						</div>
					<?php } ?>
				</div><!--close price-->
			<?php } ?>
	      
	
	      
	      
			<?php if ($options) { ?>
				<div class="options">
					<h2><?php echo $text_option; ?></h2>
					
					<?php foreach ($options as $option) { ?>
			
			
						<?php if ($option['type'] == 'select') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<select name="option[<?php echo $option['product_option_id']; ?>]">
								<option value=""><?php echo $text_select; ?></option>
								<?php foreach ($option['option_value'] as $option_value) { ?>
									<option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
									<?php if ($option_value['price']) { ?>
										(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
									<?php } ?>
									</option>
								<?php } ?>
								</select>
							</div>
						<?php } ?>
						
						
						
						<?php if ($option['type'] == 'radio') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<?php foreach ($option['option_value'] as $option_value) { ?>
									<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
									<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
									<?php if ($option_value['price']) { ?>
										(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
									<?php } ?>
									</label>
								<?php } ?>
							</div>
						<?php } ?>
						
						
						<?php if ($option['type'] == 'checkbox') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<?php foreach ($option['option_value'] as $option_value) { ?>
									<input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
									<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
									<?php if ($option_value['price']) { ?>
										(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
									<?php } ?>
									</label>
								<?php } ?>
							</div>
						<?php } ?>
						
						
						<?php if ($option['type'] == 'image') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<table class="option-image">
									<?php foreach ($option['option_value'] as $option_value) { ?>
										<tr>
											<td>
												<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
											</td>
											<td>
												<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
													<img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" />
												</label>
											</td>
											<td>
												<label for="option-value-<?php echo $option_value['product_option_value_id']; ?>">
													<?php echo $option_value['name']; ?>
													<?php if ($option_value['price']) { ?>
														(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
													<?php } ?>
												</label>
											</td>
										</tr>
									<?php } ?>
								</table>
							</div>
						<?php } ?>
						
						
						
						<?php if ($option['type'] == 'text') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
							</div>
						<?php } ?>
						
						<?php if ($option['type'] == 'textarea') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
							</div>
						<?php } ?>
						
						<?php if ($option['type'] == 'file') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<a id="button-option-<?php echo $option['product_option_id']; ?>" class="button"><span><?php echo $button_upload; ?></span></a>
								<input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
							</div>
						<?php } ?>
						
						<?php if ($option['type'] == 'date') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
							</div>
						<?php } ?>
						
						<?php if ($option['type'] == 'datetime') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
							</div>
						<?php } ?>
						
						<?php if ($option['type'] == 'time') { ?>
							<div id="option-<?php echo $option['product_option_id']; ?>" class="option">
								<?php if ($option['required']) { ?>
									<span class="required">*</span>
								<?php } ?>
								<?php echo $option['name']; ?>:
								<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
							</div>
						<?php } ?>
					
					<?php } ?>
				</div><!--close options -->
			<?php } ?>
		        	      
	      
			<div class="cart">
				<div><?php echo $text_qty; ?>
					<input type="text" name="quantity" size="2" value="<?php echo $minimum; ?>" />
					<input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" />
					<a onclick="addToCart('<?php echo $product_id; ?>');" id="button-cart" class="button button-primary"><span><?php echo $button_cart; ?></span></a>
				</div>
				<a href="#" onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a><br/>
				<a href="#" onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a>
				<?php if ($minimum > 1) { ?>
					<div class="minimum"><?php echo $text_minimum; ?></div>
				<?php } ?>
			</div>
			      
		     <?php /*
			<?php if ($review_status) { ?>
			<div class="review">
				<div><img src="catalog/view/theme/default/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" /><a href="#" onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $reviews; ?></a>
				<a href="#" onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $text_write; ?></a>
			</div>
			
			<div class="share">
			
				<!-- AddThis Button BEGIN -->
					<div class="addthis_default_style"><a class="addthis_button_compact"><?php echo $text_share; ?></a> <a class="addthis_button_email"></a><a class="addthis_button_print"></a> <a class="addthis_button_facebook"></a> <a class="addthis_button_twitter"></a></div>
					<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script> 
				<!-- AddThis Button END --> 
			</div>
			</div>
			<?php } ?>
		      
			*/ ?>
		      
    		<?php echo $description; ?>
	      
	    </div><!--close product-description-->
	</div><!--close product-info-->
  
  
  
 <?php /* 
	<div id="tabs" class="htabs">
		<a href="#tab-description"><?php echo $tab_description; ?></a>
		<?php if ($attribute_groups) { ?>
			<a href="#tab-attribute"><?php echo $tab_attribute; ?></a>
		<?php } ?>
		<?php if ($review_status) { ?>
			<a href="#tab-review"><?php echo $tab_review; ?></a>
		<?php } ?>
		<?php if ($products) { ?>
			<a href="#tab-related"><?php echo $tab_related; ?> (<?php echo count($products); ?>)</a>
		<?php } ?>
	</div>
*/ ?>
	
	<?php if ($attribute_groups) { ?>
		<div id="tab-attribute" class="tab-content">
			<table class="attribute">
				<?php foreach ($attribute_groups as $attribute_group) { ?>
				<thead>
					<tr>
						<td colspan="2"><?php echo $attribute_group['name']; ?></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
						<tr>
							<td><?php echo $attribute['name']; ?></td>
							<td><?php echo $attribute['text']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
				<?php } ?>
			</table>
		</div>
	<?php } ?>
  
  
 <?php /*
  
<?php if ($review_status) { ?>
	<div id="tab-review" class="tab-content">
		<div id="review"></div>
		<h2 id="review-title"><?php echo $text_write; ?></h2>
		<?php echo $entry_name; ?>
		<input type="text" name="name" value="" />
		<?php echo $entry_review; ?>
		<textarea name="text"></textarea>
		<span><?php echo $text_note; ?></span>
		<?php echo $entry_rating; ?>
		<span><?php echo $entry_bad; ?></span>
		<input type="radio" name="rating" value="1" />
		<input type="radio" name="rating" value="2" />
		<input type="radio" name="rating" value="3" />
		<input type="radio" name="rating" value="4" />
		<input type="radio" name="rating" value="5" />
		 <span><?php echo $entry_good; ?></span>
		<?php echo $entry_captcha; ?>
		<input type="text" name="captcha" value="" />
		<img src="index.php?route=product/product/captcha" alt="" id="captcha" />
		<div class="buttons">
			<div class="right"><a id="button-review" class="button"><span><?php echo $button_continue; ?></span></a></div>
		</div>
	</div>
<?php } ?>
  
*/ ?>  
  
  
<?php if ($products) { ?>
	<div id="tab-related" class="tab-content">
		<div class="box-product">
			<?php foreach ($products as $product) { ?>
				<div>
					<?php if ($product['thumb']) { ?>
						<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
					<?php } ?>
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
					
					<?php if ($product['price']) { ?>
						<div class="price">
							<?php if (!$product['special']) { ?>
								<?php echo $product['price']; ?>
							<?php } else { ?>
								<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if ($product['rating']) { ?>
						<div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
					<?php } ?>
					<a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a>
				</div>
			<?php } ?>
		</div>
	</div><!--close tab-related -->
<?php } ?>






<?php if ($tags) { ?>
	<div class="tags"><?php echo $text_tags; ?>
	<?php foreach ($tags as $tag) { ?>
		<a href="<?php echo $tag['href']; ?>"><?php echo $tag['tag']; ?></a>,
	<?php } ?>
	</div>
<?php } ?>
  
  
	<?php echo $content_bottom; ?>
	  
	</div><!--main-content-->
</div><!--content-container-->
  
<?php echo $footer; ?>