<div id="categories-menu">
	<h4><?php echo $heading_title; ?></h4>
	<ul>
		<?php foreach ($categories as $category) { ?>
			<li <?php if ($category['category_id'] == $category_id) { ?>class="category-current"<?php } ?>>
				<a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
				<?php if ($category['children']) { ?>
					<ul>
					<?php foreach ($category['children'] as $child) { ?>
						<li <?php if ($child['category_id'] == $child_id) { ?>class="child-category-current"<?php } ?>>
							<a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a>
						</li>
					<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
</div>

