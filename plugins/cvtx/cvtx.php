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
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('CVTX_VERSION', '0.1');
define('CVTX_PLUGIN_URL', plugin_dir_url( __FILE__ ));


// define post types
$cvtx_types = array('cvtx_top'      => array('cvtx_top_ord',
                                             'cvtx_top_short'),
                    'cvtx_antrag'   => array('cvtx_antrag_ord',
                                             'cvtx_antrag_num',
                                             'cvtx_antrag_top',
                                             'cvtx_antrag_steller',
                                             'cvtx_antrag_grund',
                                             'cvtx_antrag_info'),
                    'cvtx_aeantrag' => array('cvtx_aeantrag_zeile',
                                             'cvtx_aeantrag_num',
                                             'cvtx_aeantrag_antrag',
                                             'cvtx_aeantrag_steller',
                                             'cvtx_aeantrag_grund',
                                             'cvtx_aeantrag_verfahren',
                                             'cvtx_aeantrag_detail',
                                             'cvtx_aeantrag_info'));


/* add custom meta boxes */

add_action('add_meta_boxes', 'cvtx_add_meta_boxes');
function cvtx_add_meta_boxes() {
    // Tagesordnungspunkte
    add_meta_box('cvtx_top_ord', 'Nummer', 'cvtx_top_ord', 'cvtx_top', 'side', 'high');
    add_meta_box('cvtx_top_short', 'Kürzel', 'cvtx_top_short', 'cvtx_top', 'normal', 'high');
    
    // Anträge
    add_meta_box('cvtx_antrag_ord', 'Antragsnummer', 'cvtx_antrag_ord', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_steller', 'AntragstellerIn(nen)', 'cvtx_antrag_steller', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_grund', 'Begründung', 'cvtx_antrag_grund', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_info', 'Weitere Informationen', 'cvtx_antrag_info', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_top', 'Tagesordnungspunkt', 'cvtx_antrag_top', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_pdf', 'PDF', 'cvtx_metabox_pdf', 'cvtx_antrag', 'side', 'low');
    
    // Änderungsanträge
    add_meta_box('cvtx_aeantrag_zeile', 'Zeile(n)', 'cvtx_aeantrag_zeile', 'cvtx_aeantrag', 'side', 'high');
    add_meta_box('cvtx_aeantrag_steller', 'AntragstellerIn(nen)', 'cvtx_aeantrag_steller', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_grund', 'Begründung', 'cvtx_aeantrag_grund', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_verfahren', 'Verfahren', 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_info', 'Weitere Informationen', 'cvtx_aeantrag_info', 'cvtx_aeantrag', 'normal', 'low');
    add_meta_box('cvtx_aeantrag_antrag', 'Antrag', 'cvtx_aeantrag_antrag', 'cvtx_aeantrag', 'side', 'high');
    // show/hide pdf-box for of aeantrag
    if (get_option('cvtx_aeantrag_pdf')) {
        add_meta_box('cvtx_aeantrag_pdf', 'PDF', 'cvtx_metabox_pdf', 'cvtx_aeantrag', 'side', 'low');
    }
}


/* Tagesordnungspunkte */

// TOP-Nummer
function cvtx_top_ord() {
    global $post;
    echo('<label>Nummer:</label> <input name="cvtx_top_ord" type="text" maxlength="4" value="'.get_post_meta($post->ID, 'cvtx_top_ord', true).'" />');
}

// Kürzel
function cvtx_top_short() {
    global $post;
    echo('<label>Kürzel:</label> <input name="cvtx_top_short" type="text" value="'.get_post_meta($post->ID, 'cvtx_top_short', true).'" />');
}


/* Anträge */

// Antragsnummer
function cvtx_antrag_ord() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);
    echo(get_post_meta($top_id, 'cvtx_top_short', true).'-<input name="cvtx_antrag_ord" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_antrag_ord', true).'" />');
}

// Tagesordnungspunkt
function cvtx_antrag_top() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);    
    echo (cvtx_dropdown_tops($top_id, 'Keine Tagesordnungspunkte angelegt.'));
}

// Antragsteller
function cvtx_antrag_steller() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_antrag_steller">'.get_post_meta($post->ID, 'cvtx_antrag_steller', true).'</textarea>');
}

// Begründung
function cvtx_antrag_grund() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_antrag_grund">'.get_post_meta($post->ID, 'cvtx_antrag_grund', true).'</textarea>');
}

