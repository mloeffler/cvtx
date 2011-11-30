<?php
/**
 * @package cvtx
 * @version 0.1
 */
/*
Plugin Name: cvtx
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


$types = array('cvtx_top'      => array('cvtx_top_ord', 'cvtx_top_short'),
               'cvtx_antrag'   => array('cvtx_antrag_ord', 'cvtx_antrag_num', 'cvtx_antrag_top', 'cvtx_antrag_steller', 'cvtx_antrag_grund'),
               'cvtx_aeantrag' => array('cvtx_aeantrag_zeile', 'cvtx_aeantrag_num', 'cvtx_aeantrag_antrag', 'cvtx_aeantrag_steller', 'cvtx_aeantrag_grund', 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag_detail'));


/* add custom meta boxes */

add_action('add_meta_boxes', 'cvtx_add_meta_boxes');
function cvtx_add_meta_boxes() {
    // Tagesordnungspunkte
    add_meta_box('cvtx_top_ord', 'Nummer', 'cvtx_top_ord', 'cvtx_top', 'normal', 'high');
    add_meta_box('cvtx_top_short', 'Kürzel', 'cvtx_top_short', 'cvtx_top', 'normal', 'high');
    
    // Anträge
    add_meta_box('cvtx_antrag_ord', 'Antragsnummer', 'cvtx_antrag_ord', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_steller', 'Antragsteller', 'cvtx_antrag_steller', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_grund', 'Begründung', 'cvtx_antrag_grund', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_top', 'Tagesordnungspunkt', 'cvtx_antrag_top', 'cvtx_antrag', 'side', 'high');
    
    // Änderungsanträge
    add_meta_box('cvtx_aeantrag_zeile', 'Zeile(n)', 'cvtx_aeantrag_zeile', 'cvtx_aeantrag', 'side', 'high');
    add_meta_box('cvtx_aeantrag_steller', 'Antragsteller', 'cvtx_aeantrag_steller', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_grund', 'Begründung', 'cvtx_aeantrag_grund', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_verfahren', 'Verfahren', 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_antrag', 'Antrag', 'cvtx_aeantrag_antrag', 'cvtx_aeantrag', 'side', 'high');
}


/* Tagesordnungspunkte */

// TOP-Nummer
function cvtx_top_ord() {
    global $post;
    echo('<label>Nummer:</label> <input name="cvtx_top_ord" type="text" value="'.get_post_meta($post->ID, 'cvtx_top_ord', true).'" />');
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
    echo(get_post_meta($top_id, 'cvtx_top_short', true).'-<input name="cvtx_antrag_ord" type="text" value="'.get_post_meta($post->ID, 'cvtx_antrag_ord', true).'" />');
}

// Tagesordnungspunkt
function cvtx_antrag_top() {
    global $post;
    $post_id = $post->ID;
    $top_id  = get_post_meta($post_id, 'cvtx_antrag_top', true);
    
    $lquery = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'meta_value_num', 'meta_key' => 'cvtx_top_ord', 'order' => 'ASC'));
    if ($lquery->have_posts()) {
        echo('<select name="cvtx_antrag_top">');
        
        while ($lquery->have_posts()) {
            $lquery->the_post();
            
            echo('<option value="'.get_the_ID().'"'.(get_the_ID() == $top_id ? ' selected="selected"' : '').'>');
            echo('TOP '.get_post_meta(get_the_ID(), 'cvtx_top_ord', true).': ');
            echo(get_the_title());
            echo('</option>');
        }
        
        echo('</select>');
    } else {
        echo('Keine Tagesordnungspunkte angelegt.');
    }

    wp_reset_postdata();
    $post = get_post($post_id);
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


/* Änderungsanträge */

// Ä-Antragsnummer / Zeile
function cvtx_aeantrag_zeile() {
    global $post;
    $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
    $top_id    = get_post_meta($antrag_id, 'cvtx_antrag_top', true);
    echo(get_post_meta($top_id, 'cvtx_top_short', true).'-'.get_post_meta($antrag_id, 'cvtx_antrag_ord', true).'-<input name="cvtx_aeantrag_zeile" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
}

// Antrag (DIRTY!!)
function cvtx_aeantrag_antrag() {
    global $post;
    $post_id = $post->ID;
    $antrag_id = get_post_meta($post_id, 'cvtx_aeantrag_antrag', true);
    
    // Tagesordnungspunkte auflisten
    $tquery = new WP_Query(array('post_type' => 'cvtx_top', 'orderby' => 'meta_value_num', 'meta_key' => 'cvtx_top_ord', 'order' => 'ASC'));
    if ($tquery->have_posts()) {
        echo('<select name="cvtx_aeantrag_antrag">');
        
        while ($tquery->have_posts()) {
            $tquery->the_post();
            $top_id = get_the_ID();
            $ord    = get_post_meta($top_id, 'cvtx_top_ord', true);
            $short  = get_post_meta($top_id, 'cvtx_top_short', true);
            
            echo('<optgroup label="TOP '.$ord.': '.get_the_title().'">');
            
            // Zugehörige Anträge auflisten
            $aquery = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                         'meta_key'   => 'cvtx_antrag_top',
                                         'meta_value' => $top_id));
            if ($aquery->have_posts()) {
                while ($aquery->have_posts()) {
                    $aquery->the_post();
                    echo('<option value="'.get_the_ID().'"'.(get_the_ID() == $antrag_id ? ' selected="selected"' : '').'>');
                    echo($short.'-'.get_post_meta(get_the_ID(), 'cvtx_antrag_ord', true).' '.get_the_title());
                    echo('</option>');
                }
            }
            
            echo('</optgroup>');

            wp_reset_postdata();
            $post = get_post($top_id);
        }
        
        echo('</select>');
    } else {
        echo('Keine Tagesordnungspunkte angelegt.');
    }

    wp_reset_postdata();
    $post = get_post($post_id);
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



add_action('init', 'create_post_types');
function create_post_types() {
    /* Tagesordnungspunkte */
    register_post_type('cvtx_top',
        array('labels' => array(
              'name' => __('TOPs'),
              'singular_name' => __('TOP'),
              'add_new_item' => __('TOP erstellen'),
              'edit_item' => __('TOP bearbeiten'),
              'view_item' => __('TOP ansehen')),
        'public' => true,
        '_builtin' => false,
        'has_archive' => true,
        'rewrite' => array('slug' => 'top'),
        'supports' => array('title'),
        )
    );

    /* Anträge */
    register_post_type('cvtx_antrag',
        array('labels' => array(
              'name' => __('Anträge'),
              'singular_name' => __('Antrag'),
              'add_new_item' => __('Antrag erstellen'),
              'edit_item' => __('Antrag bearbeiten'),
              'view_item' => __('Antrag ansehen')),
        'public' => true,
        '_builtin' => false,
        'has_archive' => true,
        'rewrite' => array('slug' => 'antrag'),
        'supports' => array('title', 'editor'),
        )
    );

    /* Änderungsanträge */
    register_post_type('cvtx_aeantrag',
        array('labels' => array(
              'name' => __('Ä-Anträge'),
              'singular_name' => __('Ä-Antrag'),
              'add_new_item' => __('Änderungsantrag erstellen'),
              'edit_item' => __('Änderungsantrag bearbeiten'),
              'view_item' => __('Änderungsantrag ansehen')),
        'public' => true,
        '_builtin' => false,
        'has_archive' => true,
        'rewrite' => array('slug' => 'aeantrag'),
        'supports' => array('title', 'editor'),
        )
    );
}



/* Update lists */

add_filter('manage_edit-cvtx_top_columns', 'cvtx_top_columns');
function cvtx_top_columns($columns) {
	$columns = array(
		"cb"             => '<input type="checkbox" />',
		"cvtx_top_ord"   => "Nummer",
		"title"          => "Tagesordnungspunkt",
		"cvtx_top_short" => "Kürzel",
	);
	return $columns;
}

// Register the column as sortable
add_filter('manage_edit-cvtx_top_sortable_columns', 'cvtx_register_sortable_top');
function cvtx_register_sortable_top($columns) {
    $columns['cvtx_top_ord'] = 'cvtx_top_ord';
    return $columns;
}

add_filter('manage_edit-cvtx_antrag_columns', 'cvtx_antrag_columns');
function cvtx_antrag_columns($columns) {
	$columns = array(
		"cb"                  => '<input type="checkbox" />',
		"cvtx_antrag_ord"     => "Nummer",
		"title"               => "Antragstitel",
		'cvtx_antrag_steller' => "Antragsteller",
		"cvtx_antrag_top"     => "Tagesordnungspunkt"
	);
	return $columns;
}

// Register the column as sortable
add_filter('manage_edit-cvtx_antrag_sortable_columns', 'cvtx_register_sortable_antrag');
function cvtx_register_sortable_antrag($columns) {
    $columns['cvtx_antrag_ord']     = 'cvtx_antrag_ord';
    $columns['cvtx_antrag_steller'] = 'cvtx_antrag_steller';
    return $columns;
}

add_filter('manage_edit-cvtx_aeantrag_columns', 'cvtx_aeantrag_columns');
function cvtx_aeantrag_columns($columns) {
	$columns = array(
		"cb"                      => '<input type="checkbox" />',
		"cvtx_aeantrag_ord"       => "Nummer",
		"title"                   => "Änderungsantrag",
		'cvtx_aeantrag_steller'   => "Antragsteller",
		"cvtx_aeantrag_verfahren" => "Verfahren",
		"cvtx_aeantrag_antrag"    => "Antrag",
		"cvtx_aeantrag_top"       => "Tagesordnungspunkt"
	);
	return $columns;
}

// Register the column as sortable
add_filter('manage_edit-cvtx_aeantrag_sortable_columns', 'cvtx_register_sortable_aeantrag');
function cvtx_register_sortable_aeantrag($columns) {
    $columns['cvtx_aeantrag_ord']       = 'cvtx_aeantrag_ord';
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
            echo('TOP '.get_post_meta($post->ID, 'cvtx_top_ord', true));
            break;
        case 'cvtx_top_short':
            echo(get_post_meta($post->ID, 'cvtx_top_short', true));
            break;
            
        // Anträge
        case 'cvtx_antrag_ord':
            $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);
            echo(get_post_meta($top_id, 'cvtx_top_short', true).'-'.get_post_meta($post->ID, 'cvtx_antrag_ord', true));
            break;
        case 'cvtx_antrag_num':
            echo(get_post_meta($post->ID, 'cvtx_antrag_num', true));
            break;
        case 'cvtx_antrag_steller':
            echo(get_post_meta($post->ID, 'cvtx_antrag_steller', true));
            break;
        case "cvtx_antrag_top":
            $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);
            echo('TOP '.get_post_meta($top_id, 'cvtx_top_ord', true).': '.get_the_title($top_id));
            break;
            
        // Ä-Anträge
        case 'cvtx_aeantrag_ord':
            $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
            $top_id    = get_post_meta($antrag_id, 'cvtx_antrag_top', true);
            echo(get_post_meta($top_id, 'cvtx_top_short', true).'-'.get_post_meta($antrag_id, 'cvtx_antrag_ord', true).'-'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true));
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
            $top_id    = get_post_meta($antrag_id, 'cvtx_antrag_top', true);
            echo(get_post_meta($top_id, 'cvtx_top_short', true).'-'.get_post_meta($antrag_id, 'cvtx_antrag_ord', true).' '.get_the_title($antrag_id));
            break;
        case "cvtx_aeantrag_top":
            $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
            $top_id    = get_post_meta($antrag_id, 'cvtx_antrag_top', true);
            echo('TOP '.get_post_meta($top_id, 'cvtx_top_ord', true).': '.get_the_title($top_id));
            break;
    }
}

