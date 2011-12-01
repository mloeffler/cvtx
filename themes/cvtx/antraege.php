<?php
/*
Template Name: Antrags&uuml;bersicht
*/
?>
<?php get_header(); ?>
	<div class="inner">
		<ul>
		<?php
		// TOP-Query
		$loop = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'cvtx_top_ord'));
		while ($loop->have_posts()):$loop->the_post();
			$top_id = $post->ID;?>
			<li><h3><?php the_title(); ?></h3><ul>
			<?php
			$loop2 = new WP_Query(array('post_type' => 'cvtx_antrag'));
			while ($loop2->have_posts() ) : $loop2->the_post();?>
				<?php if(get_post_meta($post->ID,'cvtx_antrag_top',true) == $top_id):?>
				<li><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></li>
				<?php endif;?>
			<?php endwhile;?>
			</ul></li>
		<?php endwhile;?>
		</ul>
	</div>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>