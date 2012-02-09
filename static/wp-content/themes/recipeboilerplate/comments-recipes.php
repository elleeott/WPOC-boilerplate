<?php if ( comments_open() ) : ?> 
<div class="fb-comments">
	<fb:comments href="<?php echo the_permalink(); ?>'"></fb:comments>
</div>
<?php endif; ?>
<?php if ( have_comments() ) : ?>
    <?php comments_number('No comments', 'One comment', '% comments'); ?>
    <ol>
		<?php wp_list_comments( array( 'callback' => 'custom_comments' ) );?>
	</ol>
<?php endif; ?>
<?php comment_form(); ?>