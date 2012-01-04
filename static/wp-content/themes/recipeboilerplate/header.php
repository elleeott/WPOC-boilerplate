<?php global $isapage; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			<?php
			    if ( is_single() ) { single_post_title(); echo ' | '; bloginfo('name'); }
			    elseif ( is_home() ) { echo 'Blog | '; bloginfo('name'); get_page_number(); } //blog
			    elseif ( is_front_page() ) {   bloginfo('name'); echo ' | '; bloginfo('description'); get_page_number(); } //hp
			    elseif ( is_page() ) { single_post_title(); echo ' | '; bloginfo('name'); }
			    elseif ( is_search() ) { bloginfo('name'); print ' | Search results for ' . esc_html($s); get_page_number(); }
			    elseif ( is_404() && (isset($isapage)) ) { bloginfo('name'); print ' | Not Found'; }
			    else { wp_title("",true);  echo ' | '; bloginfo('name'); get_page_number();}
			?>
		</title>
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, mininum-scale=1.0, user-scalable=no" name="viewport">

		<!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
		<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="latest posts" />
		<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="latest comments" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	</head>
	<?php if(isset($isapage)): ?>
		<body class="store">
		<?php else : ?>
		<body <?php body_class(); ?>>
	<?php endif; ?>
		<div id="outer-container">
			<header>		
				<div class="container">
					<div id="logo"><a href="/">logo</a></div> 
	
					<div id="support-links">
						<?php wp_nav_menu(array( 'theme_location' => 'secondary-nav' ) ); ?>
					</div>
					<?php if(function_exists('cart_items')) { ?>
							<div id="cart"><a href="/store/index.php?route=checkout/cart">Shopping Cart (<?php echo cart_items(); ?>)</a></div>
					<?php } ?>
				</div>	
				<nav>
					<div class="container clearfix">
						<?php  wp_nav_menu(array( 'theme_location' => 'primary-nav' ) ); ?> 
					</div>
				</nav>				
			</header>
			<div class="breadcrumbs">
			<?php
			/*
				$crumbs = explode("/",$_SERVER["REQUEST_URI"]);
				foreach($crumbs as $crumb){
				    echo ucfirst(str_replace(array(".php","_"),array(""," "),$crumb) . ' ');
				}
			*/
			?>
			</div>