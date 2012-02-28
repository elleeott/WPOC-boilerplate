<?php echo $header; ?>
<div id="content-container" class="store-checkout clearfix">
	<div class="container">
	  <h1><?php echo $heading_title; ?></h1>
		<div class="checkout">
			<div id="checkout" class="checkout-section">
				<div class="checkout-heading"><?php echo $text_checkout_option; ?></div>
				<div class="checkout-content"></div>
			</div>
			<?php if (!$logged) { ?>
				<div id="payment-address" class="checkout-section">
					<div class="checkout-heading"><span><?php echo $text_checkout_account; ?></span></div>
					<div class="checkout-content"></div>
				</div>
			<?php } else { ?>
				<div id="payment-address" class="checkout-section">
					<div class="checkout-heading"><span><?php echo $text_checkout_payment_address; ?></span></div>
					<div class="checkout-content"></div>
				</div>
			<?php } ?>
			<?php if ($shipping_required) { ?>
				<div id="shipping-address" class="checkout-section">
					<div class="checkout-heading"><?php echo $text_checkout_shipping_address; ?></div>
					<div class="checkout-content"></div>
				</div>
				<div id="shipping-method" class="checkout-section">
					<div class="checkout-heading"><?php echo $text_checkout_shipping_method; ?></div>
					<div class="checkout-content"></div>
				</div>
			<?php } ?>
			<div id="payment-method" class="checkout-section">
				<div class="checkout-heading"><?php echo $text_checkout_payment_method; ?></div>
				<div class="checkout-content"></div>
			</div>
			<div id="confirm" class="checkout-section">
				<div class="checkout-heading"><?php echo $text_checkout_confirm; ?></div>
				<div class="checkout-content"></div>
			</div>
		</div><!--close checkout-->
	  <?php echo $content_bottom; ?>
	</div><!--close container-->
</div><!--close content-container-->




<script type="text/javascript">

/*
// toggle guest vs. register checkout
$('#checkout .checkout-content input[name=\'account\']').live('change', function() {
	if ($(this).attr('value') == 'register') {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_account; ?>');
	} else {
		$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
	}

});
*/

$(document).ready(function() {

	$('.checkout-heading a').live('click', function() {
		$('.checkout-content').slideUp('slow');
		$(this).parent().parent().find('.checkout-content').slideDown('slow');
	});

});

<?php if (!$logged) { ?> 
$(document).ready(function() {
	$.ajax({
		url: 'index.php?route=checkout/login',
		dataType: 'json',
		success: function(json) {
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {		
				$('#checkout .checkout-content').html(json['output']);
				$('#checkout .checkout-content').slideDown('slow');
				_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step1: Checkout Options']);
			}
		}
	});	
});		
<?php } else { ?>
$(document).ready(function() {
	$.ajax({
		url: 'index.php?route=checkout/address/payment',
		dataType: 'json',
		success: function(json) {
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {
				$('#payment-address .checkout-content').html(json['output']);
				$('#payment-address .checkout-content').slideDown('slow');
				_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step2: Billing Details']);
			}
		}
	});	
});
<?php } ?>

$(document).ready(function() {
	$.ajax({
		url: 'index.php?route=checkout/guest',
		dataType: 'json',
		success: function(json) {
			$('.warning').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {			
				$('#payment-address .checkout-content').html(json['output']);
				
				//$('#checkout .checkout-content').slideUp('slow');
				
				$('#payment-address .checkout-content').slideDown('slow');
				
				$('.checkout-heading a').remove();
				
				//$('#checkout .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
				_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step2: Billing Details']);
			}
		}
	});
});


// Checkout
/*
$('#button-account').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/' + $('input[name=\'account\']:checked').attr('value'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-account').attr('disabled', true);
			$('#button-account').addClass('busy');
		},		
		complete: function() {
			$('#button-account').attr('disabled', false);
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.warning').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['output']) {			
				$('#payment-address .checkout-content').html(json['output']);
				
				$('#checkout .checkout-content').slideUp('slow');
				
				$('#payment-address .checkout-content').slideDown('slow');
				
				$('.checkout-heading a').remove();
				
				$('#checkout .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
				_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step2: Billing Details']);
			}
		}
	});
});
*/

