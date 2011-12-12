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


// define post types
$cvtx_types = array('cvtx_top'      => array('cvtx_top_ord',
                                             'cvtx_top_short'),
                    'cvtx_antrag'   => array('cvtx_antrag_ord',
                                             'cvtx_antrag_num',
                                             'cvtx_antrag_top',
                                             'cvtx_antrag_steller',
                                             'cvtx_antrag_steller_short',
                                             'cvtx_antrag_email',
                                             'cvtx_antrag_phone',
                                             'cvtx_antrag_grund',
                                             'cvtx_antrag_info'),
                    'cvtx_aeantrag' => array('cvtx_aeantrag_zeile',
                                             'cvtx_aeantrag_num',
                                             'cvtx_aeantrag_antrag',
                                             'cvtx_aeantrag_steller',
                                             'cvtx_aeantrag_steller_short',
                                             'cvtx_aeantrag_email',
                                             'cvtx_aeantrag_phone',
                                             'cvtx_aeantrag_grund',
                                             'cvtx_aeantrag_verfahren',
                                             'cvtx_aeantrag_detail',
                                             'cvtx_aeantrag_info'));


/* add custom meta boxes */

if (is_admin()) add_action('add_meta_boxes', 'cvtx_add_meta_boxes');
function cvtx_add_meta_boxes() {
    // Tagesordnungspunkte
    add_meta_box('cvtx_top_meta', 'Metainformationen', 'cvtx_top_meta', 'cvtx_top', 'side', 'high');
    
    // Anträge
    add_meta_box('cvtx_antrag_meta', 'Metainformationen', 'cvtx_antrag_meta', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_steller', 'AntragstellerIn(nen)', 'cvtx_antrag_steller', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_grund', 'Begründung', 'cvtx_antrag_grund', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_info', 'Weitere Informationen', 'cvtx_antrag_info', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_pdf', 'PDF', 'cvtx_metabox_pdf', 'cvtx_antrag', 'side', 'low');
    
    // Änderungsanträge
    add_meta_box('cvtx_aeantrag_meta', 'Metainformationen', 'cvtx_aeantrag_meta', 'cvtx_aeantrag', 'side', 'high');
    add_meta_box('cvtx_aeantrag_steller', 'AntragstellerIn(nen)', 'cvtx_aeantrag_steller', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_grund', 'Begründung', 'cvtx_aeantrag_grund', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_verfahren', 'Verfahren', 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_info', 'Weitere Informationen', 'cvtx_aeantrag_info', 'cvtx_aeantrag', 'normal', 'low');
    // show/hide pdf-box for of aeantrag
    if (get_option('cvtx_aeantrag_pdf')) {
        add_meta_box('cvtx_aeantrag_pdf', 'PDF', 'cvtx_metabox_pdf', 'cvtx_aeantrag', 'side', 'low');
    }
}


/* Tagesordnungspunkte */

// Metainformationen (TOP-Nummer und Kürzel)
function cvtx_top_meta() {
    global $post;
    echo('<label for="cvtx_top_ord_field">TOP-Nummer:</label><br />');
    echo('<input name="cvtx_top_ord" id="cvtx_top_ord_field" type="text" maxlength="4" value="'.get_post_meta($post->ID, 'cvtx_top_ord', true).'" />');
    echo('<br />');
    echo('<label for="cvtx_top_short_field">Kürzel:</label><br />');
    echo('<input name="cvtx_top_short" id="cvtx_top_short_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_top_short', true).'" />');
    echo('<p id="message" class="error">');
    echo('<span id="unique_error_cvtx_top_ord" class="cvtx_unique_error">Es existiert bereits ein TOP mit dieser Nummer.</span> ');
    echo('<span id="unique_error_cvtx_top_short" class="cvtx_unique_error">Es existiert bereits ein TOP mit diesem Kürzel.</span> ');
    echo('<span id="empty_error_cvtx_top_ord" class="cvtx_empty_error">Bitte TOP-Nummer vergeben.</span> ');
    echo('<span id="empty_error_cvtx_top_short" class="cvtx_empty_error">Bitte Kürzel für den TOP vergeben.</span> ');
    echo('</p>');
}


/* Anträge */

