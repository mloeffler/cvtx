<?php get_header(); ?>

<div data-role="content" class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h2 class="standalone-title"><?php the_title(); ?></h2>
		<span id="the-content"><?php the_content(); ?></span>
	<?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>