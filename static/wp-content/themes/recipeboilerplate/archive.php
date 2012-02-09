		<article>
			<?php the_time('F j, Y'); ?>
			<h1>
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h1>
			<div>
				<?php the_content(); ?>
				<p>
					Categories: <?php the_category(', ') ?>
				</p>
				<p>
					<?php the_tags(); ?>
				</p>
			</div>
		</article>
