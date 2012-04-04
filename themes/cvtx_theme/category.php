<?php
/**
 * Kategorie-Template, stellt Posts aus einer bestimmten 
 * Kategorie dar.
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
		<div class="inner">
		<h1 class="page-title"><?php
			printf( __( 'Category: %s', 'cvtx_theme' ), '<span>' . single_cat_title( '', false ) . '</span>' );
		?></h1>
		<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) )
				echo '<div class="archive-meta">' . $category_description . '</div>';
		?>
		<?php if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>
	
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent link to %s', 'cvtx_theme'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
					<small><?php the_time(__('j.m.Y', 'cvtx_theme')) ?></small>
	
					<div class="entry">
						<?php the_content(__('Read the rest of this entry &raquo;', 'cvtx_theme')); ?>
					</div>
	
					<p class="postmetadata"><small><?php the_tags(__('Tags:', 'kubrick') . ' ', ', ', '<br />'); ?> <?php printf(__('Published in %s', 'cvtx_theme'), get_the_category_list(', ')); ?> | <?php edit_post_link(__('Edit', 'cvtx_theme'), '', ' | '); ?>  <?php comments_popup_link(__('No comments &#187;', 'cvtx_theme'), __('1 comment &#187;', 'cvtx_theme'), __('% comments &#187;', 'cvtx_theme'), '', __('Comments closed', 'cvtx_theme') ); ?></small></p>
				</div>
	
			<?php endwhile; ?>

	<?php /* Display navigation to next/previous pages when applicable */ ?>
	<?php if (  $wp_query->max_num_pages > 1 ) : ?>
					<div id="nav-below" class="navigation">
						<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'cvtx_theme' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'cvtx_theme' ) ); ?></div>
					</div><!-- #nav-below -->
	<?php endif; ?>
	
	<?php else : ?>
		<h2 class="center"><?php _e('Not found', 'cvtx_theme'); ?></h2>
		<p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>

<?php get_footer(); ?>