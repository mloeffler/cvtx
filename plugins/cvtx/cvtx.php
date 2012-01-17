<?php
/**
 * @package cvtx
 * @version 0.1
 */
/*
Plugin Name: cvtx project
Plugin URI: http://cvtx-project.org
Description: Das Antragssystem „cvtx“ stellt zahlreiche Hilfsmittel zur Verfügung, um Tagesordnungen, Anträge, Änderungsanträge und Antragsreader auf politischen Kongressen oder Mitgliederversammlungen zu verwalten. Es basiert auf dem Textsatzsystem LaTeX und ist verfügbar als Open Source.
Author: Alexander Fecke & Max Löffler
Version: 0.1
Author URI: http://alexander-fecke.de
License: GPLv2 or later
*/


define('CVTX_VERSION', '0.1');
define('CVTX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CVTX_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once(CVTX_PLUGIN_DIR.'/cvtx_admin.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_latex.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_widgets.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_theme.php');


// define post types
$cvtx_types = array('cvtx_reader'   => array(),
                    'cvtx_top'      => array('cvtx_top_ord',
                                             'cvtx_sort',
                                             'cvtx_top_short'),
                    'cvtx_antrag'   => array('cvtx_antrag_ord',
                                             'cvtx_sort',
                                             'cvtx_antrag_top',
                                             'cvtx_antrag_steller',
                                             'cvtx_antrag_steller_short',
                                             'cvtx_antrag_email',
                                             'cvtx_antrag_phone',
                                             'cvtx_antrag_grund',
                                             'cvtx_antrag_info'),
                    'cvtx_aeantrag' => array('cvtx_aeantrag_zeile',
                                             'cvtx_sort',
                                             'cvtx_aeantrag_antrag',
                                             'cvtx_aeantrag_steller',
                                             'cvtx_aeantrag_steller_short',
                                             'cvtx_aeantrag_email',
                                             'cvtx_aeantrag_phone',
                                             'cvtx_aeantrag_grund',
                                             'cvtx_aeantrag_verfahren',
                                             'cvtx_aeantrag_detail',
                                             'cvtx_aeantrag_info'));


add_action('init', 'cvtx_init');
/**
 * Create custom post types
 */
function cvtx_init() {
    // load language files
    load_plugin_textdomain('cvtx', false, dirname(plugin_basename(__FILE__)).'/languages/');

    // Reader
    register_post_type('cvtx_reader',
        array('labels'             => array(
              'name'               => __('Reader', 'cvtx'),
              'singular_name'      => __('Reader', 'cvtx'),
              'add_new_item'       => __('Reader erstellen', 'cvtx'),
              'new_item'           => __('Neuer Reader', 'cvtx'),
              'edit_item'          => __('Reader bearbeiten', 'cvtx'),
              'view_item'          => __('Reader ansehen', 'cvtx'),
              'search_items'       => __('Reader suchen', 'cvtx'),
              'not_found'          => __('Keine Reader gefunden', 'cvtx'),
              'not_found_in_trash' => __('Keine Reader im Papierkorb gefunden', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_reader_small.png',
        'rewrite'     => array('slug' => 'reader'),
        'supports'    => array('title'),
        )
    );

    // Tagesordnungspunkte
    register_post_type('cvtx_top',
        array('labels'             => array(
              'name'               => __('TOPs', 'cvtx'),
              'singular_name'      => __('TOP', 'cvtx'),
              'add_new_item'       => __('TOP erstellen', 'cvtx'),
              'edit_item'          => __('TOP bearbeiten', 'cvtx'),
              'view_item'          => __('TOP ansehen', 'cvtx'),
              'new_item'           => __('Neuer TOP', 'cvtx'),
              'search_items'       => __('TOPs suchen', 'cvtx'),
              'not_found'          => __('Keine TOPs gefunden', 'cvtx'),
              'not_found_in_trash' => __('Keine TOPs im Papierkorb gefunden', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_top_small.png',
        'rewrite'     => array('slug' => 'top'),
        'supports'    => array('title', 'editor'),
        )
    );

    // Anträge
    register_post_type('cvtx_antrag',
        array('labels'             => array(
              'name'               => __('Anträge', 'cvtx'),
              'singular_name'      => __('Antrag', 'cvtx'),
              'add_new_item'       => __('Antrag erstellen', 'cvtx'),
              'edit_item'          => __('Antrag bearbeiten', 'cvtx'),
              'view_item'          => __('Antrag ansehen', 'cvtx'),
              'new_item'           => __('Neuer Antrag', 'cvtx'),
              'search_items'       => __('Anträge suchen', 'cvtx'),
              'not_found'          => __('Keine Anträge gefunden', 'cvtx'),
              'not_found_in_trash' => __('Keine Anträge im Papierkorb gefunden', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_antrag_small.png',
        'rewrite'     => array('slug' => 'antrag'),
        'supports'    => array('title', 'editor'),
        )
    );

    // Änderungsanträge
    register_post_type('cvtx_aeantrag',
        array('labels'             => array(
              'name'               => __('Änderungsanträge', 'cvtx'),
              'singular_name'      => __('Änderungsantrag', 'cvtx'),
              'add_new_item'       => __('Änderungsantrag erstellen', 'cvtx'),
              'edit_item'          => __('Änderungsantrag bearbeiten', 'cvtx'),
              'view_item'          => __('Änderungsantrag ansehen', 'cvtx'),
              'menu_name'          => __('Ä-Anträge', 'cvtx'),
              'new_item'           => __('Neuer Änderungsantrag', 'cvtx'),
              'search_items'       => __('Änderungsanträge suchen', 'cvtx'),
              'not_found'          => __('Keine Änderungsanträge gefunden', 'cvtx'),
              'not_found_in_trash' => __('Keine Änderungsanträge im Papierkorb gefunden', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_aeantrag_small.png',
        'rewrite'     => array('slug' => 'aeantrag'),
        'supports'    => array('editor'),
        )
    );
    
    // Register Taxonomy
    register_taxonomy('cvtx_tax_reader', 'cvtx_antrag',
                      array('hierarchical' => true,
                            'label'        => 'Reader',
                            'show_ui'      => false,
                            'query_var'    => true,
                            'rewrite'      => false));
}


if (is_admin()) add_action('delete_post', 'cvtx_delete_post');
/**
 * Removes all latex files if antrag or aeantrag is deleted.
 *
 * @todo drop cvtx_aeantraege when cvtx_antrag deleted? drop cvtx_antrag when cvtx_top deleted?
 */
function cvtx_delete_post($post_id) {
    global $post;
    
    if (is_object($post)) {
        if ($post->post_type == 'cvtx_reader') {
            wp_delete_term('cvtx_reader_'.$post->ID, 'cvtx_tax_reader');
        } else if ($post->post_type == 'cvtx_top') {
            $query = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                    'value'   => $post->ID,
                                                                    'compare' => '='))));
        } else if ($post->post_type == 'cvtx_antrag') {
            $query = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                    'value'   => $post->ID,
                                                                    'compare' => '='))));
            cvtx_remove_files($post);
        } else if ($post->post_type == 'cvtx_aeantrag') {
            cvtx_remove_files($post);
        }
        
        if (isset($query) && $query != null && $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post(get_the_ID(), true);
            }
        }
    }
}


