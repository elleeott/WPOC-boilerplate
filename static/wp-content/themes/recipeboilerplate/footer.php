		<footer>
			<div class="container">
				
				<div id="search-form"><?php get_search_form(); ?></div>
				<div id="login-out"><?php wp_loginout(); ?></div>
				<div id="login-form"><?php wp_login_form(); ?></div>
				<div id="register"><?php wp_register(); ?></div>
				<div id="wp-meta">
					<ul>
						<li>site name: <?php bloginfo('name');?></li>
						<li>site description: <?php bloginfo('description');?></li>
						<li>admin email: <?php bloginfo('admin_email');?></li>
						<li>url: <?php bloginfo('url');?></li>
						<li>wpurl: <?php bloginfo('wpurl');?></li>
						<li>stylesheet directory: <?php bloginfo('stylesheet_directory');?></li>
						<li>stylesheet_url: <?php bloginfo('stylesheet_url');?></li>
						<li>template_directory: <?php bloginfo('template_directory');?></li>
						<li>template_url: <?php bloginfo('template_url');?></li>
						<li>atom_url: <a href="<?php bloginfo('atom_url');?>"><?php bloginfo('atom_url');?></a></li>
						<li>rss2_url: <a href="<?php bloginfo('rss2_url');?>"><?php bloginfo('rss2_url');?></a></li>
						<li>rss_url: <a href="<?php bloginfo('rss_url');?>"><?php bloginfo('rss_url');?></a></li>
						<li>pingback_url: <a href="<?php bloginfo('pingback_url');?>"><?php bloginfo('pingback_url');?></a></li>
						<li>rdf_url: <a href="<?php bloginfo('rdf_url');?>"><?php bloginfo('rdf_url');?></a></li>
						<li>comments_atom_url: <a href="<?php bloginfo('comments_atom_url');?>"><?php bloginfo('comments_atom_url');?></a></li>
						<li>comments_rss2_url: <a href="<?php bloginfo('comments_rss2_url');?>"><?php bloginfo('comments_rss2_url');?></a></li>
						<li>charset: <?php bloginfo('charset');?></li>
						<li>html_type: <?php bloginfo('html_type');?></li>
						<li>text_direction: <?php bloginfo('text_direction');?></li>
						<li>version: <?php bloginfo('version');?></li>
						<li>blogid: <?php echo get_current_blog_id(); ?></li>
					</ul>
					<div id="post-type">post type archive title: <?php echo post_type_archive_title(); ?></div>
				</div>
			</div>			
		</footer>
	</div><!-- close outer-container -->
	<?php wp_footer(); ?>
<?php /*	
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
*/ ?>
	</body>
</html>