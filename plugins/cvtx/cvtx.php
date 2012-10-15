<?php
/**
 * @package cvtx
 * @version 0.2
 */

/*
Plugin Name: cvtx Agenda Plugin
Plugin URI: http://cvtx-project.org
Description: Das Antragssystem „cvtx“ stellt zahlreiche Hilfsmittel zur Verfügung, um Tagesordnungen, Anträge, Änderungsanträge und Antragsreader auf politischen Kongressen oder Mitgliederversammlungen zu verwalten. Es basiert auf dem Textsatzsystem LaTeX und ist verfügbar als Open Source.
Author: Alexander Fecke & Max Löffler
Version: 0.2
Author URI: http://alexander-fecke.de
License: GPLv2 or later
*/

// DEBUG
require_once(ABSPATH.'wp-includes/pluggable.php');
require_once(ABSPATH.'wp-admin/includes/plugin.php');

define('CVTX_VERSION', '0.2');
define('CVTX_PLUGIN_FILE', plugin_basename(__FILE__));
define('CVTX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CVTX_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once(CVTX_PLUGIN_DIR.'/cvtx_admin.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_latex.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_widgets.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_theme.php');
require_once(CVTX_PLUGIN_DIR.'/cvtx_forms.php');

// load language files
load_plugin_textdomain('cvtx', false, dirname(CVTX_PLUGIN_FILE).'/languages');

// define post types
$cvtx_types = array('cvtx_reader'   => array('cvtx_reader_style'),
                    'cvtx_top'      => array('cvtx_top_ord',
                                             'cvtx_sort',
                                             'cvtx_top_short',
                                             'cvtx_top_antraege',
                                             'cvtx_top_applications',
                                             'cvtx_top_appendix'),
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
                                             'cvtx_aeantrag_info'),
                 'cvtx_application' => array('cvtx_application_ord',
                                             'cvtx_sort',
                                             'cvtx_application_top',
                                             'cvtx_application_email',
                                             'cvtx_application_phone'));

// Used MIME types
$cvtx_mime_types = array('pdf' => 'application/pdf',
                         'tex' => 'application/x-latex',
                         'log' => 'text/plain');

// HTML Purifier Instance / Configuration
$cvtx_purifier = null;
$cvtx_purifier_config = null;


add_action('init', 'cvtx_init');
/**
 * Create custom post types
 */