// Weitere Infos
function cvtx_antrag_info() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_antrag_info">'.get_post_meta($post->ID, 'cvtx_antrag_info', true).'</textarea>');
}


/* Änderungsanträge */

// Ä-Antragsnummer / Zeile
function cvtx_aeantrag_zeile() {
    global $post;
    $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
    $top_id    = get_post_meta($antrag_id, 'cvtx_antrag_top', true);
    echo(get_post_meta($top_id, 'cvtx_top_short', true).'-'.get_post_meta($antrag_id, 'cvtx_antrag_ord', true).'-<input name="cvtx_aeantrag_zeile" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
}

// Antrag
function cvtx_aeantrag_antrag() {
    global $post;
    $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
    cvtx_dropdown_antraege($antrag_id, 'Keine Tagesordnungspunkte angelegt.');
}

// Antragsteller
function cvtx_aeantrag_steller() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_steller">'.get_post_meta($post->ID, 'cvtx_aeantrag_steller', true).'</textarea>');
}

// Begründung
function cvtx_aeantrag_grund() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_grund">'.get_post_meta($post->ID, 'cvtx_aeantrag_grund', true).'</textarea>');
}

// Weitere Infos
function cvtx_aeantrag_info() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_info">'.get_post_meta($post->ID, 'cvtx_aeantrag_info', true).'</textarea>');
}

// Verfahren
function cvtx_aeantrag_verfahren() {
    global $post;
    echo('<label>Verfahren</label> <select name="cvtx_aeantrag_verfahren"><option></option>');
    $verfahren = array('Übernahme', 'Modifizierte Übernahme', 'Abstimmung', 'Zurückgezogen', 'Erledigt');
    foreach ($verfahren as $verf) {
        echo('<option'.($verf == get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true) ? ' selected="selected"' : '').'>'.$verf.'</option>');
    }
    echo('</select><br />');
    echo('<label>Details</label> <textarea style="width: 100%" name="cvtx_aeantrag_detail">'.get_post_meta($post->ID, 'cvtx_aeantrag_detail', true).'</textarea>');
}


/* Allgemeingültige Meta-Boxen */

// Link zum PDF
function cvtx_metabox_pdf() {
    global $post;
    
    // check if pdf file exists
    if ($file = cvtx_get_file($post, 'pdf')) {
        echo('<a href="'.$file.'">Download (pdf)</a> ');
    }
    // show info otherwise
    else {
        echo('Kein PDF erstellt. ');
    }

    // check if tex file exists
    if ($file = cvtx_get_file($post, 'tex')) {
        echo('<a href="'.$file.'">(tex)</a> ');
    }
    // check if log file exists
    if ($file = cvtx_get_file($post, 'log')) {
        echo('<a href="'.$file.'">(log)</a> ');
    }
}



