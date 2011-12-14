<?php if ( have_comments() ) : ?>
	<?php comments_number('No comments', 'One comment', '% comments'); ?>
	<ol>
		<?php wp_list_comments(); ?>
	</ol>
<?php endif; ?>
<?php comment_form(); ?>