if (is_admin()) add_action('wp_trash_post', 'cvtx_trash_post');
/**
 * Moves all child data to the trash.
 */
function cvtx_trash_post($post_id) {
    global $post;

    if (is_object($post)) {
        if ($post->post_type == 'cvtx_top') {
            $query = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                    'value'   => $post->ID,
                                                                    'compare' => '='))));
        } else if ($post->post_type == 'cvtx_antrag') {
            $query = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                    'value'   => $post->ID,
                                                                    'compare' => '='))));
        }
        
        if (isset($query) && $query != null && $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_trash_post(get_the_ID());
            }
        }
    }
}


/**
 * Returns a globally sortable string
 */
function cvtx_get_sort($post_type, $top=false, $antrag=false, $zeile=false, $vari=false) {
    $sorts           = array();
    $sorts['top']    = ($top    !== false ? (intval($top)    ? sprintf('%1$05d', intval($top))    : 'ZZZZZ' ) : 'AAAAA' );
    $sorts['antrag'] = ($antrag !== false ? (intval($antrag) ? sprintf('%1$05d', intval($antrag)) : 'ZZZZZ' ) : 'AAAAA' );
    $sorts['zeile']  = ($zeile  !== false ? (intval($zeile)  ? sprintf('%1$06d', intval($zeile))  : 'ZZZZZZ') : 'AAAAAA');
    $sorts['vari']   = ($vari   !== false ? (intval($vari)   ? sprintf('%1$06d', intval($vari))   : 'ZZZZZZ') : 'AAAAAA');

    foreach ($sorts as $key => $value) {
        if (intval($value) > 0) {
            $code = '';
            for ($i = 0; $i < strlen($value); $i++) {
                $code .= chr(intval(substr($value, $i, 1)) + 65);
            }
        } else {
            $code = $value;
        }
        $sorts[$key] = $code;
    }
    
    return implode($sorts);
}