// Metainformationen (Antragsnummer, TOP)
function cvtx_antrag_meta() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);    
    
    echo('<label for="cvtx_antrag_top_select">Tagesordnungspunkt:</label><br />');
    echo(cvtx_dropdown_tops($top_id, 'Keine Tagesordnungspunkte angelegt.'));
    echo('<br />');
    echo('<label for="cvtx_antrag_ord_field">Antragsnummer:</label><br />');
    echo('<label id="cvtx_top_kuerzel">'.get_post_meta($top_id, 'cvtx_top_short', true).'</label>-');
    echo('<input name="cvtx_antrag_ord" id="cvtx_antrag_ord_field" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_antrag_ord', true).'" />');
    echo('<p id="message" class="error">');
    echo('<span id="unique_error_cvtx_antrag_ord" class="cvtx_unique_error">Es liegt bereits ein Antrag mit identischer Antragsnummer vor.</span> ');
    echo('<span id="empty_error_cvtx_antrag_ord" class="cvtx_empty_error">Bitte Antragsnummer vergeben.</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_antrag_steller() {
    global $post;
    echo('<label for="cvtx_antrag_steller_short">Kurzfassung:</label> ');
    echo('<input type="text" id="cvtx_antrag_steller_short" name="cvtx_antrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_antrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_antrag_steller">'.get_post_meta($post->ID, 'cvtx_antrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_antrag_email">Kontakt (E-Mail):</label> ');
    echo('<input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" value="'.get_post_meta($post->ID, 'cvtx_antrag_email', true).'" /> ');
    echo('<label for="cvtx_antrag_phone">Kontakt (Telefon):</label> ');
    echo('<input type="text" id="cvtx_antrag_phone" name="cvtx_antrag_phone" value="'.get_post_meta($post->ID, 'cvtx_antrag_phone', true).'" />');
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

// Metainformationen (Ä-Antragsnummer / Zeile, Antrag)
function cvtx_aeantrag_meta() {
    global $post;
    $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);

    echo('<label for="cvtx_aeantrag_antrag_select">Antrag:</label><br />');
    echo(cvtx_dropdown_antraege($antrag_id, 'Keine Tagesordnungspunkte angelegt.'));
    echo('<br />');
    echo('<label for="cvtx_aeantrag_zeile_field">Zeile:</label><br />');
    echo('<input name="cvtx_aeantrag_zeile" id="cvtx_aeantrag_zeile_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
    echo('<p id="message" class="error">');
    echo('<span id="unique_error_cvtx_aeantrag_zeile" class="cvtx_unique_error">Es liegt bereits ein Änderungsantrag mit identischer Zeilenangabe vor.</span> ');
    echo('<span id="empty_error_cvtx_aeantrag_zeile" class="cvtx_empty_error">Bitte Zeile für den Änderungsantrag angeben.</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_aeantrag_steller() {
    global $post;
    echo('<label for="cvtx_aeantrag_steller_short">Kurzfassung:</label> ');
    echo('<input type="text" id="cvtx_aeantrag_steller_short" name="cvtx_aeantrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_steller">'.get_post_meta($post->ID, 'cvtx_aeantrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_aeantrag_email">Kontakt (E-Mail):</label> ');
    echo('<input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_email', true).'" /> ');
    echo('<label for="cvtx_aeantrag_phone">Kontakt (Telefon):</label> ');
    echo('<input type="text" id="cvtx_aeantrag_phone" name="cvtx_aeantrag_phone" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_phone', true).'" />');
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

if (is_admin()) add_filter('manage_edit-cvtx_top_columns', 'cvtx_top_columns');
function cvtx_top_columns($columns) {
	$columns = array('cb'              => '<input type="checkbox" />',
                     'title'           => 'Tagesordnungspunkt',
                     'cvtx_top_short'  => 'Kürzel',
                     'cvtx_top_status' => '');
	return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_antrag_columns', 'cvtx_antrag_columns');
function cvtx_antrag_columns($columns) {
    $columns = array('cb'                  => '<input type="checkbox" />',
                     'title'               => 'Antragstitel',
                     'cvtx_antrag_steller' => 'AntragstellerIn(nen)',
                     'cvtx_antrag_top'     => 'Tagesordnungspunkt',
                     'cvtx_antrag_status'  => '');
	return $columns;
}

// Register the column as sortable
if (is_admin()) add_filter('manage_edit-cvtx_antrag_sortable_columns', 'cvtx_register_sortable_antrag');
function cvtx_register_sortable_antrag($columns) {
    $columns['cvtx_antrag_steller'] = 'cvtx_antrag_steller';
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_aeantrag_columns', 'cvtx_aeantrag_columns');
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
if (is_admin()) add_filter('manage_edit-cvtx_aeantrag_sortable_columns', 'cvtx_register_sortable_aeantrag');
function cvtx_register_sortable_aeantrag($columns) {
    $columns['cvtx_aeantrag_steller']   = 'cvtx_aeantrag_steller';
    $columns['cvtx_aeantrag_verfahren'] = 'cvtx_aeantrag_verfahren';
    return $columns;
}

if (is_admin()) add_action('manage_posts_custom_column', 'cvtx_format_lists');
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
            echo(get_post_meta($post->ID, 'cvtx_antrag_steller_short', true));
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
            echo(get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true));
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
            if (get_option('cvtx_aeantrag_pdf') && $file = cvtx_get_file($post, 'pdf', 'url')) {
                echo('<a href="'.$file.'">Download (pdf)</a>');
            }
            break;
    }
}

if (is_admin()) add_filter('request', 'cvtx_order_lists');
function cvtx_order_lists($vars) {
    global $post_type;
    if (isset($vars['orderby'])) {
        // Anträge
        if ($vars['orderby'] == 'cvtx_antrag_ord' || ($post_type == 'cvtx_antrag' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_antrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_steller_short', 'orderby' => 'meta_value'));
        }
        // Änderungsanträge
        else if ($vars['orderby'] == 'cvtx_aeantrag_ord' || ($post_type == 'cvtx_aeantrag' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_steller_short', 'orderby' => 'meta_value'));
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
        // Add sortable antrag_num-field
        if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_top'])) {
            $_POST['cvtx_antrag_num'] = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true) * 100000
                                      + (isset($_POST['cvtx_antrag_ord']) ? $_POST['cvtx_antrag_ord'] : 0);
        } else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
            preg_match('/([0-9]+)(.*)/', $_POST['cvtx_aeantrag_zeile'], $match);
            $top_id = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
            $_POST['cvtx_aeantrag_num'] = get_post_meta($top_id, 'cvtx_top_ord', true) * 100000
                                        + get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true)
                                        + $match[1]/1000000;
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


if (is_admin()) add_action('admin_menu', 'cvtx_config_page');
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
        
        // wordpress anonymous user
        update_option('cvtx_anon_user', (isset($_POST['cvtx_anon_user']) ? $_POST['cvtx_anon_user'] : 1));
        
        // recpatcha settings
        update_option('cvtx_use_recaptcha', isset($_POST['cvtx_use_recaptcha']) && $_POST['cvtx_use_recaptcha']);
        update_option('cvtx_recaptcha_publickey', $_POST['cvtx_recaptcha_publickey']);
        update_option('cvtx_recaptcha_privatekey', $_POST['cvtx_recaptcha_privatekey']);
        
        // mail settings
        update_option('cvtx_send_from_email', stripslashes($_POST['cvtx_send_from_email']));
        update_option('cvtx_send_rcpt_email', stripslashes($_POST['cvtx_send_rcpt_email']));
        update_option('cvtx_send_create_antrag_owner', isset($_POST['cvtx_send_create_antrag_owner']) && $_POST['cvtx_send_create_antrag_owner']);
        update_option('cvtx_send_create_antrag_admin', isset($_POST['cvtx_send_create_antrag_admin']) && $_POST['cvtx_send_create_antrag_admin']);
        update_option('cvtx_send_create_aeantrag_owner', isset($_POST['cvtx_send_create_aeantrag_owner']) && $_POST['cvtx_send_create_aeantrag_owner']);
        update_option('cvtx_send_create_aeantrag_admin', isset($_POST['cvtx_send_create_aeantrag_admin']) && $_POST['cvtx_send_create_aeantrag_admin']);
        update_option('cvtx_send_create_antrag_owner_subject', $_POST['cvtx_send_create_antrag_owner_subject']);
        update_option('cvtx_send_create_antrag_owner_body', $_POST['cvtx_send_create_antrag_owner_body']);
        update_option('cvtx_send_create_antrag_admin_subject', $_POST['cvtx_send_create_antrag_admin_subject']);
        update_option('cvtx_send_create_antrag_admin_body', $_POST['cvtx_send_create_antrag_admin_body']);
        update_option('cvtx_send_create_aeantrag_owner_subject', $_POST['cvtx_send_create_aeantrag_owner_subject']);
        update_option('cvtx_send_create_aeantrag_owner_body', $_POST['cvtx_send_create_aeantrag_owner_body']);
        update_option('cvtx_send_create_aeantrag_admin_subject', $_POST['cvtx_send_create_aeantrag_admin_subject']);
        update_option('cvtx_send_create_aeantrag_admin_body', $_POST['cvtx_send_create_aeantrag_admin_body']);
    }


    // print config page
    $aentrag_format    = get_option('cvtx_aeantrag_format');
    $aentrag_pdf       = get_option('cvtx_aeantrag_pdf');
    $pdflatex_cmd      = get_option('cvtx_pdflatex_cmd');
    $drop_texfile      = get_option('cvtx_drop_texfile');
    $drop_logfile      = get_option('cvtx_drop_logfile');
    $anon_user         = get_option('cvtx_anon_user');
    // mail settings
    $send_from_email   = get_option('cvtx_send_from_email');
    $send_rcpt_email   = get_option('cvtx_send_rcpt_email');
    $sendantragowner   = get_option('cvtx_send_create_antrag_owner');
    $sendantragadmin   = get_option('cvtx_send_create_antrag_admin');
    $sendaeantragowner = get_option('cvtx_send_create_aeantrag_owner');
    $sendaeantragadmin = get_option('cvtx_send_create_aeantrag_admin');
    // mail design
    $sendantragowner_subject   = get_option('cvtx_send_create_antrag_owner_subject');
    $sendantragowner_body      = get_option('cvtx_send_create_antrag_owner_body');
    $sendantragadmin_subject   = get_option('cvtx_send_create_antrag_admin_subject');
    $sendantragadmin_body      = get_option('cvtx_send_create_antrag_admin_body');
    $sendaeantragowner_subject = get_option('cvtx_send_create_aeantrag_owner_subject');
    $sendaeantragowner_body    = get_option('cvtx_send_create_aeantrag_owner_body');
    $sendaeantragadmin_subject = get_option('cvtx_send_create_aeantrag_admin_subject');
    $sendaeantragadmin_body    = get_option('cvtx_send_create_aeantrag_admin_body');
	// reCaptcha settings
	$use_recpatcha 			= get_option('cvtx_use_recaptcha');
	$recaptcha_publickey 	= get_option('cvtx_recaptcha_publickey');
	$recaptcha_privatekey   = get_option('cvtx_recaptcha_privatekey');

    if (isset($ms) && count($ms) > 0) {
        echo('<ul>');
        foreach ($ms as $msg) {
            if ($msg == 'no_cvtx_pdflatex_cmd') {
                echo('<li>Kein Pfad angegeben. LaTeX kann so nicht funktionieren, Mensch.</li>');
            }
        }
        echo('</ul>');    
    }

    echo('<div class="wrap">');
    echo('<div id="icon-options-general" class="icon32"><br /></div>');
    echo('<h2>cvtx Konfiguration</h2>');

    echo('<h2 class="nav-tab-wrapper" id="cvtx_navi">');
    	echo('<a class="nav-tab cvtx_tool" href="#cvtx_tool">Antragstool</a>');
    	echo('<a class="nav-tab cvtx_latex" href="#cvtx_latex">LaTeX</a>');
    	echo('<a class="nav-tab cvtx_mail" href="#cvtx_mail">Benachrichtigungen</a>');
    echo('</h2>');
    
    echo('<form action="" method="post" id="cvtx-conf">');

	echo('<ul id="cvtx_options">');
	echo('<li id="cvtx_tool" class="active">'); 
    	
    	echo('<table class="form-table">');
			echo('<tr valign="top">');
    			echo('<th scope="row">');
		    		echo('<label for="cvtx_aeantrag_format">Kurzbezeichnung für Änderungsanträge</label>');
		    	echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_aeantrag_format" name="cvtx_aeantrag_format" type="text" value="'.($aentrag_format ? $aentrag_format : '%antrag%-%zeile%').'" /> ');
			    	echo('<span class="description">(%antrag%, %zeile%)</span>');
			    echo('</td>');
    		echo('</tr>');

			echo('<tr valign="top">');
    			echo('<th scope="row">');
    				echo('<label for="cvtx_aeantrag_pdf">PDF</label>');
    			echo('</th>');
    			echo('<td>');
			    	echo('<input id="cvtx_aeantrag_pdf" name="cvtx_aeantrag_pdf" type="checkbox" '.($aentrag_pdf ? 'checked="checked"' : '').'" /> ');
    				echo('<label for="cvtx_aeantrag_pdf">PDF-Versionen für Änderungsanträge erzeugen</label>');
    			echo('</td>');
    		echo('</tr>');

			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_anon_user">Anonymous Nutzer</label>');
				echo('</th>');
				echo('<td>');
				    echo('<select name="cvtx_anon_user" id="cvtx_anon_user">');
				    foreach (get_users() as $user) {
	        			echo('<option'.($user->ID == $anon_user ? ' selected="selected" ' : '').' value="'.$user->ID.'">'.$user->user_login.'</option>');
	    			}
		    		echo('</select>');
		    		echo(' <span class="description">Wordpress-Nutzer, dem alle anonym eingetragenen Anträge und Änderungsanträge zugeordnet werden.</span>');
    			echo('</td>');
    		echo('</tr>');
    	echo('</table>');
    	
    	echo('<h3>reCaptcha</h3>');
    	
    	echo('<table class="form-table">');
    		echo('<tr valign="top">');
    			echo('<th scope="row"');
    				echo('<label for="cvtx_use_recaptcha">reCaptcha benutzen</label>');
    			echo('</th>');
   				echo('<td>');
    				echo('<input id="cvtx_use_recaptcha" name="cvtx_use_recaptcha" type="checkbox" '.($use_recpatcha ? 'checked="checked"' : ''). '" /> ');
			    	echo('<span class="description">Um die Eingabe von Änderungsanträgen und Anträgen Spam-sicher zu machen, wird der Einsatz von reCaptcha empfohlen.</span>');
    			echo('</td>');
    		echo('</tr>');
    		
   			echo('<tr valign="top">');
   				echo('<th scope="row">');
   					echo('<label for="cvtx_recaptcha_publickey">Öffentlicher reCaptcha-Schlüssel</label>');
   				echo('</th>');
   				echo('<td>');
   					echo('<input id="cvtx_recpatcha_publickey" name="cvtx_recaptcha_publickey" type="text" value="'.$recaptcha_publickey.'" /> ');
   					echo('<span class="description">Ein Schlüsselpaar erhältst du <a href="http://www.google.com/recaptcha/whyrecaptcha">hier</a>.</span>');
   				echo('</td>');
   			echo('</tr>');

   			echo('<tr valign="top">');
   				echo('<th scope="row">');
   					echo('<label for="cvtx_recaptcha_privatekey">Privater reCaptcha-Schlüssel</label>');
   				echo('</th>');
   				echo('<td>');
   					echo('<input id="cvtx_recpatcha_privatekey" name="cvtx_recaptcha_privatekey" type="text" value="'.$recaptcha_privatekey.'" /> ');
   				echo('</td>');
   			echo('</tr>');

    	echo('</table>');
    	
	echo('</li>');
	
	echo('<li id="cvtx_latex">');

    	echo('<table class="form-table">');
			echo('<tr valign="top">');
    			echo('<th scope="row">');
				    echo('<label for="cvtx_pdflatex_cmd">LaTeX-Pfad</label>');
				echo('</th>');
				echo('<td>');
				    echo('<input id="cvtx_pdflatex_cmd" name="cvtx_pdflatex_cmd" type="text" value="'.$pdflatex_cmd.'" /> ');
				    echo('<span class="description">Systempfad zur pdflatex-Anwendung</span>');
				echo('</td>');
			echo('</tr>');

			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label>Erzeugte Tex-Files löschen</label>');
				echo('</th>');
				echo('<td>');
					echo('<fieldset>');
				   		echo('<input id="cvtx_drop_texfile_yes" name="cvtx_drop_texfile" type="radio" value="1" '.($drop_texfile == 1 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_texfile_yes">immer</label> ');
    					echo('<input id="cvtx_drop_texfile_if" name="cvtx_drop_texfile" type="radio" value="2" '.($drop_texfile != 1 && $drop_texfile != 3 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_texfile_if">nur wenn fehlerfrei</label> ');
    					echo('<input id="cvtx_drop_texfile_no" name="cvtx_drop_texfile" type="radio" value="3" '.($drop_texfile == 3 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_texfile_no">nie</label>');
    				echo('</fieldset>');
    			echo('</td>');
    		echo('</tr>');
    		
    		echo('<tr valign=top">');
    			echo('<th scope="row">');
    				echo('<label>Erzeugte log-Files löschen</label>');
				echo('</th>');
				echo('<td>');
					echo('<fieldset>');
				    	echo('<input id="cvtx_drop_logfile_yes" name="cvtx_drop_logfile" type="radio" value="1" '.($drop_logfile == 1 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_logfile_yes">immer</label> ');
    					echo('<input id="cvtx_drop_logfile_if" name="cvtx_drop_logfile" type="radio" value="2" '.($drop_logfile != 1 && $drop_logfile != 3 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_logfile_if">nur wenn fehlerfrei</label> ');
    					echo('<input id="cvtx_drop_logfile_no" name="cvtx_drop_logfile" type="radio" value="3" '.($drop_logfile == 3 ? 'checked="checked"' : '').'" /> ');
    					echo('<label for="cvtx_drop_logfile_no" value="">nie</label>');
    				echo('</fieldset>');
    			echo('</td>');
    		echo('</tr>');
    	echo('</table>');
    	
  	echo('</li>');
 
 	echo('<li id="cvtx_mail">');

		echo('<table class="form-table">');
			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_from_email">Absender-Adresse</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_from_email" name="cvtx_send_from_email" type="text" value="'.stripslashes(htmlspecialchars($send_from_email ? $send_from_email : get_bloginfo('admin_email'))).'" />');
			    	echo(' <span class="description">E-Mail-Adresse, die als Absender für Benachrichtigungen verwendet werden soll</span>');
			    echo('</td>');
			echo('</tr>');
			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_rcpt_email">E-Mail-Adresse</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_rcpt_email" name="cvtx_send_rcpt_email" type="text" value="'.stripslashes(htmlspecialchars($send_rcpt_email ? $send_rcpt_email : get_bloginfo('admin_email'))).'" />');
			    	echo(' <span class="description">E-Mail-Adresse, an welche Benachrichtigungen über neu erstellte Anträge gesendet werden</span>');
			    echo('</td>');
			echo('</tr>');
		echo('</table>');
			
		echo('<h4>Neuer Antrag erstellt</h4>');
    	echo('<span class="description">Mögliche Felder: %top%, %top_kuerzel%, %titel%, %antragsteller%, %antragsteller_kurz%, %antragstext%, %begruendung%.</span>');
    	
    	echo('<table class="form-table">');	
			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_owner">E-Mail-Bestätigung</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_antrag_owner" name="cvtx_send_create_antrag_owner" type="checkbox" '.($sendantragowner ? 'checked="checked"' : '').'" /> ');
    				echo('<span class="description">Dem Antragsteller wird eine E-Mail zur Bestätigung geschickt</label>');
    			echo('</td>');
    		echo('</tr>');
    		
    		echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_owner_subject">Betreff</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_antrag_owner_subject" name="cvtx_send_create_antrag_owner_subject" size="58" type="text" value="'
			             .($sendantragowner_subject ? $sendantragowner_subject : 'Antrag eingereicht „%titel%“')
         				 .'" />');
				echo('</td>');
			echo('</tr>');
			
			echo('<tr valign=top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_owner_body">Nachricht</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_owner_body" name="cvtx_send_create_antrag_owner_body">'
						.($sendantragowner_body ? $sendantragowner_body : "Hej,\n\n"
                                                                         ."dein Antrag „%titel%“ zum %top% wurde erfolgreich eingereicht. "
                                                                         ."Bevor er auf der Website zu sehen sein wird, muss er "
                                                                         ."erst noch eine Antragsnummer bekommen und dann "
                                                                         ."freigeschaltet werden.\n\n"
                                                                         ."Zur Bestätigung hier nochmal deine Angaben:\n\n"
                                                                         ."%top%\n\n"
                                                                         ."%titel%\n\n"
                                                                         ."%antragstext%\n\n"
                                                                         ."Begründung:\n%begruendung%\n\n"
                                                                         ."AntragstellerInnen:\n%antragsteller%\n")
         				.'</textarea>');
         		echo('</td>');
			echo('</tr>');
			
			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_admin">Admin-Information</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_antrag_admin" name="cvtx_send_create_antrag_admin" type="checkbox" '.($sendantragadmin ? 'checked="checked"' : '').'" /> ');
    				echo('<label for="cvtx_send_create_antrag_admin">Administrator eine E-Mail zur Information schicken</label>');
    			echo('</td>');
    		echo('</tr>');
    		
    		echo('<tr valign="top">');
    			echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_admin_subject">Betreff</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_antrag_admin_subject" name="cvtx_send_create_antrag_admin_subject" size="58" type="text" value="'
    	    			 .($sendantragadmin_subject ? $sendantragadmin_subject : 'Neuer Antrag eingereicht (%titel%)')
        	 			 .'" />');
        	 	echo('</td>');
        	 echo('</tr>');
 	       	 
			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_antrag_admin_body">Nachricht</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_admin_body" name="cvtx_send_create_antrag_admin_body">'
        	 			.($sendantragadmin_body ? $sendantragadmin_body : "Hej,\n\n"
                                                                         ."es wurde ein neuer Antrag zu %top% eingereicht. Bitte prüfen und veröffentlichen!\n\n"
                                                                         .home_url('/wp-admin')."\n\n"
                                                                         ."%top%\n\n"
                                                                         ."%titel%\n\n"
                                                                         ."%antragstext%\n\n"
                                                                         ."Begründung:\n%begruendung%\n\n"
                                                                         ."AntragstellerInnen:\n%antragsteller%\n")
         				 .'</textarea>');
         		echo('</td>');
         	echo('</tr>');
		echo('</table>');
         	
	    echo('<h4>Neuer Änderungsantrag erstellt</h4>');
    	echo('<span class="description">Mögliche Felder: %top%, %top_kuerzel%, %antrag%, %antrag_kuerzel%, %zeile%, %antragsteller%, %antragsteller_kurz%, %antragstext%, %begruendung%.</span>');
	    	
	    echo('<table class="form-table">');
        	echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_aeantrag_owner">Antragsteller-Mail</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_aeantrag_owner" name="cvtx_send_create_aeantrag_owner" type="checkbox" '.($sendaeantragowner ? 'checked="checked"' : '').'" /> ');
    				echo('<label for="cvtx_send_create_aeantrag_owner">Antragsteller eine E-Mail zur Bestätigung schicken</label>');
    			echo('</td>');
    		echo('</tr>');
    	
    		echo('<tr valign="top">');
    			echo('<th scope="row">');
    				echo('<label for="cvtx_send_create_aeantrag_owner_subject">Betreff</label>');
    			echo('</th>');
    			echo('<td>');
			    	echo('<input id="cvtx_send_create_aeantrag_owner_subject" name="cvtx_send_create_aeantrag_owner_subject" size="58" type="text" value="'
    	     			 .($sendaeantragowner_subject ? $sendaeantragowner_subject : 'Änderungsantrag zu %antrag_kuerzel% (Zeile %zeile%) eingereicht')
    	     			 .'" />');
    	    	echo('</td>');
    	    echo('</tr>');
    	     
    	    echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_aeantrag_owner_body">Nachricht</label>');
				echo('</th>');
				echo('<td>');
				echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_owner_body" name="cvtx_send_create_aeantrag_owner_body">'
        	 		 .($sendaeantragowner_body ? $sendaeantragowner_body : "Hej,\n\n"
                                                                          ."dein Änderungsantrag zum Antrag %antrag% wurde erfolgreich eingereicht. "
                                                                          ."Bevor er auf der Website zu sehen sein wird, muss er "
                                                                          ."erst noch eine Antragsnummer bekommen und dann "
                                                                          ."freigeschaltet werden.\n\n"
                                                                          ."Zur Bestätigung hier nochmal deine Angaben:\n\n"
                                                                          ."Antrag:\n%antrag%\n\n"
                                                                          ."Zeile:\n%zeile%\n\n"
                                                                          ."%antragstext%\n\n"
                                                                          ."Begründung:\n%begruendung%\n\n"
                                                                          ."AntragstellerInnen:\n%antragsteller%\n")
        	 		 .'</textarea>');
        		echo('</td>');
        	echo('</tr>');

			echo('<tr valign="top">');
				echo('<th scope="row">');
					echo('<label for="cvtx_send_create_aeantrag_admin">Admin-Information</label>');
				echo('</th>');
				echo('<td>');
			    	echo('<input id="cvtx_send_create_aeantrag_admin" name="cvtx_send_create_aeantrag_admin" type="checkbox" '.($sendaeantragadmin ? 'checked="checked"' : '').'" /> ');
   					echo('<label for="cvtx_send_create_aeantrag_admin">Administrator eine E-Mail zur Information schicken</label>');
   				echo('</td>');
   			echo('</tr>');
   			
   			echo('<tr valign="top">');
   				echo('<th scope="row">');
   					echo('<label for="cvtx_send_create_aeantrag_admin_subject">Betreff</label>');
   				echo('</th>');
   				echo('<td>');
					echo('<input id="cvtx_send_create_aeantrag_admin_subject" name="cvtx_send_create_aeantrag_admin_subject" size="58" type="text" value="'
        	 			 .($sendaeantragadmin_subject ? $sendaeantragadmin_subject : 'Neuer Änderungsantrag zu %antrag_kuerzel% (Zeile %zeile%) erstellt')
        	 			 .'" />');
    			echo('</td>');
    		echo('</tr>');
    		
    		echo('<tr valign="top">');
    			echo('<th scope="row">');
    				echo('<label for="cvtx_send_create_aeantrag_admin_body">Nachricht</label>');
    			echo('</th>');
    			echo('<td>');
			    	echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_admin_body" name="cvtx_send_create_aeantrag_admin_body">'
    	    			 .($sendaeantragadmin_body ? $sendaeantragadmin_body : "Hej,\n\n"
                                                                              ."es wurde ein neuer Änderungsantrag zum Antrag %antrag% eingereicht. Bitte prüfen und veröffentlichen!\n\n"
                                                                              .home_url('/wp-admin')."\n\n"
                                                                              ."Antrag:\n%antrag%\n\n"
                                                                              ."Zeile:\n%zeile%\n\n"
                                                                              ."%antragstext%\n\n"
                                                                              ."Begründung:\n%begruendung%\n\n"
                                                                              ."AntragstellerInnen:\n%antragsteller%\n")
    	    			 .'</textarea>');
    	    	echo('</td>');
    	    echo('</tr>');
    	echo('</table>');
    	
    echo('</li>');
    echo('</ul>');

    echo('<p class="submit"><input type="submit" name="submit" value="Einstellungen speichern" /></p>');
    echo('</form>');
    echo('</div>');
}

/**
 * Add Cvtx-Script and Styles to Admin Pages...
*/
add_action('admin_enqueue_scripts','cvtx_admin_script');
function cvtx_admin_script() {
	wp_enqueue_style('cvtx_style', plugins_url('/cvtx_style.css', __FILE__));
	wp_enqueue_script('cvtx_script', plugins_url('/cvtx_script.js', __FILE__));
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
    $str = str_replace(array('ä',  'ö',  'ü',  'ß',  'Ä',  'Ö',  'Ü'),
                       array('ae', 'oe', 'ue', 'ss', 'Ae', 'Oe', 'Ue'), $str);
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


if (is_admin()) add_filter('post_row_actions', 'cvtx_hide_quick_edit', 10, 2);
/**
 * Hide the quickedit function in admin area
 */
function cvtx_hide_quick_edit($actions) {
    global $post, $cvtx_types;
    if(in_array($post->post_type, array_keys($cvtx_types))) {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}


if (is_admin()) add_action('admin_head', 'cvtx_manage_media_buttons');
/**
 * Hide media buttons above the rich text editor
 */
function cvtx_manage_media_buttons() {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        remove_all_actions('media_buttons');
    }
}


if (is_admin()) add_filter('mce_buttons', 'cvtx_manage_mce_buttons');
/**
 * Restrict first button row of the rich text editor
 *
 * @todo include 'formatselect'
 *
 * @param array $buttons rich edit buttons that are enabled
 */
function cvtx_manage_mce_buttons($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array('bold', 'italic', '|', 'bullist', 'numlist', '|', 'undo', 'redo');
    } else {
        return $buttons;
    }
}


if (is_admin()) add_filter('mce_buttons_2', 'cvtx_manage_mce_buttons_2');
/**
 * Restrict second button row of the rich text editor
 */
function cvtx_manage_mce_buttons_2($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array();
    } else {
        return $buttons;
    }
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
                                 'orderby'   => 'meta_value_num',
                                 'meta_key'  => 'cvtx_top_ord',
                                 'order'     => 'ASC'));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_aeantrag_antrag" id="cvtx_aeantrag_antrag_select">';
        while ($tquery->have_posts()) {
            $tquery->the_post();
            // optgroup for top
            $output .= '<optgroup label="'.get_the_title().'">';
            
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
