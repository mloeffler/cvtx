<?php
/**
 * Template Name: Änderungsantragsübersicht
 *
 * Dieses Template stellt eine Übersicht über alle bereits
 * eingerichteten Anträge dar.
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
    
<div class="inner" role="content">

    <?php

    $antraege       = (isset($_POST['aeantrag_antraege']) ? $_POST['aeantrag_antraege'] : false);
    $show_empty     = (isset($_POST['aeantrag_show_empty'])     && $_POST['aeantrag_show_empty']     ? true : false);
    $show_verfahren = (isset($_POST['aeantrag_show_verfahren']) && $_POST['aeantrag_show_verfahren'] ? true : false);
    $show_steller   = (isset($_POST['aeantrag_show_steller'])   && $_POST['aeantrag_show_steller']   ? true : false);
    
    if (is_array($antraege) || $show_empty || $show_verfahren || $show_steller) $hide = true;
    else $hide = false;

    // TOP-Query
    $loop = new WP_Query(array('post_type' => 'cvtx_top',
                               'orderby'   => 'meta_value',
                               'meta_key'  => 'cvtx_sort',
                               'order'     => 'ASC',
                               'nopaging'  => true,
                               'meta_query'=> array(array('key'   => 'cvtx_top_antraege',
                                                          'value' => 'off',
                                                          'compare' => '!='))));
    if($loop->have_posts()):?>
      <div id="liste"><br/>
        <div class="toggler"><a href="#" data-role="button" <?php if($hide) echo ' data-icon="arrow-u"'; else echo ' data-icon="arrow-d"'; ?>><?php if($hide) print __('Show filters','cvtx_theme'); else print __('Hide filters', 'cvtx_theme'); ?></a></div>
        <form method="post" action="#" id="filter" <?php if($hide) print 'style="display:none"'; ?> data-ajax="false">
          <label for="aeantrag_antraege" class="select" style="display:none"><?php print __('Agenda points and amendments', 'cvtx_theme'); ?></label>
          <select id="aeantrag_antraege" style="width: 100%" multiple="multiple" size="20" name="aeantrag_antraege[]" data-native-menu="false">
          	<option value="" data-placeholder="true"><?php print __('Agenda points and amendments', 'cvtx_theme'); ?></option>
          <?php
          while ($loop->have_posts()):$loop->the_post(); $top_id = $post->ID;?>
            <optgroup label="<?php the_title(); ?>">
            <?php
            $loop2 = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                        'orderby'    => 'meta_value',
                                        'meta_key'   => 'cvtx_sort',
                                        'order'      => 'ASC',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                    'value'   => $top_id,
                                                                    'compare' => '='))));
            while ($loop2->have_posts()): $loop2->the_post(); ?>
              <option value="<?php print $post->ID; ?>" label="<?php the_title(); ?>"<?php if(isset($_POST['aeantrag_antraege']) && in_array($post->ID, $_POST['aeantrag_antraege'])) print ' selected="true"'; ?>><?php the_title(); ?></option>
            <?php endwhile;?>
          </optgroup>
          <?php endwhile;?>
          </select>
          <p>
            <input id="aeantrag_show_empty" name="show_empty" type="checkbox" <?php if($show_empty) print 'checked="true"'; ?> />
            <label for="aeantrag_show_empty"><?php print __('Show only resolutions with amendments', 'cvtx_theme'); ?></label>
          </p>
          <input type="submit" value="Liste anzeigen" />
        </form>
      </div>
    <?php endif; ?>

    <?php if($antraege): ?>
    	<div id="result" class="noellipsis">
      	<ul data-role="listview" data-inset="true">
      		<?php foreach($antraege as $antrag_id): ?>
	        	<?php $loop3 = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
  		                                        'orderby'    => 'meta_value',
      		                                    'meta_key'   => 'cvtx_sort',
          		                                'nopaging'   => true,
              		                            'order'      => 'ASC',
                  		                        'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                      		                                                'value'   => $antrag_id,
                          		                                            'compare' => '=')))); ?>
        		<?php if (!$show_empty || $loop3->have_posts()): ?>
          		<?php $post = get_post($antrag_id); ?>
          		<li data-role="list-divider"><?php the_title(); ?></li>
          		<?php if($loop3->have_posts()): ?>
            		<?php while($loop3->have_posts()):$loop3->the_post();?>
	        				<li><span class="ui-li-aside"><?php _e('Line', 'cvtx'); ?>: <strong><?php print get_post_meta($post->ID,'cvtx_aeantrag_zeile',true); ?></strong></span>
		        				<h3><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller',true);?></h3><br/>
		        				<p class="aeantrag_conent"><strong><?php _e('Text', 'cvtx'); ?></strong>: <?php the_content(); ?></p><br/>
	  	      				<p class="aeantrag_expl"><strong><?php _e('Explanation', 'cvtx'); ?></strong>: <?php print get_post_meta($post->ID,'cvtx_aeantrag_grund',true);?></p><br/>
	            			<p class="aeantrag_procedure"><strong><?php _e('Procedure', 'cvtx'); ?></strong>: <?php print get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true); ?></p>
	        				</li>
  	          	<?php endwhile;?>
    	     		<?php endif; ?>
    	     	<?php endif; ?>
       		<?php endforeach; ?>
     		</ul>
			</div> <!-- end #result -->
		<?php endif; ?>

	</div>
    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/ae_antraege.js"></script>

<?php get_footer(); ?>