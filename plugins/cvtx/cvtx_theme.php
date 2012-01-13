<?php

/**
 * themed output of cvtx_antrag_steller/cvtx_aeantrag_steller/cvtx_antrag_steller_short/cvtx_aeantrag_steller_short
 * 
 * @param associative Array
 *        post_id => Do you want a specific posts antragsteller?
 *        short   => Short or long version? Default to true
 */
function cvtx_theme_antragsteller($args = array('post_id' => false, 'short' => true)) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        $field = $post->post_type.'_steller'.($args['short'] ? '_short' : '');
        $antragsteller = get_post_meta($post->ID,$field,true);
        if(!empty($antragsteller)){
            printf(__('<strong>AntragstellerInnen:</strong> %1$s'),$antragsteller);
        }
    }
}

/**
 * themed output of cvtx_antrag_grund
 * 
 * @param post_id Do you want a specific posts grund?
 *
 */
function cvtx_theme_grund($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        $grund = get_post_meta($post->ID,$post->post_type.'_grund',true);
        if(!empty($grund)) {
            printf(__('<strong>Begr&uuml;ndung:</strong> %1$s'),$grund);
        }
    }
}

/**
 * themed output of all aenderungsantraege to given post or post_id
 * 
 * @param post_id Do you want a specific posts aenderungsantraege?
 *
 */
function cvtx_theme_aenderungsantraege($post_id = false) {
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
            <h3><?php print __('&Auml;nderungsantr&auml;ge','cvtx'); ?><?php print (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) ? ' zu '.cvtx_get_short($post) : ''; ?></h3>
            <table cellpadding="3" cellspacing="0" valign="top">
                <tr>
                    <th><strong>Zeile</strong></th>
                    <th><strong>AntragstellerInnen</strong></th>
                    <th><strong>Antragstext</strong></th>
                    <th><strong>Begr&uuml;ndung</strong></th>
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

/**
 * themed output for add_aenderungsantraege
 */
function cvtx_theme_add_aeantrag($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        print '<div id="add_aeantrag">';
        printf(__('<h3>&Auml;nderungsantrag zu %s erstellen</h3>','cvtx'), cvtx_get_short($post));
        cvtx_submit_aeantrag($post->ID);
        print '</div>';
    }
}

/**
 * themed output for an antrags pdf
 */
function cvtx_theme_pdf($post_id = false) {
    if(!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if(is_object($post)) {
        if ($file = cvtx_get_file($post, 'pdf')) {
            echo '<h3>PDF</h3>';
            echo '<a href="'.$file.'">Download (pdf)</a>';
        }
    }
}

function cvtx_theme_zeile($post_id = false) {
   if(!isset($post_id) || !$post_id) global $post;
   else $post = get_post($post_id);
   if(is_object($post)) {
      $zeile = get_post_meta($post->ID,'cvtx_aeantrag_zeile',true);
      if(!empty($zeile)){
          printf(__('<p><strong>Zeile:</strong> %1$s</p>','cvtx'),$zeile);
      }
   }
}
?>