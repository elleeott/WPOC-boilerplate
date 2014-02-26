<?php get_header(); ?>
<div id="content-container" class="clearfix">
	<style type="text/css">
		.cart a {
			width:200px;
			text-align:center;
			background:#7DD5E3;
			color:#fff;
			text-transform:uppercase;
			margin:40px auto;
			display:block;
			padding:2em 4em;
			border-radius:80px;
			font-weight:bold;
			font-family:helvetica,arial,sans-serif;
			font-size:24px;
		}
		.cart a:hover {
			background:#70C0CC;
			text-decoration:none;
		}
	</style>
	<div class="cart">
		<a href="http://3dcart-frictio-us.3dcartstores.com/add_cart.asp?out=1&quick=1&item_id=4" onclick="_gaq.push(['_link', 'http://3dcart-frictio-us.3dcartstores.com/add_cart.asp?out=1&quick=1&item_id=4']); return false;">add to 3dcart</a>
	</div>

	<div id="hero">
		<div class="container">
			<div class="flexslider">
				<ul class="slides">
					<?php query_posts('post_type=products&meta_key=_thumbnail_id'); ?>
					<?php while(have_posts()): the_post(); ?>
						<li class="slide">
							<h1><?php the_title(); ?></h1>
							<?php the_post_thumbnail('product-med'); ?>
						</li>
					<?php endwhile; ?>
					<li class="slide">
						<h1>Beans n&rsquo; Chicken</h1>
						<img src="<?php echo $static_subdomain; echo autoVer('/static/img/assets/recipe_wings.png'); ?> "/>
						<a class="button button-primary">get this recipe</a>
					</li>
					<li class="slide">
						<h1>Beans n&rsquo; Chicken</h1>
						<img src="<?php echo $static_subdomain; echo autoVer('/static/img/assets/recipe_wings.png'); ?> "/>
						<a class="button button-primary">get this recipe</a>
					</li>
					<li class="slide">
						<h1>Beans n&rsquo; Chicken</h1>
						<img src="<?php echo $static_subdomain; echo autoVer('/static/img/assets/recipe_wings.png'); ?> "/>
						<a class="button button-primary">get this recipe</a>
					</li>
					<li class="slide">
						<h1>Beans n&rsquo; Chicken</h1>
						<img src="<?php echo $static_subdomain; echo autoVer('/static/img/assets/recipe_wings.png'); ?> "/>
						<a class="button button-primary">get this recipe</a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container widgets">
		<?php dynamic_sidebar( 'tertiary' ); ?>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>



