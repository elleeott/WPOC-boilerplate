<?php

//custom menus
function register_custom_menus() {
	register_nav_menus(
		array(
			'primary-nav' => 'Primary Nav',
			'secondary-nav' => 'Secondary Nav'
		)
	); 
}
add_action( 'init', 'register_custom_menus' );


//register sidebars
function bp_register_sidebars() {
	register_sidebar(array(
		'name' => 'Primary',
		'id'   => 'primary-sidebar',
		'description'   => 'This is a widgetized area.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>'
		)
	);
	register_sidebar(array(
		'name' => 'Secondary',
		'id'   => 'secondary-sidebar',
		'description'   => 'This is a widgetized area.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>'
		)
	);
	register_sidebar(array(
		'name' => 'Tertiary',
		'id'   => 'tertiary-sidebar',
		'description'   => 'This is a widgetized area.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>'
		)
	);
}
add_action( 'widgets_init', 'bp_register_sidebars' );


// custom comments
function custom_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	?>

	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'twentyeleven' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'twentyeleven' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
					<br />
				<?php endif; ?>

				<?php if(is_singular('recipes')){ ?>
					<?php echo "Rating: "; ?>
					<?php if(get_comment_meta( $comment->comment_ID, 'rating', true )!=NULL){ ?>
						<?php echo get_comment_meta( $comment->comment_ID, 'rating', true ); ?>
					<?php } else {?>
						<?php echo "No Rating"; ?>
					<?php }	?>
				<?php }	?>

			<div class="comment-content">


			<?php comment_text(); ?>
			</div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php

}

?>