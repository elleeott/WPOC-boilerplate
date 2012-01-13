// dom ready functions
$(document).ready(function(){

	if (document.documentElement.clientWidth >= 600) { // viewport width
	//if (screen.width >= 600) { //display width
		//fancybox popovers
		$('#support-links a').fancybox({
			'width' : 560,
			'height' : 400,
			'padding' :20,
			'type' : 'iframe'
		});
		$('a.fancybox').fancybox();
		
		//top menu
		$('#menu-primary-nav > li').hover(function() {
			$(this).children('ul').css({display:'block'});
		}, function() {
			$(this).children('ul').css({display:'none'});
		});
	}
	
	//add to cart
	$('.add-to-cart-form button').click(function(){
		//alert('!');
		//return;
		theID=$(this).siblings('input[type=\'hidden\']').val();
		theQty=$(this).siblings('input[type=\'text\']').val();
		//alert(theID + ' - ' + theQty);
		$.ajax({
			url: '/store/index.php?route=checkout/cart/update',
			type: 'post',
			dataType: 'json',
			//data:'product_id=' + theID + '&quantity=' + theQty,
			data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
			//success: window.location = '/store/index.php?route=checkout/cart'
			success:
			
			function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			$('.option').removeClass('field-error');
				if (json['error']) {
					if (json['error']['warning']) {
						$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
					
						$('.warning').fadeIn('slow');
					}
					
					for (i in json['error']) {
						$('#option-' + i).prepend('<span class="error">' + json['error'][i] + '</span>');
						$('#option-' + i).addClass('field-error');
					}
				}	
				if (json['success']) {
					$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '</div>');
						
					$('.success').fadeIn('slow');
						
					$('#cart_total').html(json['total']);
					
					//$('html, body').animate({ scrollTop: 0 }, 'slow'); 
				}	
			}
			
		});
		return false;
	});
});

//window loaded functions
$(window).load(function(){  
    $('.flexslider').flexslider({
    	animation:'slide',
    	controlsContainer: '#hero .container'
    });
});





