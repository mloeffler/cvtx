<?php
/*
Template Name: Antrags&uuml;bersicht
*/
?>
<?php get_header(); ?>
	<div class="inner">
		<ul id="antraege">
		<?php
		// TOP-Query
		$loop = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'cvtx_top_ord', 'order' => 'ASC'));
		while ($loop->have_posts()):$loop->the_post();
			$top_id = $post->ID;?>
			<li class="top"><h3><?php the_title(); ?></h3><ul>
			<?php
			$loop2 = new WP_Query(array('post_type' => 'cvtx_antrag'));
			while ($loop2->have_posts() ) : $loop2->the_post();?>
				<?php if(get_post_meta($post->ID,'cvtx_antrag_top',true) == $top_id):?>
				<li class="antrag"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
				<span class="steller"><strong>AntragstellerInnen:</strong> <?php print get_post_meta($post->ID,'cvtx_antrag_steller',true);?></strong></span>
				<ul class="options">
					<li><a href="#">PDF ansehen</a></li>
					<li><a href="#">&Auml;nderungsantrag hinzuf&uuml;gen</a></li>
					<li><a href="#">&Auml;nderungsantrags&uuml;bersicht</a></li>
				</ul>
				<ul class="ae_antraege">
					<h4>&Auml;nderungsantr&auml;ge</h4>
					<?php $antrag_id = $post->ID; ?>
					<?php $loop3 = new WP_Query(array('post_type' => 'cvtx_aeantrag',
													  'meta_key' => 'cvtx_aeantrag_antrag',
													  'meta_value' => $antrag_id,
													  'order_by' => 'cvtx_aeantrag_zeile',
													  'order' => 'ASC'));
					while($loop3->have_posts()):$loop3->the_post();?>
						<li><?php the_title(); ?> (AntragstellerInnen: <em><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller',true);?></em>)</li>
					<?php endwhile;?>
				</ul></li>
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