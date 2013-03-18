<?php
/**
 * Template Name: Bewerbung erstellen
 *
 * Dieses Template zeigt ein Formular zum Erstellen von Bewerbungen an.
 * Dafür muss das Plugin "cvtx" installiert sein.
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
				<?php if(function_exists('cvtx_submit_application')) cvtx_submit_application(); ?>
			</div>
		</div>
	<?php endwhile; else: ?>
		<p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
	<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>