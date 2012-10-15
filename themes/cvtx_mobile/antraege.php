<?php
/**
 * Template Name: Antragsübersicht
 *
 * Dieses Template stellt eine Übersicht über alle bereits
 * eingerichteten Anträge, Änderungsanträge und TOPs dar.
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
  <div class="inner" data-role="content">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
        <div class="entry">
          <?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
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
      <ul id="antraege" data-role="listview" data-inset="true">
      	<?php $rss_url = add_query_arg(array('post_type' => 'cvtx_antrag'),get_feed_link('rss2'));?>
      	<li class="rss top"><h3><?php print __('RSS-Feed', 'cvtx_theme'); ?></h3><?php printf('<p>'.__('Stay up to date? Sign up to our %1$s!', 'cvtx_theme').'</p>','<a href="'.$rss_url.'">RSS-Feed</a>'); ?></li>        
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
      // query top-content
      $loop4 = new WP_Query(array('post_type'  => 'cvtx_application',
                                  'meta_key'   => 'cvtx_sort',
                                  'orderby'    => 'meta_value',
                                  'nopaging'   => true,
                                  'order'      => 'ASC',
                                  'meta_query' => array(array('key'     => 'cvtx_application_top',
                                                              'value'   => $top_id,
                                                              'compare' => '='))));
			$posts = $loop2->post_count + $loop4->post_count;
      print '<span class="ui-li-count">'.$posts.'</span>';
      if($loop2->have_posts()) print '<ul>';
      while ($loop2->have_posts()): $loop2->the_post(); ?>
        <li class="antrag"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <span class="steller">
          <strong><?php print __('Author(s)', 'cvtx_theme'); ?>:</strong>
          <?php print get_post_meta($post->ID,'cvtx_antrag_steller_short',true);?>
        </span>
        <span class="ui-li-count"><?php echo cvtx_get_amendment_count($post->ID); ?></span>
        <div class="clear-block"></div></li>
      <?php endwhile;?>
      <?php if($loop2->have_posts()) print '</ul>';?>
      
      <?php
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

<?php get_footer(); ?>