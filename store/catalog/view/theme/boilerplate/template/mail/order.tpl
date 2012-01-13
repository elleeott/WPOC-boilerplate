<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body style="margin:0; padding:0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td align="center">
		<table width="680"  border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td align="left">
					<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img src="<?php echo $store_url; ?>image/email/confirm/logo.png" border="0" alt="<?php echo $store_name; ?>"></a>
					<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<div style="font-size:18px;font-family:georgia;color:#444;">
						Thank you for your Order!
					</div>
					<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<div style="font-size:14px;font-family:georgia;color:#444;">
						Thank you for shopping at <?php echo $store_name; ?>. We&rsquo;re delighted to confirm that your order has been received successfully.
					</div>
					<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<div style="font-size:14px;font-family:georgia;color:#444;">
						<b>Here is your Order Reference number:</b> <?php echo $order_id; ?>
					</div>
					<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<div style="font-size:14px;font-family:georgia;color:#444;">
						Please note that your order is expected to ship within 2-3 business days, and you will receive an email with order tracking information at that time. Here is a summary of the order you placed on <?php echo $date_added; ?>.
					</div>
					<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #DDDDDD;border-left: 1px solid #DDDDDD;">
					      <tr>
					        <td bgcolor="#efefef" colspan="2" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_order_detail; ?></b></td>
					      </tr>
					      <tr>
					        <td style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;">
					          <b><?php echo $text_order_id; ?></b> <?php echo $order_id; ?><br>
					          <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?><br>
					          <b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br>
					          <?php if ($shipping_method) { ?>
					          <b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
					          <?php } ?>
					        </td>
					        <td align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_email; ?></b> <?php echo $email; ?><br>
					          <b><?php echo $text_telephone; ?></b> <?php echo $telephone; ?><br>
					          <b><?php echo $text_ip; ?></b> <?php echo $ip; ?><br></td>
					      </tr>
					  </table>
					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #DDDDDD;border-left: 1px solid #DDDDDD;">
					      <tr>
					        <td bgcolor="#efefef" align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_payment_address; ?></b></td>
					        <?php if ($shipping_address) { ?>
					        <td bgcolor="#efefef" align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_shipping_address; ?></b></td>
					        <?php } ?>
					      </tr>
					      <tr>
					        <td align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $payment_address; ?></td>
					        <?php if ($shipping_address) { ?>
					        <td align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $shipping_address; ?></td>
					        <?php } ?>
					      </tr>
					  </table>
					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px solid #DDDDDD;border-left: 1px solid #DDDDDD;">
					      <tr>
					        <td bgcolor="#efefef" align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_product; ?></b></td>
					        <td bgcolor="#efefef" align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_model; ?></b></td>
					        <td bgcolor="#efefef" align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_quantity; ?></b></td>
					        <td bgcolor="#efefef" align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_price; ?></b></td>
					        <td bgcolor="#efefef" align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $text_total; ?></b></td>
					      </tr>
					      <?php foreach ($products as $product) { ?>
					      <tr>
					        <td align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo str_replace("&amp;amp;", "&amp;", htmlentities($product['name'], ENT_QUOTES, "UTF-8")); ?>
					          <?php foreach ($product['option'] as $option) { ?>
					          <br>
					          <span style="font-size:11px;font-color:#666666;"> - <?php echo $option['name']; ?></span>
					          <?php } ?></td>
					        <td align="left" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $product['model']; ?></td>
					        <td align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $product['quantity']; ?></td>
					        <td align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $product['price']; ?></td>
					        <td align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $product['total']; ?></td>
					      </tr>
					      <?php } ?>
					      <?php foreach ($totals as $total) { ?>
					      <tr>
					        <td colspan="4" align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><b><?php echo $total['title']; ?>:</b></td>
					        <td align="right" style="border-right: 1px solid #DDDDDD;border-bottom: 1px solid #DDDDDD;padding:7px;font-size:12px;font-family:arial,helvetica,sans-serif;"><?php echo $total['text']; ?></td>
					      </tr>
					      <?php } ?>
					  </table>
					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">

					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					  <div style="font-size:14px;font-family:georgia;color:#444;">
						  Have a question or concern about your order? Reply to this email with any questions.
						  Our friendly Customer Service Representatives are here to help. 
						  We want you to be totally satisfied with your <?php echo $store_name; ?> experience.
					  </div>
					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">
					
					  <div style="font-size:14px;font-family:georgia;color:#444;">
						Best regards,<br>
						<?php echo $store_name; ?> Customer Service
						</div>
						<img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">

					  <img src="<?php echo $store_url; ?>image/email/confirm/1x1.gif" height="20" width="680">				
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
