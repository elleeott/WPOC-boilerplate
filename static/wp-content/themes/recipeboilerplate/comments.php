<section class="comments">
	<?php if (function_exists('fb_comments')) : ?>
		<?php fb_comments(); ?>
	<?php endif; ?>
	<?php if ( have_comments() ) : ?>
		<div class="comments-list">
			<h3><?php comments_number('No comments', 'One comment', '% comments'); ?></h3>
			<ol>
				<?php wp_list_comments( array( 'callback' => 'custom_comments' ) );?>
			</ol>
		</div>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>