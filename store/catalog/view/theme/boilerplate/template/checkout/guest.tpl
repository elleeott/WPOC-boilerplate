<fieldset class="required">
	<label for="firstname"><?php echo $entry_firstname; ?></label>
	<input type="text" name="firstname" value="<?php echo $firstname; ?>" />
</fieldset>
<fieldset class="required">
	<label for="lastname"><?php echo $entry_lastname; ?></label>
	<input type="text" name="lastname" value="<?php echo $lastname; ?>" />
</fieldset>
<fieldset class="required">
	<label for="email"><?php echo $entry_email; ?></label>
	<input type="email" name="email" value="<?php echo $email; ?>" />
</fieldset>
<fieldset class="required">
	<label for="telephone"><?php echo $entry_telephone; ?></label>
	<input type="text" name="telephone" value="<?php echo $telephone; ?>" />
</fieldset>
<fieldset>
	<label for="fax"><?php echo $entry_fax; ?></label>
	<input type="text" name="fax" value="<?php echo $fax; ?>" />
</fieldset>

<fieldset>
	<label for="company"><?php echo $entry_company; ?></label>
	<input type="text" name="company" value="<?php echo $company; ?>" />
</fieldset>
<fieldset class="required">
	<label for="address_1"><?php echo $entry_address_1; ?></label>
	<input type="text" name="address_1" value="<?php echo $address_1; ?>" />
</fieldset>
<fieldset>
	<label for="address_2"><?php echo $entry_address_2; ?></label>
	<input type="text" name="address_2" value="<?php echo $address_2; ?>" />
</fieldset>
<fieldset class="required">
	<label for="address_2"><?php echo $entry_city; ?></label>
	<input type="text" name="city" value="<?php echo $city; ?>" />
</fieldset>
<fieldset class="required">
	<label for="postcode"><?php echo $entry_postcode; ?></label>
	<input type="text" name="postcode" value="<?php echo $postcode; ?>" />
</fieldset>
<fieldset class="required">
	<label for="country_id"><?php echo $entry_country; ?></label>
	<select name="country_id" class="required" onchange="$('#payment-address select[name=\'zone_id\']').load('index.php?route=checkout/address/zone&country_id=' + this.value);">
	<option value=""><?php echo $text_select; ?></option>
	<?php foreach ($countries as $country) { ?>
	<?php if ($country['country_id'] == $country_id) { ?>
	<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
	<?php } else { ?>
	<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
	<?php } ?>
	<?php } ?>
	</select>
</fieldset>
<fieldset class="required">
	<label for="zone_id"><?php echo $entry_zone; ?></label>
	<select name="zone_id" class="required">
	</select>
</fieldset>
<?php if ($shipping_required) { ?>
<div>
	<?php if ($shipping_address) { ?>
		<input type="checkbox" name="shipping_address" value="1" id="shipping" checked="checked" />
	<?php } else { ?>
		<input type="checkbox" name="shipping_address" value="1" id="shipping" />
	<?php } ?>
	<label for="shipping"><?php echo $entry_shipping; ?></label>

</div>
<?php } ?>
<div class="buttons">
	<div class="right"><a id="button-guest" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
</div>
<script type="text/javascript"><!--
$('#payment-address select[name=\'zone_id\']').load('index.php?route=checkout/address/zone&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>');
//--></script> 