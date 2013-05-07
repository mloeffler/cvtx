<?php
/**
 * @package cvtx
 */


add_action('cvtx_theme_antragsteller', 'cvtx_antragsteller_action', 10, 1);
/**
 * themed output of cvtx_antrag_steller/cvtx_aeantrag_steller/cvtx_antrag_steller_short/cvtx_aeantrag_steller_short
 * 
 * @param associative Array
 *        post_id => Do you want a specific posts antragsteller?
 *        short   => Short or long version? Default to true
 */
function cvtx_antragsteller_action($args = array('post_id' => false, 'short' => true)) {
    if (!isset($args['post_id']) || !$args['post_id']) global $post;
    else $post = get_post($args['post_id']);
    
    if (is_object($post)) {
        // Fetch authors
        $field = $post->post_type.'_steller'.(isset($args['short']) && $args['short'] ? '_short' : '');
        $antragsteller = get_post_meta($post->ID, $field, true);
        
        // Purify authors
        if (is_plugin_active('html-purified/html-purified.php')) {
            global $cvtx_purifier, $cvtx_purifier_config;
            $antragsteller = $cvtx_purifier->purify($antragsteller, $cvtx_purifier_config);
        }
        // Trim authors
        $antragsteller = trim($antragsteller);
        
        // Anything left to print?
        if (!empty($antragsteller)) {
            // Convert line breaks to paragraphs
            $antragsteller = '<p>'.preg_replace('/[\r\n]+/', '</p><p>', $antragsteller).'</p>';
            
            // Print authors
            echo('<strong>'.__('Author(s)', 'cvtx').':</strong> ');
            printf('%1$s', $antragsteller);
        }
    }
}


add_action('cvtx_theme_grund', 'cvtx_grund_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 */
function cvtx_grund_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    
    if (is_object($post)) {
        // Fetch explanation
        $grund = get_post_meta($post->ID, $post->post_type.'_grund', true);
        
        // Purify explanation
        if (is_plugin_active('html-purified/html-purified.php')) {
            global $cvtx_purifier, $cvtx_purifier_config;
            $grund = $cvtx_purifier->purify($grund, $cvtx_purifier_config);
        }
        // Trim explanation
        $grund = trim($grund);
        
        // Anything left to print?
        if (!empty($grund)) {
            // Convert line breaks to paragraphs
            $grund = '<p>'.preg_replace('/[\r\n]+/', '</p><p>', $grund).'</p>';
            
            // Print explanation
            echo('<strong>'.__('Explanation', 'cvtx').':</strong> ');
            printf('%1$s', $grund);
        }
    }
}

add_action('cvtx_theme_gender', 'cvtx_gender_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_gender_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post)) {
        $gender = get_post_meta($post->ID, $post->post_type.'_gender', true);
        if (!empty($gender)) {
            echo('<p>'.__('Gender', 'cvtx').': ');
            $gender_arr = array(__('female','cvtx'), __('male','cvtx'), __('not specified', 'cvtx'));
            printf('<span>%1$s</span>', $gender_arr[$gender-1]);
            echo('</p>');
        }
    }
}

add_action('cvtx_theme_birthdate', 'cvtx_birthdate_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_birthdate_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post)) {
        $birthdate = get_post_meta($post->ID, $post->post_type.'_birthdate', true);
        if (!empty($birthdate)) {
            echo('<p>'.__('Date of Birth', 'cvtx').': ');
            printf('<span>%1$s</span>', $birthdate);
            echo('</p>');
        }
    }
}

add_action('cvtx_theme_kv', 'cvtx_kv_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_kv_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    $options = get_option('cvtx_options');
    if (is_object($post) && !empty($options['cvtx_application_kvs_name']) && !empty($options['cvtx_application_kvs'])) {
        $kv = get_post_meta($post->ID, 'cvtx_application_kv', true);
        if (!empty($kv)) {
            echo('<p>'.$options['cvtx_application_kvs_name'].': ');
            printf('<span>%1$s</span>', $options['cvtx_application_kvs'][$kv]);
            echo('</p>');
        }
    }
}

add_action('cvtx_theme_bv', 'cvtx_bv_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_bv_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    $options = get_option('cvtx_options');
    if (is_object($post) && !empty($options['cvtx_application_bvs_name']) && !empty($options['cvtx_application_bvs'])) {
        $bv = get_post_meta($post->ID, 'cvtx_application_bv', true);
        if (!empty($bv)) {
            echo('<p>'.$options['cvtx_application_bvs_name'].': ');
            printf('<span>%1$s</span>', $options['cvtx_application_bvs'][$bv]);
            echo('</p>');
        }
    }
}

