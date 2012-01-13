<?php get_header(); ?>
<div id="content-container" class="clearfix">
		<section>
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
	<div class="container">	
			<div id="promo-tiles" class="clearfix">
				<div></div>
				<div></div>
				<div class="last"></div>
			</div>
		</section>
	</div>
</div><!--end content container -->

<?php get_footer(); ?>