<?php
/**
 * @package cvtx
 * @version 0.1
 */
/*
Plugin Name: cvtx Antragstool
Plugin URI: http://wordpress.org/extend/plugins/cvtx/
Description: Dunno.
Author: Alexander Fecke & Max Löffler
Version: 0.1
Author URI: http://alexander-fecke.de
License: GPLv2 or later
*/

/* 
Copyright 2011 - Alexander Fecke & Max Löffler

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('CVTX_VERSION', '0.1');
define('CVTX_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once('cvtx_admin.php');


// define post types
$cvtx_types = array('cvtx_top'      => array('cvtx_top_ord',
                                             'cvtx_top_sort',
                                             'cvtx_top_short'),
                    'cvtx_antrag'   => array('cvtx_antrag_ord',
                                             'cvtx_antrag_sort',
                                             'cvtx_antrag_top',
                                             'cvtx_antrag_steller',
                                             'cvtx_antrag_steller_short',
                                             'cvtx_antrag_email',
                                             'cvtx_antrag_phone',
                                             'cvtx_antrag_grund',
                                             'cvtx_antrag_info'),
                    'cvtx_aeantrag' => array('cvtx_aeantrag_zeile',
                                             'cvtx_aeantrag_sort',
                                             'cvtx_aeantrag_antrag',
                                             'cvtx_aeantrag_steller',
                                             'cvtx_aeantrag_steller_short',
                                             'cvtx_aeantrag_email',
                                             'cvtx_aeantrag_phone',
                                             'cvtx_aeantrag_grund',
                                             'cvtx_aeantrag_verfahren',
                                             'cvtx_aeantrag_detail',
                                             'cvtx_aeantrag_info'));


add_action('init', 'cvtx_create_post_types');
/**
 * Create custom post types
 */
