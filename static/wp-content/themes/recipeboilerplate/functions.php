<?php
function theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'theme_setup' );


// Get the page number
function get_page_number() {
    if ( get_query_var('paged') ) {
        print ' | ' . __( 'Page ' , 'recipeboilerplate') . get_query_var('paged');
    }
} // end get_page_number

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


// enqueue required scripts per the following method to output correctly in OC:
// http://scribu.net/wordpress/optimal-script-loading.html
if (!is_admin()) {
	function reg_scripts() {
        $protocol='http:';
        if(!empty($_SERVER['HTTPS'])) {
            $protocol='https:';
        }
		
		//remove l10n js
		wp_deregister_script( 'l10n' );	
			
		//reqister protocol relative google cdn jquery
	    wp_deregister_script( 'jquery' );
		wp_register_script('jquery', $protocol.'//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js', false, NULL, true);
		
		// register fancybox
		wp_register_script('fancybox', $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).'/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), NULL, true);
		
		// register theme script
		wp_register_script('site-script', $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).autoVer('/static/js/index.js'), array('jquery'), NULL, true);
	}
	function print_scripts() {
		wp_print_scripts('jquery');	
		wp_print_scripts('fancybox');	
		wp_print_scripts('site-script');	
	}
	add_action('init', 'reg_scripts'); 
	add_action('wp_footer', 'print_scripts'); 
}

//enqueue css
if (!is_admin()) {
	function reg_styles() {
	    $protocol='http:';
	    if(!empty($_SERVER['HTTPS'])) {
	        $protocol='https:';
	    }
	    if (is_page_template('fancy-template.php')){
		    $fancy = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).autoVer('/static/css/fancy.css');
			wp_register_style('fancy',$fancy,false,NULL,'screen');
			wp_enqueue_style('fancy');
		} else {
		    $print = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).autoVer('/static/css/print.css');
		    $base = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).autoVer('/static/css/base.css');
		    $fancybox = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).autoVer('/static/js/fancybox/jquery.fancybox-1.3.4.css');
			wp_register_style('print',$print,false,NULL,'print');
			wp_enqueue_style('print');
			wp_register_style('base',$base,false,NULL,'screen');
			wp_enqueue_style('base');	    
			wp_register_style('fancybox',$fancybox,false,NULL,'screen');
			wp_enqueue_style('fancybox');	    
	    }
	}
	add_action('wp_print_styles','reg_styles');
}

//add modification date to url string for cachebusting
function autoVer($url){
    $path = pathinfo($url);
    $ext = $path['extension'];
    $ver = '.'.filemtime($_SERVER['DOCUMENT_ROOT'].$url).'.'.$ext;
    return str_replace('static/','',$path['dirname']).'/'.str_replace('.'.$ext, $ver, $path['basename']);
}

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