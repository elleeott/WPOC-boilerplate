<div class="left">
	<h2><?php echo $text_new_customer; ?></h2>
	<p><?php echo $text_checkout; ?></p>
	<label for="register">
	<?php if ($account == 'register') { ?>
		<input type="radio" name="account" value="register" id="register" checked="checked" />
	<?php } else { ?>
		<input type="radio" name="account" value="register" id="register" />
	<?php } ?>
	<?php echo $text_register; ?>
	</label>
	<?php if ($guest_checkout) { ?>
	<label for="guest">
		<?php if ($account == 'guest') { ?>
			<input type="radio" name="account" value="guest" id="guest" checked="checked" />
		<?php } else { ?>
			<input type="radio" name="account" value="guest" id="guest" />
		<?php } ?>
		<?php echo $text_guest; ?>
	</label>
	<?php } ?>

	<p><?php echo $text_register_account; ?></p>
	<a id="button-account" class="button button-primary"><span><?php echo $button_continue; ?></span></a>
</div>

<div id="login" class="right">
	<h2><?php echo $text_returning_customer; ?></h2>
	<p><?php echo $text_i_am_returning_customer; ?></p>
	<?php echo $entry_email; ?>
	<input type="text" name="email" value="" />
	<?php echo $entry_password; ?>
	<input type="password" name="password" value="" />
	<a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
	<a id="button-login" class="button button-primary"><span><?php echo $button_login; ?></span></a>
</div>
<script type="text/javascript"><!--
$('#login input').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#button-login').click();
	}
});
//--></script>   