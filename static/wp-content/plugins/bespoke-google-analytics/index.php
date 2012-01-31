<?php
/*
Plugin Name: Custom Google Analytics
Description: Adds google analytics tracking and conversion event tracking
*/

function insert_ga_code() { 
	global $wpdb;
	$store_name = 'Your Store Name Here';
	$web_id = 'UA-28366078-1';
	
	if(isset($_SESSION['order_id'])) {	
		$order_id = $_SESSION['order_id'];
		$order_details = $wpdb->get_row($wpdb->prepare("
			SELECT * 
			FROM oc_order
			WHERE order_id = $order_id
		"));
		
		$shipping = $wpdb->get_var($wpdb->prepare("
			SELECT value
			FROM oc_order_total
			WHERE order_id = $order_id
			AND code = 'shipping'
		"));
		$sub_total = $wpdb->get_var($wpdb->prepare("
			SELECT value
			FROM oc_order_total
			WHERE order_id = $order_id
			AND code = 'sub_total'
		"));
		$tax = $wpdb->get_var($wpdb->prepare("
			SELECT value
			FROM oc_order_total
			WHERE order_id = $order_id
			AND code = 'tax'
		"));
		if(!$tax){
			$tax = 0;
		}
		$order_product_details = $wpdb->get_results($wpdb->prepare("
			SELECT * 
			FROM oc_order_product
			WHERE order_id = $order_id
		"));
		//print_r($order_details);
		//print_r($order_product_details);
	}
	?>
	
	
	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $web_id;?>']);
	  _gaq.push(['_trackPageview']);
	  
	<?php if(isset($order_id)) :?>
		_gaq.push(['_addTrans','<?php echo $order_id; ?>','<?php echo $store_name; ?>','<?php echo $sub_total; ?>','<?php echo $tax;  ?>','<?php echo $shipping; ?>','<?php echo $order_details->shipping_city ;?>','<?php echo $order_details->shipping_zone ;?>','<?php echo $order_details->shipping_country;?>']);
		<?php foreach ($order_product_details as $row) : ?>
			<?php $sku = $wpdb->get_var($wpdb->prepare("
				SELECT sku
				FROM oc_product
				WHERE product_id = $row->product_id
			"));
			?>
			_gaq.push(['_addItem','<?php echo $order_id; ?>','<?php echo $sku; ?>','<?php echo $row->name; ?>','<?php /*product category or variation*/ ?>','<?php echo $row->price; ?>','<?php echo $row->quantity; ?>']);
		<?php endforeach; ?>
		_gaq.push(['_trackTrans']);
	<?php endif;?>
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	<?php
	
	unset($_SESSION['order_id']);
 
}
