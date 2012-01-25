<?php
//create static subdomain
$protocol='http:';
if(!empty($_SERVER['HTTPS'])) {
    $protocol='https:';
}
global $static_subdomain;
$static_subdomain = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']);

//basic theme support setup
function theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'theme_setup' );

//additional image sizes
add_image_size( 'product-large',900,9999);
add_image_size( 'product-med',300,9999);
add_image_size( 'product-thumb',150,200);
add_image_size( 'recipe-large',400,9999);
add_image_size( 'recipe-med',300,9999);
add_image_size( 'recipe-thumb',150,9999);

// custom gallery code
function get_custom_gallery() {
	global $post;
	$featured_img_id = get_post_thumbnail_id($post->ID);
	$attached_imgs = get_posts(array(
		'post_type'=>'attachment',
		'post_mime_type'=>'image',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'post_parent'=>$post->ID
	));
	echo '<ul class="slides">';
	foreach($attached_imgs as $attachment) {
		$prod_large = wp_get_attachment_image_src($attachment->ID,'product-large',true);
		$prod_med = wp_get_attachment_image_src($attachment->ID,'product-med',true);
		$prod_thumb = wp_get_attachment_image_src($attachment->ID,'product-small',true);
		if($attachment->ID == $featured_img_id){
			echo '<li class="focus-img"><a class="fancybox" href="'.$prod_large[0].'"><img class="photo" src="'.$prod_med[0].'" width="'.$prod_med[1].'" height="'.$prod_med[2].'"/></a></li>';
		} else {
			echo '<li><a class="fancybox" href="'.$prod_large[0].'"><img src="'.$prod_med[0].'" width="'.$prod_med[1].'" height="'.$prod_med[2].'"/></a></li>';
		}
	}
	echo '</ul>';
}

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
		global $static_subdomain;
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
		wp_register_script('fancybox', $static_subdomain.'/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), NULL, true);
		
		// register flexslider
		wp_register_script('flexslider', $static_subdomain.'/js/jquery.flexslider-min.js', array('jquery'), NULL, true);
		
		// register theme script
		wp_register_script('site-script', $static_subdomain . autoVer('/static/js/index.js'), array('jquery'), NULL, true);
	}
	function print_scripts() {
		wp_print_scripts('jquery');	
		wp_print_scripts('fancybox');	
		wp_print_scripts('flexslider');	
		wp_print_scripts('site-script');	
	}
	add_action('init', 'reg_scripts'); 
	add_action('wp_footer', 'print_scripts'); 
}

//enqueue css
if (!is_admin()) {
	function reg_styles() {
		global $static_subdomain;
	    if (is_page_template('fancy-template.php')){
		    $fancy = $static_subdomain . autoVer('/static/css/fancy.css');
			wp_register_style('fancy',$fancy,false,NULL,'screen');
			wp_enqueue_style('fancy');
		} else {
		    $print = $static_subdomain . autoVer('/static/css/print.css');
		    $base = $static_subdomain . autoVer('/static/css/base.css');
		    $fancybox = $static_subdomain . autoVer('/static/js/fancybox/jquery.fancybox-1.3.4.css');
		    $flexslider = $static_subdomain . autoVer('/static/css/flexslider.css');
			wp_register_style('print',$print,false,NULL,'print');
			wp_enqueue_style('print');
			wp_register_style('base',$base,false,NULL,'screen');
			wp_enqueue_style('base');	    
			wp_register_style('fancybox',$fancybox,false,NULL,'screen');
			wp_enqueue_style('fancybox');	    
			wp_register_style('flexslider',$flexslider,false,NULL,'screen');
			wp_enqueue_style('flexslider');	    
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

// auto-populate description field using the_excerpt 
function custom_meta_description(){
	global $post;
	if(is_single()){
		$custom_excerpt = get_the_excerpt();
		if($custom_excerpt != '') {
			echo esc_attr($custom_excerpt);
		}
	} else {
		echo 'something something';
	}
}

//customize mce buttons
/*
function add_mce_buttons($buttons){
	return array('formatselect','bold','italic','strikethrough','bullist','numlist','sup','sub','blockquote','link','unlink','undo','redo','charmap','fullscreen');
}
add_filter("mce_buttons", "add_mce_buttons");
*/

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
function set_the_title() {
	//two parameters passed from OC pages
	global $isapage; 
	global $page_title; 
	
	//HP
	if (is_front_page()) { 
		bloginfo('name'); 
		echo ' :: '; 
		bloginfo('description'); 
		get_page_number(); 
	} 
	//single post pages
	elseif (is_single()) {
		single_post_title();
		echo ' :: ';
		echo get_post_type();
		echo ' :: ';
		bloginfo('name');
	}
	//pages
	elseif (is_page()) { 
		single_post_title();
		echo ' :: ';
		bloginfo('name');
	}
	//search results
	elseif (is_search()) {
		bloginfo('name');
		echo ' :: Search results for ' . esc_html($s);
		get_page_number();
	}
	//Opencart Pages
	elseif (is_404() && (isset($isapage))) { 
		bloginfo('name'); 
		echo ' :: '; 
		echo $page_title;
	}
	//actual 404s
	elseif (is_404()) {
		echo 'Not Found';
		echo ' :: ';
		bloginfo('name');
	}
	//everything else
	else {
		wp_title("",true);  
		echo ' :: '; 
		bloginfo('name'); 
		get_page_number();
	}
}

function get_page_thumbnail() {
	global $static_subdomain;
	if(has_post_thumbnail()){
		$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
		
		echo $thumb['0'];
	} else {
		echo $static_subdomain.'img/site_logo.png';
	}
}


