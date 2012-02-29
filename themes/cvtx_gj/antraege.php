<?php
/**
 * Template Name: Antragsübersicht
 *
 * Dieses Template stellt eine Übersicht über alle bereits
 * eingerichteten Anträge, Änderungsanträge und TOPs dar.
 *
 * @package WordPress
 * @subpackage cvtx
 */
?>

<?php get_header(); ?>
  <div class="inner">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
        <div class="entry">
          <?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx') . '</p>'); ?>
        </div>
      </div>
    <?php endwhile; endif; ?>
  
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
      	<li class="rss top"><h3>RSS-Feed</h3><?php printf('<p>'.__('Um immer über neue Anträge auf dem Laufenden zu bleiben, abonniere doch einfach den %1$s!', 'cvtx').'</p>','<a href="'.$rss_url.'">RSS-Feed</a>'); ?></li>
        <li class="top overview"><h3>Übersicht</h3>
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
      <ul>
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
      while ($loop2->have_posts()): $loop2->the_post(); ?>
        <li class="antrag"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <span class="steller">
          <strong>AntragstellerInnen:</strong>
          <?php print get_post_meta($post->ID,'cvtx_antrag_steller_short',true);?>
        </span>
        <ul class="options">
          <li><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); else echo('Kein PDF erstellt.'); ?></li>
          <li><a href="<?php the_permalink(); ?>#add_aeantrag" rel="extern" class="add_ae_antraeg" meta-id="<?php print $post->ID; ?>">Änderungsantrag hinzufügen</a></li>
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
            <li><a href="<?php the_permalink(); ?>" rel="extern" class="ae_antraege_overview" meta-id="<?php print $post->ID; ?>">Änderungsantragsübersicht</a></li>
          <?php endif;?>
        </ul><div id="result-<?php print $post->ID; ?>" class="ae_antraege_result"></div>
        <?php if($loop3->have_posts()): ?>
        <ul class="ae_antraege">
          <h4>Änderungsanträge</h4>
        <?php
          while($loop3->have_posts()):$loop3->the_post();?>
            <li><span><?php the_title(); ?></span> (AntragstellerInnen: <em><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true);?></em>)</li>
          <?php endwhile;?>
        </ul>
        <?php endif;?>
        <div class="clear-block"></div></li>
      <?php endwhile;?>
      </ul>
      
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
          <h4><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) the_title('<a href="'.$file.'">', ' (pdf)</a>'); else the_title(); ?></h4>
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
  </div>
  <?php get_sidebar(); ?>
<?php get_footer(); ?>