add_action('cvtx_theme_topics', 'cvtx_topics_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_topics_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    $options = get_option('cvtx_options');
    if (is_object($post) && !empty($options['cvtx_application_topics'])) {
        $topics = get_post_meta($post->ID, 'cvtx_application_topics', array());
        $topic_arr = array();
        foreach($topics as $topic_id) {
            array_push($topic_arr,trim($options['cvtx_application_topics'][$topic_id]));
        }
        if (!empty($topic_arr)) {
            echo('<p>'.__('Topics','cvtx').': ');
            printf('<span>%1$s</span>', implode(', ',$topic_arr));
            echo('</p>');
        }
    }
}

add_action('cvtx_theme_website', 'cvtx_website_action', 10, 1);
/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_website_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post)) {
        $website = get_post_meta($post->ID, 'cvtx_application_website', true);
        if (!empty($website)) {
            echo('<p><a href="'.$website.'" class="extern">'.__('Website','cvtx').'</a></p>');
        }
    }
}


add_action('cvtx_theme_aenderungsantraege', 'cvtx_aenderungsantraege_action', 10, 1);
/**
 * themed output of all aenderungsantraege to given post or post_id
 * 
 * @param post_id Do you want a specific posts aenderungsantraege?
 *
 */
function cvtx_aenderungsantraege_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
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
    if ($loop->have_posts()): ?>
        <div id="ae_antraege" class="entry-content">
            <h3><?php _e('Amendments', 'cvtx'); ?><?php if (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) _e(' to ', 'cvtx').cvtx_get_short($post); ?></h3>
            <table cellpadding="3" cellspacing="0" valign="top" class="ae_antraege_table">
                <tr>
                    <th><strong><?php _e('Line', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Author(s)', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Text', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Explanation', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Procedure', 'cvtx'); ?></strong></th>
                </tr>
                <?php 
                while ($loop->have_posts()): $loop->the_post(); ?>
                    <tr <?php if(cvtx_map_procedure(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true)) === 'd') echo('class="withdrawn"'); ?>>
                        <td class="zeile"><strong><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true)); ?></strong></td>
                        <td class="steller"><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true));?></td>
                        <td class="text"><?php the_content(); ?></td>
                        <td class="grund"><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true)); ?></td>
                        <td class="verfahren"><span class="flag <?php echo(cvtx_map_procedure(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true))); ?>"></span><span class="procedure"><span class="arrow"></span><strong><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true)); ?></strong><?php if(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true)) echo('<p/>'); ?><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true)); ?></span></td>
                    </tr>
                <?php endwhile;?>
            </table>
        </div>
   <?php endif; wp_reset_postdata();
}


/**
 * maps procedures to numbers or the other way around
 */
function cvtx_map_procedure($input) {
    $map = array('a' => __('Adoption', 'cvtx'),
                 'b' => __('modified adoption', 'cvtx'),
                 'c' => __('Vote', 'cvtx'),
                 'd' => __('Withdrawn', 'cvtx'),
                 'e' => __('Obsolete', 'cvtx'));
    foreach ($map as $key => $value) {
        if ($input === $value) return $key;
        if ($input === $key)   return $value;
    }
    return false;
}


add_action('cvtx_theme_add_aeantrag', 'cvtx_add_aeantrag_action', 10, 1);
/**
 * themed output for add_aenderungsantraege
 */
function cvtx_add_aeantrag_action($args = array('post_id' => false, 'show_recaptcha' => true)) {
    if (!isset($args['post_id']) || !$args['post_id']) global $post;
    else $post = get_post($post_id);
    if (is_object($post) && $post->post_type == 'cvtx_antrag') {
        echo('<div id="add_aeantrag" class="entry-content">');
        printf(__('<h3>Create amendment to %s</h3>', 'cvtx'), cvtx_get_short($post));
        cvtx_submit_aeantrag($post->ID, (isset($args) && is_array($args) && isset($args['show_recaptcha']) ? $args['show_recaptcha'] : true));
        echo('</div>');
    }
}


add_action('cvtx_theme_pdf', 'cvtx_pdf_action', 10, 1);
/**
 * themed output for pdfs of all different cvtx_post_types
 */
function cvtx_pdf_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post)) {
        if ($file = cvtx_get_file($post, 'pdf')) {
            echo('<h3>PDF</h3>');
            echo('<a href="'.$file.'" rel="external">'.__('Download', 'cvtx').' (pdf)</a>');
        }
    }
}


add_action('cvtx_theme_zeile', 'cvtx_zeile_action', 10, 1);
/**
 * themed output for line
 */
function cvtx_zeile_action($post_id = false) {
   if (!isset($post_id) || !$post_id) global $post;
   else $post = get_post($post_id);
   if (is_object($post)) {
      $zeile = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
      if (!empty($zeile)) {
          printf(__('<p><strong>Line:</strong> %1$s</p>', 'cvtx'), $zeile);
      }
   }
}

add_action('cvtx_theme_cv', 'cvtx_cv_action', 10, 1);
/**
 * themed output of life career
 */
function cvtx_cv_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post) && $post->post_type == 'cvtx_application') {
        $cv = get_post_meta($post->ID, 'cvtx_application_cv', true);
        if(!empty($cv)) {
            echo ('<h2>'.__('Life career', 'cvtx').'</h2>');
            echo ('<p>'.get_post_meta($post->ID, 'cvtx_application_cv', true).'</p>');
        }
    }
}