// Login
/*
$('#button-login').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/login',
		type: 'post',
		data: $('#checkout #login :input'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-login').attr('disabled', true);
			$('#button-login').addClass('busy');
		},	
		complete: function() {
			$('#button-login').attr('disabled', false);
			$('.busy').removeClass('busy');
		},				
		success: function(json) {
			$('.warning').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['total']) {
				$('#cart_total').html(json['total']);
			}
			
			if (json['logged']) {
				$('#welcome').html(json['logged']);
			}
						
			if (json['error']) {
				$('#checkout .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
				
				$('.warning').fadeIn('slow');
			} else {								
				$.ajax({
					url: 'index.php?route=checkout/address/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}						
						
						if (json['output']) {
							$('#payment-address .checkout-content').html(json['output']);
							
							$('#checkout .checkout-content').slideUp('slow');
							
							$('#payment-address .checkout-content').slideDown('slow');
							
							$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
							
							$('.checkout-heading a').remove();
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step2: Billing Details']);
						}
					}
				});	
			}
		}
	});	
});
*/

// Register
/*
$('#button-register').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/register',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-register').attr('disabled', true);
			$('#button-register').addClass('busy');
		},	
		complete: function() {
			$('#button-register').attr('disabled', false); 
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.warning').remove();
			$('.error').remove();
						
			if (json['redirect']) {
				location = json['redirect'];
			}
						
			if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
					
					$('.warning').fadeIn('slow');
				}
				
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\'] + br').after('<span class="error">' + json['error']['firstname'] + '</span>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\'] + br').after('<span class="error">' + json['error']['lastname'] + '</span>');
				}	
				
				if (json['error']['email']) {
					$('#payment-address input[name=\'email\'] + br').after('<span class="error">' + json['error']['email'] + '</span>');
				}
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\'] + br').after('<span class="error">' + json['error']['telephone'] + '</span>');
				}		
										
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\'] + br').after('<span class="error">' + json['error']['address_1'] + '</span>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\'] + br').after('<span class="error">' + json['error']['city'] + '</span>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\'] + br').after('<span class="error">' + json['error']['postcode'] + '</span>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\'] + br').after('<span class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\'] + br').after('<span class="error">' + json['error']['zone'] + '</span>');
				}
				
				if (json['error']['password']) {
					$('#payment-address input[name=\'password\'] + br').after('<span class="error">' + json['error']['password'] + '</span>');
				}	
				
				if (json['error']['confirm']) {
					$('#payment-address input[name=\'confirm\'] + br').after('<span class="error">' + json['error']['confirm'] + '</span>');
				}																																	
			} else {
				<?php if ($shipping_required) { ?>				
				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');
				
				if (shipping_address) {
					$.ajax({
						url: 'index.php?route=checkout/shipping',
						dataType: 'json',
						success: function(json) {
							if (json['redirect']) {
								location = json['redirect'];
							}
														
							if (json['output']) {
								$('#shipping-method .checkout-content').html(json['output']);
								
								$('#payment-address .checkout-content').slideUp('slow');
								
								$('#shipping-method .checkout-content').slideDown('slow');
								_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step4: Shipping Method']);
								
								$('#checkout .checkout-heading a').remove();
								$('#payment-address .checkout-heading a').remove();
								$('#shipping-address .checkout-heading a').remove();
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();											
								
								$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
								$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
	
								$.ajax({
									url: 'index.php?route=checkout/address/shipping',
									dataType: 'json',
									success: function(json) {
										if (json['redirect']) {
											location = json['redirect'];
										}										
										
										if (json['output']) {
											$('#shipping-address .checkout-content').html(json['output']);
										}
									}
								});	
							}
						}
					});	
				} else {
					$.ajax({
						url: 'index.php?route=checkout/address/shipping',
						dataType: 'json',
						success: function(json) {
							if (json['redirect']) {
								location = json['redirect'];
							}
										
							if (json['output']) {
								$('#shipping-address .checkout-content').html(json['output']);
								
								$('#payment-address .checkout-content').slideUp('slow');
								
								$('#shipping-address .checkout-content').slideDown('slow');
								
								$('#checkout .checkout-heading a').remove();
								$('#payment-address .checkout-heading a').remove();
								$('#shipping-address .checkout-heading a').remove();
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();							

								$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
								_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step3: Shipping Address']);
							}
						}
					});			
				}
				<?php } else { ?>
				$.ajax({
					url: 'index.php?route=checkout/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}	
											
						if (json['output']) {
							$('#payment-method .checkout-content').html(json['output']);
							
							$('#payment-address .checkout-content').slideUp('slow');
							
							$('#payment-method .checkout-content').slideDown('slow');
							
							$('#checkout .checkout-heading a').remove();
							$('#payment-address .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();								
							
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step5: Payment Method']);
						}
					}
				});					
				<?php } ?>
				
				$.ajax({
					url: 'index.php?route=checkout/address/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
									
						if (json['output']) {
							$('#payment-address .checkout-content').html(json['output']);
							
							$('#payment-address .checkout-heading span').html('<?php echo $text_checkout_payment_address; ?>');
						}
					}
				});
			}	 
		}
	});	
});
*/

