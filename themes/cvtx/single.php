<?php
/*
*/
?>
<?php get_header(); ?>
		<div class="inner">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<div class="entry">
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
					<p class="postmetadata alt">
					<small>
						<?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/wordpress/time-since/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); $time_since = sprintf(__('%s ago', 'kubrick'), time_since($entry_datetime)); */ ?>
						<?php printf(__('Dieser %1$s wurde am %2$s um %3$s eingestellt.', 'kubrick'),get_post_type_object(get_post_type())->labels->singular_name, get_the_time(__('l, j. F Y', 'kubrick')), get_the_time(), get_the_category_list(', ')); ?>
					</small>
				</p>

			</div>
		</div>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.', 'kubrick'); ?></p>
	<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>