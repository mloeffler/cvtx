<?php
/**
 * @package cvtx
 */


/* add custom meta boxes */

if (is_admin()) add_action('add_meta_boxes', 'cvtx_add_meta_boxes');
function cvtx_add_meta_boxes() {
    // Reader
    add_meta_box('cvtx_reader_contents', __('Inhalt', 'cvtx'),
                 'cvtx_reader_contents', 'cvtx_reader', 'normal', 'high');
    add_meta_box('cvtx_reader_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_reader', 'side', 'low');
    
    // Tagesordnungspunkte
    add_meta_box('cvtx_top_meta', __('Metainformationen', 'cvtx'),
                 'cvtx_top_meta', 'cvtx_top', 'side', 'high');
    
    // Anträge
    add_meta_box('cvtx_antrag_meta', __('Metainformationen', 'cvtx'),
                 'cvtx_antrag_meta', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_steller', __('AntragstellerIn(nen)', 'cvtx'),
                 'cvtx_antrag_steller', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_grund', __('Begründung', 'cvtx'),
                 'cvtx_antrag_grund', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_info', __('Weitere Informationen', 'cvtx'),
                 'cvtx_antrag_info', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_antrag', 'side', 'low');
    add_meta_box('cvtx_antrag_reader', __('Readerzuordnung', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_antrag', 'side', 'low');
    
    // Änderungsanträge
    add_meta_box('cvtx_aeantrag_meta', __('Metainformationen', 'cvtx'),
                 'cvtx_aeantrag_meta', 'cvtx_aeantrag', 'side', 'high');
    add_meta_box('cvtx_aeantrag_steller', __('AntragstellerIn(nen)', 'cvtx'),
                 'cvtx_aeantrag_steller', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_grund', __('Begründung', 'cvtx'),
                 'cvtx_aeantrag_grund', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_verfahren', __('Verfahren', 'cvtx'),
                 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_info', __('Weitere Informationen', 'cvtx'),
                 'cvtx_aeantrag_info', 'cvtx_aeantrag', 'normal', 'low');
    // show/hide pdf-box for of aeantrag
    if (get_option('cvtx_aeantrag_pdf')) {
        add_meta_box('cvtx_aeantrag_pdf', __('PDF', 'cvtx'),
                     'cvtx_metabox_pdf', 'cvtx_aeantrag', 'side', 'low');
    }
    add_meta_box('cvtx_aeantrag_reader', __('Readerzuordnung', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_aeantrag', 'side', 'low');

    // Applications
    add_meta_box('cvtx_application_meta', __('Metainformationen', 'cvtx'),
                 'cvtx_application_meta', 'cvtx_application', 'side', 'high');
    add_meta_box('cvtx_application_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_application', 'side', 'low');
    add_meta_box('cvtx_application_reader', __('Readerzuordnung', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_application', 'side', 'low');
    add_meta_box('cvtx_application_upload', __('File upload', 'cvtx'),
                 'cvtx_application_upload', 'cvtx_application', 'normal', 'low');
}


/* Reader */

// Inhalt
function cvtx_reader_contents() {
    global $post;
    $reader_id = $post->ID;
    $post_bak = $post;
    
    // get objects in reder term
    $items = array();
    $query = new WP_Query(array('taxonomy' => 'cvtx_tax_reader',
                                'term'     => 'cvtx_reader_'.intval($reader_id),
                                'orderby'  => 'meta_value',
                                'meta_key' => 'cvtx_sort',
                                'order'    => 'ASC',
                                'nopaging' => true));
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = $post->ID;
    }

    // list all contents
    $output = '<div class="cvtx_reader_toc" id="cvtx_reader_toc">';
    $query  = new WP_Query(array('post_type' => array('cvtx_top',
                                                      'cvtx_antrag',
                                                      'cvtx_aeantrag',
                                                      'cvtx_application'),
                                 'orderby'   => 'meta_value',
                                 'meta_key'  => 'cvtx_sort',
                                 'order'     => 'ASC',
                                 'nopaging'  => true));
    if ($query->have_posts()) {
        $open_top    = false;
        $open_antrag = false;
        while ($query->have_posts()) {
            $query->the_post();
            $title = get_the_title();
            if (empty($title)) $title = __('(no title)', 'cvtx');
            $checked = (in_array($post->ID, $items) ? 'checked="checked"' : '');
            $unpublished = ($post->post_status != 'publish' || ($post->post_type == 'cvtx_application' && !cvtx_get_file($post)) ? 'cvtx_reader_unpublished' : '');
            
            if ($post->post_type == 'cvtx_top') {
                if ($open_top) {
                    if ($open_antrag) {
                        $output     .= '</div>';
                        $open_antrag = false;
                    }
                    $output  .= '</div>';
                    $open_top = false;
                }
                $open_top = true;
                
                $output .= '<a name="cvtx_'.get_the_ID().'"></a>';
                $output .= '<div class="cvtx_reader_toc_top">';
                $output .= ' <label class="cvtx_top '.$unpublished.'">'.$title.'</label>';
                $output .= ' (<a href="#cvtx_'.get_the_ID().'" class="select_all">'.__('alle', 'cvtx').'</a>/';
                $output .=   '<a href="#cvtx_'.get_the_ID().'" class="select_none">'.__('keine', 'cvtx').'</a>)';
            } else if ($post->post_type == 'cvtx_antrag') {
                if ($open_antrag) { $output .= '</div>'; $open_antrag = false; }
                $open_antrag = true;
                
                $output .= '<a name="cvtx_'.get_the_ID().'"></a>';
                $output .= '<div class="cvtx_reader_toc_antrag">';
                $output .= ' <input type="checkbox" id="cvtx_antrag_'.get_the_ID().'" name="cvtx_post_ids['.get_the_ID().']" '.$checked.' /> ';
                $output .= ' <label class="cvtx_antrag '.$unpublished.'" for="cvtx_antrag_'.get_the_ID().'">'.$title.'</label>';
                $output .= ' (<a href="#cvtx_'.get_the_ID().'" class="select_all">'.__('alle', 'cvtx').'</a>/';
                $output .=   '<a href="#cvtx_'.get_the_ID().'" class="select_none">'.__('keine', 'cvtx').'</a>)';
                $output .= ' <br />';
            } else if ($post->post_type == 'cvtx_aeantrag') {
                $output .= '<div class="cvtx_reader_toc_aeantrag">';
                $output .= ' <input type="checkbox" id="cvtx_aeantrag_'.get_the_ID().'" name="cvtx_post_ids['.get_the_ID().']" '.$checked.' /> ';
                $output .= ' <label class="cvtx_aeantrag '.$unpublished.'" for="cvtx_aeantrag_'.get_the_ID().'">'.$title.'</label>';
                $output .= '</div>';
            } else if ($post->post_type == 'cvtx_application') {
                $output .= '<a name="cvtx_'.get_the_ID().'"></a>';
                $output .= '<div class="cvtx_reader_toc_application">';
                $output .= ' <input type="checkbox" id="cvtx_application_'.get_the_ID().'" name="cvtx_post_ids['.get_the_ID().']" '.$checked.' /> ';
                $output .= ' <label class="cvtx_application '.$unpublished.'" for="cvtx_application_'.get_the_ID().'">'.$title.'</label>';
                $output .= '</div>';
            }
        }
        if ($open_antrag) { $output .= '</div>'; $open_antrag = false; }
        if ($open_top)    { $output .= '</div>'; $open_top    = false; }
    }
    $output .= '</div> ';
    $output .= '<span class="description">'.__('Grau hinterlegte Einträge sind bisher nicht freigeschaltet und werden deshalb nicht im Reader angezeigt.', 'cvtx').'</span>';
    echo($output);
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
}


/* Tagesordnungspunkte */

// Metainformationen (TOP-Nummer und Kürzel)
function cvtx_top_meta() {
    global $post;

    echo('<label for="cvtx_top_ord_field">'.__('TOP-Nummer', 'cvtx').':</label><br />');
    echo('<input name="cvtx_top_ord" id="cvtx_top_ord_field" type="text" maxlength="4" value="'.get_post_meta($post->ID, 'cvtx_top_ord', true).'" />');
    echo('<br />');
    echo('<label for="cvtx_top_short_field">'.__('Kürzel', 'cvtx').':</label><br />');
    echo('<input name="cvtx_top_short" id="cvtx_top_short_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_top_short', true).'" />');
    echo('<br />');

    echo('<p id="admin_message" class="error">');
    echo(' <span id="unique_error_cvtx_top_ord" class="cvtx_unique_error">'.__('Diese Nummer ist bereits vergeben.', 'cvtx').'</span> ');
    echo(' <span id="unique_error_cvtx_top_short" class="cvtx_unique_error">'.__('Dieses Kürzel ist bereits vergeben.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_top_ord" class="cvtx_empty_error">'.__('Bitte TOP-Nummer vergeben.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_top_short" class="cvtx_empty_error">'.__('Bitte Kürzel für den TOP vergeben.', 'cvtx').'</span> ');
    echo('</p>');
    
    echo('<label for="cvtx_top_antraege">'.__('Hinzufügen folgender Inhalte ermöglichen', 'cvtx').':</label><br />');
    $enable_antrag = get_post_meta($post->ID, 'cvtx_top_antraege', true);
    $enable_antrag = ($enable_antrag == 'off' ? false : true);
    echo('<input name="cvtx_top_antraege" id="cvtx_top_antraege" type="checkbox" '.($enable_antrag ? 'checked="checked"' : '').' /> ');
    echo('<label for="cvtx_top_antraege">'.__('Anträge', 'cvtx').'</label>');
    echo(' ');
    $enable_appl = get_post_meta($post->ID, 'cvtx_top_applications', true);
    $enable_appl = ($enable_appl == 'on' ? true : false);
    echo('<input name="cvtx_top_applications" id="cvtx_top_applications" type="checkbox" '.($enable_appl ? 'checked="checked"' : '').' /> ');
    echo('<label for="cvtx_top_applications">'.__('Applications', 'cvtx').'</label>');
}


/* Anträge */

// Metainformationen (Antragsnummer, TOP)
function cvtx_antrag_meta() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);    
    
    echo('<label for="cvtx_antrag_top_select">'.__('Tagesordnungspunkt', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_tops($top_id, __('Keine Tagesordnungspunkte für Anträge angelegt', 'cvtx').'.', true, false));
    echo('<br />');
    echo('<label for="cvtx_antrag_ord_field">'.__('Antragsnummer', 'cvtx').':</label><br />');
    echo('<input name="cvtx_antrag_ord" id="cvtx_antrag_ord_field" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_antrag_ord', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo('<span id="unique_error_cvtx_antrag_ord" class="cvtx_unique_error">'.__('Es liegt bereits ein Antrag mit identischer Antragsnummer vor.', 'cvtx').'</span> ');
    echo('<span id="empty_error_cvtx_antrag_ord" class="cvtx_empty_error">'.__('Bitte Antragsnummer vergeben.', 'cvtx').'</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_antrag_steller() {
    global $post;
    echo('<label for="cvtx_antrag_steller_short">'.__('Kurzfassung', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_antrag_steller_short" name="cvtx_antrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_antrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_antrag_steller">'.get_post_meta($post->ID, 'cvtx_antrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_antrag_email">'.__('Kontakt (E-Mail)', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" value="'.get_post_meta($post->ID, 'cvtx_antrag_email', true).'" /> ');
    echo('<label for="cvtx_antrag_phone">'.__('Kontakt (Telefon)', 'cvtx').':</label> ');
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

    echo('<label for="cvtx_aeantrag_antrag_select">'.__('Antrag', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_antraege($antrag_id, __('Keine Tagesordnungspunkte angelegt', 'cvtx').'.'));
    echo('<br />');
    echo('<label for="cvtx_aeantrag_zeile_field">'.__('Zeile', 'cvtx').':</label><br />');
    echo('<input name="cvtx_aeantrag_zeile" id="cvtx_aeantrag_zeile_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo('<span id="unique_error_cvtx_aeantrag_zeile" class="cvtx_unique_error">'.__('Es liegt bereits ein Änderungsantrag mit identischer Zeilenangabe vor.', 'cvtx').'</span> ');
    echo('<span id="empty_error_cvtx_aeantrag_zeile" class="cvtx_empty_error">'.__('Bitte Zeile für den Änderungsantrag angeben.', 'cvtx').'</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_aeantrag_steller() {
    global $post;
    echo('<label for="cvtx_aeantrag_steller_short">'.__('Kurzfassung', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_aeantrag_steller_short" name="cvtx_aeantrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_steller">'.get_post_meta($post->ID, 'cvtx_aeantrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_aeantrag_email">'.__('Kontakt (E-Mail)', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_email', true).'" /> ');
    echo('<label for="cvtx_aeantrag_phone">'.__('Kontakt (Telefon)', 'cvtx').':</label> ');
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
    echo('<label for="cvtx_aeantrag_verfahren">'.__('Verfahren', 'cvtx').'</label> <select name="cvtx_aeantrag_verfahren" id="cvtx_aeantrag_verfahren"><option></option>');
    $verfahren = array(__('Übernahme', 'cvtx'), __('Modifizierte Übernahme', 'cvtx'), __('Abstimmung', 'cvtx'), __('Zurückgezogen', 'cvtx'), __('Erledigt', 'cvtx'));
    foreach ($verfahren as $verf) {
        echo('<option'.($verf == get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true) ? ' selected="selected"' : '').'>'.$verf.'</option>');
    }
    echo('</select> ');

    echo('<br />');
    
    echo('<textarea style="width: 100%" for="cvtx_aeantrag_detail" name="cvtx_aeantrag_detail">'.get_post_meta($post->ID, 'cvtx_aeantrag_detail', true).'</textarea>');
}


/* Applications */

// Metainformationen (application number, TOP)
function cvtx_application_meta() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_application_top', true);    
    
    echo('<label for="cvtx_antrag_top_select">'.__('Tagesordnungspunkt', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_tops($top_id, __('Keine Tagesordnungspunkte für Bewerbungen angelegt', 'cvtx').'.', false, true));
    echo('<br />');
    echo('<label for="cvtx_application_ord_field">'.__('Bewerbungsnummer', 'cvtx').':</label><br />');
    echo('<input name="cvtx_application_ord" id="cvtx_application_ord_field" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_application_ord', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo(' <span id="unique_error_cvtx_application_ord" class="cvtx_unique_error">'.__('Es liegt bereits eine Bewerbung mit identischer Bewerbungsnummer vor.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_application_ord" class="cvtx_empty_error">'.__('Bitte Bewerbungsnummer vergeben.', 'cvtx').'</span> ');
    echo('</p>');
}

add_action('post_edit_form_tag', 'cvtx_post_edit_form_tag');
/**
 * add "enctype="multipart/form-data" to application-edit-page
 */
function cvtx_post_edit_form_tag() {
    global $post;
    
    if ($post->post_type == 'cvtx_application') {
        echo(' enctype="multipart/form-data"');
    }
}

/**
 * Prints an application-upload-formular
 */
function cvtx_application_upload() {
    global $post;
    
    // get the attachments ID
    $download = cvtx_get_file($post);
    
    // an attachment has already been uploaded
    if ($download) {
        echo('<p><a href="'.$download.'">'.__('View application', 'cvtx').'</a></p>');
    } else {
        echo('<p>'.__('Bisher kein PDF hochgeladen.', 'cvtx').'</p>');
    }
    
    // actual form
    echo('<p>');
    echo(' <label for="cvtx_application_file">');
    echo(($download ? __('Bewerbung neu hochladen', 'cvtx') : __('Bewerbung hochladen', 'cvtx')));
    echo(':</label> ');
    echo(' <input type="file" name="cvtx_application_file" id="cvtx_application_file" />');
    echo('</p>');
}


/* Allgemeingültige Meta-Boxen */

/**
 * Link zum PDF
 */
function cvtx_metabox_pdf() {
    global $post;
    
    // check if pdf file exists
    if ($file = cvtx_get_file($post, 'pdf') ) {
        echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a> ');
    }
    // show info otherwise
    else {
        echo(__('Kein PDF verfügbar.', 'cvtx').' ');
    }

    if ($post->post_type != 'cvtx_application') {
        // check if tex file exists
        if ($file = cvtx_get_file($post, 'tex')) {
            echo('<a href="'.$file.'">(tex)</a> ');
        }
        // check if log file exists
        if ($file = cvtx_get_file($post, 'log')) {
            echo('<a href="'.$file.'">(log)</a> ');
        }
    }
}

/**
 * Readerzuordnung
 */
function cvtx_metabox_reader() {
    global $post;
    $post_bak = $post;
    
    // get terms of object
    $tax_items = array();
    if ($terms = wp_get_object_terms($post->ID, 'cvtx_tax_reader')) {
        foreach ($terms as $term) {
            $tax_items[] = $term->name;
        }
    }
    
    // get reader objects
    $items = array();
    $query = new WP_Query(array('post_type' => 'cvtx_reader',
                                'order'     => 'ASC',
                                'nopaging'  => true));
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            if (in_array('cvtx_reader_'.$post->ID, $tax_items)) {
                $items[] = get_the_title();
            }
        }
    }
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
    
    // any term+reader-combination?
    if (count($items) > 0) {
        if ($post->post_type == 'cvtx_antrag') {
            echo(__('Der Antrag erscheint in den folgenden Readern:', 'cvtx'));
        } else if ($post->post_type == 'cvtx_aeantrag') {
            echo(__('Der Änderungsantrag erscheint in den folgenden Readern:', 'cvtx'));
        } else if ($post->post_type == 'cvtx_application') {
            echo(__('Die Bewerbung erscheint in den folgenden Readern:', 'cvtx'));
        }
        
        echo('<ul class="zeichen">');
        foreach ($items as $item) {
            echo('<li>'.$item.'</li>');
        }
        echo('</ul>');
    } else {
        if ($post->post_type == 'cvtx_antrag') {
            echo(__('Der Antrag ist bisher keinem Reader zugeordnet.', 'cvtx'));
        } else if ($post->post_type == 'cvtx_aeantrag') {
            echo(__('Der Änderungsantrag ist bisher keinem Reader zugeordnet.', 'cvtx'));
        } else if ($post->post_type == 'cvtx_application') {
            echo(__('Die Bewerbung ist bisher keinem Reader zugeordnet.', 'cvtx'));
        }
    }
}


/* Update lists */

if (is_admin()) add_filter('manage_edit-cvtx_reader_columns', 'cvtx_reader_columns');
function cvtx_reader_columns($columns) {
    $columns = array('cb'                 => '<input type="checkbox" />',
                     'title'              => __('Reader', 'cvtx'),
                     'cvtx_reader_status' => '',
                     'date'               => __('Date', 'cvtx'));
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_top_columns', 'cvtx_top_columns');
function cvtx_top_columns($columns) {
    $columns = array('cb'              => '<input type="checkbox" />',
                     'title'           => __('Tagesordnungspunkt', 'cvtx'),
                     'cvtx_top_short'  => __('Kürzel', 'cvtx'),
                     'cvtx_top_status' => '',
                     'date'            => __('Date', 'cvtx'));
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_antrag_columns', 'cvtx_antrag_columns');
function cvtx_antrag_columns($columns) {
    $columns = array('cb'                  => '<input type="checkbox" />',
                     'title'               => __('Antragstitel', 'cvtx'),
                     'cvtx_antrag_steller' => __('AntragstellerIn(nen)', 'cvtx'),
                     'cvtx_antrag_top'     => __('Tagesordnungspunkt', 'cvtx'),
                     'cvtx_antrag_status'  => '',
                     'date'                => __('Date', 'cvtx'));
    return $columns;
}

// Register the column as sortable
if (is_admin()) add_filter('manage_edit-cvtx_antrag_sortable_columns', 'cvtx_register_sortable_antrag');
function cvtx_register_sortable_antrag($columns) {
    $columns['cvtx_antrag_steller'] = 'cvtx_antrag_steller';
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_application_columns', 'cvtx_application_columns');
function cvtx_application_columns($columns) {
    $columns = array('cb'                       => '<input type="checkbox" />',
                     'title'                    => __('Application', 'cvtx'),
                     'cvtx_application_status'  => '',
                     'date'                     => __('Date', 'cvtx'));
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_aeantrag_columns', 'cvtx_aeantrag_columns');
function cvtx_aeantrag_columns($columns) {
    $columns = array('cb'                      => '<input type="checkbox" />',
                     'title'                   => __('Amendment', 'cvtx'),
                     'cvtx_aeantrag_steller'   => __('AntragstellerIn(nen)', 'cvtx'),
                     'cvtx_aeantrag_verfahren' => __('Verfahren', 'cvtx'),
                     'cvtx_aeantrag_antrag'    => __('Antrag', 'cvtx'),
                     'cvtx_aeantrag_status'    => '',
                     'date'                    => __('Date', 'cvtx'));
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
        // Reader
        case 'cvtx_reader_status':
            echo(($post->post_status == 'publish' ? '+ ' : ''));
            if ($file = cvtx_get_file($post, 'pdf', 'url')) {
                echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>');
            }
            break;
            
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
        case 'cvtx_sort':
            echo(get_post_meta($post->ID, 'cvtx_sort', true));
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
                echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>');
            }
            break;
            
        // Ä-Anträge
        case 'cvtx_aeantrag_ord':
            echo(cvtx_get_short($post));
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
                echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>');
            }
            break;
            
        // Applications
        case 'cvtx_application_ord':
            echo(cvtx_get_short($post));
            break;
        case "cvtx_application_status":
            echo(($post->post_status == 'publish' ? '+ ' : ''));
            if ($file = cvtx_get_file($post, 'pdf', 'url')) {
                echo('<a href="'.$file.'">'.__('Download', 'cvtx').' (pdf)</a>');
            }
            break;
    }
}

if (is_admin()) add_filter('request', 'cvtx_order_lists');
function cvtx_order_lists($vars) {
    global $post_type;
    if (isset($vars['orderby'])) {
        // Anträge
        if ($vars['orderby'] == 'cvtx_antrag_ord' || $vars['orderby'] == 'cvtx_aeantrag_ord'
         || $vars['orderby'] == 'cvtx_top_ord'    || $vars['orderby'] == 'cvtx_application_ord'
         || ($vars['orderby'] == 'title' && ($post_type == 'cvtx_antrag'   || $post_type == 'cvtx_top'
                                          || $post_type == 'cvtx_aeantrag' || $post_type == 'cvtx_application'))) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_sort', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_antrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_steller_short', 'orderby' => 'meta_value'));
        }
        // Änderungsanträge
        else if ($vars['orderby'] == 'cvtx_aeantrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_steller_short', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_verfahren') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_verfahren', 'orderby' => 'meta_value'));
        }
    }

    return $vars;
}


if (is_admin()) add_action('admin_menu', 'cvtx_config_page');
function cvtx_config_page() {
    if (function_exists('add_submenu_page')) {
        add_submenu_page('plugins.php', 'cvtx '.__('Settings'), 'cvtx '.__('Settings'), 'manage_options', 'cvtx-config', 'cvtx_conf');
    }
}

function cvtx_conf() {
    if (isset($_POST['submit'])) {
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?'));
        }
        
        // Formatierung des Antragskürzels
        if (!isset($_POST['cvtx_antrag_format']) || empty($_POST['cvtx_antrag_format'])) {
            update_option('cvtx_antrag_format', '%top%-%antrag%');
        } else {
            update_option('cvtx_antrag_format', $_POST['cvtx_antrag_format']);
        }
        
        // Formatierung des Änderungsantragskürzels
        if (!isset($_POST['cvtx_aeantrag_format']) || empty($_POST['cvtx_aeantrag_format'])) {
            update_option('cvtx_aeantrag_format', '%antrag%-%zeile%');
        } else {
            update_option('cvtx_aeantrag_format', $_POST['cvtx_aeantrag_format']);
        }
        
        // PDF-Versionen für Änderungsanträge erzeugen?
        $aeantrag_pdf = isset($_POST['cvtx_aeantrag_pdf']) && $_POST['cvtx_aeantrag_pdf'];
        update_option('cvtx_aeantrag_pdf', $aeantrag_pdf);
        
        // LaTeX-Pfad
        if (isset($_POST['cvtx_pdflatex_cmd'])) {
            update_option('cvtx_pdflatex_cmd', $_POST['cvtx_pdflatex_cmd']);
        }
        
        // Pfad zu den LaTeX-Templates im Theme
        if (isset($_POST['cvtx_latex_tpldir']) && !empty($_POST['cvtx_latex_tpldir'])) {
            update_option('cvtx_latex_tpldir', $_POST['cvtx_latex_tpldir']);
        } else {
            update_option('cvtx_latex_tpldir', 'latex');
        }
        
        // remove tex and/or log files?
        if (isset($_POST['cvtx_drop_texfile'])) {
            update_option('cvtx_drop_texfile', intval($_POST['cvtx_drop_texfile']));
        }
        if (isset($_POST['cvtx_drop_logfile'])) {
            update_option('cvtx_drop_logfile', intval($_POST['cvtx_drop_logfile']));
        }
        
        // wordpress anonymous user
        if (isset($_POST['cvtx_anon_user'])) {
            update_option('cvtx_anon_user', intval($_POST['cvtx_anon_user']));
        }
        
        // recpatcha settings
        $use_recaptcha = isset($_POST['cvtx_use_recaptcha']) && $_POST['cvtx_use_recaptcha'];
        update_option('cvtx_use_recaptcha',        $use_recaptcha);
        update_option('cvtx_recaptcha_publickey',  $_POST['cvtx_recaptcha_publickey']);
        update_option('cvtx_recaptcha_privatekey', $_POST['cvtx_recaptcha_privatekey']);
        
        // mail settings
        $send_html_mail             = isset($_POST['cvtx_send_html_mail'])
                                         && $_POST['cvtx_send_html_mail'];
        $send_create_antrag_owner   = isset($_POST['cvtx_send_create_antrag_owner'])
                                         && $_POST['cvtx_send_create_antrag_owner'];
        $send_create_antrag_admin   = isset($_POST['cvtx_send_create_antrag_admin'])
                                         && $_POST['cvtx_send_create_antrag_admin'];
        $send_create_aeantrag_owner = isset($_POST['cvtx_send_create_aeantrag_owner'])
                                         && $_POST['cvtx_send_create_aeantrag_owner'];
        $send_create_aeantrag_admin = isset($_POST['cvtx_send_create_aeantrag_admin'])
                                         && $_POST['cvtx_send_create_aeantrag_admin'];
        update_option('cvtx_send_html_mail',             $send_html_mail);
        update_option('cvtx_send_from_email',            stripslashes($_POST['cvtx_send_from_email']));
        update_option('cvtx_send_rcpt_email',            stripslashes($_POST['cvtx_send_rcpt_email']));
        update_option('cvtx_send_create_antrag_owner',   $send_create_antrag_owner);
        update_option('cvtx_send_create_antrag_admin',   $send_create_antrag_admin);
        update_option('cvtx_send_create_aeantrag_owner', $send_create_aeantrag_owner);
        update_option('cvtx_send_create_aeantrag_admin', $send_create_aeantrag_admin);
        update_option('cvtx_send_create_antrag_owner_subject', $_POST['cvtx_send_create_antrag_owner_subject']);
        update_option('cvtx_send_create_antrag_owner_body',    $_POST['cvtx_send_create_antrag_owner_body']);
        update_option('cvtx_send_create_antrag_admin_subject', $_POST['cvtx_send_create_antrag_admin_subject']);
        update_option('cvtx_send_create_antrag_admin_body',    $_POST['cvtx_send_create_antrag_admin_body']);
        update_option('cvtx_send_create_aeantrag_owner_subject', $_POST['cvtx_send_create_aeantrag_owner_subject']);
        update_option('cvtx_send_create_aeantrag_owner_body',    $_POST['cvtx_send_create_aeantrag_owner_body']);
        update_option('cvtx_send_create_aeantrag_admin_subject', $_POST['cvtx_send_create_aeantrag_admin_subject']);
        update_option('cvtx_send_create_aeantrag_admin_body',    $_POST['cvtx_send_create_aeantrag_admin_body']);
        
        // default reader settings
        if (isset($_POST['cvtx_default_reader_antrag']) && is_array($_POST['cvtx_default_reader_antrag'])) {
            update_option('cvtx_default_reader_antrag', implode(', ', $_POST['cvtx_default_reader_antrag']));
        }
        if (isset($_POST['cvtx_default_reader_aeantrag']) && is_array($_POST['cvtx_default_reader_aeantrag'])) {
            update_option('cvtx_default_reader_aeantrag', implode(', ', $_POST['cvtx_default_reader_aeantrag']));
        }
        if (isset($_POST['cvtx_default_reader_application']) && is_array($_POST['cvtx_default_reader_application'])) {
            update_option('cvtx_default_reader_application', implode(', ', $_POST['cvtx_default_reader_application']));
        }
    }


    /* get settings */
    
    // cvtx settings
    $antrag_format              = get_option('cvtx_antrag_format');
    if (!$antrag_format)          $antrag_format   = '%top%-%antrag%';
    $aeantrag_format            = get_option('cvtx_aeantrag_format');
    if (!$aeantrag_format)        $aeantrag_format = '%antrag%-%zeile%';
    $aeantrag_pdf               = get_option('cvtx_aeantrag_pdf');
    $anon_user                  = get_option('cvtx_anon_user');
    if (!$anon_user)              $anon_user = 1;
    $default_reader_antrag      = get_option('cvtx_default_reader_antrag');
    $default_reader_aeantrag    = get_option('cvtx_default_reader_aeantrag');
    $default_reader_application = get_option('cvtx_default_reader_application');
    $reader = cvtx_get_reader();

    // mail settings
    $cvtx_send_html_mail = get_option('cvtx_send_html_mail');
    $send_from_email     = get_option('cvtx_send_from_email');
    if (!$send_from_email) $send_from_email = get_bloginfo('admin_email');
    $send_from_email     = stripslashes(htmlspecialchars($send_from_email));
    $send_rcpt_email     = get_option('cvtx_send_rcpt_email');
    if (!$send_rcpt_email) $send_rcpt_email = get_bloginfo('admin_email');
    $send_rcpt_email     = stripslashes(htmlspecialchars($send_rcpt_email));
    $sendantragowner     = get_option('cvtx_send_create_antrag_owner');
    $sendantragadmin     = get_option('cvtx_send_create_antrag_admin');
    $sendaeantragowner   = get_option('cvtx_send_create_aeantrag_owner');
    $sendaeantragadmin   = get_option('cvtx_send_create_aeantrag_admin');
    // mail design
    $sendantragowner_subject       = get_option('cvtx_send_create_antrag_owner_subject');
    if (!$sendantragowner_subject)   $sendantragowner_subject   = __('Antrag eingereicht „%titel%“', 'cvtx');
    $sendantragowner_body          = get_option('cvtx_send_create_antrag_owner_body');
    if (!$sendantragowner_body)      $sendantragowner_body      = __("Hej,\n\n"
                                                                 ."dein Antrag „%titel%“ zum %top% wurde erfolgreich eingereicht. "
                                                                 ."Bevor er auf der Website zu sehen sein wird, muss er "
                                                                 ."erst noch eine Antragsnummer bekommen und dann "
                                                                 ."freigeschaltet werden.\n\n"
                                                                 ."Zur Bestätigung hier nochmal deine Angaben:\n\n"
                                                                 ."%top%\n\n"
                                                                 ."%titel%\n\n"
                                                                 ."%antragstext%\n\n"
                                                                 ."Begründung:\n%begruendung%\n\n"
                                                                 ."AntragstellerInnen:\n%antragsteller%\n", 'cvtx');
    $sendantragadmin_subject       = get_option('cvtx_send_create_antrag_admin_subject');
    if (!$sendantragadmin_subject)   $sendantragadmin_subject   = __('Neuer Antrag eingereicht (%titel%)', 'cvtx');
    $sendantragadmin_body          = get_option('cvtx_send_create_antrag_admin_body');
    if (!$sendantragadmin_body)      $sendantragadmin_body      = __("Hej,\n\n"
                                                                 ."es wurde ein neuer Antrag zu %top% eingereicht. "
                                                                 ."Bitte prüfen und veröffentlichen!\n\n"
                                                                 .home_url('/wp-admin')."\n\n"
                                                                 ."%top%\n\n"
                                                                 ."%titel%\n\n"
                                                                 ."%antragstext%\n\n"
                                                                 ."Begründung:\n%begruendung%\n\n"
                                                                 ."AntragstellerInnen:\n%antragsteller%\n", 'cvtx');
    $sendaeantragowner_subject     = get_option('cvtx_send_create_aeantrag_owner_subject');
    if (!$sendaeantragowner_subject) $sendaeantragowner_subject = __('Änderungsantrag zu %antrag_kuerzel% (Zeile %zeile%) eingereicht', 'cvtx');
    $sendaeantragowner_body        = get_option('cvtx_send_create_aeantrag_owner_body');
    if (!$sendaeantragowner_body)    $sendaeantragowner_body    = __("Hej,\n\n"
                                                                 ."dein Änderungsantrag zum Antrag %antrag% wurde erfolgreich eingereicht. "
                                                                 ."Bevor er auf der Website zu sehen sein wird, muss er "
                                                                 ."erst noch eine Antragsnummer bekommen und dann "
                                                                 ."freigeschaltet werden.\n\n"
                                                                 ."Zur Bestätigung hier nochmal deine Angaben:\n\n"
                                                                 ."Antrag:\n%antrag%\n\n"
                                                                 ."Zeile:\n%zeile%\n\n"
                                                                 ."%antragstext%\n\n"
                                                                 ."Begründung:\n%begruendung%\n\n"
                                                                 ."AntragstellerInnen:\n%antragsteller%\n", 'cvtx');
    $sendaeantragadmin_subject     = get_option('cvtx_send_create_aeantrag_admin_subject');
    if (!$sendaeantragadmin_subject) $sendaeantragadmin_subject = __('Neuer Änderungsantrag zu %antrag_kuerzel% (Zeile %zeile%) erstellt', 'cvtx');
    $sendaeantragadmin_body        = get_option('cvtx_send_create_aeantrag_admin_body');
    if (!$sendaeantragadmin_body)    $sendaeantragadmin_body    = __("Hej,\n\n"
                                                                 ."es wurde ein neuer Änderungsantrag zum Antrag %antrag% eingereicht. "
                                                                 ."Bitte prüfen und veröffentlichen!\n\n"
                                                                 .home_url('/wp-admin')."\n\n"
                                                                 ."Antrag:\n%antrag%\n\n"
                                                                 ."Zeile:\n%zeile%\n\n"
                                                                 ."%antragstext%\n\n"
                                                                 ."Begründung:\n%begruendung%\n\n"
                                                                 ."AntragstellerInnen:\n%antragsteller%\n", 'cvtx');
    
    // reCaptcha settings
    $use_recpatcha        = get_option('cvtx_use_recaptcha');
    $recaptcha_publickey  = get_option('cvtx_recaptcha_publickey');
    $recaptcha_privatekey = get_option('cvtx_recaptcha_privatekey');
    
    // latex settings
    $pdflatex_cmd     = get_option('cvtx_pdflatex_cmd');
    $drop_texfile     = get_option('cvtx_drop_texfile');
    if (!$drop_texfile) $drop_texfile = 2;
    $drop_logfile     = get_option('cvtx_drop_logfile');
    if (!$drop_logfile) $drop_logfile = 2;
    $latex_tpldir     = get_option('cvtx_latex_tpldir');
    if (!$latex_tpldir) $latex_tpldir = 'latex';


    // print config page
    echo('<div class="wrap">');
    echo('<div id="icon-options-general" class="icon32"><br /></div>');
    echo('<h2>cvtx '.__('Settings').'</h2>');

    echo('<h2 class="nav-tab-wrapper" id="cvtx_navi">');
        echo('<a class="nav-tab cvtx_tool" href="#cvtx_tool">'.__('Antragstool', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_mail" href="#cvtx_mail">'.__('Benachrichtigungen', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_recaptcha" href="#cvtx_recaptcha">'.__('Spam-Schutz', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_latex" href="#cvtx_latex">'.__('LaTeX', 'cvtx').'</a>');
    echo('</h2>');
    
    echo('<form action="" method="post" id="cvtx-conf">');

    echo('<ul id="cvtx_options">');
    echo('<li id="cvtx_tool" class="active">'); 
        
        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_antrag_format">'.__('Kurzbezeichnung für Anträge und Bewerbungen', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_antrag_format" name="cvtx_antrag_format" type="text" value="'.$antrag_format.'" /> ');
                    echo('<span class="description">(%top%, %antrag%)</span>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_aeantrag_format">'.__('Kurzbezeichnung für Änderungsanträge', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_aeantrag_format" name="cvtx_aeantrag_format" type="text" value="'.$aeantrag_format.'" /> ');
                    echo('<span class="description">(%antrag%, %zeile%)</span>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_aeantrag_pdf">'.__('PDF-Erstellung', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                echo('<input id="cvtx_aeantrag_pdf" name="cvtx_aeantrag_pdf" type="checkbox" '
                          .($aeantrag_pdf ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_aeantrag_pdf">'.__('PDF-Versionen für Änderungsanträge erzeugen', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_anon_user">'.__('Anonymer Benutzer', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<select name="cvtx_anon_user" id="cvtx_anon_user">');
                    foreach (get_users() as $user) {
                        echo('<option'.($user->ID == $anon_user ? ' selected="selected" ' : '')
                             .' value="'.$user->ID.'">'.$user->user_login.'</option>');
                    }
                    echo('</select>');
                    echo(' <span class="description">'.__('Wordpress-Nutzer, dem alle anonym eingetragenen Anträge und Änderungsanträge zugeordnet werden.', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
            
        echo('<h4>'.__('Readerzuordnung', 'cvtx').'</h4>');
        
        echo('<table class="form-table">');    
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_antrag">'.__('Neue Anträge den folgenden Readern zuordnen', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    if (count($reader) > 0) {
                        echo('<select name="cvtx_default_reader_antrag[]" id="cvtx_default_reader_antrag" multiple="multiple">');
                        // list reader terms
                        foreach ($reader as $item) {
                            $selected = (strpos($default_reader_antrag, $item['term']) !== false ? 'selected="selected"' : '' );
                            echo('<option value="'.$item['term'].'" '.$selected.'>'.$item['title'].'</option>');
                        }
                        echo('</select>');
                    } else {
                        echo(__('Bisher keine Reader erstellt.', 'cvtx'));
                    }
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_aeantrag">'.__('Neue Änderungsanträge den folgenden Readern zuordnen', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    if (count($reader) > 0) {
                        echo('<select name="cvtx_default_reader_aeantrag[]" id="cvtx_default_reader_aeantrag" multiple="multiple">');
                        // list reader terms
                        foreach ($reader as $item) {
                            $selected = (strpos($default_reader_aeantrag, $item['term']) !== false ? 'selected="selected"' : '' );
                            echo('<option value="'.$item['term'].'" '.$selected.'>'.$item['title'].'</option>');
                        }
                    } else {
                        echo(__('Bisher keine Reader erstellt.', 'cvtx'));
                    }
                    echo('</select> ');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_application">'.__('Neue Bewerbungen den folgenden Readern zuordnen', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    if (count($reader) > 0) {
                        echo('<select name="cvtx_default_reader_application[]" id="cvtx_default_reader_application" multiple="multiple">');
                        // list reader terms
                        foreach ($reader as $item) {
                            $selected = (strpos($default_reader_application, $item['term']) !== false ? 'selected="selected"' : '' );
                            echo('<option value="'.$item['term'].'" '.$selected.'>'.$item['title'].'</option>');
                        }
                    } else {
                        echo(__('Bisher keine Reader erstellt.', 'cvtx'));
                    }
                    echo('</select> ');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
    echo('</li>');
 
     echo('<li id="cvtx_mail">');

        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_html_mail">'.__('HTML-Mail', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_html_mail"
                          name="cvtx_send_html_mail" type="checkbox" '.($cvtx_send_html_mail ? 'checked ="checked"' :'').'" /> ');
                    echo('<span class="description">'.__('E-Mail als HTML-Mail versenden', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_from_email">'.__('Absender-Adresse', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_from_email" name="cvtx_send_from_email" type="text" value="'.$send_from_email.'" />');
                    echo(' <span class="description">'.__('E-Mail-Adresse, die als Absender für Benachrichtigungen verwendet werden soll', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_rcpt_email">'.__('E-Mail-Adresse', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_rcpt_email" name="cvtx_send_rcpt_email" type="text" value="'.$send_rcpt_email.'" />');
                    echo(' <span class="description">'.__('E-Mail-Adresse, an welche Benachrichtigungen über neu erstellte Anträge gesendet werden', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
            
        echo('<h4>'.__('Neuer Antrag erstellt', 'cvtx').'</h4>');
        echo('<span class="description">'.__('Mögliche Felder: %top%, %top_kuerzel%, %titel%, %antragsteller%, %antragsteller_kurz%, %antragstext%, %begruendung%.', 'cvtx').'</span>');
        
        echo('<table class="form-table">');    
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner">'.__('E-Mail-Bestätigung', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_owner"'
                        .' name="cvtx_send_create_antrag_owner" type="checkbox"'
                        .($sendantragowner ? 'checked="checked"' : '').'" /> ');
                    echo('<span class="description">'.__('Dem Antragsteller wird eine E-Mail zur Bestätigung geschickt', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner_subject">'.__('Betreff', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_owner_subject" size="58"'
                        .' name="cvtx_send_create_antrag_owner_subject" type="text"'
                        .' value="'.$sendantragowner_subject.'" />');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign=top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner_body">'.__('Nachricht', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_owner_body"'
                        .' name="cvtx_send_create_antrag_owner_body">'.$sendantragowner_body.'</textarea>');
                 echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin">'.__('Admin-Information', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_admin" name="cvtx_send_create_antrag_admin"'
                        .' type="checkbox" '.($sendantragadmin ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_send_create_antrag_admin">'.__('Administrator eine E-Mail zur Information schicken', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin_subject">'.__('Betreff', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_admin_subject" size="58"'
                        .' name="cvtx_send_create_antrag_admin_subject" type="text"'
                        .' value="'.$sendantragadmin_subject.'" />');
                 echo('</td>');
             echo('</tr>');
                 
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin_body">'.__('Nachricht', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_admin_body" name="cvtx_send_create_antrag_admin_body">'
                         .$sendantragadmin_body.'</textarea>');
                 echo('</td>');
             echo('</tr>');
        echo('</table>');
             
        echo('<h4>'.__('Neuer Änderungsantrag erstellt', 'cvtx').'</h4>');
        echo('<span class="description">'.__('Mögliche Felder: %top%, %top_kuerzel%, %antrag%, %antrag_kuerzel%, %zeile%, %antragsteller%, %antragsteller_kurz%, %antragstext%, %begruendung%.', 'cvtx').'</span>');
            
        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner">'.__('Antragsteller-Mail', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_owner" name="cvtx_send_create_aeantrag_owner"'
                        .' type="checkbox" '.($sendaeantragowner ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_send_create_aeantrag_owner">'.__('Antragsteller eine E-Mail zur Bestätigung schicken', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
        
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner_subject">'.__('Betreff', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_owner_subject"'
                        .' name="cvtx_send_create_aeantrag_owner_subject" size="58" type="text"'
                        .' value="'.$sendaeantragowner_subject.'" />');
                echo('</td>');
            echo('</tr>');
             
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner_body">'.__('Nachricht', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_owner_body"'
                    .' name="cvtx_send_create_aeantrag_owner_body">'.$sendaeantragowner_body.'</textarea>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_admin">'.__('Admin-Information', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_admin" name="cvtx_send_create_aeantrag_admin"'
                        .' type="checkbox" '.($sendaeantragadmin ? 'checked="checked"' : '').'" /> ');
                       echo('<label for="cvtx_send_create_aeantrag_admin">'.__('Administrator eine E-Mail zur Information schicken', 'cvtx').'</label>');
                   echo('</td>');
               echo('</tr>');
               
               echo('<tr valign="top">');
                   echo('<th scope="row">');
                       echo('<label for="cvtx_send_create_aeantrag_admin_subject">'.__('Betreff', 'cvtx').'</label>');
                   echo('</th>');
                   echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_admin_subject"'
                        .' name="cvtx_send_create_aeantrag_admin_subject" size="58" type="text"'
                        .' value="'.$sendaeantragadmin_subject.'" />');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_admin_body">'.__('Nachricht', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_admin_body"'
                        .' name="cvtx_send_create_aeantrag_admin_body">'.$sendaeantragadmin_body.'</textarea>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
    echo('</li>');
    
    echo('<li id="cvtx_recaptcha">');
        
        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_use_recaptcha">'.__('Spam-Schutz aktivieren', 'cvtx').'</label>');
                echo('</th>');
                   echo('<td>');
                    echo('<input id="cvtx_use_recaptcha" name="cvtx_use_recaptcha"'
                        .' type="checkbox" '.($use_recpatcha ? 'checked="checked"' : ''). '" /> ');
                    echo('<span class="description">'.__('Um die Eingabe von Anträgen und Änderungsanträgen Spam-sicher zu machen, wird der Einsatz von reCaptcha empfohlen.', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
            
               echo('<tr valign="top">');
                   echo('<th scope="row">');
                       echo('<label for="cvtx_recaptcha_publickey">'.__('Öffentlicher reCaptcha-Schlüssel', 'cvtx').'</label>');
                   echo('</th>');
                   echo('<td>');
                       echo('<input id="cvtx_recaptcha_publickey" name="cvtx_recaptcha_publickey" type="text" value="'.$recaptcha_publickey.'" /> ');
                       echo('<span class="description">'.__('Schlüsselpaare können <a href="http://www.google.com/recaptcha/whyrecaptcha">hier</a> erzeugt werden.', 'cvtx').'</span>');
                   echo('</td>');
               echo('</tr>');

               echo('<tr valign="top">');
                   echo('<th scope="row">');
                       echo('<label for="cvtx_recaptcha_privatekey">'.__('Privater reCaptcha-Schlüssel', 'cvtx').'</label>');
                   echo('</th>');
                   echo('<td>');
                       echo('<input id="cvtx_recaptcha_privatekey" name="cvtx_recaptcha_privatekey" type="text" value="'.$recaptcha_privatekey.'" /> ');
                   echo('</td>');
               echo('</tr>');
        echo('</table>');
        
    echo('</li>');
    
    echo('<li id="cvtx_latex">');

        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_pdflatex_cmd">'.__('LaTeX to pdf path', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_pdflatex_cmd" name="cvtx_pdflatex_cmd" type="text" value="'.$pdflatex_cmd.'" /> ');
                    echo('<span class="description">'.__('Systempfad zur pdflatex-Anwendung', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label>'.__('Remove generated .tex-files', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<fieldset>');
                        echo('<input id="cvtx_drop_texfile_yes" name="cvtx_drop_texfile" type="radio"'
                            .' value="1" '.($drop_texfile == 1 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_texfile_yes">'.__('always', 'cvtx').'</label> ');
                        echo('<input id="cvtx_drop_texfile_if" name="cvtx_drop_texfile" type="radio"'
                            .' value="2" '.($drop_texfile == 2 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_texfile_if">'.__('if successfull', 'cvtx').'</label> ');
                        echo('<input id="cvtx_drop_texfile_no" name="cvtx_drop_texfile" type="radio"'
                            .' value="3" '.($drop_texfile == 3 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_texfile_no">'.__('never', 'cvtx').'</label>');
                    echo('</fieldset>');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign=top">');
                echo('<th scope="row">');
                    echo('<label>'.__('Remove generated .log-files', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<fieldset>');
                        echo('<input id="cvtx_drop_logfile_yes" name="cvtx_drop_logfile" type="radio"'
                            .' value="1" '.($drop_logfile == 1 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_logfile_yes">'.__('always', 'cvtx').'</label> ');
                        echo('<input id="cvtx_drop_logfile_if" name="cvtx_drop_logfile" type="radio"'
                            .' value="2" '.($drop_logfile == 2 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_logfile_if">'.__('if successfull', 'cvtx').'</label> ');
                        echo('<input id="cvtx_drop_logfile_no" name="cvtx_drop_logfile" type="radio"'
                            .' value="3" '.($drop_logfile == 3 ? 'checked="checked"' : '').'" /> ');
                        echo('<label for="cvtx_drop_logfile_no">'.__('never', 'cvtx').'</label>');
                    echo('</fieldset>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_latex_tpldir">'.__('User templates', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_latex_tpldir" name="cvtx_latex_tpldir" type="text" value="'.$latex_tpldir.'" /> ');
                    echo('<span class="description">'.__('Unterverzeichnis des aktivierten Themes, in dem spezielle LaTeX-Templates liegen', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
      echo('</li>');
    echo('</ul>');

    echo('<p class="submit"><input type="submit" name="submit" value="'.__('Save settings', 'cvtx').'" /></p>');
    echo('</form>');
    echo('</div>');
}


/**
 * Add Cvtx-Script and Styles to Admin Pages
 */
if (is_admin()) add_action('admin_enqueue_scripts', 'cvtx_admin_script');
function cvtx_admin_script() {
    wp_enqueue_style('cvtx_style', plugins_url('/cvtx_style.css', __FILE__));
    wp_enqueue_script('cvtx_script', plugins_url('/cvtx_script.js', __FILE__));
}


if (is_admin()) add_filter('post_row_actions', 'cvtx_hide_quick_edit', 10, 2);
/**
 * Hide the quickedit function in admin area
 */
function cvtx_hide_quick_edit($actions) {
    global $post, $cvtx_types;

    // hide quickedit only if cvtx post_type
    if (in_array($post->post_type, array_keys($cvtx_types))) {
        unset($actions['inline hide-if-no-js']);

        // hide preview if post type top or application
        if ($post->post_type == 'cvtx_top' || $post->post_type == 'cvtx_application') {
            unset($actions['view']);
        }
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


if (is_admin()) add_filter('mce_buttons', 'cvtx_mce_manage_buttons');
/**
 * Restrict first button row of the rich text editor
 *
 * @todo include 'formatselect'
 *
 * @param array $buttons rich edit buttons that are enabled
 */
function cvtx_mce_manage_buttons($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array('bold', 'italic', '|', 'bullist', 'numlist', '|', 'undo', 'redo', '|', 'formatselect');
    } else {
        return $buttons;
    }
}


if (is_admin()) add_filter('mce_buttons_2', 'cvtx_mce_manage_buttons_2');
/**
 * Restrict second button row of the rich text editor
 */
function cvtx_mce_manage_buttons_2($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array();
    } else {
        return $buttons;
    }
}


if (is_admin()) add_filter('tiny_mce_before_init', 'cvtx_mce_before_init');
/**
 * Restrict blockformats of the rich text editor
 */
function cvtx_mce_before_init($settings) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        $settings['theme_advanced_blockformats'] = __('Zwischenüberschrift', 'cvtx').'=h3; '.__('Unterüberschrift', 'cvtx').'=h4';
    }
    return $settings;
}

if (is_admin()) add_filter('add_menu_classes','show_pending_number');
/**
 * Add a count of pending antrage/aeatraege in the admin-sidebar
 */
function show_pending_number($menu) {
    foreach ($menu as $key => $sub) {
        $type = false;
        if (isset($sub[5]) && $sub[5] == 'menu-posts-cvtx_antrag') {
            $type = 'cvtx_antrag';
        } else if (isset($sub[5]) && $sub[5] == 'menu-posts-cvtx_aeantrag') {
            $type = 'cvtx_aeantrag';
        } else if (isset($sub[5]) && $sub[5] == 'menu-posts-cvtx_application') {
            $type = 'cvtx_application';
        }
        
        if ($type) {
            $count = cvtx_get_pending($type);
            $menu[$key][0] .= '<span class="awaiting-mod count-'.$count.'"><span class="pending-count">'.$count.'</span></span>';
        }
    }
    return $menu;
}

/**
 * Add a cvtx-item to the wp_admin_bar
 */
function cvtx_admin_bar_render(){
    global $wp_admin_bar;
    // Parent, directs to the cvtx-config-page
    $wp_admin_bar->add_menu(array(
        'id'    => 'cvtx',
        'title' => __('cvtx', 'cvtx'),
        'href'  => home_url('/wp-admin/plugins.php?page=cvtx-config')
    ));
    // link to cvtx_antrag
    $count = cvtx_get_pending('cvtx_antrag');
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_antrag',
        'title'  => __('Anträge', 'cvtx').' <span class="pending-count count-'.$count.'">'.$count.'</span</span>',
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_antrag'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_aeantrag
    $count = cvtx_get_pending('cvtx_aeantrag');
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_aeantrag',
        'title'  => __('Änderungsanträge', 'cvtx').' <span class="pending-count count-'.$count.'">'.$count.'</span</span>',
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_aeantrag'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_application
    $count = cvtx_get_pending('cvtx_application');
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_application',
        'title'  => __('Applications', 'cvtx').' <span class="pending-count count-'.$count.'">'.$count.'</span</span>',
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_application'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_top
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_tops',
        'title'  => __('TOPs', 'cvtx'),
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_top'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_reader
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_reader',
        'title'  => __('Reader', 'cvtx'),
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_reader'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx-config-page
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_config',
        'title'  => __('Settings'),
        'href'   => home_url('/wp-admin/plugins.php?page=cvtx-config'),
        'meta'   => array('class' => 'cvtx')
    ));
}
add_action('wp_before_admin_bar_render', 'cvtx_admin_bar_render');

/**
 * Return all posts of a specified type, which are either pending or draft
 * @param $type
 */
function cvtx_get_pending($type) {
    $count = wp_count_posts($type);
    return $count->pending + $count->draft;
}
?>
