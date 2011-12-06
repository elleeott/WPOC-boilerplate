<?php if ( have_comments() ) : ?>
	<h4>Comments</h4>
	<ol>
		<?php //wp_list_comments(); ?>
		<?php wp_list_comments( array( 'callback' => 'custom_comments' ) );?>
	</ol>
<?php endif; ?>
<?php comment_form(); ?>