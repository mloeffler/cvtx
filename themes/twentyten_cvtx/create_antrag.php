<?php
/**
 * Template Name: Antrag erstellen
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<h2><?php the_title(); ?></h2>
					<div class="entry">
						<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
						<?php if(function_exists('cvtx_submit_antrag')) cvtx_submit_antrag(); ?>
					</div>
				</div>
			<?php endwhile; else: ?>
			    <p><?php _e('Es konnten leider keine Einträge gefunden werden!', 'cvtx'); ?></p>
			<?php endif; ?>
		
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
