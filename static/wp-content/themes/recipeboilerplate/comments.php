<section class="comments">
	<?php if (function_exists('fb_comments')) : ?>
		<?php fb_comments(); ?>
	<?php endif; ?>
	<?php if ( have_comments() ) : ?>
		<?php comments_number('No comments', 'One comment', '% comments'); ?>
		<ol>
			<?php wp_list_comments( array( 'callback' => 'custom_comments' ) );?>
		</ol>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>