function cvtx_create_post_types() {
    // Tagesordnungspunkte
    register_post_type('cvtx_top',
        array('labels'        => array(
              'name'          => __('TOPs'),
              'singular_name' => __('TOP'),
              'add_new_item'  => __('TOP erstellen'),
              'edit_item'     => __('TOP bearbeiten'),
              'view_item'     => __('TOP ansehen')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'rewrite'     => array('slug' => 'top'),
        'supports'    => array('title'),
        )
    );

    // Anträge
    register_post_type('cvtx_antrag',
        array('labels'        => array(
              'name'          => __('Anträge'),
              'singular_name' => __('Antrag'),
              'add_new_item'  => __('Antrag erstellen'),
              'edit_item'     => __('Antrag bearbeiten'),
              'view_item'     => __('Antrag ansehen')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'rewrite'     => array('slug' => 'antrag'),
        'supports'    => array('title', 'editor'),
        )
    );

    // Änderungsanträge
    register_post_type('cvtx_aeantrag',
        array('labels'        => array(
              'name'          => __('Ä-Anträge'),
              'singular_name' => __('Ä-Antrag'),
              'add_new_item'  => __('Änderungsantrag erstellen'),
              'edit_item'     => __('Änderungsantrag bearbeiten'),
              'view_item'     => __('Änderungsantrag ansehen')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'rewrite'     => array('slug' => 'aeantrag'),
        'supports'    => array('editor'),
        )
    );
}


if (is_admin()) add_action('delete_post', 'cvtx_delete_post');
/**
 * Removes all latex files if antrag or aeantrag is deleted.
 *
 * @todo drop cvtx_aeantraege when cvtx_antrag deleted? drop cvtx_antrag when cvtx_top deleted?
 */
function cvtx_delete_post($post_id) {
    global $post;

    if (is_object($post) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag')) {
        cvtx_remove_files($post);
    }
}


/**
 * Returns a globally sortable string
 */
function cvtx_get_sort($post_type, $top, $antrag=0, $zeile=0, $vari=0) {
    $sorts = array('top'    => sprintf('%1$05d', $top),
                   'antrag' => sprintf('%1$05d', $antrag),
                   'zeile'  => sprintf('%1$06d', $zeile),
                   'vari'   => sprintf('%1$06d', $vari));
    foreach ($sorts as $key => $value) {
        $code = '';
        for ($i = 0; $i < strlen($value); $i++) {
            $code .= chr(intval(substr($value, $i, 1)) + 65);
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
            $_POST['cvtx_top_sort'] = cvtx_get_sort('cvtx_top', get_post_meta($post_id, 'cvtx_top_ord', true));
        } else if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_top'])) {
            $top_ord    = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true);
            $antrag_ord = (isset($_POST['cvtx_antrag_ord']) ? $_POST['cvtx_antrag_ord'] : 0);

            $_POST['cvtx_antrag_sort'] = cvtx_get_sort('cvtx_antrag', $top_ord, $antrag_ord);
        } else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
            $top_id         = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
            $top_ord        = get_post_meta($top_id, 'cvtx_top_ord', true);
            $antrag_ord     = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true);
            $aeantrag_zeile = 0;
            $aeantrag_vari  = 0;
            if (preg_match('/^([0-9]+)/', $_POST['cvtx_aeantrag_zeile'], $match1)) $aeantrag_zeile = $match1[1];
            if (preg_match('/([0-9]+)$/', $_POST['cvtx_aeantrag_zeile'], $match2) && strlen($match2[1]) < strlen($_POST['cvtx_aeantrag_zeile'])) $aeantrag_vari = $match2[1];
            $_POST['cvtx_aeantrag_sort'] = cvtx_get_sort('cvtx_aeantrag', $top_ord, $antrag_ord, $aeantrag_zeile, $aeantrag_vari);
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
            $mails   = array('owner' => array('subject' => '', 'body' => ''),
                             'admin' => array('subject' => '', 'body' => ''));
            $headers = $headers = 'From: '.get_option('cvtx_send_from_email')."\r\n";
            
            // post type antrag created
            if ($post->post_type == 'cvtx_antrag') {
                $mails['owner'] = array('subject' => get_option('cvtx_send_create_antrag_owner_subject'), 'body' => get_option('cvtx_send_create_antrag_owner_body'));
                $mails['admin'] = array('subject' => get_option('cvtx_send_create_antrag_admin_subject'), 'body' => get_option('cvtx_send_create_antrag_admin_body'));
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        $content = str_replace('%top_kuerzel%', 'TOP '.get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true), $content);
                        $content = str_replace('%top%', get_the_title($_POST['cvtx_antrag_top']), $content);
                        $content = str_replace('%titel%', $post->post_title, $content);
                        $content = str_replace('%antragsteller%', $_POST['cvtx_antrag_steller'], $content);
                        $content = str_replace('%antragsteller_kurz%', $_POST['cvtx_antrag_steller_short'], $content);
                        $content = str_replace('%antragstext%', $post->post_content, $content);
                        $content = str_replace('%begruendung%', $_POST['cvtx_antrag_grund'], $content);
                        $mails[$rcpt][$part] = $content;
                    }
                }
                
                // send mail(s) if option enabled
                if (get_option('cvtx_send_create_antrag_owner')) wp_mail($_POST['cvtx_antrag_email'], $mails['owner']['subject'], $mails['owner']['body'], $headers);
                if (get_option('cvtx_send_create_antrag_admin')) wp_mail(get_option('cvtx_send_rcpt_email'), $mails['admin']['subject'], $mails['admin']['body'], $headers);
            }
            // post type aeantrag created
            else if ($post->post_type == 'cvtx_aeantrag') {
                $mails['owner'] = array('subject' => get_option('cvtx_send_create_aeantrag_owner_subject'), 'body' => get_option('cvtx_send_create_aeantrag_owner_body'));
                $mails['admin'] = array('subject' => get_option('cvtx_send_create_aeantrag_admin_subject'), 'body' => get_option('cvtx_send_create_aeantrag_admin_body'));
                
                $top_id = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        $content = str_replace('%top_kuerzel%', 'TOP '.get_post_meta($top_id, 'cvtx_top_ord', true), $content);
                        $content = str_replace('%top%', get_the_title($top_id), $content);
                        $content = str_replace('%antrag_kuerzel%', get_post_meta($top_id, 'cvtx_top_short', true)
                                                                  .'-'.get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true), $content);
                        $content = str_replace('%antrag%', get_the_title($_POST['cvtx_aeantrag_antrag']), $content);
                        $content = str_replace('%zeile%', $_POST['cvtx_aeantrag_zeile'], $content);
                        $content = str_replace('%antragsteller%', $_POST['cvtx_aeantrag_steller'], $content);
                        $content = str_replace('%antragsteller_kurz%', $_POST['cvtx_aeantrag_steller_short'], $content);
                        $content = str_replace('%antragstext%', $post->post_content, $content);
                        $content = str_replace('%begruendung%', $_POST['cvtx_aeantrag_grund'], $content);
                        $mails[$rcpt][$part] = $content;
                    }
                }
                
                // send mail(s) if option enabled
                if (get_option('cvtx_send_create_aeantrag_owner')) wp_mail($_POST['cvtx_aeantrag_email'], $mails['owner']['subject'], $mails['owner']['body'], $headers);
                if (get_option('cvtx_send_create_aeantrag_admin')) wp_mail(get_option('cvtx_send_rcpt_email'), $mails['admin']['subject'], $mails['admin']['body'], $headers);
            }
        }
        
    }
}


