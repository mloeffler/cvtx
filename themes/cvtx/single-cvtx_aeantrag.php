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
					$antragsteller = get_post_meta($post->ID,'cvtx_aeantrag_steller',true);
					if(!empty($antragsteller)){
						printf(__('<strong>AntragstellerInnen:</strong> %1$s'),$antragsteller);
					}
					$zeile = get_post_meta($post->ID,'cvtx_aeantrag_zeile',true);
					if(!empty($zeile)){
						printf(__('<p><strong>Zeile:</strong> %1$s</p>'),$zeile);
					}
					?>
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx') . '</p>'); ?>
					<?php
					$grund = get_post_meta($post->ID,'cvtx_aeantrag_grund',true);
					if(!empty($grund)){
						printf(__('<strong>Begr&uuml;ndung:</strong> %1$s'),$grund);
					}
					?>
					
					<?php if ($file = cvtx_get_file($post, 'pdf')): ?>
						<h3>PDF</h3>
							<a href="<?php echo $file; ?>">Download (pdf)</a>
					<?php endif; ?>

				</div>
					<p class="postmetadata alt">
					<small>
						<?php printf(__('Dieser %1$s wurde am %2$s um %3$s eingestellt.'),
									 get_post_type_object(get_post_type())->labels->singular_name, 
								     get_the_time(__('j. F Y')), 
								     get_the_time()); ?>
					</small>
				</p>
				
		</div>
	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.', 'kubrick'); ?></p>

<?php endif; ?>
	</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>