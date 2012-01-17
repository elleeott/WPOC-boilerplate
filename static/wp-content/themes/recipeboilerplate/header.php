<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta property="og:title" content="<?php set_the_title(); ?>" name="title" />
		<meta property="og:site_name" content="<?php echo bloginfo('name'); ?>" />
		<meta property="og:image" content="<?php get_page_thumbnail(); ?>" />
		<meta property="og:url" content="<?php the_permalink(); ?>" />
		<meta property="og:description" content="<?php custom_meta_description(); ?>" name="description" />
		<title><?php set_the_title(); ?></title>
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, mininum-scale=1.0" name="viewport" />

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
	<?php include_once($_SERVER['DOCUMENT_ROOT'].'/ga_tracking.php'); ?>
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
				<nav id="full-nav">
					<div class="container clearfix">
						<?php  wp_nav_menu(array( 'theme_location' => 'primary-nav' ) ); ?> 
					</div>
				</nav>	
				<nav id="mobile-nav">
					<?php $args =array(
						'post_type'=>'nav_menu_item'
					); ?>
					<?php $items = wp_get_nav_menu_items('primary-nav',$args); ?>
					<?php //print_r($items); ?>
					<form>
						<select>
							<?php foreach ($items as $item) { ?>
								<option value="<?php echo $item->url; ?>"><?php echo $item->title; ?></option>
							<?php } ?>
						</select>
						<button type="submit">go</button>
					</form>
				</nav>			
			</header>
			<div class="breadcrumbs container">
			
				<?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?>  seconds.<br>
				<?php echo 'session data: '; print_r($_SESSION); ?>
			</div>
			
			
			
			
			
			