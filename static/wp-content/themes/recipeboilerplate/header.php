<?php
	//two parameters passed from OC pages
	global $isapage; 
	global $page_title; 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta content="<?php custom_meta_description(); ?>" name="description" />
		<title>
			<?php
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
			?>
		</title>
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, mininum-scale=1.0, user-scalable=no" name="viewport" />

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
			<header class="main">		
				<div class="container">
					<div id="logo"><a href="/">logo</a></div> 
						<a href="/store/index.php?route=account/login">login</a>
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
			<div class="breadcrumbs container">
			
				<?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?>  seconds.
			</div>