add_action('init', 'create_post_types');
function create_post_types() {
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


/* Update lists */

add_filter('manage_edit-cvtx_top_columns', 'cvtx_top_columns');
function cvtx_top_columns($columns) {
	$columns = array('cb'              => '<input type="checkbox" />',
                     'title'           => 'Tagesordnungspunkt',
                     'cvtx_top_short'  => 'Kürzel',
                     'cvtx_top_status' => '');
	return $columns;
}

add_filter('manage_edit-cvtx_antrag_columns', 'cvtx_antrag_columns');
function cvtx_antrag_columns($columns) {
    $columns = array('cb'                  => '<input type="checkbox" />',
                     'title'               => 'Antragstitel',
                     'cvtx_antrag_steller' => 'AntragstellerIn(nen)',
                     'cvtx_antrag_top'     => 'Tagesordnungspunkt',
                     'cvtx_antrag_status'  => '');
	return $columns;
}

// Register the column as sortable
add_filter('manage_edit-cvtx_antrag_sortable_columns', 'cvtx_register_sortable_antrag');
function cvtx_register_sortable_antrag($columns) {
    $columns['cvtx_antrag_steller'] = 'cvtx_antrag_steller';
    return $columns;
}

add_filter('manage_edit-cvtx_aeantrag_columns', 'cvtx_aeantrag_columns');
function cvtx_aeantrag_columns($columns) {
	$columns = array('cb'                      => '<input type="checkbox" />',
                     'title'                   => 'Änderungsantrag',
                     'cvtx_aeantrag_steller'   => 'AntragstellerIn(nen)',
                     'cvtx_aeantrag_verfahren' => 'Verfahren',
                     'cvtx_aeantrag_antrag'    => 'Antrag',
                     'cvtx_aeantrag_status'    => '');
	return $columns;
}

// Register the column as sortable
add_filter('manage_edit-cvtx_aeantrag_sortable_columns', 'cvtx_register_sortable_aeantrag');
function cvtx_register_sortable_aeantrag($columns) {
    $columns['cvtx_aeantrag_steller']   = 'cvtx_aeantrag_steller';
    $columns['cvtx_aeantrag_verfahren'] = 'cvtx_aeantrag_verfahren';
    return $columns;
}

add_action('manage_posts_custom_column', 'cvtx_format_lists');
function cvtx_format_lists($column) {
    global $post;
    switch ($column) {
        // TOPs
        case 'cvtx_top_ord':
            echo(cvtx_get_short($post));
            break;
        case 'cvtx_top_short':
            echo(get_post_meta($post->ID, 'cvtx_top_short', true));
            break;
        case 'cvtx_top_status':
            echo(($post->post_status == 'publish' ? '+' : ''));
            break;
            
        // Anträge
        case 'cvtx_antrag_ord':
            echo(cvtx_get_short($post));
            break;
        case 'cvtx_antrag_num':
            echo(get_post_meta($post->ID, 'cvtx_antrag_num', true));
            break;
        case 'cvtx_antrag_steller':
            echo(get_post_meta($post->ID, 'cvtx_antrag_steller', true));
            break;
        case "cvtx_antrag_top":
            $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);
            echo(get_the_title($top_id));
            break;
        case "cvtx_antrag_status":
            echo(($post->post_status == 'publish' ? '+ ' : ''));
            if ($file = cvtx_get_file($post, 'pdf', 'url')) {
                echo('<a href="'.$file.'">Download (pdf)</a>');
            }
            break;
            
        // Ä-Anträge
        case 'cvtx_aeantrag_ord':
            echo(cvtx_get_short($post));
            break;
        case 'cvtx_aeantrag_num':
            echo(get_post_meta($post->ID, 'cvtx_aeantrag_num', true));
            break;
        case 'cvtx_aeantrag_steller':
            echo(get_post_meta($post->ID, 'cvtx_aeantrag_steller', true));
            break;
        case "cvtx_aeantrag_verfahren":
            echo(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true));
            break;
        case "cvtx_aeantrag_antrag":
            $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
            echo(get_the_title($antrag_id));
            break;
        case "cvtx_aeantrag_status":
            echo(($post->post_status == 'publish' ? '+ ' : ''));
            $dir = wp_upload_dir();
            if (get_option('cvtx_aeantrag_pdf') && is_file($dir['basedir'].'/'.sanitize_title(get_the_title($post->ID)).'.pdf')) {
                echo('<a href="'.$dir['baseurl'].'/'.sanitize_title(get_the_title($post->ID)).'.pdf">Download (pdf)</a>');
            }
            break;
    }
}

add_filter('request', 'cvtx_order_lists');
function cvtx_order_lists($vars) {
    global $post_type;
    if (isset($vars['orderby'])) {
        // Anträge
        if ($vars['orderby'] == 'cvtx_antrag_ord' || ($post_type == 'cvtx_antrag' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_antrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_steller', 'orderby' => 'meta_value'));
        }
        // Änderungsanträge
        else if ($vars['orderby'] == 'cvtx_aeantrag_ord' || ($post_type == 'cvtx_aeantrag' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_steller', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_verfahren') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_verfahren', 'orderby' => 'meta_value'));
        }
        // TOPs
        else if ($vars['orderby'] == 'cvtx_top_ord' ||  ($post_type == 'cvtx_top' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_top_ord', 'orderby' => 'meta_value_num'));
        }
    }

    return $vars;
}



add_action('wp_insert_post', 'cvtx_insert_post', 10, 2);
function cvtx_insert_post($post_id, $post = null) {
    global $cvtx_types;

    if (in_array($post->post_type, array_keys($cvtx_types))) {
        // Loop through the POST data
        foreach ($cvtx_types[$post->post_type] as $key) {
            // Add sortable antrag_num-field
            if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_top']) && isset($_POST['cvtx_antrag_ord'])) {
                $_POST['cvtx_antrag_num'] = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true) * 100000
                                          + $_POST['cvtx_antrag_ord'];
            } else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
                preg_match('/([0-9]+)(.*)/', $_POST['cvtx_aeantrag_zeile'], $match);
                $top_id = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
                $_POST['cvtx_aeantrag_num'] = get_post_meta($top_id, 'cvtx_top_ord', true) * 100000
                                            + get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true)
                                            + $match[1]/1000000;
            }
            
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
        cvtx_create_pdf($post_id, $post);
    }
}


