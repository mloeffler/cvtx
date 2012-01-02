<?php

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

    echo('<p id="admin_message" class="error">');
    echo(' <span id="unique_error_cvtx_top_ord" class="cvtx_unique_error">Diese Nummer ist bereits vergeben.</span> ');
    echo(' <span id="unique_error_cvtx_top_short" class="cvtx_unique_error">Dieses Kürzel ist bereits vergeben.</span> ');
    echo(' <span id="empty_error_cvtx_top_ord" class="cvtx_empty_error">Bitte TOP-Nummer vergeben.</span> ');
    echo(' <span id="empty_error_cvtx_top_short" class="cvtx_empty_error">Bitte Kürzel für den TOP vergeben.</span> ');
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
    echo('<p id="admin_message" class="error">');
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
    echo('<p id="admin_message" class="error">');
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
        case 'cvtx_antrag_sort':
            echo(get_post_meta($post->ID, 'cvtx_antrag_sort', true));
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
        case 'cvtx_aeantrag_sort':
            echo(get_post_meta($post->ID, 'cvtx_aeantrag_sort', true));
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
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_sort', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_antrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_antrag_steller_short', 'orderby' => 'meta_value'));
        }
        // Änderungsanträge
        else if ($vars['orderby'] == 'cvtx_aeantrag_ord' || ($post_type == 'cvtx_aeantrag' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_sort', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_steller') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_steller_short', 'orderby' => 'meta_value'));
        } else if ($vars['orderby'] == 'cvtx_aeantrag_verfahren') {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_aeantrag_verfahren', 'orderby' => 'meta_value'));
        }
        // TOPs
        else if ($vars['orderby'] == 'cvtx_top_ord' ||  ($post_type == 'cvtx_top' && $vars['orderby'] == 'title')) {
            $vars = array_merge($vars, array('meta_key' => 'cvtx_top_sort', 'orderby' => 'meta_value'));
        }
    }

    return $vars;
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
    	echo('<a class="nav-tab cvtx_mail" href="#cvtx_mail">Benachrichtigungen</a>');
    	echo('<a class="nav-tab cvtx_recaptcha" href="#cvtx_recaptcha">Spam-Schutz</a>');
    	echo('<a class="nav-tab cvtx_latex" href="#cvtx_latex">LaTeX</a>');
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
    				echo('<label for="cvtx_aeantrag_pdf">PDF-Erstellung</label>');
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
	
	echo('<li id="cvtx_recaptcha">');
    	
    	echo('<table class="form-table">');
    		echo('<tr valign="top">');
    			echo('<th scope="row">');
    				echo('<label for="cvtx_use_recaptcha">Spam-Schutz aktivieren</label>');
    			echo('</th>');
   				echo('<td>');
    				echo('<input id="cvtx_use_recaptcha" name="cvtx_use_recaptcha" type="checkbox" '.($use_recpatcha ? 'checked="checked"' : ''). '" /> ');
			    	echo('<span class="description">Um die Eingabe von Anträgen und Änderungsanträgen Spam-sicher zu machen, wird der Einsatz von reCaptcha empfohlen.</span>');
    			echo('</td>');
    		echo('</tr>');
    		
   			echo('<tr valign="top">');
   				echo('<th scope="row">');
   					echo('<label for="cvtx_recaptcha_publickey">Öffentlicher reCaptcha-Schlüssel</label>');
   				echo('</th>');
   				echo('<td>');
   					echo('<input id="cvtx_recaptcha_publickey" name="cvtx_recaptcha_publickey" type="text" value="'.$recaptcha_publickey.'" /> ');
   					echo('<span class="description">Schlüsselpaare können <a href="http://www.google.com/recaptcha/whyrecaptcha">hier</a> erzeugt werden.</span>');
   				echo('</td>');
   			echo('</tr>');

   			echo('<tr valign="top">');
   				echo('<th scope="row">');
   					echo('<label for="cvtx_recaptcha_privatekey">Privater reCaptcha-Schlüssel</label>');
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
        $settings['theme_advanced_blockformats'] = 'Zwischenüberschrift=h3; Unterüberschrift=h4';
    }
    return $settings;
}

?>
