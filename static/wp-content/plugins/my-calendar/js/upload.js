jQuery(document).ready(function() {
	jQuery('#upload_image_button').click(function() {
	 formfield = jQuery('#event_image').attr('name');
	 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
	});
	window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#event_image').val(imgurl);
	 tb_remove();
	}
});