/**
 * Hide the quickedit function in admin area
 */
if (is_admin()) add_filter('post_row_actions', 'cvtx_hide_quick_edit', 10, 2);
function cvtx_hide_quick_edit($actions) {
    global $post, $cvtx_types;
    if(in_array($post->post_type, array_keys($cvtx_types))) {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

// replaces filter "the title" in order to generate custom titles for post-types "top", "antrag" and "aeantrag"
add_filter('the_title', 'cvtx_the_title', 1, 2);
function cvtx_the_title($before='', $title='') {
    if(is_numeric($title)) $post = &get_post($title);
    
    if(isset($post)) {
        $title = $post->post_title;
        
        // Antrag
        if($post->post_type == 'cvtx_antrag') {
            $title = cvtx_get_short($post).' '.$post->post_title;
        }
        // Änderungsantrag
        else if($post->post_type == 'cvtx_aeantrag') {
            $title = cvtx_get_short($post);
        }
        // Tagesordnungspunkt
        else if($post->post_type == 'cvtx_top') {
            $title = cvtx_get_short($post).': '.$post->post_title;
        }
    }
    
    return $title;
}


add_action('admin_menu', 'cvtx_config_page');
function cvtx_config_page() {
    if (function_exists('add_submenu_page')) {
        add_submenu_page('plugins.php', 'cvtx Antragstool', 'cvtx Antragstool', 'manage_options', 'cvtx-config', 'cvtx_conf');
    }
}

function cvtx_conf() {
    if (isset($_POST['submit'])) {
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?'));
        }
        
        // Formatierung des Änderungsantagskürzels
        if (empty($_POST['cvtx_aeantrag_format'])) {
            update_option('cvtx_aeantrag_format', '%antrag%-%zeile%');
        } else {
            update_option('cvtx_aeantrag_format', $_POST['cvtx_aeantrag_format']);
        }
        
        // PDF-Versionen für Änderungsanträge erzeugen?
        update_option('cvtx_aeantrag_pdf', isset($_POST['cvtx_aeantrag_pdf']) && $_POST['cvtx_aeantrag_pdf']);
        
        // LaTeX-Pfad
        if (empty($_POST['cvtx_pdflatex_cmd'])) {
            $ms[] = 'no_cvtx_pdflatex_cmd';
        } else {
            update_option('cvtx_pdflatex_cmd', $_POST['cvtx_pdflatex_cmd']);
        }
        
        // remove tex and/or log files?
        update_option('cvtx_drop_texfile', (isset($_POST['cvtx_drop_texfile']) ? intval($_POST['cvtx_drop_texfile']) : 2));
        update_option('cvtx_drop_logfile', (isset($_POST['cvtx_drop_logfile']) ? intval($_POST['cvtx_drop_logfile']) : 2));
        update_option('cvtx_anon_user', (isset($_POST['cvtx_anon_user']) ? $_POST['cvtx_anon_user'] : 1));
    }

    // load config page
    require('cvtx_conf.php');
}


/**
 * Erstellt ein PDF aus gespeicherten Anträgen
 */
//add_action('save_post', 'cvtx_create_pdf', 20, 2);
function cvtx_create_pdf($post_id, $post = null) {
    $pdflatex = get_option('cvtx_pdflatex_cmd');
    
    if (isset($post) && is_object($post) && !empty($pdflatex)) {
        $out_dir = wp_upload_dir();
        $tpl_dir = get_template_directory().'/latex';
    
        // prepare antrag
        if ($post->post_type == 'cvtx_antrag') {
            // file
            $file = $file = $out_dir['basedir'].'/';
            if ($short = cvtx_get_short($post)) {
                $file .= sanitize_title($short.'_'.$post->post_title);
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
            $file = $file = $out_dir['basedir'].'/';
            if ($short = cvtx_get_short($post)) {
                $file .= sanitize_title($short);
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
            // drop old pdf file
            if (is_file($file.'.pdf')) unlink($file.'.pdf');
            
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
    if ($short = cvtx_get_short($post)) {
        if ($post->post_type == 'cvtx_antrag') {
            $file = sanitize_title($short.'_'.$post->post_title);
        } else if ($post->post_type == 'cvtx_aeantrag') {
            $file = sanitize_title($short);
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
 * Print dropdown menu of all tops
 *
 * @param $selected post_id of selected top, otherwise null
 * @param $message message that will be displayed if no top exists
 */
function cvtx_dropdown_tops($selected = null, $message = '') {
    global $post;
    $post_bak = $post;
	$output = '';

    $tquery = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'meta_value_num', 'meta_key' => 'cvtx_top_ord', 'order' => 'ASC'));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_antrag_top">';
        while ($tquery->have_posts()) {
            $tquery->the_post();
            $output .= '<option value="'.get_the_ID().'"'.(get_the_ID() == $selected ? ' selected="selected"' : '').'>';
            $output .= get_the_title();
            $output .= '</option>';
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
 * Print dropdown menu of all anträge grouped by tops
 *
 * @param $selected post_id of selected antrag, otherwise null
 * @param $message message that will be displayed if no top exists
 */
function cvtx_dropdown_antraege($selected = null, $message = '') {
    global $post;
    $post_bak = $post;

    // Tagesordnungspunkte auflisten
    $tquery = new WP_Query(array('post_type' => 'cvtx_top',
                                 'orderby'   => 'meta_value_num',
                                 'meta_key'  => 'cvtx_top_ord',
                                 'order'     => 'ASC'));
    if ($tquery->have_posts()) {
        echo('<select name="cvtx_aeantrag_antrag">');
        while ($tquery->have_posts()) {
            $tquery->the_post();
            // print optgroup for top
            echo('<optgroup label="'.get_the_title().'">');
            
            // list anträge in top
            $aquery = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                         'orderby'    => 'meta_value_num',
                                         'meta_key'   => 'cvtx_antrag_ord',
                                         'order'      => 'ASC',
                                         'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                     'value'   => get_the_ID(),
                                                                     'compare' => '='))));
            if ($aquery->have_posts()) {
                while ($aquery->have_posts()) {
                    $aquery->the_post();
                    echo('<option value="'.get_the_ID().'"'.(get_the_ID() == $selected ? ' selected="selected"' : '').'>');
                    echo(get_the_title());
                    echo('</option>');
                }
            }
            
            echo('</optgroup>');
        }
        echo('</select>');
    }
    // print info message if no top exists
    else {
        echo($message);
    }
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
}

/**
 * Shortcode for including an antrag-formular inside a post or page. Usage: [submit_antrag]
 */
add_shortcode('submit_antrag', 'cvtx_submit_antrag');

/**
 * Method which evaluates input of antrags-creation form and saves it to the wordpress database
 */
function cvtx_submit_antrag() {

	// Request Variables, if already submitted, set corresponding variables to '' else
    $cvtx_antrag_title   = (!empty($_POST['cvtx_antrag_title']) ? trim($_POST['cvtx_antrag_title']) : '');
    $cvtx_antrag_steller = (!empty($_POST['cvtx_antrag_steller']) ? trim($_POST['cvtx_antrag_steller']) : '');
    $cvtx_antrag_top     = (!empty($_POST['cvtx_antrag_top']) ? trim($_POST['cvtx_antrag_top']) : '');
    $cvtx_antrag_text    = (!empty($_POST['cvtx_antrag_text']) ? trim($_POST['cvtx_antrag_text']) : '');
    $cvtx_antrag_grund   = (!empty($_POST['cvtx_antrag_grund']) ? trim($_POST['cvtx_antrag_grund']) : '');
    $cvtx_antrag_info    = (!empty($_POST['cvtx_antrag_info']) ? trim($_POST['cvtx_antrag_info']) : '');

	// Check whether the form has been submitted and the wp_nonce for security reasons
	if (isset($_POST['cvtx_form_create_antrag_submitted'] ) && wp_verify_nonce($_POST['cvtx_form_create_antrag_submitted'], 'cvtx_form_create_antrag') ){
  		
  		// check whether the required fields have been submitted
 		if($cvtx_antrag_title != '' && $cvtx_antrag_text != '' && $cvtx_antrag_steller != ''){
 			// create an array which holds all data about the antrag
			$antrag_data = array(
				'post_title' => $cvtx_antrag_title,
				'cvtx_antrag_steller' => $cvtx_antrag_steller,
				'cvtx_antrag_top' => $cvtx_antrag_top,
				'post_status' => 'pending',
				'post_author' => get_option('cvtx_anon_user'),
				'post_content' => $cvtx_antrag_text,
				'post_type' => 'cvtx_antrag',
				'cvtx_antrag_grund' => $cvtx_antrag_grund,
				'cvtx_antrag_info' => $cvtx_antrag_info);
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
			echo '<p id="message" class="error">Der Antrag konnte nicht gespeichert werden, weil einige benötigte Felder'. 
				 '(mit einem <span class="form-required" title="Dieses Feld wird benötigt">*<'.
				 '/span> bezeichnet) nicht ausgefüllt wurden.';
		}
	}
	// nothing has been submitted yet -> include creation form
	if(!isset($erstellt))
	    echo cvtx_create_antrag_form($cvtx_antrag_title, $cvtx_antrag_steller, $cvtx_antrag_text);
}

/**
 * Creates formular for creating antraege
 *
 * @param $cvtx_antrag_title title if it has been already submitted
 * @param $cvtx_antrag_steller antrag_steller if it have been already submitted
 * @param $cvtx_antrag_top top of antrag if it has already been submitted
 * @param $cvtx_antrag_text text of antrag if it has already been submitted
 * @param $cvtx_antrag_grund antragsbegruendung, if already submitted
 * @param $cvtx_antrag_info info if it has already been submitted
 */
function cvtx_create_antrag_form($cvtx_antrag_title = '', $cvtx_antrag_steller = '', $cvtx_antrag_top = null, $cvtx_antrag_text = '', $cvtx_antrag_grund = '', $cvtx_antrag_info = '') {
	$output  = '';
	
	// specify form
	$output .= '<form id="create_antrag_form" class="cvtx_antrag" method="post" action="">';
	
	// Wp-nonce for security reasons
	$output .= wp_nonce_field('cvtx_form_create_antrag','cvtx_form_create_antrag_submitted');
	
	// Antragstitel
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_title">Antragstitel: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_antrag_title" name="cvtx_antrag_title" value="'.$cvtx_antrag_title.'" size="100%" /><br>';
	$output .= '</div>';
	
	// TOP
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_top">TOP:</label><br/>';
	$output .= cvtx_dropdown_tops($cvtx_antrag_top,'Keine Tagesordnungspunkte angelegt').'<br/>';
	$output .= '</div>';
	
	// Antragsteller
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_steller">AntragstellerInnen: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_antrag_steller" name="cvtx_antrag_steller" value="'.$cvtx_antrag_steller.'" size="100%" /><br/>';
	$output .= '</div>';
	
	// Antragstext
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_text">Antragstext: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<textarea id="cvtx_antrag_text" name="cvtx_antrag_text" size="100%" cols="60" rows="20" />'.$cvtx_antrag_text.'</textarea><br/>';
	$output .= '</div>';

	// Antragsgrund
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_antrag_grund">Antragsbegründung:</label><br/>';
	$output .= '<textarea id="cvtx_antrag_grund" name="cvtx_antrag_grund" size="100%" cols="60" rows="10" />'.$cvtx_antrag_grund.'</textarea><br/>';
	$output .= '</div>';
	
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

	$cvtx_aeantrag_zeile = (!empty($_POST['cvtx_aeantrag_zeile']) ? trim($_POST['cvtx_aeantrag_zeile']) : '');
	$cvtx_aeantrag_steller = (!empty($_POST['cvtx_aeantrag_steller']) ? trim($_POST['cvtx_aeantrag_steller']) : '');
	$cvtx_aeantrag_text = (!empty($_POST['cvtx_aeantrag_text']) ? trim($_POST['cvtx_aeantrag_text']) : '');
	$cvtx_aeantrag_grund = (!empty($_POST['cvtx_aeantrag_grund']) ? trim($_POST['cvtx_aeantrag_grund']) : '');
	
	if(isset($_POST['cvtx_form_create_aeantrag_submitted']) && wp_verify_nonce($_POST['cvtx_form_create_aeantrag_submitted'], 'cvtx_form_create_aeantrag') && $cvtx_aeantrag_antrag != 0) {
		
		// check whethter the required fields have been set
		if($cvtx_aeantrag_zeile != '' && $cvtx_aeantrag_text != '' && $cvtx_aeantrag_steller != '' && $cvtx_aeantrag_antrag != '') {
			$aeantrag_data = array(
				'cvtx_aeantrag_steller' => $cvtx_aeantrag_steller,
				'cvtx_aeantrag_antrag' => $cvtx_aeantrag_antrag,
				'cvtx_aeantrag_grund' => $cvtx_aeantrag_grund,
				'cvtx_aeantrag_zeile' => $cvtx_aeantrag_zeile,
				'post_status' => 'pending',
				'post_author' => get_option('cvtx_anon_user'),
				'post_content' => $cvtx_aeantrag_text,
				'post_type' => 'cvtx_aeantrag',
			);
			// submit the post!
			if($antrag_id = wp_insert_post($aeantrag_data)) {
				echo '<p id="message" class="success">Der Änderungsantrag wurde erstellt und muss noch freigeschaltet werden.</p>';
				$erstellt = true;
			}
			else {
				echo '<p id="message" class="error">Der Änderungsantrag wurde nicht gespeichert. Bitte tanzen Sie um den Tisch und probieren sie es dann mit einer anderen Computer-Stellung noch einmal.';
			}
		}
		else {
			echo '<p id="message" class="error">Der Änderungsantrag konnte nicht gespeichert werden, weil einige benötigte Felder'.
				 '(mit einem <span class="form-required" title="Dieses Feld wird benötigt">*</span> be'.
				 'zeichnet) nicht ausgefüllt wurden.';
		}
	}
	if(!isset($erstellt))
		echo cvtx_create_aeantrag_form($cvtx_aeantrag_zeile,$cvtx_aeantrag_text,$cvtx_aeantrag_steller,$cvtx_aeantrag_grund,$cvtx_aeantrag_antrag);
}


/**
 * Creates formular for creating ae_antraege
 *
 * @param $cvtx_aeantrag_zeile zeile if it has been already submitted
 * @param $cvtx_aeantrag_text text of aeantrag if it has already been submitted
 * @param $cvtx_aeantrag_steller aeantrag_steller if it have been already submitted
 * @param $cvtx_aeantrag_grund aeantragsbegruendung, if already submitted
 * @param $cvtx_aeantrag_antrag antrag to which the ae_antrag is dedicated
 */
function cvtx_create_aeantrag_form($cvtx_aeantrag_zeile = '', $cvtx_aeantrag_text = '', $cvtx_aeantrag_steller = '', $cvtx_aeantrag_grund = '', $cvtx_aeantrag_antrag = 0) {
	$output  = '';
	
	// specify form
	$output .= '<form id="create_aeantrag_form" class="cvtx_antrag" method="post" action="">';
	
	// Wp-nonce for security reasons
	$output .= wp_nonce_field('cvtx_form_create_aeantrag','cvtx_form_create_aeantrag_submitted');
	
	// Antragstitel
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_zeile">Zeile: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_aeantrag_zeile" name="cvtx_aeantrag_zeile" value="'.$cvtx_aeantrag_zeile.'" size="4" /><br>';
	$output .= '</div>';
		
	// Antragsteller
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_steller">AntragstellerInnen: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<input type="text" id="cvtx_aeantrag_steller" name="cvtx_aeantrag_steller" value="'.$cvtx_aeantrag_steller.'" size="80" /><br/>';
	$output .= '</div>';
	
	// Antrag
	$output .= '<input type="hidden" id="cvtx_aeantrag_antrag" name="cvtx_aeantrag_antrag" value="'.$cvtx_aeantrag_antrag.'"/>';
	
	// Antragstext
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_text">Antragstext: <span class="form-required" title="Dieses Feld wird benötigt">*</span></label><br/>';
	$output .= '<textarea id="cvtx_aeantrag_text" name="cvtx_aeantrag_text" size="100%" cols="60" rows="10" />'.$cvtx_aeantrag_text.'</textarea><br/>';
	$output .= '</div>';

	// Antragsgrund
	$output .= '<div class="form-item">';
	$output .= '<label for="cvtx_aeantrag_grund">Antragsbegründung:</label><br/>';
	$output .= '<textarea id="cvtx_aeantrag_grund" name="cvtx_aeantrag_grund" size="100%" cols="60" rows="5" />'.$cvtx_aeantrag_grund.'</textarea><br/>';
	$output .= '</div>';
	
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
