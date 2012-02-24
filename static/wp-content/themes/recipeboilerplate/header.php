<?php global $isOpenCartPage; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, mininum-scale=1.0, user-scalable=0" name="viewport" />
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>
		<title><?php set_the_title(); ?></title>
		<!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
		<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="latest posts" />
		<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="latest comments" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<?php if(function_exists('insert_ga_code')) insert_ga_code(); ?>
		<script type="text/javascript" src="http://use.typekit.com/guf3svx.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>	
	</head>
	
	<?php if(isset($isOpenCartPage)): ?>
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
							<div id="cart"><a href="/store/index.php?route=checkout/cart">Shopping Cart (<span id="cart_total"><?php echo cart_items(); ?></span>)</a></div>
					<?php } ?>
					<?php get_search_form(); ?>		
				</div>	
				<nav id="full-nav">
					<div class="container clearfix">
						<?php  wp_nav_menu(array( 'theme_location' => 'primary-nav' ) ); ?> 
					</div>
				</nav>	
				<nav id="mobile-nav">
					<?php get_mobile_nav(); ?>
				</nav>	
			</header>
			<div class="breadcrumbs container">
			
				<?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?>  seconds.<br>
				<?php // echo 'session data: '; print_r($_SESSION); ?><br/>
				<?php // echo 'cookies: '; print_r($_COOKIE); ?>
			</div>