<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($payment_methods) { ?>
<p><?php echo $text_payment_method; ?></p>
  <?php foreach ($payment_methods as $payment_method) { ?>
    <?php if ($payment_method['code'] == $code || !$code) { ?>
      <?php $code = $payment_method['code']; ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" checked="checked" />
      <?php } else { ?>
      <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>" id="<?php echo $payment_method['code']; ?>" />
      <?php } ?>
   <label for="<?php echo $payment_method['code']; ?>"><?php echo $payment_method['title']; ?></label>
  <?php } ?>
<?php } ?>
<?php echo $text_comments; ?>
<textarea name="comment" rows="8"><?php echo $comment; ?></textarea>

<?php if ($text_agree) { ?>
<div class="buttons">
  <div class="right"><?php echo $text_agree; ?>
    <?php if ($agree) { ?>
    <input type="checkbox" name="agree" value="1" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="agree" value="1" />
    <?php } ?>
    <a id="button-payment" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
</div>
<?php } else { ?>
<div class="buttons">
  <div class="right"><a id="button-payment" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
</div>
<?php } ?>