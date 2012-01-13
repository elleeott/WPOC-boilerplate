<?php echo $header; ?>

<div id="content-container" class="clearfix">
	<div class="container">
		<div class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
			<?php } ?>
		</div>
		<h1><?php echo $heading_title; ?></h1>
		<?php echo $text_message; ?>
		<div class="buttons">
			<div class="right"><a href="<?php echo $continue; ?>" class="button button-primary"><span><?php echo $button_continue; ?></span></a></div>
		</div>
		<?php echo $content_bottom; ?>
	</div>
</div><!--close content-container -->
<?php echo $footer; ?>