add_filter('request', 'cvtx_order_lists');
function cvtx_order_lists($vars) {
    if (isset($vars['orderby'])) {
        // Anträge
        if ($vars['orderby'] == 'cvtx_antrag_ord') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_antrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_steller', 'orderby' => 'meta_value'));
        }
        // Änderungsanträge
        else if ($vars['orderby'] == 'cvtx_aeantrag_ord') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_num', 'orderby' => 'meta_value_num'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_steller', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_verfahren') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_verfahren', 'orderby' => 'meta_value'));
        }
        // TOPs
        else if ($vars['orderby'] == 'cvtx_top_ord') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_top_ord', 'orderby' => 'meta_value_num'));
        }
    }

    return $vars;
}



add_action('wp_insert_post', 'cvtx_insert_post', 10, 2);
function cvtx_insert_post($post_id, $post = null) {
    global $types;
                   
    if (in_array($post->post_type, array_keys($types))) {
        // Loop through the POST data
        foreach ($types[$post->post_type] as $key) {
            // Add sortable antrag_num-field
            if ($post->post_type == 'cvtx_antrag') {
                $_POST['cvtx_antrag_num'] = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true)
                                           .'-'.$_POST['cvtx_antrag_ord'];
            } else if ($post->post_type == 'cvtx_aeantrag') {
                $top_id = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
                $_POST['cvtx_aeantrag_num'] = get_post_meta($top_id, 'cvtx_top_ord', true)
                                             .'-'.get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true)
                                             .'-'.$_POST['cvtx_antrag_zeile'];
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
    }
}