add_action('wp_insert_post', 'cvtx_insert_post', 10, 2);
function cvtx_insert_post($post_id, $post = null) {
    global $cvtx_types;

    if (in_array($post->post_type, array_keys($cvtx_types))) {
        // Add globally sortable field
        if ($post->post_type == 'cvtx_top') {
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_top', get_post_meta($post_id, 'cvtx_top_ord', true));
        } else if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_top'])) {
            $top_ord    = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true);
            $antrag_ord = (isset($_POST['cvtx_antrag_ord']) ? $_POST['cvtx_antrag_ord'] : 0);

            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_antrag', $top_ord, $antrag_ord);
        } else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
            $top_id     = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
            $top_ord    = get_post_meta($top_id, 'cvtx_top_ord', true);
            $antrag_ord = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true);
            
            // fetch main info on line (first few numbers)
            if (preg_match('/^([0-9]+)/', $_POST['cvtx_aeantrag_zeile'], $match1)) {
                $aeantrag_zeile = $match1[1];
            } else $aeantrag_zeile = 0;
            // look for vari-ending (some kind of -2 at the end of the line field)
            if (preg_match('/([0-9]+)$/', $_POST['cvtx_aeantrag_zeile'], $match2)
             && strlen($match2[1]) < strlen($_POST['cvtx_aeantrag_zeile'])) {
                $aeantrag_vari = $match2[1];
            } else $aeantrag_vari = false;
            
            // get sort code for aeantrag
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_aeantrag', $top_ord, $antrag_ord, $aeantrag_zeile, $aeantrag_vari);
        }
                
        // Generate short antragsteller if field is empty
        if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_steller']) && !empty($_POST['cvtx_antrag_steller'])
         && (!isset($_POST['cvtx_antrag_steller_short']) || empty($_POST['cvtx_antrag_steller_short']))) {
            $parts = preg_split('/[,;\(\n]+/', $_POST['cvtx_antrag_steller'], 2);
            if (count($parts) == 2) $_POST['cvtx_antrag_steller_short'] = trim($parts[0]).' u.a.';
            else                    $_POST['cvtx_antrag_steller_short'] = $_POST['cvtx_antrag_steller'];
        } else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_steller']) && !empty($_POST['cvtx_aeantrag_steller'])
                && (!isset($_POST['cvtx_aeantrag_steller_short']) || empty($_POST['cvtx_aeantrag_steller_short']))) {
            $parts = preg_split('/[,;\(\n]+/', $_POST['cvtx_aeantrag_steller'], 2);
            if (count($parts) == 2) $_POST['cvtx_aeantrag_steller_short'] = trim($parts[0]).' u.a.';
            else                    $_POST['cvtx_aeantrag_steller_short'] = $_POST['cvtx_aeantrag_steller'];
        }
        
        // update reader taxonomy
        if ($post->post_type == 'cvtx_reader') {
            // Add term if new reader is created
            if (!term_exists('cvtx_reader_'.$post_id, 'cvtx_tax_reader')) {
                wp_insert_term('cvtx_reader_'.$post_id, 'cvtx_tax_reader');
            }
            
            // get all previously selected posts for this reader term
            $old   = array();
            $query = new WP_Query(array('taxonomy' => 'cvtx_tax_reader',
                                        'term'     => 'cvtx_reader_'.intval($post_id),
                                        'orderby'  => 'meta_value',
                                        'meta_key' => 'cvtx_sort',
                                        'order'    => 'ASC',
                                        'nopaging' => true));
            while ($query->have_posts()) {
                $query->the_post();
                $old[] = get_the_ID();
            }
            
            // get all selected posts for this reader term
            $new = array();
            if (isset($_POST['cvtx_post_ids']) && is_array($_POST['cvtx_post_ids'])) {
                $new = array_keys($_POST['cvtx_post_ids']);
            }
            
            // fetch terms by object and copy all - except this reader!
            $terms = array();
            foreach (array_unique(array_merge($old, $new)) as $item) {
                $terms["$item"] = array();
                foreach (wp_get_object_terms($item, 'cvtx_tax_reader') as $term) {
                    if ($term->name != 'cvtx_reader_'.intval($post_id)) $terms["$item"][] = $term->name;
                }
            }
            
            // update object terms
            foreach ($old as $item) {
                if (!in_array($item, $new)) {
                    wp_set_object_terms($item, $terms["$item"], 'cvtx_tax_reader');
                }
            }
            
            // add this reader to terms list and update object terms
            foreach ($new as $item) {
                $terms["$item"][] = 'cvtx_reader_'.intval($post_id);
                if (!in_array($item, $old)) {
                    wp_set_object_terms($item, $terms["$item"], 'cvtx_tax_reader');
                }
            }
        }
        
        // add default reader terms to antrag or aeantrag
        if ($post->post_type == 'cvtx_antrag') {
            $items = array();
            $terms = explode(', ', get_option('cvtx_default_reader_antrag'));
            foreach ($terms as $term) {
                if (term_exists($term, 'cvtx_tax_reader')) $items[] = $term;
            }
            if (count($items) > 0) {
                wp_set_object_terms($post->ID, $items, 'cvtx_tax_reader');
            }
        } else if ($post->post_type == 'cvtx_aeantrag') {
            $items = array();
            $terms = explode(', ', get_option('cvtx_default_reader_aeantrag'));
            foreach ($terms as $term) {
                if (term_exists($term, 'cvtx_tax_reader')) $items[] = $term;
            }
            if (count($items) > 0) {
                wp_set_object_terms($post->ID, $items, 'cvtx_tax_reader');
            }
        }
            
        // Loop through the POST data
        foreach ($cvtx_types[$post->post_type] as $key) {
            // save data
            $value = @$_POST[$key];
            if (empty($value)) {
                delete_post_meta($post_id, $key);
                continue;
            }

            // If value is a string it should be unique
            if (!is_array($value)) {
                // Update meta
                if (!update_post_meta($post_id, $key, $value)) {
                    // Or add the meta data
                    add_post_meta($post_id, $key, $value);
                }
            } else {
                // If passed along is an array, we should remove all previous data
                delete_post_meta($post_id, $key);
                
                // Loop through the array adding new values to the post meta as different entries with the same name
                foreach ($value as $entry)
                    add_post_meta($post_id, $key, $entry);
            }
        }
        
        // create pdf
        if (is_admin()) cvtx_create_pdf($post_id, $post);
        // send mails if antrag created
        else {
            $headers = array('From: '.get_option('cvtx_send_from_email', get_bloginfo('admin_email'))."\r\n",
                             (get_option('cvtx_send_html_mail') ? "Content-Type: text/html\r\n" : ''));
            
            // post type antrag created
            if ($post->post_type == 'cvtx_antrag') {
                $mails['owner'] = array('subject' => get_option('cvtx_send_create_antrag_owner_subject'),
                                        'body'    => get_option('cvtx_send_create_antrag_owner_body'));
                $mails['admin'] = array('subject' => get_option('cvtx_send_create_antrag_admin_subject'),
                                        'body'    => get_option('cvtx_send_create_antrag_admin_body'));
                
                $fields = array('%top_kuerzel%'        => 'TOP '.get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true),
                                '%top%'                => get_the_title($_POST['cvtx_antrag_top']),
                                '%titel%'              => $post->post_title,
                                '%antragsteller%'      => $_POST['cvtx_antrag_steller'],
                                '%antragsteller_kurz%' => $_POST['cvtx_antrag_steller_short'],
                                '%antragstext%'        => $post->post_content,
                                '%begruendung%'        => $_POST['cvtx_antrag_grund']);
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        if($part=='body' && get_option('cvtx_send_html_mail',true) == true) {
                            $tpl = get_template_directory().'/mail.php';
                            if(is_file($tpl)) {
                                $content = nl2br(strtr($content, $fields));
                                ob_start();
                                require($tpl);
                                $out = ob_get_contents();
                                ob_end_clean();
	                            $mails[$rcpt][$part] = $out;
                            }
                            else $mails[$rcpt][$part] = strtr($content, $fields);
                        }
                        else
                            $mails[$rcpt][$part] = strtr($content, $fields);
                    }
                }
                
                // send mail(s) if option enabled
                if (get_option('cvtx_send_create_antrag_owner')) {
                    wp_mail($_POST['cvtx_antrag_email'],
                            $mails['owner']['subject'],
                            $mails['owner']['body'],
                            implode("\r\n", $headers) . "\r\n");
                }
                if (get_option('cvtx_send_create_antrag_admin')) {
                    wp_mail(get_option('cvtx_send_rcpt_email', get_bloginfo('admin_email')),
                            $mails['admin']['subject'],
                            $mails['admin']['body'],
                            implode("\r\n", $headers));
                }
            }
            // post type aeantrag created
            else if ($post->post_type == 'cvtx_aeantrag') {
                $mails['owner'] = array('subject' => get_option('cvtx_send_create_aeantrag_owner_subject'),
                                        'body'    => get_option('cvtx_send_create_aeantrag_owner_body'));
                $mails['admin'] = array('subject' => get_option('cvtx_send_create_aeantrag_admin_subject'),
                                        'body'    => get_option('cvtx_send_create_aeantrag_admin_body'));
                
                $top_id = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
                $fields = array('%top_kuerzel%'        => 'TOP '.get_post_meta($top_id, 'cvtx_top_ord', true),
                                '%top%'                => get_the_title($top_id),
                                '%antrag_kuerzel%'     => get_post_meta($top_id, 'cvtx_top_short', true).'-'
                                                         .get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true),
                                '%antrag%'             => get_the_title($_POST['cvtx_aeantrag_antrag']),
                                '%zeile%'              => $_POST['cvtx_aeantrag_zeile'],
                                '%antragsteller%'      => $_POST['cvtx_aeantrag_steller'],
                                '%antragsteller_kurz%' => $_POST['cvtx_aeantrag_steller_short'],
                                '%antragstext%'        => $post->post_content,
                                '%begruendung%'        => $_POST['cvtx_aeantrag_grund']);
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        $mails[$rcpt][$part] = strtr($content, $fields);
                    }
                }
                
                // send mail(s) if option enabled
                if (get_option('cvtx_send_create_aeantrag_owner')) {
                    wp_mail($_POST['cvtx_aeantrag_email'],
                            $mails['owner']['subject'],
                            $mails['owner']['body'],
                            $headers);
                }
                if (get_option('cvtx_send_create_aeantrag_admin')) {
                    wp_mail(get_option('cvtx_send_rcpt_email'),
                            $mails['admin']['subject'],
                            $mails['admin']['body'],
                            $headers);
                }
            }
        }
        
    }
}


/**
 * Erstellt ein PDF aus gespeicherten Anträgen
 *
 * @param int $post_id Post-ID
 * @param object $post the post
 */
