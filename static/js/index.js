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
		theID=$(this).siblings('input[type=\'hidden\']').val();
		theQty=$(this).siblings('input[type=\'text\']').val();
		//alert(theID + ' - ' + theQty);
		$.ajax({
			url: '/store/index.php?route=checkout/cart/update',
			type: 'post',
			dataType: 'json',
			data:'product_id=' + theID + '&quantity=' + theQty,
			success: window.location = '/store/index.php?route=checkout/cart'
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





