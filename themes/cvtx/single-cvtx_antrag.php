<?php
/*
*/
?>
<?php get_header(); ?>
		<div class="inner">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<div class="entry">
					<?php
					$antragsteller = get_post_meta($post->ID,'cvtx_antrag_steller',true);
					if(!empty($antragsteller)){
						printf(__('<strong>AntragstellerInnen:</strong> %1$s'),$antragsteller);
					}
					?>
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'kubrick') . '</p>'); ?>
					<?php
					$grund = get_post_meta($post->ID,'cvtx_antrag_grund',true);
					if(!empty($grund)){
						printf(__('<strong>Begr&uuml;ndung:</strong> %1$s'),$grund);
					}
					?>
				</div>
					<p class="postmetadata alt">
					<small>
						<?php printf(__('Dieser %1$s wurde am %2$s um %3$s eingestellt.'),
									 get_post_type_object(get_post_type())->labels->singular_name, 
								     get_the_time(__('j. F Y')), 
								     get_the_time()); ?>
					</small>
				</p>
				<?php $antrag_id = $post->ID; ?>
				<?php $loop3 = new WP_Query(array('post_type' => 'cvtx_aeantrag',
												  'meta_key' => 'cvtx_aeantrag_antrag',
												  'meta_value' => $antrag_id,
												  'order_by' => 'cvtx_aeantrag_zeile',
												  'order' => 'ASC'));
				if($loop3->have_posts()):?>
				<div id="ae_antraege">
					<h3>&Auml;nderungsantr&auml;ge<?php
						if(isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) { 
							echo ' zu "'; 
							the_title(); 
							echo '"';
						}
					?></h3>
					<table cellpadding="3" cellspacing="0" valign="left">
						<tr>
							<th><strong>Zeile</strong></th>
							<th><strong>AntragstellerInnen</strong></th>
							<th><strong>Antragstext</strong></th>
							<th><strong>Begr&uuml;ndung</strong></th>
						</tr>
					<?php 
					while($loop3->have_posts()):$loop3->the_post();?>
						<tr>
							<td class="zeile"><?php print get_post_meta($post->ID,'cvtx_aeantrag_zeile',true); ?></td>
							<td class="steller"><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller',true);?></td>
							<td class="text"><?php the_content(); ?></td>
							<td class="grund"><?php print get_post_meta($post->ID,'cvtx_aeantrag_grund',true);?></td>
						</tr>
					<?php endwhile;?>
					</table>
				</div>
				<?php endif; ?>
		</div>
	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.', 'kubrick'); ?></p>

<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>