//removes quick edit from custom post type list
if (is_admin()) {
	add_filter('post_row_actions', 'remove_quick_edit', 10, 2);
}

function remove_quick_edit($actions) {
    global $post, $types;
    if(in_array($post->post_type, array_keys($types))) {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

// replaces filter "the title" in order to generate custom titles for post-types "antrag" and "aeantrag"
add_filter('the_title','cvtx_the_title',1,2);
function cvtx_the_title($before='',$title='') {
	if(is_numeric($title)) $post = &get_post($title);
	if(isset($post)) {
		$title_new = $post->post_title;
	  if($post->post_type == 'cvtx_antrag') {
	  	// number of antrag
			$nr = get_post_meta($post->ID, 'cvtx_antrag_ord',true);
  		// top short
  		$top = get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
  		// put it together!
  		$title_new = $top.'-'.$nr.' '.$post->post_title;
  	}
  	else if($post->post_type == 'cvtx_aeantrag') {
  		// id of antrag
    	$a_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
    	// id of top
    	$top_id = get_post_meta($a_id, 'cvtx_antrag_top', true);
    	// number of antrag
			$nr = get_post_meta($a_id, 'cvtx_antrag_ord',true);
			// top short
    	$top = get_post_meta($top_id, 'cvtx_top_short', true);
    	// zeile of ae_antrag
    	$zeile = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
    	// put it together!
    	$title_new = '&Auml;'.$top.'-'.$nr.'-'.$zeile;
  	}
    return $title_new;
  }
  else
  	return $title;
}

?>
