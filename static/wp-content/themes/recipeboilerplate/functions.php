<?php

//basic theme support setup
$content_width = 940;
function theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'theme_setup' );

//additional image sizes
add_image_size( 'product-large',900,9999);
add_image_size( 'product-med',300,9999);
add_image_size( 'product-thumb',150,200);
add_image_size( 'recipe-large',720,400,true);
add_image_size( 'recipe-med',300,9999);
add_image_size( 'recipe-thumb',150,9999);
add_image_size( 'xtra-large', 900, 9999, false );

//add new image size to UI
function additional_image_sizes($sizes) {
        $addsizes = array(
                "xtra-large" => __( "Extra Large")
                );
        $newsizes = array_merge($sizes, $addsizes);
        return $newsizes;
}
add_filter('image_size_names_choose', 'additional_image_sizes');



// remove extra inline spacing added by wp-caption
function fixed_img_caption_shortcode($attr, $content = null) {
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ( $output != '' ) return $output;
	extract(shortcode_atts(array(
		'id'=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''), $attr));
	if ( 1 > (int) $width || empty($caption) )
	return $content;
	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
	return '<span ' . $id . 'class="wp-caption ' . esc_attr($align) .'" style="width:'.$width.'px;">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">'
	. $caption . '</p></span>';
}
add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
add_shortcode('caption', 'fixed_img_caption_shortcode');


