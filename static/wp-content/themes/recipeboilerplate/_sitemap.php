<h2>Sitemap</h2>

<h3>Pages</h3>
<ul>
	<?php wp_list_pages(array('title_li'=>'')); ?>
</ul>

<?php $cats = get_categories();?>


<h2>Posts</h2>
<ul>
	<?php foreach($cats as $cat){ ?>
		<li>
			<h3><?php echo $cat->name; ?></h3>	
			<ul>
				<?php $results = get_posts(array(
					'category'=>$cat->cat_ID
				));?>
				<?php //print_r($results); ?>
				<?php foreach($results as $post): setup_postdata($post); ?>
					<?php $category = get_the_category(); ?>
					<?php if ($category[0]->cat_ID == $cat->cat_ID): ?>
						<li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>		
		</li>
	<?php } ?>
</ul>
