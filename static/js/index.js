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
	} else {
		$('#mobile-nav button').click(function(){
			window.location=$('#mobile-nav select option:selected').val();
			return false;
		});
	}
	
	//product page add to cart
	$('.products-detail .add-to-cart-form a.button').click(function(){
		$.ajax({
			url: '/store/index.php?route=checkout/cart/update',
			type: 'post',
			dataType: 'json',
			data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
			//success: window.location = '/store/index.php?route=checkout/cart'
			success:
			
			function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			$('.option').removeClass('field-error');
				if (json['error']) {
					if (json['error']['warning']) {
						$('#notification').html('<div class="warning container" style="display: none;">' + json['error']['warning'] + '</div>');
						$('.warning').fadeIn('slow').delay(1000).fadeOut('slow');
					}
					
					for (i in json['error']) {
						$('#option-' + i).prepend('<span class="error">' + json['error'][i] + '</span>');
						$('#option-' + i).addClass('field-error');
					}
				}	
				if (json['success']) {
					$('#notification').html('<div class="success container" style="display: none;">' + json['success'] + '</div>');
					$('.success').fadeIn('slow').delay(1000).fadeOut('slow');
					$('#cart_total').html(json['total']);
					prodName= $('h1.fn').html();
					_gaq.push(['_trackEvent', 'Products', 'Add to Cart',prodName]);
				}	
			}
			
		});
		return false;
	});
	
	//category page add to carts
	$('.products-category .add-to-cart-form .button').click(function(){
		product_id = $(this).siblings('input[name=\'product_id\']').val();
		prodName = $(this).parents('.product-cell').find('h3 a').html();
		$.ajax({
			url: '/store/index.php?route=checkout/cart/update',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.success, .warning, .attention, .information, .error').remove();
				
				if (json['redirect']) {
					location = json['redirect'];
				}
				if (json['error']) {
					if (json['error']['warning']) {
						$('#notification').html('<div class="warning container" style="display: none;">' + json['error']['warning'] + '</div>');
						$('.warning').fadeIn('slow').delay(1000).fadeOut('slow');
						$('html, body').animate({ scrollTop: 0 }, 'slow');
					}
				}	 
				if (json['success']) {
					$('#notification').html('<div class="success container" style="display: none;">' + json['success'] + '</div>');
					$('.success').fadeIn('slow').delay(1000).fadeOut('slow');
					$('#cart_total').html(json['total']);
					_gaq.push(['_trackEvent', 'Products', 'Add to Cart',prodName]);
				}	
			}
		});
		
		return false;
	});	
	
});


//window loaded functions
$(window).load(function(){  

	//hp slider
    $('body.home .flexslider').flexslider({
    	animation:'slide',
    	controlsContainer: '#hero .container'
    });
    
    //prod page slider
    $('body.single-products .flexslider').flexslider({
    	animation:'slide',
    	controlsContainer: '.product-img',
    	animationDuration: 200,
    	directionNav: false
    });
});