// Payment Address	
/*
$('#payment-address #button-address').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/address/payment',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#payment-address #button-address').attr('disabled', true);
			$('#payment-address #button-address').addClass('busy');
		},	
		complete: function() {
			$('#payment-address #button-address').attr('disabled', false);
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
				}	
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
				}		
										
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
				}
			} else {
				<?php if ($shipping_required) { ?>
				$.ajax({
					url: 'index.php?route=checkout/address/shipping',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
									
						if (json['output']) {
							$('#shipping-address .checkout-content').html(json['output']);
						
							$('#payment-address .checkout-content').slideUp('slow');
							
							$('#shipping-address .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step3: Shipping Address']);
							
							$('#payment-address .checkout-heading a').remove();
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
							
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						}
					}
				});
				<?php } else { ?>
				$.ajax({
					url: 'index.php?route=checkout/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}	
											
						if (json['output']) {
							$('#payment-method .checkout-content').html(json['output']);
						
							$('#payment-address .checkout-content').slideUp('slow');
							
							$('#payment-method .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step5: Payment Method']);
							
							$('#payment-address .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
														
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						}
					}
				});	
				<?php } ?>
				
				$.ajax({
					url: 'index.php?route=checkout/address/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
									
						if (json['output']) {
							$('#payment-address .checkout-content').html(json['output']);
						}
					}
				});					
			}	  
		}
	});	
});

*/
// Shipping Address	
/*		
$('#shipping-address #button-address').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/address/shipping',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#shipping-address #button-address').attr('disabled', true);
			$('#shipping-address #button-address').addClass('busy');
		},	
		complete: function() {
			$('#shipping-address #button-address').attr('disabled', false);
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
				}
				
				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
				}	
				
				if (json['error']['email']) {
					$('#shipping-address input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
				}
				
				if (json['error']['telephone']) {
					$('#shipping-address input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
				}		
										
				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
				}	
				
				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
				}	
				
				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').after('<span class="error">' + json['error']['postcode'] + '</span>');
				}	
				
				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').after('<span class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone'] + '</span>');
				}
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
									
						if (json['output']) {
							$('#shipping-method .checkout-content').html(json['output']);
							
							$('#shipping-address .checkout-content').slideUp('slow');
							
							$('#shipping-method .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step4: Shipping Method']);
							
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
							
							$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');							
						}
						
						$.ajax({
							url: 'index.php?route=checkout/address/shipping',
							dataType: 'json',
							success: function(json) {
								if (json['redirect']) {
									location = json['redirect'];
								}	
													
								if (json['output']) {
									$('#shipping-address .checkout-content').html(json['output']);
								}
							}
						});						
					}
				});	
			}  
		}
	});	
});


*/
// Guest



