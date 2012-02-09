<?php if ( comments_open() ) : ?> 
<div class="fb-comments">
	<fb:comments href="<?php echo the_permalink(); ?>'"></fb:comments>
</div>
<?php endif; ?>
<?php if ( have_comments() ) : ?>
	<?php comments_number('No comments', 'One comment', '% comments'); ?>
	<ol>
		<?php wp_list_comments(); ?>
	</ol>
<?php endif; ?>
<?php 
	$args = array(
		'comment_notes_before' => '<fb:comments href="<?php the_permalink(); ?>" width="880"></fb:comments>'
	);
?>
<?php comment_form($args); ?>