		<article>
			<?php the_time('F j, Y'); ?>
			<?php /*<div class="fb-like" data-href="<?php the_permalink();?>" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false"></div>*/ ?>
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
