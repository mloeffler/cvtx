<?php
/**
 * Template für einzelne Anträge
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
		<div class="inner">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<div class="entry">
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
    				<h2><?php echo __('Life career', 'cvtx') ?></h2>
				    <?php echo '<p>'.get_post_meta($post->ID, 'cvtx_application_cv', true).'</p>'; ?>
					<?php do_action('cvtx_theme_pdf'); ?>					
				</div>
				<div class="metainfos">
				    <?php $link = wp_get_attachment_image_src(get_post_meta($post->ID, 'cvtx_application_photo_id', true), 'cvtx_application_theme'); ?>
				    <?php $link_large = wp_get_attachment_image_src(get_post_meta($post->ID, 'cvtx_application_photo_id', true), 'large'); ?>
				    <?php echo('<a href="'.$link_large[0].'" class="thickbox"><img src="'.$link[0].'"/></a>'); ?>
				</div>
				<p class="postmetadata alt">
					<small><?php printf(__('This %1$s was published on %2$s at %3$s.'),
									 __(get_post_type_object(get_post_type())->labels->singular_name,'cvtx'), 
								     get_the_time(__('j. F Y')), 
								     get_the_time()); ?>
					</small>
				</p>
			</div>
		<?php endwhile; else: ?>
		<p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
		<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>