<?php

/**
 * themed output of cvtx_antrag_steller/cvtx_aeantrag_steller/cvtx_antrag_steller_short/cvtx_aeantrag_steller_short
 * 
 * @param associative Array
 *        post_id => Do you want a specific posts antragsteller?
 *        short   => Short or long version? Default to true
 */
function cvtx_antragsteller_action($args = array('post_id' => false, 'short' => true)) {
    if(!isset($args['post_id']) || !$args['post_id']) global $post;
    else $post = get_post($args['post_id']);
    if(is_object($post)) {
        $field = $post->post_type.'_steller'.(isset($args['short']) && $args['short'] ? '_short' : '');
        $antragsteller = get_post_meta($post->ID,$field,true);
        if(!empty($antragsteller)){
            echo('<strong>'.__('Author(s)', 'cvtx').':</strong> ');
            printf('%1$s', $antragsteller);
        }
    }
}
add_action('cvtx_theme_antragsteller','cvtx_antragsteller_action',10,1);

/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_grund_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        $grund = get_post_meta($post->ID,$post->post_type.'_grund',true);
        if(!empty($grund)) {
            echo('<strong>'.__('Explanation', 'cvtx').':</strong> ');
            printf('%1$s', $grund);
        }
    }
}
add_action('cvtx_theme_grund','cvtx_grund_action',10,1);

/**
 * themed output of all aenderungsantraege to given post or post_id
 * 
 * @param post_id Do you want a specific posts aenderungsantraege?
 *
 */
function cvtx_aenderungsantraege_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
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
        <div id="ae_antraege">
            <h3><?php print __('Amendments', 'cvtx'); ?><?php print (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) ? __(' to ', 'cvtx').cvtx_get_short($post) : ''; ?></h3>
            <table cellpadding="3" cellspacing="0" valign="top">
                <tr>
                    <th><strong><?php _e('Line', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Author(s)', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Text', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Explanation', 'cvtx'); ?></strong></th>
                </tr>
                <?php 
                while($loop->have_posts()):$loop->the_post();?>
                <tr>
                    <td class="zeile"><strong><?php print get_post_meta($post->ID,'cvtx_aeantrag_zeile',true); ?></strong></td>
                    <td class="steller"><?php print get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true);?></td>
                    <td class="text"><?php the_content(); ?></td>
                    <td class="grund"><?php print get_post_meta($post->ID,'cvtx_aeantrag_grund',true);?></td>
                </tr>
                <?php endwhile;?>
            </table>
        </div>
   <?php endif; wp_reset_postdata();
}
add_action('cvtx_theme_aenderungsantraege','cvtx_aenderungsantraege_action',10,1);

/**
 * themed output for add_aenderungsantraege
 */
function cvtx_add_aeantrag_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        print '<div id="add_aeantrag">';
        printf(__('<h3>Create amendment to %s</h3>','cvtx'), cvtx_get_short($post));
        cvtx_submit_aeantrag($post->ID);
        print '</div>';
    }
}
add_action('cvtx_theme_add_aeantrag','cvtx_add_aeantrag_action',10,1);

/**
 * themed output for pdfs of all different cvtx_post_types
 */
function cvtx_pdf_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        if ($file = cvtx_get_file($post, 'pdf')) {
            echo '<h3>PDF</h3>';
            echo '<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>';
        }
    }
}
add_action('cvtx_theme_pdf','cvtx_pdf_action',10,1);

/**
 * themed output for line
 */
function cvtx_zeile_action($post_id = false) {
   if(!isset($post_id) || !$post_id) global $post;
   else $post = get_post($post_id);
   if(is_object($post)) {
      $zeile = get_post_meta($post->ID,'cvtx_aeantrag_zeile',true);
      if(!empty($zeile)){
          printf(__('<p><strong>Line:</strong> %1$s</p>','cvtx'),$zeile);
      }
   }
}
add_action('cvtx_theme_zeile','cvtx_zeile_action',10,1);

/**
 * themed output for reader
 */