function cvtx_create_pdf($post_id, $post = null) {
    $pdflatex = get_option('cvtx_pdflatex_cmd');
    
    if (isset($post) && is_object($post) && !empty($pdflatex)) {
        $out_dir = wp_upload_dir();
        $tpl_dir = get_template_directory().'/'.get_option('cvtx_latex_tpldir');
    
        // prepare antrag
        if ($post->post_type == 'cvtx_antrag') {
            // file
            $file = $out_dir['basedir'].'/';
            if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
                $file .= cvtx_sanitize_file_name($short.'_'.$post->post_title);
            } else {
                $file .= $post->ID;
            }
            
            // use special theme template for id=x if exists
            if (is_file($tpl_dir.'/single-cvtx_antrag-'.$post_id.'.php')) {
                $tpl = $tpl_dir.'/single-cvtx_antrag-'.$post_id.'.php';
            }
            // use theme template
            else if(is_file($tpl_dir.'/single-cvtx_antrag.php')) {
                $tpl = $tpl_dir.'/single-cvtx_antrag.php';
            }
            // use default template
            else if(is_file(CVTX_PLUGIN_DIR.'/latex/single-cvtx_antrag.php')) {
                $tpl = CVTX_PLUGIN_DIR.'/latex/single-cvtx_antrag.php';
            }
        }
        // prepare Ä-Antrag if pdf-option enabled
        else if ($post->post_type == 'cvtx_aeantrag' && get_option('cvtx_aeantrag_pdf')) {
            // file
            $file = $out_dir['basedir'].'/';
            if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
                $file .= cvtx_sanitize_file_name($short);
            } else {
                $file .= $post->ID;
            }

            // use special theme template for id=x if exists
            if (is_file($tpl_dir.'/single-cvtx_aeantrag-'.$post_id.'.php')) {
                $tpl = $tpl_dir.'/single-cvtx_aeantrag-'.$post_id.'.php';
            }
            // use theme template
            else if(is_file($tpl_dir.'/single-cvtx_aeantrag.php')) {
                $tpl = $tpl_dir.'/single-cvtx_aeantrag.php';
            }
            // use default template
            else if(is_file(CVTX_PLUGIN_DIR.'/latex/single-cvtx_aeantrag.php')) {
                $tpl = CVTX_PLUGIN_DIR.'/latex/single-cvtx_aeantrag.php';
            }
        }
        // prepare Reader
        else if ($post->post_type == 'cvtx_reader') {
            // file
            $file = $out_dir['basedir'].'/';
            if ($post->post_status == 'publish') {
                $file .= cvtx_sanitize_file_name($post->post_title);
            } else {
                $file .= $post->ID;
            }
            
            // use special theme template for id=x if exists
            if (is_file($tpl_dir.'/single-cvtx_reader-'.$post_id.'.php')) {
                $tpl = $tpl_dir.'/single-cvtx_reader-'.$post_id.'.php';
            }
            // use theme template
            else if(is_file($tpl_dir.'/single-cvtx_reader.php')) {
                $tpl = $tpl_dir.'/single-cvtx_reader.php';
            }
            // use default template
            else if(is_file(CVTX_PLUGIN_DIR.'/latex/single-cvtx_reader.php')) {
                $tpl = CVTX_PLUGIN_DIR.'/latex/single-cvtx_reader.php';
            }
        }

        // create pdf if template found
        if (isset($tpl) && !empty($tpl) && isset($file) && !empty($file)) {
            // drop old files by name/id and ending
            $filelist = array($out_dir['basedir'].'/'.$post->ID);
            if ($post->post_status == 'publish') $filelist[] = $file;
            foreach ($filelist as $oldfile) {
                foreach (array('pdf', 'log', 'tex') as $ending) {
                    if (is_file($oldfile.'.'.$ending)) unlink($oldfile.'.'.$ending);
                }
            }
            
            // start buffering
            ob_start();
            $post_bak = $post;
            // run latex template, caputure output
            require($tpl);
            $out = ob_get_contents();
            // cleanup
            $post = $post_bak;
            wp_reset_postdata();
            ob_end_clean();

            // save output to latex file. success?
            if (file_put_contents($file.'.tex', $out) !== false) {
                $cmd = $pdflatex.' -interaction=nonstopmode -output-directory='.$out_dir['basedir'].' '.$file.'.tex';
                
                // run pdflatex
                exec($cmd);
                // if reader is generated: run it twice to build toc etc.
                if ($post->post_type == 'cvtx_reader') {
                    exec($cmd);
                }
                
                // remove .aux-file
                $endings = array('aux', 'toc', 'bbl', 'blg', 'synctex.gz');
                // remove .log-file
                if ((get_option('cvtx_drop_logfile') == 2 && is_file($file.'.pdf'))
                  || get_option('cvtx_drop_logfile') == 1) {
                    $endings[] = 'log';
                }
                // remove .tex-file
                if ((get_option('cvtx_drop_texfile') == 2 && is_file($file.'.pdf'))
                  || get_option('cvtx_drop_texfile') == 1) {
                    $endings[] = 'tex';
                }
                // remove files (if they exist)
                cvtx_remove_files($post, $endings);
            }
        }
    }
}


add_filter('the_title', 'cvtx_the_title', 1, 2);
/**
 * replaces filter "the title" in order to generate custom titles for post-types "top", "antrag" and "aeantrag"
 */
function cvtx_the_title($before='', $after='') {
    if(is_numeric($after)) $post = &get_post($after);
    
    if(isset($post)) {
        $title = (!empty($post->post_title) ? $post->post_title : __('(no title)'));
        
        if ($short = cvtx_get_short($post)) {
            // Antrag
            if($post->post_type == 'cvtx_antrag') {
                $title = $short.' '.$title;
            }
            // Änderungsantrag
            else if($post->post_type == 'cvtx_aeantrag') {
                $title = $short;
            }
            // Tagesordnungspunkt
            else if($post->post_type == 'cvtx_top') {
                $title = $short.': '.$title;
            }
        }
        else {
            return (!empty($before) ? $before : __('(no title)'));
        }
    }    
    return $title;
}


/**
 * Returns the formatted short title for post $post. If post_type not equal
 * to top/antrag/aeantrag, function will return false.
 *
 * @param $post the post
 */
