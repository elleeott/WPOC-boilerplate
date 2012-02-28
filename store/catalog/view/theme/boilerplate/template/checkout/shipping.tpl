<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($shipping_methods) { ?>
<p><?php echo $text_shipping_method; ?></p>
<table class="form">
  <?php foreach ($shipping_methods as $shipping_method) { ?>
  <thead>
  <tr>
    <th colspan="2"><?php echo $shipping_method['title']; ?></th>
  </tr>
  </thead>
  <?php if (!$shipping_method['error']) { ?>
  <?php foreach ($shipping_method['quote'] as $quote) { ?>
  <tr>
    <td><?php if ($quote['code'] == $code || !$code) { ?>
      <?php $code = $quote['code']; ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" checked="checked" />
    <label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label>
      <?php } else { ?>
      <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" id="<?php echo $quote['code']; ?>" />
    <label for="<?php echo $quote['code']; ?>"><?php echo $quote['title']; ?></label>
      <?php } ?>
    </td>
    <td><label for="<?php echo $quote['code']; ?>"><?php echo $quote['text']; ?></label></td>
  </tr>
  <?php } ?>
  <?php } else { ?>
  <tr>
    <td colspan="2"><div class="error"><?php echo $shipping_method['error']; ?></div></td>
  </tr>
  <?php } ?>
  <?php } ?>
</table>
<?php } ?>
<fieldset>
	<label for="comment"><?php echo $text_comments; ?></label>
	<textarea name="comment" rows="8"><?php echo $comment; ?></textarea>
</fieldset>


<div class="buttons">
  <div class="right"><a id="button-shipping" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
</div>