add_action('cvtx_theme_reader', 'cvtx_reader_action', 10, 1);
/**
 * themed output for reader
 */
function cvtx_reader_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post) && $post->post_type == 'cvtx_reader') {
        $items = array();
        $query = new WP_Query(array('post_type' => array('cvtx_antrag',
                                                         'cvtx_aeantrag',
                                                         'cvtx_application'),
                                    'taxonomy'  => 'cvtx_tax_reader',
                                    'term'      => 'cvtx_reader_'.intval($post->ID),
                                    'orderby'   => 'meta_value',
                                    'meta_key'  => 'cvtx_sort',
                                    'order'     => 'ASC',
                                    'nopaging'  => true));
        while ($query->have_posts()) {
            $query->the_post();
            $items[] = $post->ID;
        }
        
        echo('<p>'.__('Contents', 'cvtx').':</p>');
        // list all contents
        echo('<ul class="reader_list">');
        $open_top    = false;
        $open_antrag = false;
        foreach ($items as $item) {
            $post = get_post($item);
            if ($post->post_type == 'cvtx_top') {
                if ($open_antrag || $open_top) echo('</ul></li>');
                echo('<li><h4>'); the_title(); echo('</h4><ul>');
                $open_top    = true;
                $open_antrag = false;
                $open_app    = false;
            } else if ($post->post_type == 'cvtx_antrag') {
                if ($open_antrag) echo('</ul></li>');
                echo('<li><a href="'); the_permalink(); echo('">'); the_title(); echo('</a><ul>');
                $open_antrag = true;
            } else if ($post->post_type == 'cvtx_aeantrag') {
                echo('<li><a href="'); the_permalink(); echo('">'); the_title(); echo('</a></li>');
            } else if ($post->post_type == 'cvtx_application') {
                if ($open_antrag) echo '</ul></li>';
                echo('<li><a href="'); the_permalink(); echo('">'); the_title(); echo('</a></li>');
            }
        }
        wp_reset_postdata();
        if ($open_top)    echo('</ul></li>');
        if ($open_antrag) echo('</ul></li>');
        echo('</ul>');
    }
}


add_action('cvtx_theme_top', 'cvtx_top_action', 10, 1);
/**
 *
 */
function cvtx_top_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post)) {
        // query top-content
        $loop2 = new WP_Query(array('post_type'  => array('cvtx_antrag'),
                                    'meta_key'   => 'cvtx_sort',
                                    'orderby'    => 'meta_value',
                                    'nopaging'   => true,
                                    'order'      => 'ASC',
                                    'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                'value'   => $post->ID,
                                                                'compare' => '='))));
        echo('<ul id="antraege">');
        while ($loop2->have_posts()){
            $loop2->the_post();
            echo('<li class="top"><h3><a href="'); the_permalink(); echo('">'); the_title(); echo('</a></h3>');
            echo('<div class="top_content">');
                echo('<span class="steller">');
                    echo('<strong>'.__('Author(s)', 'cvtx').':</strong> ');
                    echo(get_post_meta($post->ID, 'cvtx_antrag_steller_short', true));
                echo('</span>');
                echo('<ul class="options">');
                    echo('<li>');
                    if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) {
                        echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>');
                    }
                    else _e('No PDF available.', 'cvtx');
                    echo('</li>');
                    echo('<li><a href="'); the_permalink();
                    echo('#add_aeantrag" rel="extern" class="add_ae_antraeg" meta-id="'.$post->ID.'">'.__('Add amendment', 'cvtx').'</a></li>');
                    $loop3 = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                                'meta_key'   => 'cvtx_sort',
                                                'orderby'    => 'meta_value',
                                                'order'      => 'ASC',
                                                'nopaging'   => true,
                                                'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                            'value'   => $post->ID,
                                                                            'compare' => '='))));
                    if ($loop3->have_posts()) {
                        echo('<li><a href="'); the_permalink();
                        echo('" rel="extern" class="ae_antraege_overview" meta-id="'.$post->ID.'">'.__('Overview', 'cvtx').'</a></li>');
                    }
                echo('</ul>');
                echo('<div id="result-'.$post->ID.'" class="ae_antraege_result"></div>');
                if ($loop3->have_posts()) {
                    echo('<ul class="ae_antraege">');
                        echo('<h4>'.__('Amendments', 'cvtx').'</h4>');
                        while($loop3->have_posts()){
                            $loop3->the_post();
                            echo('<li><span>'); the_title(); echo('</span> ('.__('Author(s)', 'cvtx').': <em>');
                            echo(get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true).'</em>)</li>');
                        }
                    echo('</ul>');
                }
            echo('<div class="clear-block"></div></div>');
        echo('</li>'); // end li.top
        }
        echo('</ul>'); // end ul#antraege
        wp_reset_postdata();
    }
}

?>