function cvtx_get_short($post) {
    // post type top
    if ($post->post_type == 'cvtx_top') {
        $top = get_post_meta($post->ID, 'cvtx_top_ord', true);

        if (!empty($top)) return 'TOP '.$top;
    }
    // post type antrag
    else if ($post->post_type == 'cvtx_antrag') {
        $top    = get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
        $antrag = get_post_meta($post->ID, 'cvtx_antrag_ord', true);

        // format and return aeantrag_short
        $format = strtr(get_option('cvtx_antrag_format'), array('%top%' => $top, '%antrag%' => $antrag));

        if (!empty($top) && !empty($antrag)) return $format;
    }
    // post type antrag
    else if ($post->post_type == 'cvtx_aeantrag') {
        // fetch antrag_id, antag, top and zeile
        $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
        $antrag_nr = get_post_meta($antrag_id, 'cvtx_antrag_ord', true);
        $top_nr    = get_post_meta(get_post_meta($antrag_id, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
        $zeile     = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
        
        // format and return aeantrag_short
        $antrag = strtr(get_option('cvtx_antrag_format'), array('%top%' => $top_nr, '%antrag%' => $antrag_nr));
        $format = strtr(get_option('cvtx_aeantrag_format'), array('%antrag%' => $antrag, '%zeile%' => $zeile));
        
        if (!empty($top_nr) && !empty($antrag_nr) && !empty($zeile)) return $format;
    }

    // default
    return false;
}


/**
 * Returns file path by post and ending if file exists.
 * 
 * @param $post the post
 * @param $ending pdf/tex/log
 * @param $base dir/url
 */
function cvtx_get_file($post, $ending = 'pdf', $base = 'url') {
    $dir  = wp_upload_dir();
    $base = 'base'.($base == 'dir' ? $base : 'url');

    // specify filename
    if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
        if ($post->post_type == 'cvtx_antrag') {
            $file = cvtx_sanitize_file_name($short.'_'.$post->post_title);
        } else if ($post->post_type == 'cvtx_aeantrag') {
            $file = cvtx_sanitize_file_name($short);
        }
    } else if ($post->post_status == 'publish' && $post->post_type == 'cvtx_reader') {
        $file = cvtx_sanitize_file_name($post->post_title);
    } else {
        $file = $post->ID;
    }
    
    // return filename if file exists
    if (is_file($dir['basedir'].'/'.$file.'.'.$ending)) {
        return $dir[$base].'/'.$file.'.'.$ending;
    }
    
    return false;
}


/**
 * Remove files
 *
 * @param object $post the post
 * @param array $endings array of endings, default * removes all LaTeX files
 */
function cvtx_remove_files($post, $endings = array('*')) {
    if (in_array('*', $endings)) {
        $endings = array('pdf', 'log', 'tex', 'aux', 'toc',
                         'bbl', 'blg', 'synctex.gz');
    }

    foreach ($endings as $ending) {
        if ($file = cvtx_get_file($post, $ending, 'dir')) unlink($file);
    }
}


/**
 * Returns a well-sanitized copy of string $str
 */
function cvtx_sanitize_file_name($str) {
    $replacements = array('Ä' => 'Ae', 'ä' => 'ae',
                          'Á' => 'A',  'á' => 'a',
                          'À' => 'A',  'à' => 'a',
                          'Â' => 'A',  'â' => 'a',
                          'Æ' => 'Ae', 'æ' => 'ae',
                          'Ã' => 'A',  'ã' => 'a',
                          'Å' => 'Aa', 'å' => 'aa',
                          'Ć' => 'C',  'ć' => 'c',
                          'Ç' => 'C',  'ç' => 'c',
                          'É' => 'E',  'é' => 'e',
                          'È' => 'E',  'è' => 'e',
                          'Ê' => 'E',  'ê' => 'e',
                          'Ë' => 'E',  'ë' => 'e',
                          'Ñ' => 'N',  'ñ' => 'n',
                          'Ó' => 'O',  'ó' => 'o',
                          'Ò' => 'O',  'ò' => 'o',
                          'Ô' => 'O',  'ô' => 'o',
                          'Õ' => 'O',  'õ' => 'o',
                          'Ø' => 'O',  'ø' => 'o',
                          'Ö' => 'Oe', 'ö' => 'oe',
                          'Œ' => 'Oe', 'œ' => 'oe',
                          'ß' => 'ss',                          
                          'Ú' => 'U',  'ú' => 'u',
                          'Ù' => 'U',  'ù' => 'u',
                          'Û' => 'U',  'û' => 'u',
                          'Ü' => 'Ue', 'ü' => 'ue');
    $str = strtr($str, $replacements);
    return sanitize_key(sanitize_file_name($str));
}


add_action('wp_ajax_cvtx_get_top_short', 'cvtx_ajax_get_top_short');
/**
 * 
 */
function cvtx_ajax_get_top_short() {
    echo get_post_meta($_REQUEST['post_id'], 'cvtx_top_short', true);
    exit();
}


add_action('wp_ajax_cvtx_validate', 'cvtx_ajax_validate');
/**
 * 
 */
function cvtx_ajax_validate() {
    global $cvtx_types;

    if (isset($_REQUEST['post_type']) && in_array($_REQUEST['post_type'], array_keys($cvtx_types))
     && isset($_REQUEST['args'])      && is_array($_REQUEST['args']) && count($_REQUEST['args']) > 0
     && isset($_REQUEST['post_id'])   && is_array($_REQUEST['post_id'])) {
        $param = array('post_type'    => $_REQUEST['post_type'],
                       'post__not_in' => $_REQUEST['post_id'],
                       'meta_query'   => $_REQUEST['args'],
                       'nopaging'     => true);
        
        $aquery = new WP_Query($param);
        if ($aquery->have_posts()) {
            echo "-ERR";
        } else {
            echo "+OK";
        }
    }

    exit();
}


/**
 * Returns an array of taxonomy terms that are connected to a reader item
 */
function cvtx_get_reader() {
    // get terms
    $terms = array();
    foreach (get_terms('cvtx_tax_reader', array('hide_empty' => false)) as $term) {
        $terms[substr($term->name, 12)] = $term->name;
    }

    $reader = array();
    // get reader
    $query = new WP_Query(array('post_type' => 'cvtx_reader',
                                'nopaging'  => true));
    while ($query->have_posts()) {
        $query->the_post();
        if (isset($terms[get_the_ID()]) && !empty($terms[get_the_ID()])) {
            $reader[] = array('title' => get_the_title(),
                              'term'  => $terms[get_the_ID()]);
        }
    }

    return $reader;
}


/**
 * Print dropdown menu of all tops
 *
 * @param $selected post_id of selected top, otherwise null
 * @param $message message that will be displayed if no top exists
 */
function cvtx_dropdown_tops($selected = null, $message = '') {
    global $post;
    $post_bak = $post;
    $output = '';

    $tquery = new WP_Query(array('post_type' => 'cvtx_top',
                                 'orderby'   => 'meta_value',
                                 'meta_key'  => 'cvtx_sort',
                                 'order'     => 'ASC',
                                 'nopaging'  => true));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_antrag_top" id="cvtx_antrag_top_select">';
        while ($tquery->have_posts()) {
            $tquery->the_post();
            $output .= '<option value="'.get_the_ID().'"'.(get_the_ID() == $selected ? ' selected="selected"' : '').'>';
            $output .= get_the_title();
            $output .= '</option>';
        }
        $output .= '</select>';
    }
    // return info message if no top exists
    else {
        return $message;
    }
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
    return $output;
}


/**
 * Print dropdown menu of all anträge grouped by tops
 *
 * @param $selected post_id of selected antrag, otherwise null
 * @param $message message that will be displayed if no top exists
 */
