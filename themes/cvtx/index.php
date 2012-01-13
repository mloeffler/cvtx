<?php
/*
 * @package WordPress
 * @subpackage cvtx
*/
?>
<?php get_header(); ?>
		<div class="inner">
		<?php if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>
	
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanenter Link zu %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
					<small><?php the_time(__('j.m.Y', 'kubrick')) ?></small>
	
					<div class="entry">
						<?php the_content(__('Read the rest of this entry &raquo;', 'kubrick')); ?>
					</div>
	
					<p class="postmetadata"><small><?php the_tags(__('Tags:', 'kubrick') . ' ', ', ', '<br />'); ?> <?php printf(__('Posted in %s', 'kubrick'), get_the_category_list(', ')); ?> | <?php edit_post_link(__('Edit', 'kubrick'), '', ' | '); ?>  <?php comments_popup_link(__('No Comments &#187;', 'kubrick'), __('1 Comment &#187;', 'kubrick'), __('% Comments &#187;', 'kubrick'), '', __('Comments Closed', 'kubrick') ); ?></small></p>
				</div>
	
			<?php endwhile; ?>
	<?php else : ?>
		<h2 class="center"><?php _e('Not Found', 'kubrick'); ?></h2>
		<p><?php _e('Es konnten leider keine EintrŠge gefunden werden!', 'cvtx'); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>

<?php get_footer(); ?>