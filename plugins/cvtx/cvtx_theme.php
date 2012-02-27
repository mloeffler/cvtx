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
            printf(__('<strong>AntragstellerInnen:</strong> %1$s'),$antragsteller);
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
            printf(__('<strong>Begr&uuml;ndung:</strong> %1$s'),$grund);
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
            <h3><?php print __('&Auml;nderungsantr&auml;ge','cvtx'); ?><?php print (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) ? __(' zu ', 'cvtx').cvtx_get_short($post) : ''; ?></h3>
            <table cellpadding="3" cellspacing="0" valign="top">
                <tr>
                    <th><strong><?php _e('Zeile', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('AntragstellerInnen', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Antragstext', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Begr&uuml;ndung', 'cvtx'); ?></strong></th>
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
        printf(__('<h3>&Auml;nderungsantrag zu %s erstellen</h3>','cvtx'), cvtx_get_short($post));
        cvtx_submit_aeantrag($post->ID);
        print '</div>';
    }
}
add_action('cvtx_theme_add_aeantrag','cvtx_add_aeantrag_action',10,1);

/**
 * themed output for an antrags pdf
 */
function cvtx_pdf_action($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        if ($file = cvtx_get_file($post, 'pdf')) {
            echo '<h3>PDF</h3>';
            echo '<a href="'.$file.'">Download (pdf)</a>';
        }
    }
}
add_action('cvtx_theme_pdf','cvtx_pdf_action',10,1);

function cvtx_zeile_action($post_id = false) {
   if(!isset($post_id) || !$post_id) global $post;
   else $post = get_post($post_id);
   if(is_object($post)) {
      $zeile = get_post_meta($post->ID,'cvtx_aeantrag_zeile',true);
      if(!empty($zeile)){
          printf(__('<p><strong>Zeile:</strong> %1$s</p>','cvtx'),$zeile);
      }
   }
}
add_action('cvtx_theme_zeile','cvtx_zeile_action',10,1);

?>