<?php

function get_oc_options() {
	global $post;
	global $wpdb;
	$oc_prod_id = get_post_meta($post->ID,'_oc_product_id',true);
	//echo $oc_prod_id;
	$options = $wpdb->get_results("

		SELECT * 
		FROM oc_product_option po 
		LEFT JOIN oc_option o 
		ON (po.option_id = o.option_id) 
		LEFT JOIN oc_option_description od 
		ON (o.option_id = od.option_id) 
		WHERE po.product_id = $oc_prod_id 
		AND od.language_id = 1
		ORDER BY o.sort_order	
	");
	//print_r($options);
?>
<form class="add-to-cart-form" action="/store/index.php?route=checkout/cart/update" method="post">
	<fieldset>
<?php	
	foreach($options as $product_option){
		$oc_option_id = $product_option->product_option_id;
		$product_option_values = $wpdb->get_results("
			SELECT * 
			FROM oc_product_option_value pov 
			LEFT JOIN oc_option_value ov 
			ON (pov.option_value_id = ov.option_value_id) 
			LEFT JOIN oc_option_value_description ovd 
			ON (ov.option_value_id = ovd.option_value_id)
			WHERE pov.product_id = $oc_prod_id 
			AND pov.product_option_id = $oc_option_id 
			AND ovd.language_id = 1
			ORDER BY ov.sort_order
		");
	 if($product_option->type == 'select' || $product_option->type == 'radio' || $product_option->type == 'checkbox') { 
?>
		<div id="option-<?php echo $oc_option_id; ?>" class="option">
				<?php if ($product_option->type == 'select') { ?>
					<label for="option[<?php echo $oc_option_id; ?>]" class="<?php if ($product_option->required) { ?>required<?php } ?>">
						<?php echo $product_option->name; ?>:
					</label>
					<select name="option[<?php echo $oc_option_id; ?>]">
						<option value="">Choose an Option</option>
						<?php foreach($product_option_values as $product_option_value){ ?>
							<option value="<?php echo $product_option_value->product_option_value_id; ?>"><?php echo $product_option_value->name; ?>
							<?php if ($product_option_value->price && $product_option_value->price>0) { ?>
								(<?php echo $product_option_value->price_prefix; ?><?php echo format_currency($product_option_value->price); ?>)
							<?php } ?>
							</option>
						<?php } ?>
					</select>
				<?php } ?>
			
				<?php if ($product_option->type == 'radio') { ?>
					<fieldset>
					<legend class="<?php if ($product_option->required) { ?>required<?php } ?>">
						<?php echo $product_option->name; ?>:
					</legend>
						<?php foreach($product_option_values as $product_option_value){ ?>
							<label for="option-value-<?php echo $product_option_value->product_option_value_id; ?>">
								<input type="radio" name="option[<?php echo $oc_option_id; ?>]" value="<?php echo $product_option_value->product_option_value_id; ?>" id="option-value-<?php echo $product_option_value->product_option_value_id; ?>" />
								<?php echo $product_option_value->name; ?>
								<?php if ($product_option_value->price && $product_option_value->price>0) { ?>
									(<?php echo $product_option_value->price_prefix; ?><?php echo format_currency($product_option_value->price); ?>)
								<?php } ?>
							</label>
						<?php } ?>
					</fieldset>
				<?php } ?>
		
				<?php if ($product_option->type == 'checkbox') { ?>
					<fieldset>
						<legend class="<?php if ($product_option->required) { ?>required<?php } ?>">
							<?php echo $product_option->name; ?>:
						</legend>
						<?php foreach($product_option_values as $product_option_value){ ?>
							<label for="option-value-<?php echo $product_option_value->product_option_value_id; ?>">
							<input type="checkbox" name="option[<?php echo $oc_option_id; ?>][]" value="<?php echo $product_option_value->product_option_value_id; ?>" id="option-value-<?php echo $product_option_value->product_option_value_id; ?>" />
							<?php echo $product_option_value->name; ?>
							<?php if ($product_option_value->price && $product_option_value->price>0) { ?>
								(<?php echo $product_option_value->price_prefix; ?><?php echo format_currency($product_option_value->price); ?>)
							<?php } ?>
							</label>
						<?php } ?>
					</fieldset>
				<?php } ?>		
		</div><!--end option -->
	<?php } ?>

<?php
	}
?>

	<div>
		<strong>Item#:</strong>
		<?php echo get_post_meta($post->ID,'_oc_product_sku',true); ?>
	</div>
	<div class="price">
		<?php if(get_post_meta($post->ID,'_oc_product_price_special',true)): ?>
			<strong>Price:</strong>
			<?php echo format_currency(get_post_meta($post->ID,'_oc_product_price',true)); ?><br/>
			<strong>Special Price:</strong>
			<?php echo format_currency(get_post_meta($post->ID,'_oc_product_price_special',true)); ?>
		<?php else: ?>
			<strong>Price:</strong>
			<?php echo format_currency(get_post_meta($post->ID,'_oc_product_price',true)); ?>
		<?php endif; ?>
	</div>

	<input type="hidden" name="product_id" value="<?php echo $oc_prod_id; ?>"/>
	<input type="text" name="quantity" value="1"/>				
	<button class="button button-primary add-to-cart">add to cart</button>
	</fieldset>					
</form>
<?php
}

function format_currency($val){
	$output = money_format('$%i',$val);
	return $output;
}









