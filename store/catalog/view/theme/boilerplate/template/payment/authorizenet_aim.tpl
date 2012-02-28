<h2><?php echo $text_credit_card; ?></h2>
<div id="payment">
	<fieldset>
		<label for="cc_owner"><?php echo $entry_cc_owner; ?></label>
		<input type="text" name="cc_owner" value="" />
	</fieldset>
	
	<fieldset class="required">
		<label for="cc_number"><?php echo $entry_cc_number; ?></label>
		<input type="text" name="cc_number" class="required" value="" />
	</fieldset>
	
	<fieldset class="required">
		<label for="cc_expire_date"><?php echo $entry_cc_expire_date; ?></label>
		<select class="required" name="cc_expire_date_month">
			<?php foreach ($months as $month) { ?>
			<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
			<?php } ?>
		</select>
		/
		<select class="required" name="cc_expire_date_year">
			<?php foreach ($year_expire as $year) { ?>
			<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
			<?php } ?>
		</select>
	</fieldset>
	
	<fieldset class="required">
		<label for="cc_cvv2"><?php echo $entry_cc_cvv2; ?></label>
		<input type="text" name="cc_cvv2" value="" size="3" />
	</fieldset>
</div>

<div class="buttons">
  <div class="right"><a id="button-confirm" class="button button-primary"><span><?php echo $button_confirm; ?></span></a></div>
</div>




<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {

	$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/authorizenet_aim/send',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(json) {
			$('.success, .warning, .attention, .information, .error').remove();
			if (json['error']) {
				//alert(json['error']);
				$('#notification').html('<div class="warning" style="display: none;">' + json['error'] + '</div>');
				$('.warning').fadeIn('slow');
				$('#button-confirm').attr('disabled', false);
			}
						
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
/*	$.ajax({
		type: 'POST',
		url: 'index.php?route=payment/authorizenet_aim/send',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				$('#button-confirm').attr('disabled', false);
			}
			
			$('.attention').remove();
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});*/

//--></script>