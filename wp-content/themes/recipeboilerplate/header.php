<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, mininum-scale=1.0, user-scalable=no" name="viewport">
		<?php require_once($_SERVER['DOCUMENT_ROOT'].'/lib/cacheBuster.php');?>
		<link rel="stylesheet" media="print" href="<?php autoVer('/static/css/print.css'); ?>" />	
		<link rel="stylesheet" media="screen" href="<?php autoVer('/static/css/base.css'); ?>" />	
		<link rel="stylesheet" media="screen" href="<?php autoVer('/static/js/fancybox/jquery.fancybox-1.3.4.css'); ?>" />			
		
		<?php //output post tags as keywords
		   /* $postTags = get_the_tags();
		    $tagNames = array();
		    foreach($postTags as $tag)
		    {
		        $tagNames[] = $tag->name;
		    }*/
		?>
		<meta name="keywords" content="<?php //echo implode($tagNames,", "); ?>" />		
		<!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>
	<body>
		<div id="container">
			<header>			
				<div id="logo"><a href="/">logo</a></div> 

				<div id="support-links">
					<?php wp_nav_menu(array( 'theme_location' => 'secondary-nav' ) ); ?>
				</div>
				<div id="cart"><a href="/store/?route=checkout/cart">Shopping Cart (<?php echo cart_items(); ?>)</a></div>
				<nav class="clearfix">
					<?php  wp_nav_menu(array( 'theme_location' => 'primary-nav' ) ); ?> 
				</nav>				
			</header>
			<div class="breadcrumbs">
			<?php
				$crumbs = explode("/",$_SERVER["REQUEST_URI"]);
				foreach($crumbs as $crumb){
				    echo ucfirst(str_replace(array(".php","_"),array(""," "),$crumb) . ' ');
				}
			?>
			</div>			
