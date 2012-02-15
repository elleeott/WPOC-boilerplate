<?php
/*
Plugin Name: Facebook Like button, sdk js  &fb comments plugin
Description: Adds Facebook OpenGraph meta tags, drops fb js in footer.
*/

require 'src/facebook.php';

$app_id = '274893932564903';
$app_secret = '19a7db0db0ad344cb1f0208159ebd2ae';
$comments_width ='720'; // this should be the width of the containing element.
$comments_login_width = '720'; // ''

//get fb user info if logged in to facebook and permissed the app
//if(comments_open()) {

	
$facebook = new Facebook(array(
  'appId' => $app_id,
  'secret' => $app_secret,
  'cookie' => true
));
$fb_user = $facebook->getUser();
if ($fb_user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $fb_user = null;
  }
}
if ($fb_user) {
	$logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


function clear_fb_cookies() {
	global $fb_user, $app_id;
	if(!$fb_user && isset($_COOKIE['comment_author_fbuid'])) {
	$_COOKIE['comment_author_'.COOKIEHASH] = '';
	$_COOKIE['comment_author_email_'.COOKIEHASH] = '';
	$_COOKIE['comment_author_url_'.COOKIEHASH] = '';
	$_COOKIE['comment_author_fbuid_'.COOKIEHASH] = '';
	}
}

add_action('init', 'clear_fb_cookies');


// show login button for facebook - show fb user info if logged in.
function fb_login() {
	global $fb_user, $user_profile, $logoutUrl, $comments_login_width;
	if (is_user_logged_in()) return; // do nothing to WP users
		?>
		<?php if($fb_user) : ?>
			<?php //echo ($user_profile); ?>
			<h5>You&rsquo;re logged in to facebook as <?php echo $user_profile['name']; ?></h5>
			<img src="https://graph.facebook.com/<?php echo $fb_user ; ?>/picture">
			<input type="hidden" name="fbuid" value="<?php echo $fb_user;?>"/>
			<input type="hidden" name="author" value="<?php echo $user_profile['name']; ?>"/>
			<input type="hidden" name="email" value="nomail@notarequiredfield.org"/>
			<input type="hidden" name="url" value="http://www.facebook.com/profile.php?id=<?php echo $fb_user; ?>"/>
			<a href="<?php echo $logoutUrl; ?>">Logout from Facebook</a>
		<?php else : ?>
			<div class="fb-login-button" data-show-faces="false" data-width="<?php echo $comments_login_width; ?>" data-max-rows="1">Login with Facebook</div>
		<?php endif; ?>
		<?php

}

add_action('comment_form_before_fields', 'fb_login',10,0); 

function add_fbuid_meta($comment_id) {
	global $fb_user;
	if(isset($_POST['fbuid'])){
		$fbuid = wp_filter_nohtml_kses($_POST['fbuid']);
		add_comment_meta($comment_id, 'fbuid', $fbuid, false);
		setcookie('comment_author_fbuid_'.COOKIEHASH, $fbuid);
	}
}
add_action ('comment_post', 'add_fbuid_meta', 1);


function fb_remove_fields($fields) {
	global $app_id;
	global $fb_user;
	if($fb_user){
		unset($fields['author']);
		unset($fields['url']);
		unset($fields['email']);
	}
	return $fields;
}

add_filter( 'comment_form_default_fields', 'fb_remove_fields' );



// generate facebook avatar code for FB user comments
function comm_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = false) {
	// check to be sure this is for a comment
	if ( !is_object($id_or_email) || !isset($id_or_email->comment_ID) || $id_or_email->user_id)
		 return $avatar;

	// check for fbuid comment meta
	$fbuid = get_comment_meta($id_or_email->comment_ID, 'fbuid', true);
	if ($fbuid) {
		// return the avatar code
		return "<img width='{$size}' height='{$size}' class='avatar avatar-{$size} fbavatar' src='http://graph.facebook.com/{$fbuid}/picture?type=square' />";
	}
	return $avatar;
}
add_filter('get_avatar','comm_avatar', 10, 5);


//put the footer facebook js code on the page
function fb_js() {
	global $app_id;
	
?>
	<div id="fb-root"></div>
	<script>
	window.fbAsyncInit = function() {
	FB.init({
		appId: '<?php echo $app_id; ?>',
		cookie: true,
		xfbml: true,
		oauth: true
	});
	FB.Event.subscribe('auth.login', function(response) {
		window.location.reload();
	});
	FB.Event.subscribe('auth.logout', function(response) {
		window.location.reload();
	});
	};
	(function() {
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol +
	'//connect.facebook.net/en_US/all.js';
	document.getElementById('fb-root').appendChild(e);
	}());
	</script>
<?php
}

add_action('wp_footer', 'fb_js');


//add facebook comments plugin
function fb_comments() {
	global $comments_width;
	if(comments_open()) {
?>
	<div class="fb-comments" data-width="<?php echo $comments_width; ?>" data-href="<?php echo the_permalink(); ?>"></div>
<?php
	}
}


// page thumbail link for opengraph meta tags in header.php
function get_page_thumbnail() {
	$protocol='http:';
	if(!empty($_SERVER['HTTPS'])) {
	    $protocol='https:';
	}
	if(is_single() && has_post_thumbnail()) {
		$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
		echo $protocol.$thumb[0];
	} else {
		echo STATIC_SUBDIR.'/img/site_logo.png';
	}
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

//output the opengraph tags
function og_tags() {
?>
	<meta property="og:title" content="<?php set_the_title(); ?>"/>
	<meta property="og:type" content="" />
	<meta property="og:site_name" content="<?php echo bloginfo('name'); ?>" />
	<meta property="og:image" content="<?php get_page_thumbnail(); ?>" />
	<meta property="og:url" content="<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" />
	<meta property="og:description" content="<?php custom_meta_description(); ?>" />
<?php

}
add_action('wp_head', 'og_tags');