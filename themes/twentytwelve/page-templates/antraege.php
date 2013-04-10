<?php
/**
 * Template Name: Antragsübersicht
 *
 * Dieses Template stellt eine Übersicht über alle bereits
 * eingerichteten Anträge, Änderungsanträge und TOPs dar.
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
            <div class="entry-content">
                <?php
                $post_bak = $post;
                // Get all TOPs
                $loop = new WP_Query(array('post_type' => 'cvtx_top',
                                           'orderby'   => 'meta_value',
                                           'meta_key'  => 'cvtx_sort',
                                           'nopaging'  => true,
                                           'order'     => 'ASC'));
                if ($loop->have_posts()): ?>
                  <ul id="antraege">
                  	<?php $rss_url = add_query_arg(array('post_type' => 'cvtx_antrag'),get_feed_link('rss2'));?>
                  	<li class="rss top"><h3><?php print __('RSS-Feed', 'twentytwelve'); ?></h3><?php printf('<p>'.__('Stay up to date? Sign up to our %1$s!', 'twentytwelve').'</p>','<a href="'.$rss_url.'">RSS-Feed</a>'); ?></li>
                    <li class="top overview"><h3><?php print __('Overview','twentytwelve'); ?></h3>
                      <ul>
                        <?php while ($loop->have_posts()): $loop->the_post();?>
                          <li class="antrag">
                            <a href="#<?php print get_post_meta($post->ID, 'cvtx_top_short', true);?>"><?php the_title(); ?></a>
                          </li>
                        <?php endwhile; ?>
                      </ul>
                    </li><div class="tester"></div>
                    
                <?php
                // show all tops
                while($loop->have_posts()): $loop->the_post(); $top_id = $post->ID;
                ?>
                  <li class="top" id="<?php print get_post_meta($post->ID,'cvtx_top_short',true);?>"><h3><?php the_title(); ?></h3>
                  <div class="top_info" id="<?php print get_post_meta($post->ID,'cvtx_top_short',true);?>_info">
                   <?php the_content(); ?>
                  </div>
                  <?php
                  // query top-content
                  $loop2 = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                              'meta_key'   => 'cvtx_sort',
                                              'orderby'    => 'meta_value',
                                              'nopaging'   => true,
                                              'order'      => 'ASC',
                                              'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                          'value'   => $top_id,
                                                                          'compare' => '='))));
                  if($loop2->have_posts()) print '<ul>';
                  while ($loop2->have_posts()): $loop2->the_post(); ?>
                    <li class="antrag"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <span class="steller">
                      <strong><?php print __('Author(s)', 'twentytwelve'); ?>:</strong>
                      <?php print get_post_meta($post->ID,'cvtx_antrag_steller_short',true);?>
                    </span>
                    <ul class="options">
                      <li><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); else echo('Kein PDF erstellt.'); ?></li>
                      <li><a href="<?php the_permalink(); ?>#add_aeantrag" rel="extern" class="add_ae_antraeg" meta-id="<?php print $post->ID; ?>"><?php print __('Add amendment', 'twentytwelve'); ?></a></li>
                      <?php $antrag_id = $post->ID; ?>
                      <?php $loop3 = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                                        'meta_key'   => 'cvtx_sort',
                                                        'orderby'    => 'meta_value',
                                                        'order'      => 'ASC',
                                                        'nopaging'   => true,
                                                        'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                                    'value'   => $antrag_id,
                                                                                    'compare' => '=')))); ?>
                      <?php if($loop3->have_posts()): ?>
                        <li><a href="<?php the_permalink(); ?>" rel="extern" class="ae_antraege_overview" meta-id="<?php print $post->ID; ?>"><?php print __('Amendment overview', 'twentytwelve'); ?></a></li>
                      <?php endif;?>
                    </ul><div id="result-<?php print $post->ID; ?>" class="ae_antraege_result"></div>
                    <?php if($loop3->have_posts()): ?>
                    <ul class="ae_antraege">
                      <h4><?php print __('Amendments', 'twentytwelve'); ?></h4>
                    <?php
                      while($loop3->have_posts()):$loop3->the_post();?>
                        <li><span><?php the_title(); ?></span> (<?php print __('Author(s)', 'twentytwelve'); ?>: <em><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true);?></em>)</li>
                      <?php endwhile;?>
                    </ul>
                    <?php endif;?>
                    <div class="clear-block"></div></li>
                  <?php endwhile;?>
                  <?php if($loop2->have_posts()) print '</ul>';?>
                  
                  <?php
                  // query top-content
                  $loop4 = new WP_Query(array('post_type'  => 'cvtx_application',
                                              'meta_key'   => 'cvtx_sort',
                                              'orderby'    => 'meta_value',
                                              'nopaging'   => true,
                                              'order'      => 'ASC',
                                              'meta_query' => array(array('key'     => 'cvtx_application_top',
                                                                          'value'   => $top_id,
                                                                          'compare' => '='))));
                  if ($loop4->have_posts()):
                  ?>
                   <ul>
                    <?php
                    while ($loop4->have_posts()): $loop4->the_post(); ?>
                     <li class="application">
                      <!--<h4><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) the_title('<a href="'.$file.'">', ' (pdf)</a>'); else the_title(); ?></h4>-->
                      <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <ul class="options">
                          <li><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); else echo('Kein PDF erstellt.'); ?></li>
                        </ul>
                      <div class="clear-block"></div>
                     </li>
                    <?php endwhile;?>
                   </ul>
                  <?php endif; ?>
                  
                  </li>
                <?php endwhile;?>
                </ul>
                <?php endif; ?>
                <?php wp_reset_postdata(); $post = $post_bak; ?>
            </div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>