function cvtx_dropdown_antraege($selected = null, $message = '') {
    global $post;
    $post_bak = $post;
    $output = '';

    // Tagesordnungspunkte auflisten
    $tquery = new WP_Query(array('post_type' => 'cvtx_top',
                                 'orderby'   => 'meta_value',
                                 'meta_key'  => 'cvtx_sort',
                                 'order'     => 'ASC',
                                 'nopaging'  => true));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_aeantrag_antrag" id="cvtx_aeantrag_antrag_select">';
        while ($tquery->have_posts()) {
            $tquery->the_post();
            // optgroup for top
            $output .= '<optgroup label="'.get_the_title().'">';
            
            // list anträge in top
            $aquery = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                         'orderby'    => 'meta_value',
                                         'meta_key'   => 'cvtx_sort',
                                         'order'      => 'ASC',
                                         'nopaging'   => true,
                                         'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                     'value'   => get_the_ID(),
                                                                     'compare' => '='))));
            if ($aquery->have_posts()) {
                while ($aquery->have_posts()) {
                    $aquery->the_post();
                    $output .= '<option value="'.get_the_ID().'"'.(get_the_ID() == $selected ? ' selected="selected"' : '').'>';
                    $output .= get_the_title();
                    $output .= '</option>';
                }
            }
            
            $output .= '</optgroup>';
        }
        $output .= '</select>';
    }
    // print info message if no top exists
    else {
        return $message;
    }
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
    return $output;
}


/**
 * Method which evaluates input of antrags-creation form and saves it to the wordpress database
 */
function cvtx_submit_antrag() {
    // Request Variables, if already submitted, set corresponding variables to '' else
    $cvtx_antrag_title   = (!empty($_POST['cvtx_antrag_title'])   ? trim($_POST['cvtx_antrag_title'])   : '');
    $cvtx_antrag_steller = (!empty($_POST['cvtx_antrag_steller']) ? trim($_POST['cvtx_antrag_steller']) : '');
    $cvtx_antrag_email   = (!empty($_POST['cvtx_antrag_email'])   ? trim($_POST['cvtx_antrag_email'])   : '');
    $cvtx_antrag_phone   = (!empty($_POST['cvtx_antrag_phone'])   ? trim($_POST['cvtx_antrag_phone'])   : '');
    $cvtx_antrag_top     = (!empty($_POST['cvtx_antrag_top'])     ? trim($_POST['cvtx_antrag_top'])     : '');
    $cvtx_antrag_text    = (!empty($_POST['cvtx_antrag_text'])    ? trim($_POST['cvtx_antrag_text'])    : '');
    $cvtx_antrag_grund   = (!empty($_POST['cvtx_antrag_grund'])   ? trim($_POST['cvtx_antrag_grund'])   : '');

    // Check whether the form has been submitted and the wp_nonce for security reasons
    if (isset($_POST['cvtx_form_create_antrag_submitted'] ) && wp_verify_nonce($_POST['cvtx_form_create_antrag_submitted'], 'cvtx_form_create_antrag') ){
          
        $recaptcha  = get_option('cvtx_use_recaptcha');
        $privatekey = get_option('cvtx_recaptcha_privatekey');
          
          if($recaptcha && !empty($privatekey)) {
            require_once(WP_PLUGIN_DIR.'/cvtx/reCaptcha/recaptchalib.php');
            $resp = recaptcha_check_answer($privatekey,
                                           $_SERVER['REMOTE_ADDR'],
                                           $_POST['recaptcha_challenge_field'],
                                           $_POST['recaptcha_response_field']);
            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                echo('<p id="message" class="error">'.__('Der Captcha wurde falsch eingegeben. Bitte versuche es erneut.', 'cvtx').'</p>');
            }
        }
        if(!$recaptcha || $resp->is_valid) {
            // check whether the required fields have been submitted
             if(!empty($cvtx_antrag_title) && !empty($cvtx_antrag_text) && !empty($cvtx_antrag_steller) && !empty($cvtx_antrag_email) && !empty($cvtx_antrag_phone)) {
                 // create an array which holds all data about the antrag
                $antrag_data = array(
                    'post_title'          => $cvtx_antrag_title,
                    'post_content'        => $cvtx_antrag_text,
                    'cvtx_antrag_steller' => $cvtx_antrag_steller,
                    'cvtx_antrag_email'   => $cvtx_antrag_email,
                    'cvtx_antrag_phone'   => $cvtx_antrag_phone,
                    'cvtx_antrag_top'     => $cvtx_antrag_top,
                    'cvtx_antrag_grund'   => $cvtx_antrag_grund,
                    'post_status'         => 'pending',
                    'post_author'         => get_option('cvtx_anon_user'),
                    'post_type'           => 'cvtx_antrag');
                // submit the post
                if($antrag_id = wp_insert_post($antrag_data)) {
                    echo '<p id="message" class="success">'.__('Der Antrag wurde erstellt und muss noch freigeschaltet werden.', 'cvtx').'</p>';
                    $erstellt = true;
                }
                else {
                    echo '<p id="message" class="error">'.__('Antrag wurde NICHT gespeichert. Warum auch immer.', 'cvtx').'</p>';
                }
            }
            // return error-message because some required fields have not been submitted
            else {
                echo '<p id="message" class="error">Der Antrag konnte nicht gespeichert werden, weil einige benötigte Felder '. 
                     '(mit einem <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*<'.
                     '/span> bezeichnet) nicht ausgefüllt wurden.</p>';
            }
        }
    }
    
    // nothing has been submitted yet -> include creation form
    if(!isset($erstellt))
        echo cvtx_create_antrag_form($cvtx_antrag_top, $cvtx_antrag_title, $cvtx_antrag_text, $cvtx_antrag_steller,
                                     $cvtx_antrag_email, $cvtx_antrag_phone, $cvtx_antrag_grund);
}

/**
 * Creates formular for creating antraege
 *
 * @param int $cvtx_antrag_top top of antrag if it has already been submitted
 * @param string $cvtx_antrag_title title if it has been already submitted
 * @param string $cvtx_antrag_text text of antrag if it has already been submitted
 * @param string $cvtx_antrag_steller antrag_steller if it have been already submitted
 * @param string $cvtx_antrag_email contact address if it has already been submitted
 * @param string $cvtx_antrag_phone phone number if it has already been submitted
 * @param string $cvtx_antrag_grund antragsbegruendung, if already submitted
 */
