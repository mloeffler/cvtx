<?php
/**
 * Search-Template
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<?php get_header(); ?>
		<div class="inner">
		<h1 class="page-title"><?php printf( __( 'Suchresultate für: %s', 'cvtx' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		
		<?php if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>
	
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanenter Link zu %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
					<small><?php the_time(__('j.m.Y', 'kubrick')) ?></small>
	
					<div class="entry">
						<?php the_content(__('Read the rest of this entry &raquo;', 'kubrick')); ?>
					</div>
	
					<p class="postmetadata"><small><?php the_tags(__('Tags:', 'kubrick') . ' ', ', ', '<br />'); ?> <?php printf(__('Veröffentlicht in %s', 'cvtx'), get_the_category_list(', ')); ?> | <?php edit_post_link(__('Bearbeiten', 'cvtx'), '', ' | '); ?>  <?php comments_popup_link(__('Keine Kommentare &#187;', 'cvtx'), __('1 Kommentar &#187;', 'cvtx'), __('% Kommentare &#187;', 'cvtx'), '', __('Kommentare geschlossen', 'cvtx') ); ?></small></p>
				</div>
	
			<?php endwhile; ?>

	<?php /* Display navigation to next/previous pages when applicable */ ?>
	<?php if ($wp_query->max_num_pages > 1) : ?>
		<div id="nav-below" class="navigation">
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Ältere Beiträge', 'cvtx' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Neuere Beiträge <span class="meta-nav">&rarr;</span>', 'cvtx' ) ); ?></div>
		</div><!-- #nav-below -->
	<?php endif; ?>
	
	<?php else : ?>
		<h2 class="center"><?php _e('Nicht gefunden', 'cvtx'); ?></h2>
		<p><?php _e('Es konnten leider keine Einträge gefunden werden!', 'cvtx'); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>

<?php get_footer(); ?>