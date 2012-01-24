<?php
/**
 * Template für einzelne Änderungsanträge
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<?php get_header(); ?>
		<div class="inner">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<div class="entry">
					<?php if(function_exists('cvtx_theme_antragsteller')) cvtx_theme_antragsteller(); ?>
					<?php if(function_exists('cvtx_theme_zeile')) cvtx_theme_zeile(); ?>
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx') . '</p>'); ?>
					<?php if(function_exists('cvtx_theme_grund')) cvtx_theme_grund(); ?>
					<?php if(function_exists('cvtx_theme_pdf')) cvtx_theme_pdf(); ?>					
				</div>
				<p class="postmetadata alt">
					<small><?php printf(__('Dieser %1$s wurde am %2$s um %3$s eingestellt.'),
									 get_post_type_object(get_post_type())->labels->singular_name, 
								     get_the_time(__('j. F Y')), 
								     get_the_time()); ?>
					</small>
				</p>
			</div>
		<?php endwhile; else: ?>
		<p><?php _e('Es konnten leider keine Einträge gefunden werden!', 'cvtx'); ?></p>
		<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>