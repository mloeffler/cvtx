<?php
/**
 * Template Name: Bewerbung erstellen
 *
 * Dieses Template zeigt ein Formular zum Erstellen von Bewerbungen an.
 * Dafür muss das Plugin "cvtx" installiert sein.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; // end of the loop. ?>
            <div class="entry-content"><?php if(function_exists('cvtx_submit_application')) cvtx_submit_application(); ?></div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>