function cvtx_reader_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        $items = array();
        $query = new WP_Query(array('taxonomy' => 'cvtx_tax_reader',
                                    'term'     => 'cvtx_reader_'.intval($post->ID),
                                    'orderby'  => 'meta_value',
                                    'meta_key' => 'cvtx_sort',
                                    'order'    => 'ASC',
                                    'nopaging' => true));
        while ($query->have_posts()) {
            $query->the_post();
            $items[] = $post->ID;
        }
        
        echo __('<p>In dieser Antragsmappe sind enthalten:</p>', 'cvtx');
        // list all contents
        echo '<ul class="reader_list">';
        $open_top    = false;
        $open_antrag = false;
        foreach($items as $item) {
            $post = get_post($item);
            if($post->post_type == 'cvtx_top') {
                if($open_antrag || $open_top) echo '</ul></li>';
        		echo '<li><h4>'; the_title(); echo '</h4><ul>';
        		$open_top = true; $open_antrag = false; $open_app = false;
        	}
        	else if($post->post_type == 'cvtx_antrag') {
        	    if($open_antrag) echo '</ul></li>';
        		echo '<li><a href="';the_permalink();echo '">'; the_title(); echo '</a><ul>';
        		$open_antrag = true;
        	}
        	else if($post->post_type == 'cvtx_aeantrag') {
        		echo '<li><a href="';the_permalink();echo '">'; the_title(); echo '</a></li>';
        	}
        	else if($post->post_type == 'cvtx_application') {
        	    if($open_antrag) echo '</ul></li>';
        	    echo '<li><a href="';the_permalink();echo '">'; the_title(); echo '</a></li>';
        	}
		}
    
        wp_reset_postdata();
	    if($open_top) echo '</ul></li>'; 
        if($open_antrag) echo '</ul></li>'; 
		echo '</ul>';
    }
}
add_action('cvtx_theme_reader','cvtx_reader_action',10,1);

function cvtx_top_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        // query top-content
        $loop2 = new WP_Query(array('post_type'  => array('cvtx_antrag'),
                                    'meta_key'   => 'cvtx_sort',
                                    'orderby'    => 'meta_value',
                                    'nopaging'   => true,
                                    'order'      => 'ASC',
                                    'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                'value'   => $post->ID,
                                                                'compare' => '='))));
        echo '<ul id="antraege">';
        while ($loop2->have_posts()){
            $loop2->the_post();
            echo '<li class="top"><h3><a href="'; the_permalink();echo '">';
                the_title();echo '</a></h3>';
                echo '<div class="top_content"><span class="steller">';
                    echo __('<strong>AntragstellerInnen:</strong> ', 'cvtx');
                    echo get_post_meta($post->ID,'cvtx_antrag_steller_short',true);
                echo '</span>';
                echo '<ul class="options">';
                    echo '<li>'; 
                    if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) 
                        echo('<a href="'.$file.'">Download (pdf)</a>'); 
                    else echo __('Kein PDF erstellt.', 'cvtx'); 
                    echo '</li>';
                    echo '<li><a href="'; the_permalink();
                    echo '#add_aeantrag" rel="extern" class="add_ae_antraeg" meta-id="'.
                         $post->ID.'">'.__('Änderungsantrag hinzufügen</a>', 'cvtx').'</li>';
                    $loop3 = new WP_Query(array(
                              'post_type'  => 'cvtx_aeantrag',
                              'meta_key'   => 'cvtx_sort',
                              'orderby'    => 'meta_value',
                              'order'      => 'ASC',
                              'nopaging'   => true,
                              'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                          'value'   => $post->ID,
                                                          'compare' => '='))));
                    if($loop3->have_posts()) {
                        echo '<li><a href="'; the_permalink(); 
                        echo '" rel="extern" class="ae_antraege_overview" meta-id="'.$post->ID.'">'.__('Änderungsantragsübersicht','cvtx').'</a></li>';
                    }
                echo '</ul>';
                echo '<div id="result-'.$post->ID.'" class="ae_antraege_result"></div>';
                if($loop3->have_posts()) {
                    echo '<ul class="ae_antraege">';
                        echo '<h4>'.__('Änderungsanträge','cvtx').'</h4>';
                        while($loop3->have_posts()){
                            $loop3->the_post();
                            echo '<li><span>';
                            the_title();
                            echo '</span> ('.__('AntragstellerInnen', 'cvtx').': <em>';
                            echo get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true).'</em>)</li>';
                        }
                    echo '</ul>';
                }
            echo '<div class="clear-block"></div></div>';
        echo '</li>'; // end li.top
        }
        echo '</ul>'; // end ul#antraege
        wp_reset_postdata();
    }
}
add_action('cvtx_theme_top','cvtx_top_action',10,1);

?>