function cvtx_init() {
    // Tagesordnungspunkte
    register_post_type('cvtx_top',
        array('labels'             => array(
              'name'               => __('Agenda points', 'cvtx'),
              'singular_name'      => __('Agenda point', 'cvtx'),
              'add_new_item'       => __('Create agenda point', 'cvtx'),
              'edit_item'          => __('Edit agenda point', 'cvtx'),
              'view_item'          => __('View agenda point', 'cvtx'),
              'menu_name'          => __('agenda points (menu_name)', 'cvtx'),
              'new_item'           => __('New agenda point', 'cvtx'),
              'search_items'       => __('Search agenda points', 'cvtx'),
              'not_found'          => __('No agenda points found', 'cvtx'),
              'not_found_in_trash' => __('No agenda points found in trash', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_top_small.png',
        'rewrite'     => array('slug' => __('agenda points (slug)', 'cvtx')),
        'supports'    => array('title', 'editor'),
        )
    );

    // Anträge
    register_post_type('cvtx_antrag',
        array('labels'             => array(
              'name'               => __('Resolutions', 'cvtx'),
              'singular_name'      => __('Resolution', 'cvtx'),
              'add_new_item'       => __('Create resolution', 'cvtx'),
              'edit_item'          => __('Edit resolution', 'cvtx'),
              'view_item'          => __('View resolution', 'cvtx'),
              'menu_name'          => __('resolutions (menu_name)', 'cvtx'),
              'new_item'           => __('New resolution', 'cvtx'),
              'search_items'       => __('Search resolutions', 'cvtx'),
              'not_found'          => __('No resolutions found', 'cvtx'),
              'not_found_in_trash' => __('No resolutions found in trash', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_antrag_small.png',
        'rewrite'     => array('slug' => __('resolutions (slug)', 'cvtx')),
        'supports'    => array('title', 'editor'),
        )
    );

    // Änderungsanträge
    register_post_type('cvtx_aeantrag',
        array('labels'             => array(
              'name'               => __('Amendments', 'cvtx'),
              'singular_name'      => __('Amendment', 'cvtx'),
              'add_new_item'       => __('Create amendment', 'cvtx'),
              'edit_item'          => __('Edit amendment', 'cvtx'),
              'view_item'          => __('View amendment', 'cvtx'),
              'menu_name'          => __('amendments (menu_name)', 'cvtx'),
              'new_item'           => __('New amendment', 'cvtx'),
              'search_items'       => __('Search amendment', 'cvtx'),
              'not_found'          => __('No amendments found', 'cvtx'),
              'not_found_in_trash' => __('No amendments found in Trash', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_aeantrag_small.png',
        'rewrite'     => array('slug' => __('amendments (slug)', 'cvtx')),
        'supports'    => array('editor'),
        )
    );

    // Applications
    register_post_type('cvtx_application',
        array('labels'             => array(
              'name'               => __('Applications', 'cvtx'),
              'singular_name'      => __('Application', 'cvtx'),
              'add_new_item'       => __('Create application', 'cvtx'),
              'edit_item'          => __('Edit application', 'cvtx'),
              'view_item'          => __('View application', 'cvtx'),
              'menu_name'          => __('Applications', 'cvtx'),
              'new_item'           => __('New application', 'cvtx'),
              'search_items'       => __('Search applications', 'cvtx'),
              'not_found'          => __('No applications found', 'cvtx'),
              'not_found_in_trash' => __('No applications found in Trash', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'rewrite'     => array('slug' => __('applications (slug)', 'cvtx')),
        'supports'    => array('title'),
        )
    );

    // Reader
    register_post_type('cvtx_reader',
        array('labels'             => array(
              'name'               => __('Readers', 'cvtx'),
              'singular_name'      => __('Reader', 'cvtx'),
              'add_new_item'       => __('Create reader', 'cvtx'),
              'new_item'           => __('New reader', 'cvtx'),
              'edit_item'          => __('Edit reader', 'cvtx'),
              'view_item'          => __('View reader', 'cvtx'),
              'menu_name'          => __('readers (menu_name)', 'cvtx'),
              'search_items'       => __('Search reader', 'cvtx'),
              'not_found'          => __('No readers found', 'cvtx'),
              'not_found_in_trash' => __('No readers found in trash', 'cvtx')),
        'public'      => true,
        '_builtin'    => false,
        'has_archive' => false,
        'menu_icon'   => CVTX_PLUGIN_URL.'images/cvtx_reader_small.png',
        'rewrite'     => array('slug' => __('readers (slug)', 'cvtx')),
        'supports'    => array('title'),
        )
    );
    
    
    // Register reader taxonomy to Anträgen
    register_taxonomy('cvtx_tax_reader', 'cvtx_antrag',
                      array('hierarchical' => true,
                            'label'        => __('Readers', 'cvtx'),
                            'show_ui'      => false,
                            'query_var'    => true,
                            'rewrite'      => false));
    
    // Register reader taxonomy to amendments
    register_taxonomy('cvtx_tax_reader', 'cvtx_aeantrag',
                      array('hierarchical' => true,
                            'label'        => __('Readers', 'cvtx'),
                            'show_ui'      => false,
                            'query_var'    => true,
                            'rewrite'      => false));
    
    // Register reader taxonomy to applications
    register_taxonomy('cvtx_tax_reader', 'cvtx_application',
                      array('hierarchical' => true,
                            'label'        => __('Readers', 'cvtx'),
                            'show_ui'      => false,
                            'query_var'    => true,
                            'rewrite'      => false));
    
    // Initialize HTML Purifier if plugin activated
    if (is_plugin_active('html-purified/html-purified.php')) {
        global $html_purifier, $cvtx_purifier, $cvtx_purifier_config;
        $cvtx_purifier        = $html_purifier->get_purifier();
        $cvtx_purifier_config = HTMLPurifier_Config::createDefault();
        $cvtx_purifier_config->set('HTML.Doctype', 'XHTML 1.1');
        $cvtx_purifier_config->set('HTML.Allowed', 'strong,b,em,i,h1,h2,h3,h4,ul,ol,li,br,p,del,ins,code,span[style],a[href]');
        $cvtx_purifier_config->set('CSS.AllowedProperties', 'text-decoration');
    }
}


/**
 * Returns a globally sortable string
 */
function cvtx_get_sort($post_type, $top=false, $subject=false, $zeile=false, $vari=false) {
    $sorts            = array();
    $sorts['top']     = ($top     !== false ? (intval($top)     ? sprintf('%1$05d', intval($top))     : 'ZZZZZ' ) : 'AAAAA' );
    $sorts['subject'] = ($subject !== false ? (intval($subject) ? sprintf('%1$05d', intval($subject)) : 'ZZZZZ' ) : 'AAAAA' );
    $sorts['zeile']   = ($zeile   !== false ? (intval($zeile)   ? sprintf('%1$06d', intval($zeile))   : 'ZZZZZZ') : 'AAAAAA');
    $sorts['vari']    = ($vari    !== false ? (intval($vari)    ? sprintf('%1$06d', intval($vari))    : 'ZZZZZZ') : 'AAAAAA');

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
/**
 * Action inserts/updates posts
 */
function cvtx_insert_post($post_id, $post = null) {
    global $cvtx_types;

    if (in_array($post->post_type, array_keys($cvtx_types))) {
        $terms = array();
    
        // Update/insert top
        if ($post->post_type == 'cvtx_top') {
            // get globally sortable string
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_top', (isset($_POST['cvtx_top_ord']) ? $_POST['cvtx_top_ord'] : ''));
            
            // check whether antraege and applications may be added to this top or not
            if (!isset($_POST['cvtx_top_antraege'])) {
                $cvtx_top_antraege = get_post_meta($post_id, 'cvtx_top_antraege', true);
                $_POST['cvtx_top_antraege'] = (is_string($cvtx_top_antraege) && !empty($cvtx_top_antraege) ? $cvtx_top_antraege : 'off');
            }
            if (!isset($_POST['cvtx_top_applications'])) {
                $cvtx_top_applications = get_post_meta($post_id, 'cvtx_top_applications', true);
                $_POST['cvtx_top_applications'] = (is_string($cvtx_top_applications) && !empty($cvtx_top_applications) ? $cvtx_top_applications : 'off');
            }
            // check whether or not the top is display as appendix
            if (!isset($_POST['cvtx_top_appendix'])) {
                $cvtx_top_appendix = get_post_meta($post_id, 'cvtx_top_appendix', true);
                $_POST['cvtx_top_appendix'] = (is_string($cvtx_top_appendix) && !empty($cvtx_top_appendix) ? $cvtx_top_appendix : 'off');
            }
        }
        // Update/insert antrag
        else if ($post->post_type == 'cvtx_antrag' && isset($_POST['cvtx_antrag_top'])) {
            // get top and validate data
            $top_ord    = get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true);
            $antrag_ord = (isset($_POST['cvtx_antrag_ord']) ? $_POST['cvtx_antrag_ord'] : 0);

            // get globally sortable string
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_antrag', $top_ord, $antrag_ord);
            
            // generate short antragsteller if field is empty
            if (!isset($_POST['cvtx_antrag_steller_short']) || empty($_POST['cvtx_antrag_steller_short'])) {
                $parts = preg_split('/[,;\(\n]+/', $_POST['cvtx_antrag_steller'], 2);
                if (count($parts) == 2) $_POST['cvtx_antrag_steller_short'] = trim($parts[0]).' '.__('et al.', 'cvtx');
                else                    $_POST['cvtx_antrag_steller_short'] = $_POST['cvtx_antrag_steller'];
            }
            
            // get default reader terms for amendments
            $terms = explode(', ', get_option('cvtx_default_reader_antrag'));
        }
        // Update/insert amendment
        else if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
            // get top and antrag and validate data
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
            
            // get globally sortable string
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_aeantrag', $top_ord, $antrag_ord, $aeantrag_zeile, $aeantrag_vari);
            
            // generate short antragsteller if field is empty
            if (!isset($_POST['cvtx_aeantrag_steller_short']) || empty($_POST['cvtx_aeantrag_steller_short'])) {
                $parts = preg_split('/[,;\(\n]+/', $_POST['cvtx_aeantrag_steller'], 2);
                if (count($parts) == 2) $_POST['cvtx_aeantrag_steller_short'] = trim($parts[0]).' '.__('et al.', 'cvtx');
                else                    $_POST['cvtx_aeantrag_steller_short'] = $_POST['cvtx_aeantrag_steller'];
            }
        
            // get default reader terms for amendments
            $terms = explode(', ', get_option('cvtx_default_reader_aeantrag'));
        }
        // Update/insert reader taxonomy
        else if ($post->post_type == 'cvtx_reader' & isset($_POST['cvtx_post_ids'])) {
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
            
            // fetch object terms for added, unselected and remaining objects and copy all of them except this reader!
            $terms = array();
            foreach (array_unique(array_merge($old, $new)) as $item) {
                $terms["$item"] = array();
                foreach (wp_get_object_terms($item, 'cvtx_tax_reader') as $term) {
                    if ($term->name != 'cvtx_reader_'.intval($post_id)) $terms["$item"][] = $term->name;
                }
            }
            
            // update object terms of unselected objects
            foreach ($old as $item) {
                if (!in_array($item, $new)) {
                    wp_set_object_terms($item, $terms["$item"], 'cvtx_tax_reader');
                }
            }
            
            // add this reader to terms list and update object terms of newly added objects
            foreach ($new as $item) {
                $terms["$item"][] = 'cvtx_reader_'.intval($post_id);
                if (!in_array($item, $old)) {
                    wp_set_object_terms($item, $terms["$item"], 'cvtx_tax_reader');
                }
            }
        }
        // Update/insert application
        else if ($post->post_type == 'cvtx_application' && isset($_POST['cvtx_application_top'])) {
            // get top and validate data
            $top_ord  = get_post_meta($_POST['cvtx_application_top'], 'cvtx_top_ord', true);
            $appl_ord = (isset($_POST['cvtx_application_ord']) ? $_POST['cvtx_application_ord'] : 0);

            // get globally sortable string
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_application', $top_ord, $appl_ord);
            
            // get default reader terms for applications
            $terms = explode(', ', get_option('cvtx_default_reader_application'));
            
            /* DAS IST NOCH IMMER EHER QUICK UND DIRTY */
            if ($post->post_status != 'auto-draft') {
                // get old filename
                $old_file = cvtx_get_file($post, 'pdf', 'dir');
                // generate file name
                $out_dir = wp_upload_dir();
                // generate short (BUGGY!!!)
                $top  = get_post_meta($_POST['cvtx_application_top'], 'cvtx_top_short', true);
                // format
                $format = strtr(get_option('cvtx_antrag_format'), array(__('%agenda_point%', 'cvtx') => $top,
                                                                        __('%resolution%', 'cvtx')   => $_POST['cvtx_application_ord']));
                if (!empty($top) && !empty($_POST['cvtx_application_ord'])) $short = $format;
                
                // application published?
                if ($post->post_status == 'publish' && isset($short)) {
                    $filename = $out_dir['path'].'/'.cvtx_sanitize_file_name($short.'_'.$post->post_title).'.pdf';
                }
                // else we use the parents ID
                else {
                    $filename = $out_dir['path'].'/'.$post->ID.'.pdf';
                }
                
                // configure attachment
                $attachment = array('post_mime_type' => 'application/pdf',
                                    'post_title'     => $post->post_type.'_'.$post->ID,
                                    'post_content'   => '',
                                    'post_status'    => 'inherit',
                                    'post_parent'    => $post->ID);
    
                // file upload
                if (isset($_FILES['cvtx_application_file']) && ($_FILES['cvtx_application_file']['size'] > 0)) {
                    $file         = $_FILES['cvtx_application_file'];
                    // of which filetype is the uploaded file?
                    $arr_filetype = wp_check_filetype(basename($file['name']));
                    $filetype     = $arr_filetype['type'];
    
                    // we accept only pdfs!
                    if ($filetype == 'application/pdf') {
                        // is there already an attachment? remove it
                        if ($existing = get_post_meta($post->ID, 'cvtx_pdf_id', true)) {
                            wp_delete_attachment($existing, true);
                        }
                        
                        // upload the pdf
                        $upload = wp_handle_upload($file, array('test_form' => false));
                        // check if upload was succesful, get meta-informations
                        if (!isset($upload['error']) && isset($upload['file'])) {
                            // move file
                            rename($upload['file'], $filename);
                            
                            // insert attachment
                            $attach_id  = wp_insert_attachment($attachment, $filename);
                            // save attachment id to application
                            update_post_meta($post->ID, 'cvtx_pdf_id', $attach_id);
                        }
                    }
                }
                else if ($old_file !== false && $old_file != $filename) {
                    // move file
                    rename($old_file, $filename);
                    // delete old attachment
                    if ($existing = get_post_meta($post->ID, 'cvtx_pdf_id', true)) {
                        wp_delete_attachment($existing, true);
                    }
                    // insert attachment
                    $attach_id  = wp_insert_attachment($attachment, $filename);
                    // save attachment id to application
                    update_post_meta($post->ID, 'cvtx_pdf_id', $attach_id);
                }
            }
        }
                
        // add default reader terms to antrag, amendment or application
        if (is_array($terms) && count($terms) > 0 && $post->post_type != 'cvtx_reader') {
            $items = array();
            foreach ($terms as $term) {
                if (term_exists($term, 'cvtx_tax_reader')) $items[] = $term;
            }
            if (count($items) > 0) {
                wp_set_object_terms($post->ID, $items, 'cvtx_tax_reader');
            }
        }
            
        // Loop through the POST data
        foreach ($cvtx_types[$post->post_type] as $key) {
            if (isset($_POST[$key])) {
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
        
        // create pdf
        if (is_admin()) cvtx_create_pdf($post_id, $post);
        // send mails if antrag created
        else {
            $tpl  = get_template_directory().'/mail.php';
            if(get_option('cvtx_send_html_mail') == FALSE || !file_exists($tpl)) $html_mail = FALSE;
            else $html_mail = TRUE;
            $headers = array('From: '.get_option('cvtx_send_from_email', get_bloginfo('admin_email'))."\r\n",
                             ($html_mail ? "Content-Type: text/html\r\n" : ''));
            
            // post type antrag created
            if ($post->post_type == 'cvtx_antrag') {
                $mails['owner'] = array('subject' => get_option('cvtx_send_create_antrag_owner_subject'),
                                        'body'    => get_option('cvtx_send_create_antrag_owner_body'));
                $mails['admin'] = array('subject' => get_option('cvtx_send_create_antrag_admin_subject'),
                                        'body'    => get_option('cvtx_send_create_antrag_admin_body'));
                
                $fields = array(__('%agenda_point_token%', 'cvtx') => __('Agenda point', 'cvtx').' '.get_post_meta($_POST['cvtx_antrag_top'], 'cvtx_top_ord', true),
                                __('%agenda_point%', 'cvtx')       => get_the_title($_POST['cvtx_antrag_top']),
                                __('%title%', 'cvtx')              => $post->post_title,
                                __('%authors%', 'cvtx')            => $_POST['cvtx_antrag_steller'],
                                __('%authors_short%', 'cvtx')      => $_POST['cvtx_antrag_steller_short'],
                                __('%text%', 'cvtx')               => $post->post_content,
                                __('%explanation%', 'cvtx')        => $_POST['cvtx_antrag_grund']);
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        if($part=='body' && $html_mail) {
                            $content = nl2br(strtr($content, $fields));
                            ob_start();
                            require($tpl);
                            $out = ob_get_contents();
                            ob_end_clean();
                            $mails[$rcpt][$part] = $out;
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
                $fields = array(__('%agenda_point_token%', 'cvtx') => __('Agenda point', 'cvtx').' '.get_post_meta($top_id, 'cvtx_top_ord', true),
                                __('%agenda_point%', 'cvtx')       => get_the_title($top_id),
                                __('%resolution_token%', 'cvtx')   => get_post_meta($top_id, 'cvtx_top_short', true).'-'
                                                                     .get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true),
                                __('%resolution%', 'cvtx')         => get_the_title($_POST['cvtx_aeantrag_antrag']),
                                __('%line%', 'cvtx')               => $_POST['cvtx_aeantrag_zeile'],
                                __('%authors%', 'cvtx')            => $_POST['cvtx_aeantrag_steller'],
                                __('%authors_short%', 'cvtx')      => $_POST['cvtx_aeantrag_steller_short'],
                                __('%text%', 'cvtx')               => $post->post_content,
                                __('%explanation%', 'cvtx')        => $_POST['cvtx_aeantrag_grund']);
                
                // replace post type data
                foreach ($mails as $rcpt => $mail) {
                    foreach ($mail as $part => $content) {
                        if($part=='body' && $html_mail) {
                            $content = nl2br(strtr($content, $fields));
                            ob_start();
                            require($tpl);
                            $out = ob_get_contents();
                            ob_end_clean();
                            $mails[$rcpt][$part] = $out;
                         }
                         else $mails[$rcpt][$part] = strtr($content, $fields);
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
    global $cvtx_mime_types;

    $pdflatex = get_option('cvtx_pdflatex_cmd');
    
    if (isset($post) && is_object($post) && !empty($pdflatex)) {
        $out_dir = wp_upload_dir();
        $out_dir = $out_dir['path'].'/';
        $tpl_dir = get_template_directory().'/'.get_option('cvtx_latex_tpldir');
    
        // prepare antrag
        if ($post->post_type == 'cvtx_antrag') {
            // file
            if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
                $file = $out_dir.cvtx_sanitize_file_name($short.'_'.$post->post_title);
            } else {
                $file = $out_dir.$post->ID;
            }
            
            // set file post type
            $file_post_type = 'cvtx_antrag';
        }
        // prepare Ä-Antrag if pdf-option enabled
        else if ($post->post_type == 'cvtx_aeantrag' && get_option('cvtx_aeantrag_pdf')) {
            // file
            if ($post->post_status == 'publish' && $short = cvtx_get_short($post)) {
                $file = $out_dir.cvtx_sanitize_file_name($short);
            } else {
                $file = $out_dir.$post->ID;
            }
            
            // set file post type
            $file_post_type = 'cvtx_aeantrag';
        }
        // prepare Reader
        else if ($post->post_type == 'cvtx_reader') {
            // file
            if ($post->post_status == 'publish') {
                $file = $out_dir.cvtx_sanitize_file_name($post->post_title);
            } else {
                $file = $out_dir.$post->ID;
            }
            
            // get reader style
            $style = get_post_meta($post->ID, 'cvtx_reader_style', true);
            if ($style != 'book' && $style != 'table') $style = 'book';
            
            // set file post type
            $file_post_type = 'cvtx_reader_'.$style;
        }
        
        // get template
        if (isset($file_post_type) && !empty($file_post_type)) {
            // use special theme template for id=x if exists
            if (is_file($tpl_dir.'/single-'.$file_post_type.'-'.$post->ID.'.php')) {
                $tpl = $tpl_dir.'/single-'.$file_post_type.'-'.$post->ID.'.php';
            }
            // use theme template
            else if(is_file($tpl_dir.'/single-'.$file_post_type.'.php')) {
                $tpl = $tpl_dir.'/single-'.$file_post_type.'.php';
            }
            // use default template
            else if(is_file(CVTX_PLUGIN_DIR.'/latex/single-'.$file_post_type.'.php')) {
                $tpl = CVTX_PLUGIN_DIR.'/latex/single-'.$file_post_type.'.php';
            }
        }

        // create pdf if template found
        if (isset($tpl) && !empty($tpl) && isset($file) && !empty($file)) {
            // drop old attachments if exists
            foreach (array('pdf', 'log', 'tex') as $ending) {
                if ($attachment = get_post_meta($post->ID, 'cvtx_'.$ending.'_id', true)) {
                    wp_delete_attachment($attachment, true);
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
                $cmd = $pdflatex.' -interaction=nonstopmode '
                                .' -output-directory='.escapeshellcmd($out_dir)
                                .' '.escapeshellcmd($file).'.tex';

                // run pdflatex
                exec($cmd);
                // if reader is generated: run it twice to build toc etc.
                if ($post->post_type == 'cvtx_reader') {
                    exec($cmd);
                    exec($cmd);
                }
                
                $attach = array('pdf', 'log', 'tex');
                // remove .aux-file
                $remove = array('aux', 'toc', 'bbl', 'blg', 'out', 'synctex.gz');
                // remove .log-file
                if ((get_option('cvtx_drop_logfile') == 2 && is_file($file.'.pdf'))
                  || get_option('cvtx_drop_logfile') == 1) {
                    $remove[] = 'log';
                }
                // remove .tex-file
                if ((get_option('cvtx_drop_texfile') == 2 && is_file($file.'.pdf'))
                  || get_option('cvtx_drop_texfile') == 1) {
                    $remove[] = 'tex';
                }
                // remove files (if they exist)
                foreach ($remove as $ending) {
                    if (is_file($file.'.'.$ending)) unlink($file.'.'.$ending);
                }
                
                // register files as attachments
                foreach ($attach as $ending) {
                    if (is_file($file.'.'.$ending) && !in_array($ending, $remove)) {
                        $attachment = array('post_mime_type' => $cvtx_mime_types[$ending],
                                            'post_title'     => $post->post_type.'_'.$post->ID,
                                            'post_content'   => '',
                                            'post_status'    => 'inherit',
                                            'post_parent'    => $post->ID);
                        $attach_id  = wp_insert_attachment($attachment, $file.'.'.$ending);
                        update_post_meta($post->ID, 'cvtx_'.$ending.'_id', $attach_id);
                    }
                }
            }
        }
    }
}


add_filter('the_title', 'cvtx_the_title', 1, 2);
/**
 * replaces filter "the title" in order to generate custom titles for post-types "top", "antrag" and "aeantrag"
 */
function cvtx_the_title($before='', $after='') {
    global $cvtx_types, $cvtx_mime_types;

    if(is_numeric($after)) $post = &get_post($after);
    
    if(isset($post)) {
        $title = (!empty($post->post_title) ? $post->post_title : __('(no title)', 'cvtx'));
        
        // add short name as prefix
        if ($short = cvtx_get_short($post)) {
            // Antrag or application
            if($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_application') {
                $title = $short.' '.$title;
            }
            // Änderungsantrag
            else if($post->post_type == 'cvtx_aeantrag') {
                $title = $short;
            }
            // Agenda point
            else if($post->post_type == 'cvtx_top' && get_post_meta($post->ID, 'cvtx_top_appendix', true) != 'on') {
                $title = sprintf(__('agenda_point_prefix_format', 'cvtx'),
                                 get_post_meta($post->ID, 'cvtx_top_ord', true)).' '.$title;
            }
        }
        // get title for generated attachments
        else if ($post->post_type == 'attachment' && $parent = get_post($post->post_parent)) {
            if (in_array($parent->post_type, array_keys($cvtx_types))) {
                $title = '';
                // post type
                switch ($parent->post_type) {
                    case 'cvtx_reader':
                        $title .= __('Reader', 'cvtx');
                        break;
                    case 'cvtx_antrag':
                        $title .= __('Resolution', 'cvtx');
                        break;
                    case 'cvtx_aeantrag':
                        $title .= __('Amendment', 'cvtx');
                        break;
                    case 'cvtx_application':
                        $title .= __('Application', 'cvtx');
                        break;
                }
                $title .= ' ';
                // short name, post title or no title
                if ($short = cvtx_get_short($parent)) {
                    $title .= $short;
                } else if (!empty($parent->post_title)) {
                    $title .= $parent->post_title;
                } else {
                    $title .= __('(no title)', 'cvtx');
                }
                // add mime type
                $mimes = array_flip($cvtx_mime_types);
                $title .= ' ('.$mimes[$post->post_mime_type].')';
                return $title;
            }
        } else {
            return (!empty($before) ? $before : __('(no title)', 'cvtx'));
        }
        return $title;
    } else {
        return $before;
    }
}


add_filter('the_content', 'cvtx_the_content', 20);
/**
 * Sanitizes the content of 
 */
function cvtx_the_content($content) {
    global $post;
    
    // Sanitize content using HTMLPurifier-plugin
    if (($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag')
     && is_plugin_active('html-purified/html-purified.php')) {
        global $cvtx_purifier, $cvtx_purifier_config;
        // Purify resolution text and meta fields
        $content = $cvtx_purifier->purify($content,  $cvtx_purifier_config);
    }

    // Returns the content.
    return $content;
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

        if (!empty($top)) return sprintf(__('agenda_point_format', 'cvtx'), $top);
    }
    // post type antrag
    else if ($post->post_type == 'cvtx_antrag') {
        $top    = get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
        $antrag = get_post_meta($post->ID, 'cvtx_antrag_ord', true);

        // format
        $format = strtr(get_option('cvtx_antrag_format'), array(__('%agenda_point%', 'cvtx') => $top,
                                                                __('%resolution%', 'cvtx')   => $antrag));

        if (!empty($top) && !empty($antrag)) return $format;
    }
    // post type application
    else if ($post->post_type == 'cvtx_application') {
        $top  = get_post_meta(get_post_meta($post->ID, 'cvtx_application_top', true), 'cvtx_top_short', true);
        $appl = get_post_meta($post->ID, 'cvtx_application_ord', true);

        // format
        $format = strtr(get_option('cvtx_antrag_format'), array(__('%agenda_point%', 'cvtx') => $top,
                                                                __('%resolution%', 'cvtx')   => $appl));

        if (!empty($top) && !empty($appl)) return $format;
    }
    // post type antrag
    else if ($post->post_type == 'cvtx_aeantrag') {
        // fetch antrag_id, antag, top and zeile
        $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);
        $antrag_nr = get_post_meta($antrag_id, 'cvtx_antrag_ord', true);
        $top_nr    = get_post_meta(get_post_meta($antrag_id, 'cvtx_antrag_top', true), 'cvtx_top_short', true);
        $zeile     = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
        
        // format and return aeantrag_short
        $antrag = strtr(get_option('cvtx_antrag_format'), array(__('%agenda_point%', 'cvtx') => $top_nr,
                                                                __('%resolution%', 'cvtx')   => $antrag_nr));
        $format = strtr(get_option('cvtx_aeantrag_format'), array(__('%resolution%', 'cvtx') => $antrag,
                                                                  __('%line%', 'cvtx')       => $zeile));
        
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
    $filepath = get_attached_file(get_post_meta($post->ID, 'cvtx_'.$ending.'_id', true));
    
    if (($post->post_type == 'cvtx_application' || $post->post_type == 'cvtx_reader'
      || $post->post_type == 'cvtx_antrag'      || $post->post_type == 'cvtx_aeantrag')
      && is_file($filepath)) {
        if ($base == 'url') {
            return wp_get_attachment_url(get_post_meta($post->ID, 'cvtx_'.$ending.'_id', true));
        } else if ($base == 'dir') {
            return $filepath;
        }
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
    if ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag' || $post->post_type == 'cvtx_reader') {
        if (in_array('*', $endings)) {
            $endings = array('pdf', 'log', 'tex', 'aux', 'toc',
                             'bbl', 'blg', 'synctex.gz');
        }
        
        foreach ($endings as $ending) {
            if ($file = cvtx_get_file($post, $ending, 'dir')) {
                echo("<pre>");
                debug_print_backtrace();
                echo("</pre>");
                unlink($file);
            }
        }
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
function cvtx_dropdown_tops($selected = null, $message = '', $antraege = true, $applications = '') {
    global $post;
    $post_bak = $post;
    $output = '';
    
    $query_conds = array();
    if (is_bool($antraege)) {
        $query_conds[] = array('key'     => 'cvtx_top_antraege',
                               'value'   => ($antraege ? 'on' : 'off'),
                               'compare' => '=');
    }
    if (is_bool($applications)) {
        $query_conds[] = array('key'     => 'cvtx_top_applications',
                               'value'   => ($applications ? 'on' : 'off'),
                               'compare' => '=');
    }

    $tquery = new WP_Query(array('post_type'  => 'cvtx_top',
                                 'orderby'    => 'meta_value',
                                 'meta_key'   => 'cvtx_sort',
                                 'order'      => 'ASC',
                                 'nopaging'   => true,
                                 'meta_query' => $query_conds));
    if ($tquery->have_posts()) {
        $output .= '<select name="cvtx_'.($applications ? 'application' : 'antrag').'_top" id="cvtx_'
            .($applications ? 'application' : 'antrag').'_top_select">';
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


remove_all_actions('do_feed_rss2' );
add_action('do_feed_rss2', 'cvtx_feed_rss2', 10, 1);
/**
 * Change feed-templates for cvtx_antrag and cvtx_aeantrag
 */
function cvtx_feed_rss2($for_comments) {
    $rss_template_antrag   = CVTX_PLUGIN_DIR.'/feeds/feed-cvtx_antrag-rss2.php';
    $rss_template_aeantrag = CVTX_PLUGIN_DIR.'/feeds/feed-cvtx_aeantrag-rss2.php';
    if(get_query_var('post_type') == 'cvtx_antrag' && file_exists($rss_template_antrag)) {
        load_template($rss_template_antrag);
    } else if (get_query_var('post_type') == 'cvtx_aeantrag' && file_exists($rss_template_aeantrag)) {
        load_template($rss_template_aeantrag);
    } else {
        do_feed_rss2($for_comments); // Call default function
    }
}


/**
 * returns meta-informations about antrag/aeantrag
 */
function cvtx_get_rss_before_content($post, $type) {
    $output = '';
    if ($type == 'cvtx_antrag') {
        $output .= '<p><strong>'.__('Concerning', 'cvtx').'</strong>: '.get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true)).'</p>';
        $output .= '<p><strong>'.__('Author(s)', 'cvtx').'</strong>: '.get_post_meta($post->ID,'cvtx_antrag_steller_short',true).'</p>';
    } else if ($type == 'cvtx_aeantrag') {
        $output .= '<p><strong>'.__('Concerning', 'cvtx').'</strong>: '.get_the_title(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true)).'</p>';
        $output .= '<p><strong>'.__('Line', 'cvtx').'</strong>: '.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'</p>';
        $output .= '<p><strong>'.__('Author(s)', 'cvtx').'</strong>: '.get_post_meta($post->ID,'cvtx_aeantrag_steller_short',true).'</p>';
    }
    return $output;
}


/**
 * Returns the download link for a specific resolution / amendment
 */
function cvtx_get_rss_after_content($post) {
    $output = '';
    if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) {
        $output = '<p><a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a></p>';
    }
    return $output;
}


function get_aeantrag_count($post_id) {
	$loop = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                             'meta_key'   => 'cvtx_sort',
                             'orderby'    => 'meta_value',
                             'order'      => 'ASC',
                             'nopaging'   => true,
                             'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                         'value'   => $post_id,
                                                         'compare' => '='))));
  return $loop->post_count;
}

?>
