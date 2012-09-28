<?php
/**
 * Template für einzelne Anträge
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
		<div class="inner" data-role="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<div class="entry">
					<?php do_action('cvtx_theme_antragsteller',array('short' => false)); ?>
					<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
					<?php do_action('cvtx_theme_grund'); ?>
					<?php do_action('cvtx_theme_pdf'); ?>					
				</div>
				<p class="postmetadata alt">
					<small><?php printf(__('This %1$s was published on %2$s at %3$s.'),
									 get_post_type_object(get_post_type())->labels->singular_name, 
								     get_the_time(__('j. F Y')), 
								     get_the_time()); ?>
					</small>
				</p>
				<?php
				global $post;
		    // specify wp_query for all aenderungsantraege to given ID
		    $loop = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
		                               'meta_key'   => 'cvtx_sort',
		                               'orderby'    => 'meta_value',
		                               'order'      => 'ASC',
		                               'nopaging'   => true,
		                               'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
		                                                           'value'   => $post->ID,
		                                                           'compare' => '='))));
		    if($loop->have_posts()):?>
	        <div id="ae_antraege" class="noellipsis">
            <h3><?php print __('Amendments', 'cvtx'); ?><?php print (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) ? __(' to ', 'cvtx').cvtx_get_short($post) : ''; ?></h3>
	        	<ul data-role="listview" data-inset="true">
              <?php 
              while($loop->have_posts()):$loop->the_post();?>
	        			<li><span class="ui-li-aside"><?php _e('Line', 'cvtx'); ?>: <strong><?php print get_post_meta($post->ID,'cvtx_aeantrag_zeile',true); ?></strong></span>
	        			 <h3><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true);?></h3><br/>
	        			 <p class="aeantrag_conent"><strong><?php _e('Text', 'cvtx'); ?></strong>: <?php the_content(); ?></p><br/>
	        			 <p class="aeantrag_expl"><strong><?php _e('Explanation', 'cvtx'); ?></strong>: <?php print get_post_meta($post->ID,'cvtx_aeantrag_grund',true);?></p><br/>
	        			 <p class="aeantrag_procedure"><strong><?php _e('Procedure', 'cvtx'); ?></strong>: <?php print get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true); ?></p>
	        			 </li>
              <?php endwhile;?>
	        	</ul>
	        

	        </div>
				<?php endif; wp_reset_postdata(); ?>
		    <?php do_action('cvtx_theme_add_aeantrag', array('show_recaptcha' => false)); ?>
			</div>
		<?php endwhile; else: ?>
		<p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
		<?php endif; ?>
		</div>
	</div>
<?php get_footer(); ?>