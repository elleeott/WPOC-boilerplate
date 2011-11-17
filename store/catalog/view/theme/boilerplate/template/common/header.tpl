<?php include($_SERVER['DOCUMENT_ROOT'].'/lib/cacheBuster.php');?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>	
	<?php if ($description) { ?><meta name="description" content="<?php echo $description; ?>" /><?php } ?>
	<?php if ($keywords) { ?><meta name="keywords" content="<?php echo $keywords; ?>" /><?php } ?>
	<?php if ($icon) { ?><link href="<?php echo $icon; ?>" rel="icon" /><?php } ?>
	<?php foreach ($links as $link) { ?><link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" /><?php } ?>

	<link rel="stylesheet" href="<?php autoVer('/static/css/style.css'); ?>" />
	
	<?php foreach ($styles as $style) { ?>
	<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
	<?php } ?>
	<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
	<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
	<![endif]-->
	<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
	<![endif]-->
	
	
	<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<!--[if IE]>
	<script type="text/javascript" src="catalog/view/javascript/jquery/fancybox/jquery.fancybox-1.3.4-iefix.js"></script>
	<![endif]--> 
	<?php foreach ($scripts as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
	<?php } ?>
	<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
	<script type="text/javascript" src="catalog/view/javascript/common.js"></script>
	<!--[if lt IE 7]>
	<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
	<script type="text/javascript">
	DD_belatedPNG.fix('#logo img');
	</script>
	<![endif]-->

	<?php echo $google_analytics; ?>
	</head>
	
	

	
	
	
	
<body>
<div id="container">

<header>

  <?php if ($logo) { ?>
  <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php autoVer($logo); ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
  <?php } ?>
  
 
  <div id="cart">
    <div class="heading">
      <h4><?php echo $text_cart; ?></h4>
      <a><span id="cart_total"><?php echo $text_items; ?></span></a></div>
    <div class="content"></div>
  </div>
  
  
  <div id="search">
    <div class="button-search"></div>
    <?php if ($filter_name) { ?>
    <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
    <?php } else { ?>
    <input type="text" name="filter_name" value="<?php echo $text_search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '#000000';" />
    <?php } ?>
  </div>
  
  
  
  <div id="welcome">
    <?php if (!$logged) { ?>
    <?php echo $text_welcome; ?>
    <?php } else { ?>
    <?php echo $text_logged; ?>
    <?php } ?>
  </div>
  <div class="links"><a href="<?php echo $home; ?>"><?php echo $text_home; ?></a><a href="<?php echo $wishlist; ?>" id="wishlist_total"><?php echo $text_wishlist; ?></a><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a><a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></div>




	<?php if ($categories) { ?>
	<nav>
	  <ul>
	    <?php foreach ($categories as $category) { ?>
	    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
	      <?php if ($category['children']) { ?>
	      <div>
	        <?php for ($i = 0; $i < count($category['children']);) { ?>
	        <ul>
	          <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
	          <?php for (; $i < $j; $i++) { ?>
	          <?php if (isset($category['children'][$i])) { ?>
	          <li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
	          <?php } ?>
	          <?php } ?>
	        </ul>
	        <?php } ?>
	      </div>
	      <?php } ?>
	    </li>
	    <?php } ?>
	  </ul>
	</nav>
	<?php } ?>
</header>










<div id="notification"></div>