// custom product gallery code
function get_product_gallery() {
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

// custom recipe gallery code
function get_recipe_gallery() {
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
		$recipe_large = wp_get_attachment_image_src($attachment->ID,'recipe-large',true);
		$recipe_med = wp_get_attachment_image_src($attachment->ID,'recipe-med',true);
		$recipe_thumb = wp_get_attachment_image_src($attachment->ID,'recipe-small',true);
		if($attachment->ID == $featured_img_id){
			echo '<li class="focus-img"><a class="fancybox" href="'.$recipe_large[0].'"><img class="photo" src="'.$recipe_large[0].'" width="'.$recipe_large[1].'" height="'.$recipe_large[2].'"/></a></li>';
		} else {
			echo '<li><a class="fancybox" href="'.$recipe_large[0].'"><img src="'.$recipe_large[0].'" width="'.$recipe_large[1].'" height="'.$recipe_large[2].'"/></a></li>';
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
        $protocol='http:';
        if(!empty($_SERVER['HTTPS'])) {
            $protocol='https:';
        }
        
        if(isset($isOpenCartPage)) {
			//remove l10n js
			wp_deregister_script( 'l10n' );	
				
			//reqister protocol relative google cdn jquery
		    wp_deregister_script( 'jquery' );
			wp_register_script('jquery', $protocol.'//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js', false, NULL, true);
			
			// register fancybox
			wp_register_script('fancybox', STATIC_SUBDIR.'/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), NULL, true);
			
			// register flexslider
			wp_register_script('flexslider', STATIC_SUBDIR.'/js/jquery.flexslider-min.js', array('jquery'), NULL, true);
			
			// register theme script
			wp_register_script('site-script', STATIC_SUBDIR . autoVer('/static/js/index.js'), array('jquery'), NULL, true);
        } else {
			//remove l10n js
			wp_deregister_script( 'l10n' );	
				
			//reqister protocol relative google cdn jquery
		    wp_deregister_script( 'jquery' );
			wp_register_script('jquery', $protocol.'//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js', false, NULL);
			
			// register fancybox
			wp_register_script('fancybox', STATIC_SUBDIR.'/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), NULL);
			
			// register flexslider
			wp_register_script('flexslider', STATIC_SUBDIR.'/js/jquery.flexslider-min.js', array('jquery'), NULL);
			
			// register theme script
			wp_register_script('site-script', STATIC_SUBDIR . autoVer('/static/js/index.js'), array('jquery'), NULL);        
			
			// register comment-form validation script
				wp_register_script('comment-form-validate', STATIC_SUBDIR . autoVer('/static/js/jquery.validate.min.js'), array('jquery'), NULL);        
			
        }
	}
	add_action('init', 'reg_scripts'); 

	function print_scripts_footer() {
		wp_print_scripts('jquery');	
		wp_print_scripts('fancybox');	
		wp_print_scripts('flexslider');	
		wp_print_scripts('site-script');	
		if(comments_open()) {
			wp_print_scripts('comment-form-validate');	
		}
	}

	function print_scripts_header() {
		wp_print_scripts('jquery');	
		wp_print_scripts('fancybox');	
		wp_print_scripts('flexslider');	
		wp_print_scripts('site-script');	
	}
	// place js files in the footer on WP pages, in header on OC pages.  OC has inline jquery dependencies that can't be easily moved.
	if(isset($isOpenCartPage)) {
		add_action('wp_head', 'print_scripts_header'); 				
	} else {
		add_action('wp_footer', 'print_scripts_footer'); 	
	}
}




//enqueue css
if (!is_admin()) {
	function reg_styles() {
	    if (is_page_template('fancy-template.php')){
		    $fancy = STATIC_SUBDIR . autoVer('/static/css/fancy.css');
			wp_register_style('fancy',$fancy,false,NULL,'screen');
			wp_enqueue_style('fancy');
		} else {
		    $print = STATIC_SUBDIR . autoVer('/static/css/print.css');
		    $base = STATIC_SUBDIR . autoVer('/static/css/base.css');
		    $fancybox = STATIC_SUBDIR . autoVer('/static/js/fancybox/jquery.fancybox-1.3.4.css');
		    $flexslider = STATIC_SUBDIR . autoVer('/static/css/flexslider.css');
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


// custom comments
function custom_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>

	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 50;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 50;

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
			</div>
		</article><!-- #comment-## -->

	<?php

}

// custom comment form
function custom_form_fields($fields) {
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
		'author' => '<fieldset><div class="comment-form-field comment-form-author clearfix"><label for="author">Name</label> ' . 
		            '<input id="author"' . ( $req ? 'class="required"' : '' ) .'name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div></fieldset>',
		'email'  => '<fieldset><div class="comment-form-field comment-form-email clearfix"><label for="email">Email</label> ' .
		            '<input id="email" ' . ( $req ? 'class="required"' : '' ) .'name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div></fieldset>',
		'url'    => '<fieldset><div class="comment-form-field comment-form-url clearfix"><label for="url">Website</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div></fieldset>',
	);
	return $fields;
}

add_filter('comment_form_default_fields','custom_form_fields',1);


//custom comment textarea
function custom_comment_textarea($default) {
	$default['comment_field'] = '<fieldset><div class="comment-form-field comment-form-comment clearfix"><label for="comment">Comment</label><textarea id="comment" class="required" name="comment" aria-required="true"></textarea></div></fieldset>';
	return $default;
}

add_filter('comment_form_defaults','custom_comment_textarea');


//output page title for header
function set_the_title() {
	//two parameters passed from OC pages
	global $isOpenCartPage; 
	global $page_title; 
	global $s;
	
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
		echo ' :: Search results for "' . esc_html($s) . '"';
		get_page_number();
	}
	//Opencart Pages
	elseif (is_404() && (isset($isOpenCartPage))) { 
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

//output social buttons
function get_social_elements() {
?>
	<span class="tweet"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a></span>
	<span class="facebook-like-button"><div class="fb-like" data-href="<?php the_permalink();?>" data-send="true" data-layout="button_count" data-width="100" data-show-faces="false"></div></span>
	<span class="pinterest-pin"><a href="http://pinterest.com/pin/create/button/" class="pin-it-button" count-layout="horizontal">Pin It</a></span>
<?php
}


//print footer social scripts
function social_scripts() {
?>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
<?php	
}

add_action('wp_footer','social_scripts');