/**
 * Erstellt ein PDF aus gespeicherten Anträgen
 */
function cvtx_create_pdf($post_id, $post = null) {
    $pdflatex = get_option('cvtx_pdflatex_cmd');
    
    if (isset($post) && is_object($post) && !empty($pdflatex)) {
        $out_dir = wp_upload_dir();
        $tpl_dir = get_template_directory().'/latex';
    
        // prepare antrag
        if ($post->post_type == 'cvtx_antrag') {
            // file
            $file = $out_dir['basedir'].'/';
            if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
                $file .= cvtx_sanitize_file_name($short.'_'.$post->post_title);
            } else {
                $file .= $post->ID;
            }
            
            // use special template for id=x if exists
            if (is_file($tpl_dir.'/single-cvtx_antrag-'.$post_id.'.php')) {
                $tpl = $tpl_dir.'/single-cvtx_antrag-'.$post_id.'.php';
            }
            // use default template
            else if(is_file($tpl_dir.'/single-cvtx_antrag.php')) {
                $tpl = $tpl_dir.'/single-cvtx_antrag.php';
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
            
            // use special template for id=x if exists
            if (is_file($tpl_dir.'/single-cvtx_aeantrag-'.$post_id.'.php')) {
                $tpl = $tpl_dir.'/single-cvtx_aeantrag-'.$post_id.'.php';
            }
            // use default template
            else if(is_file($tpl_dir.'/single-cvtx_aeantrag.php')) {
                $tpl = $tpl_dir.'/single-cvtx_aeantrag.php';
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
            
            // run latex template, caputure output
            ob_start();
            require($tpl);
            $out = ob_get_contents();
            ob_end_clean();

            // save output to latex file. success?
            if (file_put_contents($file.'.tex', $out) !== false) {
                // run pdflatex
                exec($pdflatex.' -interaction=nonstopmode -output-directory='.$out_dir['basedir'].' '.$file.'.tex');
                
                // remove .aux-file
                if (is_file($file.'.aux')) unlink($file.'.aux');
                // remove .log-file
                if (get_option('cvtx_drop_logfile') == 1 || (get_option('cvtx_drop_logfile') == 2 && is_file($file.'.pdf'))) {
                    if (is_file($file.'.log')) unlink($file.'.log');
                }
                // remove .tex-file
                if (get_option('cvtx_drop_texfile') == 1 || (get_option('cvtx_drop_texfile') == 2 && is_file($file.'.pdf'))) {
                    if (is_file($file.'.tex')) unlink($file.'.tex');
                }
            }
        }
    }
}


// replaces filter "the title" in order to generate custom titles for post-types "top", "antrag" and "aeantrag"
add_filter('the_title', 'cvtx_the_title', 1, 3);
function cvtx_the_title($before='', $after='', $echo = true) {

	if(is_numeric($after)) $post = &get_post($after);
		    
    if(isset($post)) {
        $title = $post->post_title;
        
        if ($short = cvtx_get_short($post)) {
            // Antrag
            if($post->post_type == 'cvtx_antrag') {
                $title = $short.' '.$post->post_title;
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
        	return $before;
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

        if (!empty($top) && !empty($antrag)) return $top.'-'.$antrag;
    }
    // post type antrag
    else if ($post->post_type == 'cvtx_aeantrag') {
        // fetch antrag_id, antag, top and zeile
        $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
        $antrag    = get_post_meta($antrag_id, 'cvtx_antrag_ord', true);
        $top       = get_post_meta(get_post_meta($antrag_id, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
        $zeile     = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
        
        // format and return aeantrag_short
        $format = get_option('cvtx_aeantrag_format');
        $format = str_replace('%antrag%', $top.'-'.$antrag, $format);
        $format = str_replace('%zeile%', $zeile, $format);
        
        if (!empty($top) && !empty($antrag) && !empty($zeile)) return $format;
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
        $endings = array('pdf', 'log', 'tex', 'aux', 'toc', 'bbl', 'blg', 'synctex.gz');
    }

    foreach ($endings as $ending) {
        if ($file = cvtx_get_file($post, $ending, 'dir')) unlink($file);
    }
}


/**
 * Returns latex formatted output
 *
 * @todo - how to handle '\' -> '{\\textbackslash}' ??
 *
 * @param $out input
 * @return formatted output
 */
function cvtx_get_latex($out) {
    // strip html entities
//    $out = html_entity_decode($out);
    $out = str_replace('&nbsp;', ' ', $out);
    $out = str_replace('&amp;', '&', $out);
    
    // recode special chars
    $tmp = time().'\\textbackslash'.rand();
    $out = str_replace('\\', $tmp, $out);
    $out = str_replace(array('$', '%', '_', '{', '}', '&', '#'),
                       array('\\$', '\\%', '\\_', '\\{', '\\}', '\\&', '\\#'), $out);
    $out = str_replace($tmp, '{\\textbackslash}', $out);
    
    // recode formatting rules
    $rules = array(array('search'  => array('<strong>', '</strong>'),
                         'replace' => array('\textbf{', '}')),
                   array('search'  => array('<b>', '</b>'),
                         'replace' => array('\textbf{', '}')),
                   array('search'  => array('<em>', '</em>'),
                         'replace' => array('\textit{', '}')),
                   array('search'  => array('<i>', '</i>'),
                         'replace' => array('\textit{', '}')),
                   array('search'  => array('<h3>', '</h3>'),
                         'replace' => array('\subsection*{', '}')),
                   array('search'  => array('<h4>', '</h4>'),
                         'replace' => array('\subsubsection*{', '}')),
                   array('search'  => array('<ul>', '</ul>'),
                         'replace' => array('\begin{itemize}', '\end{itemize}')),
                   array('search'  => array('<ol>', '</ol>'),
                         'replace' => array('\begin{enumerate}', '\end{enumerate}')),
                   array('search'  => array('<li>', '</li>'),
                         'replace' => array('\item ', '')),
                   array('search'  => '/<br[ ]*[\/]?>/',
                         'replace' => "\n",
                         'mode'    => 'preg'));
    // run replacing rules
    foreach ($rules as $rule) {
        if (!isset($rule['mode']) || $rule['mode'] != 'preg') {
            $out = str_replace($rule['search'], $rule['replace'], $out);
        } else {
            $out = preg_replace($rule['search'], $rule['replace'], $out);
        }
    }
    
    // strip
    $out = strip_tags($out);
    $out = trim($out);
    
    // add new lines
    $out = preg_replace("/[\r\n]+/", "\\par\n", $out);
#    $out = str_replace("\r\n", "\n", $out);
#    $out = str_replace("\n", "\\par\n", $out);
    
    return $out;
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
     && isset($_REQUEST['args'])      && is_array($_REQUEST['args'])     && count($_REQUEST['args']) > 0
     && isset($_REQUEST['post_id'])   && is_array($_REQUEST['post_id'])) {
        $param = array('post_type'    => $_REQUEST['post_type'],
                       'post__not_in' => $_REQUEST['post_id'],
                       'meta_query'   => $_REQUEST['args']);
        
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
 * Print dropdown menu of all tops
 *
 * @param $selected post_id of selected top, otherwise null
 * @param $message message that will be displayed if no top exists
 */
function cvtx_dropdown_tops($selected = null, $message = '') {
    global $post;
    $post_bak = $post;
	$output = '';

    $tquery = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'meta_value', 'meta_key' => 'cvtx_top_sort', 'order' => 'ASC'));
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
                                 'meta_key'  => 'cvtx_top_sort',
                                 'order'     => 'ASC'));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_aeantrag_antrag" id="cvtx_aeantrag_antrag_select">';
        while ($tquery->have_posts()) {
            $tquery->the_post();
            // optgroup for top
            $output .= '<optgroup label="'.get_the_title().'">';
            
            // list anträge in top
            $aquery = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                         'orderby'    => 'meta_value',
                                         'meta_key'   => 'cvtx_antrag_sort',
                                         'order'      => 'ASC',
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
  		
  		$recaptcha = get_option('cvtx_use_recaptcha');
		$privatekey = get_option('cvtx_recaptcha_privatekey');
  		
  		if($recaptcha && !empty($privatekey)) {
			require_once(WP_PLUGIN_DIR . '/cvtx/reCaptcha/recaptchalib.php');
			$resp = recaptcha_check_answer ($privatekey,
	    		                            $_SERVER["REMOTE_ADDR"],
	        		                        $_POST["recaptcha_challenge_field"],
    	        		                    $_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
	    		// What happens when the CAPTCHA was entered incorrectly
	    		echo('<p id="message" class="error">Der Captcha wurde falsch eingegeben. Bitte versuche es erneut.</p>');
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
					echo '<p id="message" class="success">Der Antrag wurde erstellt und muss noch freigeschaltet werden.</p>';
					$erstellt = true;
				}
				else {
					echo '<p id="message" class="error">Antrag wurde NICHT gespeichert. Warum auch immer.</p>';
				}
			}
			// return error-message because some required fields have not been submitted
			else {
				echo '<p id="message" class="error">Der Antrag konnte nicht gespeichert werden, weil einige benötigte Felder '. 
					 '(mit einem <span class="form-required" title="Dieses Feld wird benötigt">*<'.
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
function cvtx_create_antrag_form($cvtx_antrag_top = 0, $cvtx_antrag_title = '', $cvtx_antrag_text = '', $cvtx_antrag_steller = '',
                                 $cvtx_antrag_email = '', $cvtx_antrag_phone = '', $cvtx_antrag_grund = '') {
	$output  = '';
	
	// specify form
	$output .= '<form id="create_antrag_form" class="cvtx_antrag_form" method="post" action="">';
	
	// Wp-nonce for security reasons
	$output .= wp_nonce_field('cvtx_form_create_antrag','cvtx_form_create_antrag_submitted');
	
	// Antragstitel
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_title">Antragstitel: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_antrag_title" name="cvtx_antrag_title" class="required" value="'.$cvtx_antrag_title.'" size="80" /><br>';
	$output .= '</div>';
	
	// TOP
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_top">TOP: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= cvtx_dropdown_tops($cvtx_antrag_top, 'Keine Tagesordnungspunkte angelegt').'<br/>';
	$output .= '</div>';
	
	// Antragsteller
	$output .= '<div class="form-group">';
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_steller">AntragstellerInnen: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
    $output .= '<textarea id="cvtx_antrag_steller" name="cvtx_antrag_steller" class="required" size="100%" cols="60" rows="5" />'.$cvtx_antrag_steller.'</textarea><br/>';
	$output .= '</div>';
	
	// Kontakt (E-Mail)
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_email">E-Mail-Adresse: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label> (wird nicht veröffentlicht)<br/>';
	$output .= '<input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" class="required" value="'.$cvtx_antrag_email.'" size="70" /><br/>';
	$output .= '</div>';
	
	// Kontakt (Telefon)
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_phone">Telefonnummer: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label> (wird nicht veröffentlicht)<br/>';
	$output .= '<input type="text" id="cvtx_antrag_phone" name="cvtx_antrag_phone" class="required" value="'.$cvtx_antrag_phone.'" size="70" /><br/>';
	$output .= '</div>';
	$output .= '</div>';
		
	// Antragstext
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_text">Antragstext: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<textarea id="cvtx_antrag_text" name="cvtx_antrag_text" class="required" size="100%" cols="60" rows="20" />'.$cvtx_antrag_text.'</textarea><br/>';
	$output .= '</div>';

	// Antragsgrund
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_grund">Antragsbegründung:</label><br/>';
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
	$output .= '<input type="submit" id="cvtx_antrag_submit" class="submit" value="Antrag erstellen">';
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

  		$recaptcha = get_option('cvtx_use_recaptcha');
		$privatekey = get_option('cvtx_recaptcha_privatekey');
  		
  		if($recaptcha && !empty($privatekey)) {
			require_once(WP_PLUGIN_DIR . '/cvtx/reCaptcha/recaptchalib.php');
			$resp = recaptcha_check_answer ($privatekey,
	    		                            $_SERVER["REMOTE_ADDR"],
	        		                        $_POST["recaptcha_challenge_field"],
    	        		                    $_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
	    		// What happens when the CAPTCHA was entered incorrectly
	    		echo('<p id="message" class="error">Der Captcha wurde falsch eingegeben. Bitte versuche es erneut.</p>');
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
					echo '<p id="message" class="success">Der Änderungsantrag wurde erstellt und muss noch freigeschaltet werden.</p>';
					$erstellt = true;
				}
				else {
					echo '<p id="message" class="error">Der Änderungsantrag wurde nicht gespeichert. '
            	        .'Bitte tanzen Sie um den Tisch und probieren sie es dann mit einer anderen Computer-Stellung noch einmal.</p>';
				}
			}
			else {
				echo '<p id="message" class="error">Der Änderungsantrag konnte nicht gespeichert werden, weil einige benötigte Felder '.
					 ' (mit einem <span class="form-required" title="Dieses Feld wird benötigt">*</span> be'.
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
function cvtx_create_aeantrag_form($cvtx_aeantrag_antrag = 0, $cvtx_aeantrag_zeile = '', $cvtx_aeantrag_text = '', $cvtx_aeantrag_steller = '',
                                   $cvtx_aeantrag_email = '', $cvtx_aeantrag_phone = '', $cvtx_aeantrag_grund = '') {
	$output  = '';
	
	// specify form
	$output .= '<form id="create_aeantrag_form" class="cvtx_antrag_form" method="post" action="">';
	
	// Wp-nonce for security reasons
	$output .= wp_nonce_field('cvtx_form_create_aeantrag','cvtx_form_create_aeantrag_submitted');
	
	// Antragszeile
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_zeile">Zeile: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_aeantrag_zeile" name="cvtx_aeantrag_zeile" class="required" value="'.$cvtx_aeantrag_zeile.'" size="4" /><br>';
	$output .= '</div>';
		
	// Antragsteller
	$output .= '<div class="form-group">';
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_steller">AntragstellerInnen: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<textarea id="cvtx_aeantrag_steller" name="cvtx_aeantrag_steller" class="required" size="100%" cols="60" rows="5" />'.$cvtx_aeantrag_steller.'</textarea><br/>';
	$output .= '</div>';
	
	// E-Mail-Adresse
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_email">E-Mail-Adresse: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label> (wird nicht veröffentlicht)<br/>';
	$output .= '<input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" class="required" value="'.$cvtx_aeantrag_email.'" size="80" /><br/>';
	$output .= '</div>';
	
	// Telefonnummer
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_phone">Telefonnummer: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label> (wird nicht veröffentlicht)<br/>';
	$output .= '<input type="text" id="cvtx_aeantrag_phone" name="cvtx_aeantrag_phone" class="required" value="'.$cvtx_aeantrag_phone.'" size="80" /><br/>';
	$output .= '</div>';
	$output .= '</div>';
	
	// Antrag
	$output .= '<input type="hidden" id="cvtx_aeantrag_antrag" name="cvtx_aeantrag_antrag" value="'.$cvtx_aeantrag_antrag.'"/>';
	
	// Antragstext
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_text">Antragstext: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<textarea id="cvtx_aeantrag_text" name="cvtx_aeantrag_text" class="required" size="100%" cols="60" rows="10" />'.$cvtx_aeantrag_text.'</textarea><br/>';
	$output .= '</div>';

	// Antragsgrund
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_grund">Antragsbegründung:</label><br/>';
	$output .= '<textarea id="cvtx_aeantrag_grund" name="cvtx_aeantrag_grund" size="100%" cols="60" rows="5" />'.$cvtx_aeantrag_grund.'</textarea><br/>';
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
	$output .= '<input type="submit" id="cvtx_aeantrag_submit" class="submit" value="Änderungsantrag erstellen">';
	$output .= '</div>';
	$output .= '</form>';
	
	return $output;
}


/************************************************************************************
 * LaTeX Functions
 ************************************************************************************/

function cvtx_name() {
    echo(cvtx_get_latex(get_bloginfo('name')));
}

function cvtx_beschreibung() {
    echo(cvtx_get_latex(get_bloginfo('description')));
}

function cvtx_kuerzel($post) {
    global $cvtx_types;
    if (in_array($post->post_type, array_keys($cvtx_types))) {
        echo(cvtx_get_latex(cvtx_get_short($post)));
    }
}

function cvtx_titel($post) {
    global $cvtx_types;
    if (in_array($post->post_type, array_keys($cvtx_types))) {
        echo(cvtx_get_latex($post->post_title));
    }
}

function cvtx_antragstext($post) {
    global $cvtx_types;
    if ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex($post->post_content));
    }
}

function cvtx_begruendung($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_grund', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true)));
    }
}

function cvtx_antragsteller($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_steller', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_steller', true)));
    }
}

function cvtx_antragsteller_kurz($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_steller_short', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true)));
    }
}

function cvtx_top($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true))));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo(cvtx_get_latex(get_the_title($top_id)));
    }
}

function cvtx_top_titel($post) {
    if ($post->post_type == 'cvtx_antrag') {
        $top = get_post(get_post_meta($post->ID, 'cvtx_antrag_top', true));
        echo(cvtx_get_latex($top->post_title));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top = get_post(get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true));
        echo(cvtx_get_latex($top->post_title));
    }
}

function cvtx_top_kuerzel($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo('TOP '.cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo('TOP '.cvtx_get_latex(get_post_meta($top_id, 'cvtx_top_ord', true)));
    }
}

function cvtx_antrag($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true))));
    }
}

function cvtx_antrag_titel($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        echo(cvtx_get_latex($antrag->post_title));
    }
}

function cvtx_antrag_kuerzel($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        echo(cvtx_get_latex(cvtx_get_short($antrag)));
    }
}

function cvtx_info($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_info', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_info', true)));
    }
}

?>
