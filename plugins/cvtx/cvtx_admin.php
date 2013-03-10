<?php
/**
 * @package cvtx
 */


/* add custom meta boxes */

if (is_admin()) add_action('add_meta_boxes', 'cvtx_add_meta_boxes');
function cvtx_add_meta_boxes() {
    // Reader
    add_meta_box('cvtx_reader_meta', __('Metadata', 'cvtx'),
                 'cvtx_reader_meta', 'cvtx_reader', 'side', 'high');
    add_meta_box('cvtx_reader_contents', __('Contents', 'cvtx'),
                 'cvtx_reader_contents', 'cvtx_reader', 'normal', 'high');
    add_meta_box('cvtx_reader_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_reader', 'side', 'low');
    
    // Agenda points
    add_meta_box('cvtx_top_meta', __('Metadata', 'cvtx'),
                 'cvtx_top_meta', 'cvtx_top', 'side', 'high');
    
    // Resolutions
    add_meta_box('cvtx_antrag_meta', __('Metadata', 'cvtx'),
                 'cvtx_antrag_meta', 'cvtx_antrag', 'side', 'high');
    add_meta_box('cvtx_antrag_steller', __('Author(s)', 'cvtx'),
                 'cvtx_antrag_steller', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_grund', __('Explanation', 'cvtx'),
                 'cvtx_antrag_grund', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_info', __('Remarks', 'cvtx'),
                 'cvtx_antrag_info', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_antrag', 'side', 'low');
    add_meta_box('cvtx_antrag_reader', __('Reader assignment', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_antrag', 'side', 'low');
    
    // Amendments
    add_meta_box('cvtx_aeantrag_meta', __('Metadata', 'cvtx'),
                 'cvtx_aeantrag_meta', 'cvtx_aeantrag', 'side', 'high');
    add_meta_box('cvtx_aeantrag_steller', __('Author(s)', 'cvtx'),
                 'cvtx_aeantrag_steller', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_grund', __('Explanation', 'cvtx'),
                 'cvtx_aeantrag_grund', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_verfahren', __('Procedure', 'cvtx'),
                 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_info', __('Remarks', 'cvtx'),
                 'cvtx_aeantrag_info', 'cvtx_aeantrag', 'normal', 'low');
    // show/hide pdf-box for of aeantrag
    if (get_option('cvtx_aeantrag_pdf')) {
        add_meta_box('cvtx_aeantrag_pdf', __('PDF', 'cvtx'),
                     'cvtx_metabox_pdf', 'cvtx_aeantrag', 'side', 'low');
    }
    add_meta_box('cvtx_aeantrag_reader', __('Reader assignment', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_aeantrag', 'side', 'low');

    // Applications
    add_meta_box('cvtx_application_meta', __('Metadata', 'cvtx'),
                 'cvtx_application_meta', 'cvtx_application', 'side', 'high');
    add_meta_box('cvtx_application_pdf', __('PDF', 'cvtx'),
                 'cvtx_metabox_pdf', 'cvtx_application', 'side', 'low');
    add_meta_box('cvtx_application_reader', __('Reader assignment', 'cvtx'),
                 'cvtx_metabox_reader', 'cvtx_application', 'side', 'low');
    add_meta_box('cvtx_application_form_name', __('Personal Data', 'cvtx'),
                 'cvtx_application_form_name', 'cvtx_application', 'normal', 'high');
    add_meta_box('cvtx_application_form_photo', __('Photo', 'cvtx'),
                 'cvtx_application_form_photo', 'cvtx_application', 'normal', 'high');
    add_meta_box('cvtx_application_form_cv', __('Life career', 'cvtx'),
                 'cvtx_application_form_cv', 'cvtx_application', 'normal', 'high');
}


/* Reader */

// Metainformationen (Anzeigestil)
function cvtx_reader_meta() {
    global $post;

    // fetch info
    $style = get_post_meta($post->ID, 'cvtx_reader_style', true);
    $book  = ($style == 'book'  || !$style ? 'checked="checked"' : '') || true;     // BUGGY!
    $table = ($style == 'table'            ? 'checked="checked"' : '') && false;    // BUGGY!
    
    // output    
    echo(__('Create PDF as', 'cvtx').'<br />');
    echo('<input name="cvtx_reader_style" id="cvtx_reader_style_book" value="book" type="radio" '.$book.' /> ');
    echo('<label for="cvtx_reader_style_book">'.__('book', 'cvtx').'</label><br />');
    /*
    echo('<input name="cvtx_reader_style" id="cvtx_reader_style_table" value="table" type="radio" '.$table.' /> ');
    echo('<label for="cvtx_reader_style_table">'.__('table of amendments', 'cvtx').'</label>');
    */
}


// Inhalt
function cvtx_reader_contents() {
    global $post;
    $reader_id = $post->ID;
    $post_bak = $post;
    
    // get objects in reder term
    $items = array();
    $query = new WP_Query(array('post_type' => array('cvtx_antrag',
                                                     'cvtx_aeantrag',
                                                     'cvtx_application'),
                                'taxonomy'  => 'cvtx_tax_reader',
                                'term'      => 'cvtx_reader_'.intval($reader_id),
                                'orderby'   => 'meta_value',
                                'meta_key'  => 'cvtx_sort',
                                'order'     => 'ASC',
                                'nopaging'  => true));
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
                $output .= ' (<a href="#cvtx_'.get_the_ID().'" class="select_all">'.__('all', 'cvtx').'</a>/';
                $output .=   '<a href="#cvtx_'.get_the_ID().'" class="select_none">'.__('none', 'cvtx').'</a>)';
            } else if ($post->post_type == 'cvtx_antrag') {
                if ($open_antrag) { $output .= '</div>'; $open_antrag = false; }
                $open_antrag = true;
                
                $output .= '<a name="cvtx_'.get_the_ID().'"></a>';
                $output .= '<div class="cvtx_reader_toc_antrag">';
                $output .= ' <input type="checkbox" id="cvtx_antrag_'.get_the_ID().'" name="cvtx_post_ids['.get_the_ID().']" '.$checked.' /> ';
                $output .= ' <label class="cvtx_antrag '.$unpublished.'" for="cvtx_antrag_'.get_the_ID().'">'.$title.'</label>';
                $output .= ' (<a href="#cvtx_'.get_the_ID().'" class="select_all">'.__('all', 'cvtx').'</a>/';
                $output .=   '<a href="#cvtx_'.get_the_ID().'" class="select_none">'.__('none', 'cvtx').'</a>)';
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
    $output .= '<span class="description">'.__('Items in gray have not yet been published and therefor they will not be published in the reader.', 'cvtx').'</span>';
    echo($output);
    
    // reset data
    wp_reset_postdata();
    $post = $post_bak;
}


/* Tagesordnungspunkte */

// Metainformationen (TOP-Nummer und Kürzel)
function cvtx_top_meta() {
    global $post;

    echo('<label for="cvtx_top_ord_field">'.__('Number of agenda point', 'cvtx').':</label><br />');
    echo('<input name="cvtx_top_ord" id="cvtx_top_ord_field" type="text" maxlength="4" value="'.get_post_meta($post->ID, 'cvtx_top_ord', true).'" />');
    echo('<br />');
    echo('<label for="cvtx_top_short_field">'.__('Token', 'cvtx').':</label><br />');
    echo('<input name="cvtx_top_short" id="cvtx_top_short_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_top_short', true).'" />');
    echo('<br />');

    echo('<p id="admin_message" class="error">');
    echo(' <span id="unique_error_cvtx_top_ord" class="cvtx_unique_error">'.__('This number is used.', 'cvtx').'</span> ');
    echo(' <span id="unique_error_cvtx_top_short" class="cvtx_unique_error">'.__('This token is used.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_top_ord" class="cvtx_empty_error">'.__('Please insert number.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_top_short" class="cvtx_empty_error">'.__('Please insert token.', 'cvtx').'</span> ');
    echo('</p>');
    
    echo('<label for="cvtx_top_antraege">'.__('Enable the following contents', 'cvtx').':</label><br />');
    $enable_antrag = get_post_meta($post->ID, 'cvtx_top_antraege', true);
    $enable_antrag = ($enable_antrag == 'off' ? false : true);
    echo('<input name="cvtx_top_antraege" id="cvtx_top_antraege" type="checkbox" '.($enable_antrag ? 'checked="checked"' : '').' /> ');
    echo('<label for="cvtx_top_antraege">'.__('Resolutions', 'cvtx').'</label>');
    echo(' ');
    $enable_appl = get_post_meta($post->ID, 'cvtx_top_applications', true);
    $enable_appl = ($enable_appl == 'on' ? true : false);
    echo('<input name="cvtx_top_applications" id="cvtx_top_applications" type="checkbox" '.($enable_appl ? 'checked="checked"' : '').' /> ');
    echo('<label for="cvtx_top_applications">'.__('Applications', 'cvtx').'</label>');
    
    echo('<br />');
    $appendix = get_post_meta($post->ID, 'cvtx_top_appendix', true);
    $appendix = ($appendix == 'on' ? true : false);
    echo('<input name="cvtx_top_appendix" id="cvtx_top_appendix" type="checkbox" '.($appendix ? 'checked="checked"' : '').' /> ');
    echo('<label for="cvtx_top_appendix">'.__('View as appendix', 'cvtx').'</label>');
}


/* Anträge */

// Metainformationen (Antragsnummer, TOP)
function cvtx_antrag_meta() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_antrag_top', true);    
    
    echo('<label for="cvtx_antrag_top_select">'.__('Agenda point', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_tops($top_id, __('No agenda points enabled to resolutions.', 'cvtx'), true, ''));
    echo('<br />');
    echo('<label for="cvtx_antrag_ord_field">'.__('Resolution number', 'cvtx').':</label><br />');
    echo('<input name="cvtx_antrag_ord" id="cvtx_antrag_ord_field" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_antrag_ord', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo('<span id="unique_error_cvtx_antrag_ord" class="cvtx_unique_error">'.__('This number is used.', 'cvtx').'</span> ');
    echo('<span id="empty_error_cvtx_antrag_ord" class="cvtx_empty_error">'.__('Please insert number.', 'cvtx').'</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_antrag_steller() {
    global $post;
    echo('<label for="cvtx_antrag_steller_short">'.__('Author(s) short', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_antrag_steller_short" name="cvtx_antrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_antrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_antrag_steller">'.get_post_meta($post->ID, 'cvtx_antrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_antrag_email">'.__('E-mail address', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" value="'.get_post_meta($post->ID, 'cvtx_antrag_email', true).'" /> ');
    echo('<label for="cvtx_antrag_phone">'.__('Mobile number', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_antrag_phone" name="cvtx_antrag_phone" value="'.get_post_meta($post->ID, 'cvtx_antrag_phone', true).'" />');
}

// Begründung
function cvtx_antrag_grund() {
    global $post;
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, 'cvtx_antrag_grund', true), 'cvtx_antrag_grund_admin', 
      	array('media_buttons' => false,
              'textarea_name' => 'cvtx_antrag_grund',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
	    echo('<textarea style="width: 100%" for="cvtx_antrag_grund" name="cvtx_antrag_grund">'.get_post_meta($post->ID, 'cvtx_antrag_grund', true).'</textarea>');
    }
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

    echo('<label for="cvtx_aeantrag_antrag_select">'.__('Resolution', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_antraege($antrag_id, __('No agenda created', 'cvtx').'.'));
    echo('<br />');
    echo('<label for="cvtx_aeantrag_zeile_field">'.__('Line', 'cvtx').':</label><br />');
    echo('<input name="cvtx_aeantrag_zeile" id="cvtx_aeantrag_zeile_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo('<span id="unique_error_cvtx_aeantrag_zeile" class="cvtx_unique_error">'.__('There is another amendment concering this line.', 'cvtx').'</span> ');
    echo('<span id="empty_error_cvtx_aeantrag_zeile" class="cvtx_empty_error">'.__('Please insert line.', 'cvtx').'</span> ');
    echo('</p>');
}

// Antragsteller
function cvtx_aeantrag_steller() {
    global $post;
    echo('<label for="cvtx_aeantrag_steller_short">'.__('Author(s) short', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_aeantrag_steller_short" name="cvtx_aeantrag_steller_short" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true).'" /><br />');
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_steller">'.get_post_meta($post->ID, 'cvtx_aeantrag_steller', true).'</textarea><br />');
    echo('<label for="cvtx_aeantrag_email">'.__('E-mail address', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_email', true).'" /> ');
    echo('<label for="cvtx_aeantrag_phone">'.__('Mobile phone', 'cvtx').':</label> ');
    echo('<input type="text" id="cvtx_aeantrag_phone" name="cvtx_aeantrag_phone" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_phone', true).'" />');
}

// Begründung
function cvtx_aeantrag_grund() {
    global $post;
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true), 'cvtx_aeantrag_grund_admin', 
      	array('media_buttons' => false,
              'textarea_name' => 'cvtx_aeantrag_grund',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
	    echo('<textarea style="width: 100%" for="cvtx_aeantrag_grund" name="cvtx_aeantrag_grund">'.get_post_meta($post->ID, 'cvtx_aeantrag_grund', true).'</textarea>');
    }
}

// Weitere Infos
function cvtx_aeantrag_info() {
    global $post;
    echo('<textarea style="width: 100%" name="cvtx_aeantrag_info">'.get_post_meta($post->ID, 'cvtx_aeantrag_info', true).'</textarea>');
}

// Verfahren
function cvtx_aeantrag_verfahren() {
    global $post;
    echo('<label for="cvtx_aeantrag_verfahren">'.__('Procedure', 'cvtx').'</label> <select name="cvtx_aeantrag_verfahren" id="cvtx_aeantrag_verfahren"><option></option>');
    $verfahren = array(__('Adoption', 'cvtx'), __('Modified adoption', 'cvtx'), __('Vote', 'cvtx'), __('Withdrawn', 'cvtx'), __('Obsolete', 'cvtx'));
    foreach ($verfahren as $verf) {
        echo('<option'.($verf == get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true) ? ' selected="selected"' : '').'>'.$verf.'</option>');
    }
    echo('</select> ');

    echo('<br />');
    
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true), 'cvtx_aeantrag_detail', 
      	array('media_buttons' => false,
              'textarea_name' => 'cvtx_aeantrag_detail',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
	    echo('<textarea style="width: 100%" for="cvtx_aeantrag_detail" name="cvtx_aeantrag_detail">'.get_post_meta($post->ID, 'cvtx_aeantrag_detail', true).'</textarea>');
    }
}


/* Applications */

// Metainformationen (application number, TOP)
function cvtx_application_meta() {
    global $post;
    $top_id = get_post_meta($post->ID, 'cvtx_application_top', true);    
    
    echo('<label for="cvtx_antrag_top_select">'.__('Agenda point', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_tops($top_id, __('No agenda points enabled to applications.', 'cvtx').'.', '', true));
    echo('<br />');
    echo('<label for="cvtx_application_ord_field">'.__('Application number', 'cvtx').':</label><br />');
    echo('<input name="cvtx_application_ord" id="cvtx_application_ord_field" type="text" maxlength="5" value="'.get_post_meta($post->ID, 'cvtx_application_ord', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo(' <span id="unique_error_cvtx_application_ord" class="cvtx_unique_error">'.__('This number is used.', 'cvtx').'</span> ');
    echo(' <span id="empty_error_cvtx_application_ord" class="cvtx_empty_error">'.__('Please insert number.', 'cvtx').'</span> ');
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

// Name and first name
function cvtx_application_form_name() {
    global $post;
    echo('<label for="cvtx_application_prename">'.__('First name', 'cvtx').'</label>');
    echo('<input type="text" id="cvtx_application_prename" name="cvtx_application_prename" value="'.get_post_meta($post->ID, 'cvtx_application_prename', true).'" /><br />');
    echo('<label for="cvtx_application_surname">'.__('Family name', 'cvtx').'</label>');
    echo('<input type="text" id="cvtx_application_surname" name="cvtx_application_surname" value="'.get_post_meta($post->ID, 'cvtx_application_surname', true).'" /><br />');
}

// Image upload
function cvtx_application_form_photo() {
    global $post;
    global $cvtx_allowed_image_types;
    
    // get the attachments ID
    $image = get_post_meta($post->ID, 'cvtx_application_photo_id', true);
    // an attachment has already been uploaded
    if ($image) {
        echo('<p>'.wp_get_attachment_link($image,'thumbnail').'</p>');
    } else {
        echo('<p>'.__('No image uploaded yet.', 'cvtx').'</p>');
    }
    
    // actual form
    echo('<p>');
    echo(' <label for="cvtx_application_photo">');
    echo(($image ? __('Update photo', 'cvtx') : __('Upload photo', 'cvtx')));
    echo(':</label> ');
    echo(' <input type="file" name="cvtx_application_photo" id="cvtx_application_photo" />');
    echo('</p>');
    echo('<p><small>');
    $max_image_size = get_option('cvtx_max_image_size');
    echo(__('Allowed file endings: ','cvtx'));
    $i = 0;
    foreach($cvtx_allowed_image_types as $ending => $type) {
        echo '<span class="ending">'.$ending.'</span>';
        if($i++ != count($cvtx_allowed_image_types)-1) {
            echo ', ';
        }
    }
    echo('. '.__('Max. file size: ','cvtx').$max_image_size.' KB');
    echo('</small></p>');
}

// CV of a candidate
function cvtx_application_form_cv() {
    global $post;
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, 'cvtx_application_cv', true), 'cvtx_application_cv_admin', 
      	array('media_buttons' => false,
              'textarea_name' => 'cvtx_application_cv',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
	    echo('<textarea style="width: 100%" for="cvtx_application_cv" name="cvtx_application_cv">'.get_post_meta($post->ID, 'cvtx_application_cv', true).'</textarea>');
    }
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
        echo(__('No PDF available.', 'cvtx').' ');
    }

    // check if tex file exists
    if ($file = cvtx_get_file($post, 'tex')) {
        echo('<a href="'.$file.'">(tex)</a> ');
    }
    // check if log file exists
    if ($file = cvtx_get_file($post, 'log')) {
        echo('<a href="'.$file.'">(log)</a> ');
    }
    
    // If application, enable manual upload of pdf files
    if ($post->post_type == 'cvtx_application') {
        // fetch manually or automatic generation mode?
        $manually = (get_post_meta($post->ID, 'cvtx_application_manually', true) == "on" ? ' checked="checked"' : '');
        
        // actual form
        echo('<p>');
        echo(' <input type="checkbox" name="cvtx_application_manually" id="cvtx_application_manually" '.$manually.' />');
        echo(' <label for="cvtx_application_manually">'.__('Manually upload application', 'cvtx').'</label><br />');
        echo(' <input type="file" name="cvtx_application_file" id="cvtx_application_file" />');
        echo('</p>');
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
            echo(__('The resolution appears in the following readers:', 'cvtx'));
        } else if ($post->post_type == 'cvtx_aeantrag') {
            echo(__('The amendment appears in the following readers:', 'cvtx'));
        } else if ($post->post_type == 'cvtx_application') {
            echo(__('The application appears in the following readers:', 'cvtx'));
        }
        
        echo('<ul class="zeichen">');
        foreach ($items as $item) {
            echo('<li>'.$item.'</li>');
        }
        echo('</ul>');
    } else {
        if ($post->post_type == 'cvtx_antrag') {
            echo(__('The resolution is not assigned to any reader.', 'cvtx'));
        } else if ($post->post_type == 'cvtx_aeantrag') {
            echo(__('The amendment is not assigned to any reader.', 'cvtx'));
        } else if ($post->post_type == 'cvtx_application') {
            echo(__('The application is not assigned to any reader.', 'cvtx'));
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
                     'title'           => __('Agenda point', 'cvtx'),
                     'cvtx_top_short'  => __('Token', 'cvtx'),
                     'cvtx_top_status' => '',
                     'date'            => __('Date', 'cvtx'));
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_antrag_columns', 'cvtx_antrag_columns');
function cvtx_antrag_columns($columns) {
    $columns = array('cb'                  => '<input type="checkbox" />',
                     'title'               => __('Resolution', 'cvtx'),
                     'cvtx_antrag_steller' => __('Author(s)', 'cvtx'),
                     'cvtx_antrag_top'     => __('Agenda point', 'cvtx'),
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
                     'cvtx_application_top'     => __('Agenda point', 'cvtx'),
                     'cvtx_application_status'  => '',
                     'date'                     => __('Date', 'cvtx'));
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_aeantrag_columns', 'cvtx_aeantrag_columns');
function cvtx_aeantrag_columns($columns) {
    $columns = array('cb'                      => '<input type="checkbox" />',
                     'title'                   => __('Amendment', 'cvtx'),
                     'cvtx_aeantrag_steller'   => __('Author(s)', 'cvtx'),
                     'cvtx_aeantrag_verfahren' => __('Procedure', 'cvtx'),
                     'cvtx_aeantrag_antrag'    => __('Resolution', 'cvtx'),
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
        case "cvtx_application_top":
            $top_id = get_post_meta($post->ID, 'cvtx_application_top', true);
            echo(get_the_title($top_id));
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


if (is_admin()) add_action('admin_notices', 'cvtx_admin_notices');
/**
 * Checks if the plugins HTML-Purified and WP-reCAPTCHA are installed
 */
function cvtx_admin_notices() {
    global $cvtx_types, $post_type;
    $plugins = array();
    $screen = get_current_screen();
    
    // Check if in cvtx area
    if (in_array($post_type, array_keys($cvtx_types)) || $screen->base == "settings_page_cvtx-config") {
        // Check for HTML Purified
        if (!is_plugin_active('html-purified/html-purified.php')) {
            $plugins[0] = '<a href="http://wordpress.org/extend/plugins/html-purified/">HTML Purified</a>';
        }
        // Check for WP-reCaptcha
        if (!is_plugin_active('wp-recaptcha/wp-recaptcha.php')) {
            $plugins[1] = '<a href="http://wordpress.org/extend/plugins/wp-recaptcha/">WP-reCAPTCHA</a>';
        }
        
        // Plugins missing?
        if (!empty($plugins)) {
            echo('<div class="updated">');
            echo('<p><b>'.__('To unleash the full power of cvtx Agenda Plugin, we recommend you to install and activate the following plugin(s):', 'cvtx').'</b>');
            echo('<ul style="list-style: disc; padding-left: 20px; margin-top: 0px;">');
            foreach ($plugins as $plugin) {
                echo('<li>'.$plugin.'</li>');
            }
            echo('</ul></div>');
        }
    }
}

add_filter('plugin_action_links_'.CVTX_PLUGIN_FILE, 'cvtx_settings_link');
/**
 * Add settings link on plugin page
 */
function cvtx_settings_link($links) { 
    $settings_link = '<a href="options-general.php?page=cvtx-config.php">'.__('Settings', 'cvtx').'</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
}


if (is_admin()) add_action('before_delete_post', 'cvtx_before_delete_post');
/**
 * Removes all latex files if custom post type is deleted. // buggy
 *
 * @todo drop cvtx_aeantraege when cvtx_antrag deleted? drop cvtx_antrag when cvtx_top deleted?
 */
function cvtx_before_delete_post($post_id) {
    $post = get_post($post_id);
    
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
        }
        
        if (isset($query) && $query != null && $query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post(get_the_ID(), true);
            }
        }
        
        if ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'
         || $post->post_type == 'cvtx_reader' || $post->post_type == 'cvtx_application') {
            $query2 = new WP_Query(array('post_type'   => 'attachment',
                                         'post_status' => 'any',
                                         'nopaging'    => true,
                                         'post_parent' => $post->ID));
            while ($query2->have_posts()) {
                $query2->the_post();
                wp_delete_attachment(get_the_ID(), true);
            }
        }
    }
}


if (is_admin()) add_action('wp_trash_post', 'cvtx_trash_post');
/**
 * Moves all child data to the trash.
 */
function cvtx_trash_post($post_id) {
    $post = get_post($post_id);

    if (is_object($post)) {
        if ($post->post_type == 'cvtx_top') {
            $query = new WP_Query(array('post_type'   => array('cvtx_antrag', 'cvtx_application'),
                                        'post_status' => 'any',
                                        'nopaging'    => true,
                                        'meta_query'  => array('relation' => 'OR',
                                                               array('key'     => 'cvtx_antrag_top',
                                                                     'value'   => $post->ID,
                                                                     'compare' => '='),
                                                               array('key'     => 'cvtx_application_top',
                                                                     'value'   => $post->ID,
                                                                     'compare' => '='))));
        } else if ($post->post_type == 'cvtx_antrag') {
            $query = new WP_Query(array('post_type'   => 'cvtx_aeantrag',
                                        'post_status' => 'any',
                                        'nopaging'    => true,
                                        'meta_query'  => array(array('key'     => 'cvtx_aeantrag_antrag',
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


add_filter('posts_search', 'cvtx_posts_search');
function cvtx_posts_search($search) {
    global $wpdb, $cvtx_types;
    
    if (preg_match('/ AND \(\(\(('.$wpdb->posts.'\.post_title LIKE \'%(.*)%\')\) OR \(('.$wpdb->posts.'\.post_content LIKE \'%(.*)%\')\)\)\)/', $search, $parts) && count($parts) == 5) {
        $conds   = array($parts[1], $parts[3]);
        $conds[] = "{$wpdb->posts}.ID IN (SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_value LIKE '%".$parts[2]."%')";
        
        $antrag   = str_replace(array(__('%agenda_point%', 'cvtx'), __('%resolution%', 'cvtx')), '([\w]+)', preg_quote(get_option('cvtx_antrag_format'), '/'));
        $aeantrag = str_replace(array(__('%resolution%', 'cvtx'), __('%line%', 'cvtx')), array($antrag, '([\w]+)'), preg_quote(get_option('cvtx_aeantrag_format'), '/'));
        if (preg_match('/'.$aeantrag.'/i', $parts[2], $match)) {
            $conds[] = "(SELECT {$wpdb->postmeta}.meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_aeantrag_zeile'\n"
                      ."  LIMIT 1) LIKE '".$match[3]."%'\n"
                      ."    AND\n"
                      ."(SELECT {$wpdb->postmeta}.meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_aeantrag_antrag'\n"
                      ."  LIMIT 1) = \n"
                      ."(SELECT {$wpdb->postmeta}.post_id\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.meta_key   = 'cvtx_antrag_ord'\n"
                      ."    AND {$wpdb->postmeta}.meta_value = '".$match[2]."'\n"
                      ."    AND {$wpdb->postmeta}.post_id IN\n"
                      ."                    (SELECT {$wpdb->postmeta}.post_id\n"
                      ."                       FROM {$wpdb->postmeta}\n"
                      ."                      WHERE {$wpdb->postmeta}.meta_key   = 'cvtx_antrag_top'\n"
                      ."                        AND {$wpdb->postmeta}.meta_value = (SELECT {$wpdb->postmeta}.post_id\n"
                      ."                                                              FROM {$wpdb->postmeta}\n"
                      ."                                                             WHERE {$wpdb->postmeta}.meta_key   = 'cvtx_top_short'\n"
                      ."                                                               AND {$wpdb->postmeta}.meta_value = '".$match[1]."'\n"
                      ."                                                             LIMIT 1))\n"
                      ."  LIMIT 1)";
        }
        
        if (preg_match('/'.$antrag.'/i', $parts[2], $match)) {
            $conds[] = "(SELECT meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_antrag_ord'\n"
                      ."  LIMIT 1) LIKE '".$match[2]."%'\n"
                      ."    AND\n"
                      ."(SELECT meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_antrag_top'\n"
                      ."  LIMIT 1) =\n"
                      ."(SELECT post_id\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.meta_key = 'cvtx_top_short'\n"
                      ."    AND {$wpdb->postmeta}.meta_value = '".$match[1]."'\n"
                      ."  LIMIT 1)\n"
                      ."    AND {$wpdb->posts}.post_type = 'cvtx_antrag'\n";
            $conds[] = "(SELECT meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = (SELECT meta_value\n"
                      ."                                        FROM {$wpdb->postmeta}\n"
                      ."                                       WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."                                         AND {$wpdb->postmeta}.meta_key = 'cvtx_aeantrag_antrag'\n"
                      ."                                       LIMIT 1)\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_antrag_ord'\n"
                      ."  LIMIT 1) LIKE '".$match[2]."%'\n"
                      ."    AND\n"
                      ."(SELECT meta_value\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.post_id  = (SELECT meta_value\n"
                      ."                                        FROM {$wpdb->postmeta}\n"
                      ."                                       WHERE {$wpdb->postmeta}.post_id  = {$wpdb->posts}.ID\n"
                      ."                                         AND {$wpdb->postmeta}.meta_key = 'cvtx_aeantrag_antrag'\n"
                      ."                                       LIMIT 1)\n"
                      ."    AND {$wpdb->postmeta}.meta_key = 'cvtx_antrag_top'\n"
                      ."  LIMIT 1) =\n"
                      ."(SELECT post_id\n"
                      ."   FROM {$wpdb->postmeta}\n"
                      ."  WHERE {$wpdb->postmeta}.meta_key = 'cvtx_top_short'\n"
                      ."    AND {$wpdb->postmeta}.meta_value = '".$match[1]."'\n"
                      ."  LIMIT 1)\n"
                      ."    AND {$wpdb->posts}.post_type = 'cvtx_aeantrag'\n";
        }
        
        $search = " AND ((\n".implode($conds, ")\n\n OR (").")) ";
    }
    return $search;
}


if (is_admin()) add_action('admin_menu', 'cvtx_config_page');
function cvtx_config_page() {
    if (function_exists('add_submenu_page')) {
        add_submenu_page('options-general.php', __('cvtx Agenda Plugin', 'cvtx'), __('cvtx Agenda Plugin', 'cvtx'), 'manage_options', 'cvtx-config', 'cvtx_conf');
    }
}

function cvtx_conf() {
    if (isset($_POST['submit'])) {
        if (function_exists('current_user_can') && !current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?'));
        }
        
        // Formatierung des Antragskürzels
        if (!isset($_POST['cvtx_antrag_format']) || empty($_POST['cvtx_antrag_format'])) {
            update_option('cvtx_antrag_format', __('%agenda_point%', 'cvtx').'-'.__('%resolution%', 'cvtx'));
        } else {
            update_option('cvtx_antrag_format', $_POST['cvtx_antrag_format']);
        }
        
        // Formatierung des Änderungsantragskürzels
        if (!isset($_POST['cvtx_aeantrag_format']) || empty($_POST['cvtx_aeantrag_format'])) {
            update_option('cvtx_aeantrag_format', __('%resolution%', 'cvtx').'-'.__('%line%', 'cvtx'));
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
        
        // Privacy message
        if (isset($_POST['cvtx_privacy_message'])) {
        	update_option('cvtx_privacy_message', $_POST['cvtx_privacy_message']);
        }

        // Phone required
        $cvtx_phone_required        = isset($_POST['cvtx_phone_required'])
                                         && $_POST['cvtx_phone_required'];
        update_option('cvtx_phone_required', $cvtx_phone_required);
                
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
        
        // Settings for max image size in applications
        if (isset($_POST['cvtx_max_image_size']) && !empty($_POST['cvtx_max_image_size'])) {
            update_option('cvtx_max_image_size', $_POST['cvtx_max_image_size']);
        } else {
            update_option('cvtx_max_image_size', 400);
        }
    }


    /* get settings */
    
    // cvtx settings
    $antrag_format              = get_option('cvtx_antrag_format');
    if (!$antrag_format)          
               $antrag_format   = __('%agenda_point%', 'cvtx').'-'.__('%resolution%', 'cvtx');
    $aeantrag_format            = get_option('cvtx_aeantrag_format');
    if (!$aeantrag_format)        
               $aeantrag_format = __('%resolution%', 'cvtx').'-'.__('%line%', 'cvtx');
    $aeantrag_pdf               = get_option('cvtx_aeantrag_pdf');
    $anon_user                  = get_option('cvtx_anon_user');
    if (!$anon_user) $anon_user = 1;
    $default_reader_antrag      = get_option('cvtx_default_reader_antrag');
    $default_reader_aeantrag    = get_option('cvtx_default_reader_aeantrag');
    $default_reader_application = get_option('cvtx_default_reader_application');
    $reader                     = cvtx_get_reader();
    $cvtx_privacy_message 		  = get_option('cvtx_privacy_message');
    $cvtx_phone_required        = get_option('cvtx_phone_required');

    // mail settings
    $cvtx_send_html_mail = get_option('cvtx_send_html_mail');
    $send_from_email     = get_option('cvtx_send_from_email');
    if (!$send_from_email) 
        $send_from_email = get_bloginfo('admin_email');
    $send_from_email     = stripslashes(htmlspecialchars($send_from_email));
    $send_rcpt_email     = get_option('cvtx_send_rcpt_email');
    if (!$send_rcpt_email) 
        $send_rcpt_email = get_bloginfo('admin_email');
    $send_rcpt_email     = stripslashes(htmlspecialchars($send_rcpt_email));
    $sendantragowner     = get_option('cvtx_send_create_antrag_owner');
    $sendantragadmin     = get_option('cvtx_send_create_antrag_admin');
    $sendaeantragowner   = get_option('cvtx_send_create_aeantrag_owner');
    $sendaeantragadmin   = get_option('cvtx_send_create_aeantrag_admin');
    // mail design
    $sendantragowner_subject       = get_option('cvtx_send_create_antrag_owner_subject');
    if (!$sendantragowner_subject)   $sendantragowner_subject   = sprintf(__('Resolution submitted “%s”', 'cvtx'),
                                                                          __('%title%', 'cvtx'));
    $sendantragowner_body          = get_option('cvtx_send_create_antrag_owner_body');
    if (!$sendantragowner_body)      $sendantragowner_body      = sprintf(__("Hej,\n\n"
                                                                            .'your resolution “%3$s” to %1$s has been successfully submitted. We have '
                                                                            ."to give it a number and will publish it as soon as possible.\n\n"
                                                                            ."Here is what you submitted:\n\n"
                                                                            .'%1$s'."\n\n"
                                                                            .'%3$s'."\n\n"
                                                                            .'%6$s'."\n\n"
                                                                            ."Explanation:\n"
                                                                            .'%7$s'."\n\n"
                                                                            ."Author(s):\n"
                                                                            .'%4$s'."\n", 'cvtx'),
                                                                          __('%agenda_point%', 'cvtx'),
                                                                          __('%agenda_point_token%', 'cvtx'),
                                                                          __('%title%', 'cvtx'),
                                                                          __('%authors%', 'cvtx'),
                                                                          __('%authors_short%', 'cvtx'),
                                                                          __('%text%', 'cvtx'),
                                                                          __('%explanation%', 'cvtx'));
    $sendantragadmin_subject       = get_option('cvtx_send_create_antrag_admin_subject');
    if (!$sendantragadmin_subject)   $sendantragadmin_subject   = sprintf(__('New resolution has been submitted “%s”', 'cvtx'),
                                                                          __('%title%', 'cvtx'));
    $sendantragadmin_body          = get_option('cvtx_send_create_antrag_admin_body');
    if (!$sendantragadmin_body)      $sendantragadmin_body      = sprintf(__("Hej,\n\n"
                                                                            .'a new resolution to %1$s has been submitted. '
                                                                            ."Please check and publish it!\n\n"
                                                                            .'%8$s'."\n\n"
                                                                            .'%1$s'."\n\n"
                                                                            .'%3$s'."\n\n"
                                                                            .'%6$s'."\n\n"
                                                                            ."Explanation:\n".'%7$s'."\n\n"
                                                                            ."Author(s):\n".'%4$s'."\n", 'cvtx'),
                                                                          __('%agenda_point%', 'cvtx'),
                                                                          __('%agenda_point_token%', 'cvtx'),
                                                                          __('%title%', 'cvtx'),
                                                                          __('%authors%', 'cvtx'),
                                                                          __('%authors_short%', 'cvtx'),
                                                                          __('%text%', 'cvtx'),
                                                                          __('%explanation%', 'cvtx'),
                                                                          home_url('/wp-admin'));
    $sendaeantragowner_subject     = get_option('cvtx_send_create_aeantrag_owner_subject');
    if (!$sendaeantragowner_subject) $sendaeantragowner_subject = sprintf(__('Amendment to %1$s (line %2$s) submitted', 'cvtx'),
                                                                          __('%resolution_token%', 'cvtx'),
                                                                          __('%line%', 'cvtx'));
    $sendaeantragowner_body        = get_option('cvtx_send_create_aeantrag_owner_body');
    if (!$sendaeantragowner_body)    $sendaeantragowner_body    = sprintf(__("Hej,\n\n"
                                                                            .'your amendment to resolution %3$s has been successfully submitted. '
                                                                            ."We will give it a number and will publish it as soon as possible.\n\n"
                                                                            ."Here is what you submitted:\n\n"
                                                                            ."Resolution:\n".'%3$s'."\n\n"
                                                                            ."Line:\n".'%5$s'."\n\n"
                                                                            .'%8$s'."\n\n"
                                                                            ."Explanation:\n".'%9$s'."\n\n"
                                                                            ."Author(s):\n".'%6$s'."\n", 'cvtx'),
                                                                          __('%agenda_point%', 'cvtx'),
                                                                          __('%agenda_point_token%', 'cvtx'),
                                                                          __('%resolution%', 'cvtx'),
                                                                          __('%resolution_token%', 'cvtx'),
                                                                          __('%line%', 'cvtx'),
                                                                          __('%authors%', 'cvtx'),
                                                                          __('%authors_short%', 'cvtx'),
                                                                          __('%text%', 'cvtx'),
                                                                          __('%explanation%', 'cvtx'));
    $sendaeantragadmin_subject     = get_option('cvtx_send_create_aeantrag_admin_subject');
    if (!$sendaeantragadmin_subject) $sendaeantragadmin_subject = sprintf(__('New amendment to %1$s (line %2$s) has been submitted', 'cvtx'),
                                                                          __('%resolution_token%', 'cvtx'),
                                                                          __('%line%', 'cvtx'));
    $sendaeantragadmin_body        = get_option('cvtx_send_create_aeantrag_admin_body');
    if (!$sendaeantragadmin_body)    $sendaeantragadmin_body    = sprintf(__("Hej,\n\n"
                                                                            .'a new amendment to resolution %3$s has been submitted. '
                                                                            ."Please check and publish it!\n\n"
                                                                            .'%10$s'."\n\n"
                                                                            ."Resolution:\n".'%3$s'."\n\n"
                                                                            ."Line:\n".'%5$s'."\n\n"
                                                                            .'%8$s'."\n\n"
                                                                            ."Explanation:\n".'%9$s'."\n\n"
                                                                            ."Author(s):\n".'%6$s'."\n", 'cvtx'),
                                                                          __('%agenda_point%', 'cvtx'),
                                                                          __('%agenda_point_token%', 'cvtx'),
                                                                          __('%resolution%', 'cvtx'),
                                                                          __('%resolution_token%', 'cvtx'),
                                                                          __('%line%', 'cvtx'),
                                                                          __('%authors%', 'cvtx'),
                                                                          __('%authors_short%', 'cvtx'),
                                                                          __('%text%', 'cvtx'),
                                                                          __('%explanation%', 'cvtx'),
                                                                          home_url('/wp-admin'));
        
    // latex settings
    $pdflatex_cmd     = get_option('cvtx_pdflatex_cmd');
    $drop_texfile     = get_option('cvtx_drop_texfile');
    if (!$drop_texfile) $drop_texfile = 2;
    $drop_logfile     = get_option('cvtx_drop_logfile');
    if (!$drop_logfile) $drop_logfile = 2;
    $latex_tpldir     = get_option('cvtx_latex_tpldir');
    if (!$latex_tpldir) $latex_tpldir = 'latex';
    
    // application settings
    $cvtx_max_image_size = get_option('cvtx_max_image_size');
    if (!$cvtx_max_image_size) $cvtx_max_image_size = 400;

    // print config page
    echo('<div class="wrap">');
    echo('<div id="icon-options-general" class="icon32"><br /></div>');
    echo('<h2>cvtx '.__('Settings', 'cvtx').'</h2>');

    echo('<h2 class="nav-tab-wrapper" id="cvtx_navi">');
        echo('<a class="nav-tab cvtx_tool" href="#cvtx_tool">'.__('Agenda Plugin', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_mail" href="#cvtx_mail">'.__('Notifications', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_latex" href="#cvtx_latex">'.__('LaTeX', 'cvtx').'</a>');
        echo('<a class="nav-tab cvtx_application" href="#cvtx_application">'.__('Application Settings', 'cvtx').'</a>');
    echo('</h2>');
    
    echo('<form action="" method="post" id="cvtx-conf">');

    echo('<ul id="cvtx_options">');
    echo('<li id="cvtx_tool" class="active">'); 
        
        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_antrag_format">'.__('Token for resolutions and applications', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_antrag_format" name="cvtx_antrag_format" type="text" value="'.$antrag_format.'" /> ');
                    echo('<span class="description">('.__('%agenda_point%', 'cvtx').', '.__('%resolution%', 'cvtx').')</span>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_aeantrag_format">'.__('Token for amendments', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_aeantrag_format" name="cvtx_aeantrag_format" type="text" value="'.$aeantrag_format.'" /> ');
                    echo('<span class="description">('.__('%resolution%', 'cvtx').', '.__('%line%', 'cvtx').')</span>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_aeantrag_pdf">'.__('Generate PDF', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                echo('<input id="cvtx_aeantrag_pdf" name="cvtx_aeantrag_pdf" type="checkbox" '
                          .($aeantrag_pdf ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_aeantrag_pdf">'.__('Generate PDF files for amendments', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_anon_user">'.__('Anonymous user', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<select name="cvtx_anon_user" id="cvtx_anon_user">');
                    foreach (get_users() as $user) {
                        echo('<option'.($user->ID == $anon_user ? ' selected="selected" ' : '')
                             .' value="'.$user->ID.'">'.$user->user_login.'</option>');
                    }
                    echo('</select>');
                    echo(' <span class="description">'.__('Wordpress user, to whom all anonymously submitted stuff will be assigned.', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
            
        echo('<h4>'.__('Reader assignment', 'cvtx').'</h4>');
        
        echo('<table class="form-table">');    
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_antrag">'.__('Assign submitted resolutions to the following readers', 'cvtx').'</label>');
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
                        echo(__('No reader has been created yet.', 'cvtx'));
                    }
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_aeantrag">'.__('Assign submitted amendments to the following readers', 'cvtx').'</label>');
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
                        echo(__('No reader has been created yet.', 'cvtx'));
                    }
                    echo('</select> ');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_default_reader_application">'.__('Assign submitted applications to the following readers', 'cvtx').'</label>');
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
                        echo(__('No reader has been created yet.', 'cvtx'));
                    }
                    echo('</select> ');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
        echo('<h4>'.__('Miscellaneous').'</h4>');
        
        echo('<table class="form-table">');
            echo('<tr valign="top">');
        		    echo('<th scope="row">');
        			      echo('<label for="cvtx_privacy_message">'.__('Privacy message to be shown below e-mail and phone form fields', 'cvtx').'</label>');
        		    echo('</th>');
        		    echo('<td>');
        			      echo('<textarea id="cvtx_privacy_message" cols="40" rows="5" name="cvtx_privacy_message">'.$cvtx_privacy_message.'</textarea>');
          		  echo('</td>');
        	  echo('</tr>');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_phone_required">'.__('Phone number','cvtx')
                        .'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_phone_required" name="cvtx_phone_required" type="checkbox" '.($cvtx_phone_required ? 'checked ="checked"' :'').'" /> ');
                    echo('<span class="description">'.__('Uncheck, if input field phone should not be mandatory', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
    echo('</li>');
 
     echo('<li id="cvtx_mail">');

        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_html_mail">'.__('HTML mail', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_html_mail"
                          name="cvtx_send_html_mail" type="checkbox" '.($cvtx_send_html_mail ? 'checked ="checked"' :'').'" /> ');
                    echo('<span class="description">'.__('Send e-mails in HTML format', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_from_email">'.__('Sender address', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_from_email" name="cvtx_send_from_email" type="text" value="'.$send_from_email.'" />');
                    echo(' <span class="description">'.__('E-mail address to be used as sender address for notifications', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_rcpt_email">'.__('E-mail address', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_rcpt_email" name="cvtx_send_rcpt_email" type="text" value="'.$send_rcpt_email.'" />');
                    echo(' <span class="description">'.__('E-mail address to which notifications on newly submitted stuff will be sent', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
            
        echo('<h4>'.__('Resolution submitted', 'cvtx').'</h4>');
        echo('<span class="description">'.sprintf(__('Fields: %1$s, %2$s, %3$s, %4$s, %5$s, %6$s, %7$s.', 'cvtx'),
                                                  __('%agenda_point%', 'cvtx'),
                                                  __('%agenda_point_token%', 'cvtx'),
                                                  __('%title%', 'cvtx'),
                                                  __('%authors%', 'cvtx'),
                                                  __('%authors_short%', 'cvtx'),
                                                  __('%text%', 'cvtx'),
                                                  __('%explanation%', 'cvtx')).'</span>');
        
        echo('<table class="form-table">');    
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner">'.__('E-mail confirmation', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_owner"'
                        .' name="cvtx_send_create_antrag_owner" type="checkbox"'
                        .($sendantragowner ? 'checked="checked"' : '').'" /> ');
                    echo('<span class="description">'.__('Send a confirmation e-mail to the author(s)', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner_subject">'.__('Subject', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_owner_subject" size="58"'
                        .' name="cvtx_send_create_antrag_owner_subject" type="text"'
                        .' value="'.$sendantragowner_subject.'" />');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign=top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_owner_body">'.__('Message', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_owner_body"'
                        .' name="cvtx_send_create_antrag_owner_body">'.$sendantragowner_body.'</textarea>');
                 echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin">'.__('Inform the admin', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_admin" name="cvtx_send_create_antrag_admin"'
                        .' type="checkbox" '.($sendantragadmin ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_send_create_antrag_admin">'.__('Send an e-mail to inform the admin', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin_subject">'.__('Subject', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_antrag_admin_subject" size="58"'
                        .' name="cvtx_send_create_antrag_admin_subject" type="text"'
                        .' value="'.$sendantragadmin_subject.'" />');
                 echo('</td>');
             echo('</tr>');
                 
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_antrag_admin_body">'.__('Message', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_antrag_admin_body" name="cvtx_send_create_antrag_admin_body">'
                         .$sendantragadmin_body.'</textarea>');
                 echo('</td>');
             echo('</tr>');
        echo('</table>');
             
        echo('<h4>'.__('Amendment submitted', 'cvtx').'</h4>');
        echo('<span class="description">'.sprintf(__('Fields: %1$s, %2$s, %3$s, %4$s, %5$s, %6$s, %7$s, %8$s, %9$s.', 'cvtx'),
                                                  __('%agenda_point%', 'cvtx'),
                                                  __('%agenda_point_token%', 'cvtx'),
                                                  __('%resolution%', 'cvtx'),
                                                  __('%resolution_token%', 'cvtx'),
                                                  __('%line%', 'cvtx'),
                                                  __('%authors%', 'cvtx'),
                                                  __('%authors_short%', 'cvtx'),
                                                  __('%text%', 'cvtx'),
                                                  __('%explanation%', 'cvtx')).'</span>');

        echo('<table class="form-table">');
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner">'.__('E-mail confirmation', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_owner" name="cvtx_send_create_aeantrag_owner"'
                        .' type="checkbox" '.($sendaeantragowner ? 'checked="checked"' : '').'" /> ');
                    echo('<label for="cvtx_send_create_aeantrag_owner">'.__('Send a confirmation e-mail to the author(s)', 'cvtx').'</label>');
                echo('</td>');
            echo('</tr>');
        
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner_subject">'.__('Subject', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_owner_subject"'
                        .' name="cvtx_send_create_aeantrag_owner_subject" size="58" type="text"'
                        .' value="'.$sendaeantragowner_subject.'" />');
                echo('</td>');
            echo('</tr>');
             
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_owner_body">'.__('Message', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_owner_body"'
                    .' name="cvtx_send_create_aeantrag_owner_body">'.$sendaeantragowner_body.'</textarea>');
                echo('</td>');
            echo('</tr>');

            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_admin">'.__('Inform the admin', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_admin" name="cvtx_send_create_aeantrag_admin"'
                        .' type="checkbox" '.($sendaeantragadmin ? 'checked="checked"' : '').'" /> ');
                       echo('<label for="cvtx_send_create_aeantrag_admin">'.__('Send an e-mail to inform the admin', 'cvtx').'</label>');
                   echo('</td>');
               echo('</tr>');
               
               echo('<tr valign="top">');
                   echo('<th scope="row">');
                       echo('<label for="cvtx_send_create_aeantrag_admin_subject">'.__('Subject', 'cvtx').'</label>');
                   echo('</th>');
                   echo('<td>');
                    echo('<input id="cvtx_send_create_aeantrag_admin_subject"'
                        .' name="cvtx_send_create_aeantrag_admin_subject" size="58" type="text"'
                        .' value="'.$sendaeantragadmin_subject.'" />');
                echo('</td>');
            echo('</tr>');
            
            echo('<tr valign="top">');
                echo('<th scope="row">');
                    echo('<label for="cvtx_send_create_aeantrag_admin_body">'.__('Message', 'cvtx').'</label>');
                echo('</th>');
                echo('<td>');
                    echo('<textarea cols="60" rows="10" id="cvtx_send_create_aeantrag_admin_body"'
                        .' name="cvtx_send_create_aeantrag_admin_body">'.$sendaeantragadmin_body.'</textarea>');
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
                    echo('<span class="description">'.__('Path to pdflatex', 'cvtx').'</span>');
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
                    echo('<span class="description">'.__('Subdirectory of the used theme that provides LaTeX templates', 'cvtx').'</span>');
                echo('</td>');
            echo('</tr>');
        echo('</table>');
        
      echo('</li>');
      
      echo('<li id="cvtx_application">');
          echo('<table class="form-table">');
              echo('<tr valign="top">');
                  echo('<th scope="row">');
                      echo('<label for="cvtx_max_image_size">'.__('Max. size for application images', 'cvtx').'</label>');
                  echo('</th>');
                  echo('<td>');
                      echo('<input id="cvtx_max_image_size" name="cvtx_max_image_size" type="text" value="'.$cvtx_max_image_size.'" /> ');
                      echo('<span class="description">(KB)</span>');
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
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag' || $_REQUEST['post_type'] == 'cvtx_application'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag' || $post->post_type == 'cvtx_application'))) {
        remove_all_actions('media_buttons');
    }
}


if (is_admin()) add_filter('add_menu_classes', 'cvtx_show_pending_number');
/**
 * Add a count of pending antrage/aeatraege in the admin-sidebar
 */
function cvtx_show_pending_number($menu) {
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
function cvtx_admin_bar_render() {
    global $wp_admin_bar;
    // Parent, directs to the cvtx-config-page
    $wp_admin_bar->add_menu(array(
        'id'    => 'cvtx',
        'title' => __('cvtx Agenda Plugin', 'cvtx'),
        'href'  => home_url('/wp-admin/options-general.php?page=cvtx-config')
    ));
    // link to cvtx_antrag
    $count = cvtx_get_pending('cvtx_antrag');
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_antrag',
        'title'  => __('Resolutions', 'cvtx').' <span class="pending-count count-'.$count.'">'.$count.'</span</span>',
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_antrag'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_aeantrag
    $count = cvtx_get_pending('cvtx_aeantrag');
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_aeantrag',
        'title'  => __('Amendments', 'cvtx').' <span class="pending-count count-'.$count.'">'.$count.'</span</span>',
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
        'title'  => __('Agenda points', 'cvtx'),
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_top'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx_reader
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_reader',
        'title'  => __('Readers', 'cvtx'),
        'href'   => home_url('/wp-admin/edit.php?post_type=cvtx_reader'),
        'meta'   => array('class' => 'cvtx')
    ));
    // link to cvtx-config-page
    $wp_admin_bar->add_menu(array(
        'parent' => 'cvtx',
        'id'     => 'cvtx_config',
        'title'  => __('Settings', 'cvtx'),
        'href'   => home_url('/wp-admin/options-general.php?page=cvtx-config'),
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
