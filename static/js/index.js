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
	
});

//window loaded functions
$(window).load(function(){  

});

/* Ajax Cart */
$('#cart > .heading a').bind('click', function() {

	$('#cart').addClass('active');
	
	$.ajax({
		url: '/store/index.php?route=checkout/cart/update',
		dataType: 'json',
		success: function(json) {
			if (json['output']) {
				$('#cart .content').html(json['output']);
			}
		}
	});			
	
	$('#cart').bind('mouseleave', function() {
		$(this).removeClass('active');
	});
});


function addToCart(product_id) {
	$.ajax({
		url: '/store/index.php?route=checkout/cart/update',
		type: 'post',
		data: 'product_id=' + product_id,
		dataType: 'json',
		success: window.location = '/store/index.php?route=checkout/cart'
		/*
		function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			}
			
			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="warning" style="display: none;">' + json['error']['warning'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
					
					$('.warning').fadeIn('slow');
					
					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			}	 
						
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');
				
				$('.success').fadeIn('slow');
				
				$('#cart_total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}*/
	});
}

