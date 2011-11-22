<?php include($_SERVER['DOCUMENT_ROOT'].'/lib/cacheBuster.php');?>
<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<title></title>
		<link rel="stylesheet" media="print" href="<?php autoVer('/static/css/print.css'); ?>" />	
		<link rel="stylesheet" media="screen" href="<?php autoVer('/static/css/base.css'); ?>" />	
		<link rel="stylesheet" media="screen" href="<?php autoVer('/static/js/fancybox/jquery.fancybox-1.3.4.css'); ?>" />	

		<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->	
		<?php wp_head(); ?>
	</head>
	
	<body>
		<div id="container">
			<header>
				<div id="logo">logo</div>
				<div id="support-links"><?php wp_nav_menu(array( 'theme_location' => 'secondary-nav' ) ); ?> </div>
				<nav class="clearfix">
					<?php wp_nav_menu(array( 'theme_location' => 'primary-nav' ) ); ?> 
				</nav>				
			</header>