$('#button-guest').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/guest',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'email\'], #payment-address input[type=\'checkbox\']:checked, #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-guest').attr('disabled', true);
			$('#button-guest').addClass('busy');
			$('label.error').remove();
			$('input.error').removeClass('error');
			$('select.error').removeClass('error');
		},	
		complete: function() {
			$('#button-guest').attr('disabled', false); 
		},			
		success: function(json) {
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').addClass('error').after('<label class="error">' + json['error']['firstname'] + '</label>');
				}
				
				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').addClass('error').after('<label class="error">' + json['error']['lastname'] + '</label>');
				}	
				
				if (json['error']['email']) {
					$('#payment-address input[name=\'email\']').addClass('error').after('<label class="error">' + json['error']['email'] + '</label>');
				}
				
				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\']').addClass('error').after('<label class="error">' + json['error']['telephone'] + '</label>');
				}		
										
				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\']').addClass('error').after('<label class="error">' + json['error']['address_1'] + '</label>');
				}	
				
				if (json['error']['city']) {
					$('#payment-address input[name=\'city\'] ').addClass('error').after('<label class="error">' + json['error']['city'] + '</label>');
				}	
				
				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\']').addClass('error').after('<label class="error">' + json['error']['postcode'] + '</label>');
				}	
				
				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\']').addClass('error').after('<label class="error">' + json['error']['country'] + '</label>');
				}	
				
				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\']').addClass('error').after('<label class="error">' + json['error']['zone'] + '</label>');
				}
				$('.busy').removeClass('busy');
			} else {
				<?php if ($shipping_required) { ?>	
				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').attr('value');
				
				if (shipping_address) {
					$.ajax({
						url: 'index.php?route=checkout/shipping',
						dataType: 'json',
						success: function(json) {
							if (json['redirect']) {
								location = json['redirect'];
							}
										
							if (json['output']) {
								$('#shipping-method .checkout-content').html(json['output']);
								
								$('#payment-address .checkout-content').slideUp('slow');
								$('.busy').removeClass('busy');
								$('#shipping-method .checkout-content').slideDown('slow');
								_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step4: Shipping Method']);
								
								$('#payment-address .checkout-heading a').remove();
								$('#shipping-address .checkout-heading a').remove();
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();		
																
								$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
								$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');									
							}
							
							$.ajax({
								url: 'index.php?route=checkout/guest/shipping',
								dataType: 'json',
								success: function(json) {
									if (json['redirect']) {
										location = json['redirect'];
									}
												
									if (json['output']) {
										$('#shipping-address .checkout-content').html(json['output']);
									}
								}
							});
						}
					});					
				} else {
					$.ajax({
						url: 'index.php?route=checkout/guest/shipping',
						dataType: 'json',
						success: function(json) {
							if (json['redirect']) {
								location = json['redirect'];
							}	
													
							if (json['output']) {
								$('#shipping-address .checkout-content').html(json['output']);
								
								$('#payment-address .checkout-content').slideUp('slow');
								$('.busy').removeClass('busy');

								$('#shipping-address .checkout-content').slideDown('slow');
								_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step3: Shipping Address']);
								
								$('#payment-address .checkout-heading a').remove();
								$('#shipping-address .checkout-heading a').remove();
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();
								
								$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
							}
						}
					});
				}
				<?php } else { ?>				
				$.ajax({
					url: 'index.php?route=checkout/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}		
										
						if (json['output']) {
							$('#payment-method .checkout-content').html(json['output']);
							
							$('#payment-address .checkout-content').slideUp('slow');
							$('.busy').removeClass('busy');	
							$('#payment-method .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step2: Billing Details']);
								
							$('#payment-address .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
															
							$('#payment-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						}
					}
				});				
				<?php } ?>
			}	 
		}
	});	
});

