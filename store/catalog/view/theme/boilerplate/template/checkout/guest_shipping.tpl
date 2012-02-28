<fieldset class="required">
	<label for="firstname"><?php echo $entry_firstname; ?></label>
	<input type="text" name="firstname" class="required" value="<?php echo $firstname; ?>" />
</fieldset>

<fieldset class="required">
	<label for="lastname"><?php echo $entry_lastname; ?></label>
	<input type="text" name="lastname" class="required" value="<?php echo $lastname; ?>" />
</fieldset>

<fieldset>
	<label for="company"><?php echo $entry_company; ?></label>
	<input type="text" name="company" class="required" value="<?php echo $company; ?>" />
</fieldset>

<fieldset class="required">
	<label for="address_1"><?php echo $entry_address_1; ?></label>
	<input type="text" name="address_1" class="required" value="<?php echo $address_1; ?>" />
</fieldset>

<fieldset>
	<label for="address_2"><?php echo $entry_address_2; ?></label>
	<input type="text" name="address_2" value="<?php echo $address_2; ?>" />
</fieldset>

<fieldset class="required">
	<label for="city"><?php echo $entry_city; ?></label>
	<input type="text" name="city" class="required" value="<?php echo $city; ?>" />
</fieldset>

<fieldset class="required">
	<label for="postcode"><?php echo $entry_postcode; ?></label>
	<input type="text" name="postcode" class="required" value="<?php echo $postcode; ?>" />
</fieldset>

<fieldset class="required">
	<label for="country_id"><?php echo $entry_country; ?></label>
	<select name="country_id" class="required" onchange="$('#shipping-address select[name=\'zone_id\']').load('index.php?route=checkout/address/zone&country_id=' + this.value);">
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


<div class="buttons">
	<div class="right"><a id="button-guest-shipping" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
</div>
<script type="text/javascript"><!--
$('#shipping-address select[name=\'zone_id\']').load('index.php?route=checkout/address/zone&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>');
//--></script> 