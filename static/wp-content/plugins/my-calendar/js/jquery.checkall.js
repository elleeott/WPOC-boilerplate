jQuery(document).ready(function($) {
	$(".selectall").click(function() {
		var checked_status = $(this).prop('checked');
		var checkbox_name = $(this).attr('id');
		$('input[name="' + checkbox_name + '[]"]').each(function() {
			$(this).prop('checked', checked_status);
		});
	});
});