function cvtx_create_antrag_form($cvtx_antrag_top   = 0,  $cvtx_antrag_title = '', $cvtx_antrag_text  = '', $cvtx_antrag_steller = '',
                                 $cvtx_antrag_email = '', $cvtx_antrag_phone = '', $cvtx_antrag_grund = '') {
    $output  = '';
    
    // specify form
    $output .= '<form id="create_antrag_form" class="cvtx_antrag_form" method="post" action="">';
    
    // Wp-nonce for security reasons
    $output .= wp_nonce_field('cvtx_form_create_antrag','cvtx_form_create_antrag_submitted');
    
    // Antragstitel
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_title">'.__('Antragstitel', 'cvtx').': <span class="form-required"'
              .' title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<input type="text" id="cvtx_antrag_title" name="cvtx_antrag_title" class="required" value="'.$cvtx_antrag_title.'" size="80" /><br />';
    $output .= '</div>';
    
    // TOP
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_top">TOP: <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br />';
    $output .= cvtx_dropdown_tops($cvtx_antrag_top, __('Keine Tagesordnungspunkte angelegt', 'cvtx')).'<br />';
    $output .= '</div>';
    
    // Antragsteller
    $output .= '<div class="form-group">';
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_steller">'.__('AntragstellerInnen', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<textarea id="cvtx_antrag_steller" name="cvtx_antrag_steller" class="required" size="100%" cols="60" rows="5" />'.$cvtx_antrag_steller.'</textarea><br/>';
    $output .= '</div>';
    
    // Kontakt (E-Mail)
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_email">'.__('E-Mail-Adresse', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label> ('.__('wird nicht veröffentlicht', 'cvtx').')<br/>';
    $output .= '<input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" class="required" value="'.$cvtx_antrag_email.'" size="70" /><br/>';
    $output .= '</div>';
    
    // Kontakt (Telefon)
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_phone">'.__('Telefonnummer', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label> ('.__('wird nicht veröffentlicht', 'cvtx').')<br/>';
    $output .= '<input type="text" id="cvtx_antrag_phone" name="cvtx_antrag_phone" class="required" value="'.$cvtx_antrag_phone.'" size="70" /><br/>';
    $output .= '</div>';
    $output .= '</div>';
        
    // Antragstext
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_text">'.__('Antragstext', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<textarea id="cvtx_antrag_text" name="cvtx_antrag_text" class="required" size="100%" cols="60" rows="20" />'.$cvtx_antrag_text.'</textarea><br/>';
    $output .= '</div>';

    // Antragsgrund
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_antrag_grund">'.__('Antragsbegründung', 'cvtx').':</label><br/>';
    $output .= '<textarea id="cvtx_antrag_grund" name="cvtx_antrag_grund" size="100%" cols="60" rows="10" />'.$cvtx_antrag_grund.'</textarea><br/>';
    $output .= '</div>';

    // Check if reCaptcha is used
    $recaptcha = get_option('cvtx_use_recaptcha');
       $publickey = get_option('cvtx_recaptcha_publickey');
    
    // embed reCaptcha
    if($recaptcha && !empty($publickey)) {
        require_once(WP_PLUGIN_DIR . '/cvtx/reCaptcha/recaptchalib.php');
        $output .= '<div class="form-item">';
        $output .= recaptcha_get_html($publickey);
        $output .= '</div>';
    }
    
    // Submit-Button
    $output .= '<div class="form-item">';
    $output .= '<input type="submit" id="cvtx_antrag_submit" class="submit" value="'.__('Antrag erstellen', 'cvtx').'">';
    $output .= '</div>';
    $output .= '</form>';
    
    return $output;
}

/**
 * Method which evaluates the input of an ae_antrags_creation-form and saves it to the wordpress database
 */
function cvtx_submit_aeantrag($cvtx_aeantrag_antrag = 0) {
    $cvtx_aeantrag_zeile   = (!empty($_POST['cvtx_aeantrag_zeile'])   ? trim($_POST['cvtx_aeantrag_zeile'])   : '');
    $cvtx_aeantrag_steller = (!empty($_POST['cvtx_aeantrag_steller']) ? trim($_POST['cvtx_aeantrag_steller']) : '');
    $cvtx_aeantrag_email   = (!empty($_POST['cvtx_aeantrag_email'])   ? trim($_POST['cvtx_aeantrag_email'])   : '');
    $cvtx_aeantrag_phone   = (!empty($_POST['cvtx_aeantrag_phone'])   ? trim($_POST['cvtx_aeantrag_phone'])   : '');
    $cvtx_aeantrag_text    = (!empty($_POST['cvtx_aeantrag_text'])    ? trim($_POST['cvtx_aeantrag_text'])    : '');
    $cvtx_aeantrag_grund   = (!empty($_POST['cvtx_aeantrag_grund'])   ? trim($_POST['cvtx_aeantrag_grund'])   : '');
    
    if (isset($_POST['cvtx_form_create_aeantrag_submitted']) && $cvtx_aeantrag_antrag != 0
    && wp_verify_nonce($_POST['cvtx_form_create_aeantrag_submitted'], 'cvtx_form_create_aeantrag')) {

        $recaptcha  = get_option('cvtx_use_recaptcha');
        $privatekey = get_option('cvtx_recaptcha_privatekey');
          
          if($recaptcha && !empty($privatekey)) {
            require_once(WP_PLUGIN_DIR.'/cvtx/reCaptcha/recaptchalib.php');
            $resp = recaptcha_check_answer ($privatekey,
                                            $_SERVER['REMOTE_ADDR'],
                                            $_POST['recaptcha_challenge_field'],
                                            $_POST['recaptcha_response_field']);
            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                echo('<p id="message" class="error">'.__('Der Captcha wurde falsch eingegeben. Bitte versuche es erneut.', 'cvtx').'</p>');
            }
        }
        if(!$recaptcha || $resp->is_valid) {
            // check whethter the required fields have been set
            if (!empty($cvtx_aeantrag_zeile) && !empty($cvtx_aeantrag_text) && !empty($cvtx_aeantrag_steller)
             && !empty($cvtx_aeantrag_antrag) && !empty($cvtx_aeantrag_email) && !empty($cvtx_aeantrag_phone)) {
                $aeantrag_data = array(
                    'cvtx_aeantrag_steller' => $cvtx_aeantrag_steller,
                    'cvtx_aeantrag_antrag'  => $cvtx_aeantrag_antrag,
                    'cvtx_aeantrag_grund'   => $cvtx_aeantrag_grund,
                    'cvtx_aeantrag_zeile'   => $cvtx_aeantrag_zeile,
                    'cvtx_aeantrag_email'   => $cvtx_aeantrag_email,
                    'cvtx_aeantrag_phone'   => $cvtx_aeantrag_phone,
                    'post_status'           => 'pending',
                    'post_author'           => get_option('cvtx_anon_user'),
                    'post_content'          => $cvtx_aeantrag_text,
                    'post_type'             => 'cvtx_aeantrag',
                );
                // submit the post!
                if($antrag_id = wp_insert_post($aeantrag_data)) {
                    echo '<p id="message" class="success">'.__('Der Änderungsantrag wurde erstellt und muss noch freigeschaltet werden.', 'cvtx').'</p>';
                    $erstellt = true;
                }
                else {
                    echo '<p id="message" class="error">'.__('Der Änderungsantrag wurde nicht gespeichert. '
                        .'Bitte tanzen Sie um den Tisch und probieren sie es dann mit einer anderen Computer-Stellung noch einmal.', 'cvtx').'</p>';
                }
            }
            else {
                echo '<p id="message" class="error">Der Änderungsantrag konnte nicht gespeichert werden, weil einige benötigte Felder '.
                     ' (mit einem <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span> be'.
                     'zeichnet) nicht ausgefüllt wurden.</p>';
            }
        }
    }
    if(!isset($erstellt))
        echo cvtx_create_aeantrag_form($cvtx_aeantrag_antrag, $cvtx_aeantrag_zeile, $cvtx_aeantrag_text, $cvtx_aeantrag_steller,
                                       $cvtx_aeantrag_email, $cvtx_aeantrag_phone, $cvtx_aeantrag_grund);
}