// Guest Shipping
$('#button-guest-shipping').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/guest/shipping',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-guest-shipping').attr('disabled', true);
			$('#button-guest-shipping').addClass('busy');
			$('label.error').remove();
			$('input.error').removeClass('error');
			$('select.error').removeClass('error');
		},	
		complete: function() {
			$('#button-guest-shipping').attr('disabled', false); 
		},			
		success: function(json) {
			$('.error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').addClass('error').after('<label class="error">' + json['error']['firstname'] + '</span>');
				}
				
				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').addClass('error').after('<label class="error">' + json['error']['lastname'] + '</span>');
				}	
										
				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').addClass('error').after('<label class="error">' + json['error']['address_1'] + '</span>');
				}	
				
				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').addClass('error').after('<label class="error">' + json['error']['city'] + '</span>');
				}	
				
				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').addClass('error').after('<label class="error">' + json['error']['postcode'] + '</span>');
				}	
				
				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').addClass('error').after('<label class="error">' + json['error']['country'] + '</span>');
				}	
				
				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').addClass('error').after('<label class="error">' + json['error']['zone'] + '</span>');
				}
				$('.busy').removeClass('busy');
			} else {
				$.ajax({
					url: 'index.php?route=checkout/shipping',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
									
						if (json['output']) {
							$('#shipping-method .checkout-content').html(json['output']);
							
							$('#shipping-address .checkout-content').slideUp('slow');
							$('.busy').removeClass('busy');

							$('#shipping-method .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step4: Shipping Method']);
							
							$('#shipping-address .checkout-heading a').remove();
							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
								
							$('#shipping-address .checkout-heading').append('<a><?php echo $text_modify; ?></a>');
						}
					}
				});				
			}	 
		}
	});	
});

$('#button-shipping').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/shipping',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping').attr('disabled', true);
			$('#button-shipping').addClass('busy');
		},	
		complete: function() {
			$('#button-shipping').attr('disabled', false);
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.warning').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
					
					$('.warning').fadeIn('slow');
				}			
			} else {
				$.ajax({
					url: 'index.php?route=checkout/payment',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}
												
						if (json['output']) {
							$('#payment-method .checkout-content').html(json['output']);
							
							$('#shipping-method .checkout-content').slideUp('slow');
							
							$('#payment-method .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step5: Payment Method']);

							$('#shipping-method .checkout-heading a').remove();
							$('#payment-method .checkout-heading a').remove();
							
							$('#shipping-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError);
					}
				});					
			}
		}
	});	
});

$('#button-payment').live('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/payment', 
		type: 'post',
		data: $('#payment-method input[type=\'radio\']:checked, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment').attr('disabled', true);
			$('#button-payment').addClass('busy');
		},	
		complete: function() {
			$('#button-payment').attr('disabled', false);
			$('.busy').removeClass('busy');
		},			
		success: function(json) {
			$('.warning').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
					
					$('.warning').fadeIn('slow');
				}			
			} else {
				$.ajax({
					url: 'index.php?route=checkout/confirm',
					dataType: 'json',
					success: function(json) {
						if (json['redirect']) {
							location = json['redirect'];
						}	
					
						if (json['output']) {
							$('#confirm .checkout-content').html(json['output']);
							
							$('#payment-method .checkout-content').slideUp('slow');
							
							$('#confirm .checkout-content').slideDown('slow');
							_gaq.push(['_trackEvent', 'Checkout', 'Checkout Step','Step6: Confirm Order']);
							
							$('#payment-method .checkout-heading a').remove();
							
							$('#payment-method .checkout-heading').append('<a><?php echo $text_modify; ?></a>');	
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError);
					}
				});					
			}
		}
	});	
});
</script> 
<?php echo $footer; ?>
