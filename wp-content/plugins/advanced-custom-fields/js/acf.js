(function($){
	
	$.fn.exists = function()
	{
		return jQuery(this).length>0;
	};
	
	$(document).ready(function(){
		
		// add new buttons to screen meta
		$('#contextual-help-link-wrap').each(function(){
			$(this).hide();
		});
		$('.screen-meta-toggle.acf').each(function(){
			$('#screen-meta-links').append($(this));
		});
	
		$('.screen-meta-wrap.acf').each(function() {
			$('#screen-meta-links').before($(this));
		});
		
		$('#screen-meta-links a.show-settings').unbind('click').click(function() {
			
			var a = $(this);
			var div = a.parent();
			
			$(a.attr('href')+'-wrap').slideToggle('fast', function() {
				if (div.hasClass('screen-meta-active')) {
					div.removeClass('screen-meta-active');
					//a.css({'background-position':'right top'}).removeClass('screen-meta-shown');
					div.siblings().css('visibility', 'visible');
				}
				else {
					div.addClass('screen-meta-active');
					div.siblings().css('visibility', 'hidden');
					//a.css({'background-position':'right bottom'}).addClass('screen-meta-shown').parent().css('visibility', 'visible');
				}
			});
			return false;
		});
		
		
		$('.acf_col_right').each(function(){
		
			$('.wrap').wrapInner('<div class="acf_col_left" />');
			$('.wrap').wrapInner('<div class="acf_cols" />');
			$(this).removeClass('hidden').prependTo('.acf_cols');

		});
		
		// add active to Settings Menu
		$('#adminmenu #menu-settings').addClass('current wp-menu-open');
		
				
	});

})(jQuery);