/**
 * Creates formular for creating ae_antraege
 *
 * @param int $cvtx_aeantrag_antrag antrag to which the ae_antrag is dedicated
 * @param string $cvtx_aeantrag_zeile zeile if it has been already submitted
 * @param string $cvtx_aeantrag_text text of aeantrag if it has already been submitted
 * @param string $cvtx_aeantrag_steller aeantrag_steller if it have been already submitted
 * @param string $cvtx_aeantrag_email email of antragsteller if it have been already submitted
 * @param string $cvtx_aeantrag_phone phone number of antragsteller if it have been already submitted
 * @param string $cvtx_aeantrag_grund aeantragsbegruendung, if already submitted
 */
function cvtx_create_aeantrag_form($cvtx_aeantrag_antrag = 0, $cvtx_aeantrag_zeile  = '', $cvtx_aeantrag_text  = '', $cvtx_aeantrag_steller = '',
                                   $cvtx_aeantrag_email  = '', $cvtx_aeantrag_phone = '', $cvtx_aeantrag_grund = '') {
    $output  = '';
    
    // specify form
    $output .= '<form id="create_aeantrag_form" class="cvtx_antrag_form" method="post" action="">';
    
    // Wp-nonce for security reasons
    $output .= wp_nonce_field('cvtx_form_create_aeantrag', 'cvtx_form_create_aeantrag_submitted');
    
    // Antragszeile
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_zeile">'.__('Zeile', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<input type="text" id="cvtx_aeantrag_zeile" name="cvtx_aeantrag_zeile" class="required" value="'.$cvtx_aeantrag_zeile.'" size="4" /><br>';
    $output .= '</div>';
        
    // Antragsteller
    $output .= '<div class="form-group">';
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_steller">'.__('AntragstellerInnen', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<textarea id="cvtx_aeantrag_steller" name="cvtx_aeantrag_steller" class="required" size="100%" cols="60" rows="5" />'.$cvtx_aeantrag_steller.'</textarea><br/>';
    $output .= '</div>';
    
    // E-Mail-Adresse
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_email">'.__('E-Mail-Adresse', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label> (wird nicht veröffentlicht)<br/>';
    $output .= '<input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" class="required" value="'.$cvtx_aeantrag_email.'" size="80" /><br/>';
    $output .= '</div>';
    
    // Telefonnummer
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_phone">'.__('Telefonnummer', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label> (wird nicht veröffentlicht)<br/>';
    $output .= '<input type="text" id="cvtx_aeantrag_phone" name="cvtx_aeantrag_phone" class="required" value="'.$cvtx_aeantrag_phone.'" size="80" /><br/>';
    $output .= '</div>';
    $output .= '</div>';
    
    // Antrag
    $output .= '<input type="hidden" id="cvtx_aeantrag_antrag" name="cvtx_aeantrag_antrag" value="'.$cvtx_aeantrag_antrag.'"/>';
    
    // Antragstext
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_text">'.__('Antragstext', 'cvtx').': <span class="form-required" title="'.__('Dieses Feld wird benötigt', 'cvtx').'">*</span></label><br/>';
    $output .= '<textarea id="cvtx_aeantrag_text" name="cvtx_aeantrag_text" class="required" size="100%" cols="60" rows="10" />'.$cvtx_aeantrag_text.'</textarea><br/>';
    $output .= '</div>';

    // Antragsgrund
    $output .= '<div class="form-item">';
    $output .= '<label for="cvtx_aeantrag_grund">'.__('Antragsbegründung', 'cvtx').':</label><br/>';
    $output .= '<textarea id="cvtx_aeantrag_grund" name="cvtx_aeantrag_grund" size="100%" cols="60" rows="5" />'.$cvtx_aeantrag_grund.'</textarea><br/>';
    $output .= '</div>';

    // Check if reCaptcha is used
    $recaptcha = get_option('cvtx_use_recaptcha');
       $publickey = get_option('cvtx_recaptcha_publickey');
    
    // embed reCaptcha
    if($recaptcha && !empty($publickey)) {
        require_once(WP_PLUGIN_DIR.'/cvtx/reCaptcha/recaptchalib.php');
        $output .= '<div class="form-item">';
        $output .= recaptcha_get_html($publickey);
        $output .= '</div>';
    }
    
    // Submit-Button
    $output .= ' <div class="form-item">';
    $output .= '  <input type="submit" id="cvtx_aeantrag_submit" class="submit" value="'.__('Änderungsantrag erstellen', 'cvtx').'">';
    $output .= ' </div>';
    $output .= '</form>';
    
    return $output;
}

remove_all_actions('do_feed_rss2' );
add_action('do_feed_rss2', 'cvtx_feed_rss2', 10, 1 );
/**
 * Change feed-templates for cvtx_antrag and cvtx_aeantrag
 */
function cvtx_feed_rss2($for_comments) {
    $rss_template_antrag   = CVTX_PLUGIN_DIR. '/feeds/feed-cvtx_antrag-rss2.php';
    $rss_template_aeantrag = CVTX_PLUGIN_DIR. '/feeds/feed-cvtx_aeantrag-rss2.php';
    if(get_query_var('post_type') == 'cvtx_antrag' and file_exists($rss_template_antrag))
        load_template($rss_template_antrag);
    elseif(get_query_var('post_type') == 'cvtx_aeantrag' and file_exists($rss_template_aeantrag)) {
        load_template($rss_template_aeantrag);
    }
    else
        do_feed_rss2($for_comments); // Call default function
}

/**
 * returns meta-informations about antrag/aeantrag
 */
function get_cvtx_rss_before_content($post,$type) {
    $output  = '';
    if($type == 'cvtx_antrag') {
        $output .= '<p><strong>'.__('TOP','cvtx').'</strong>: '.get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_ord', true).'</p>';
        $output .= '<p><strong>'.__('AntragstellerInnen','cvtx').'</strong>: '.get_post_meta($post->ID,'cvtx_antrag_steller_short',true).'</p>';
    }
    elseif($type == 'cvtx_aeantrag') {
        $output .= '<p><strong>'.__('Zeile','cvtx').'</strong>: '.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'</p>';
        $output .= '<p><strong>'.__('AntragstellerInnen','cvtx').'</strong>: '.get_post_meta($post->ID,'cvtx_antrag_steller',true).'</p>';
    }
    return $output;
}

/**
 * returns download-link for antrag/aeantrag
 */
function get_cvtx_rss_after_content($post) {
    $output  = '';
    if (function_exists('cvtx_get_file')
        && $file = cvtx_get_file($post, 'pdf'))
        $output .= '<p><a href="'.$file.'">Download (pdf)</a></p>';
    return $output;
}
?>