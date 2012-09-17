<?php

/**
 * Returns latex formatted output
 *
 * @param $out input
 * @return formatted output
 */
function cvtx_get_latex($out) {
    // purify code using HTMLPurifier-plugin
    if (class_exists('HTMLPurifier') && class_exists('HTMLPurifier_Config')) {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'strong,b,em,i,h3,h4,ul,ol,li,br,p,del,ins,span[style],code');
        $config->set('HTML.Doctype', 'XHTML 1.1');
        $purifier = new HTMLPurifier($config);
        $out = $purifier->purify($out);
    }
    
    // strip html entities
    $out = html_entity_decode($out);
//    if (strpos($out, 'Benannte Zeichen für diverse Symbole')) die($out);
/*    $out = str_replace(array('&nbsp;', '&amp;', '&#8211;', '&ndash;', '&mdash;', '&#8212;'),
                       array(' ', '&', '–', '–', '—', '—'), $out);*/
    
    // recode special chars
    $tmp = time().'\\textbackslash'.rand();
    $out = str_replace('\\', $tmp, $out);
    $out = str_replace(array('$', '%', '_', '{', '}', '&', '#', '–', '€'),
                       array('\\$', '\\%', '\\_', '\\{', '\\}', '\\&', '\\#', '--', '{\euro}'), $out);
    $out = str_replace($tmp, '{\\textbackslash}', $out);
    
    // recode formatting rules
    $rules = array(array('search'  => array('<strong>', '</strong>'),
                         'replace' => array('\textbf{', '}')),
                   array('search'  => array('<b>', '</b>'),
                         'replace' => array('\textbf{', '}')),
                   array('search'  => array('<del>', '</del>'),
                         'replace' => array('\sout{', '}')),
                   array('search'  => array('<span style="text-decoration: line-through;">', '</span>'),
                         'replace' => array('\sout{', '}')),
                   array('search'  => array('<ins>', '</ins>'),
                         'replace' => array('\uline{', '}')),
                   array('search'  => array('<span style="text-decoration: underline;">', '</span>'),
                         'replace' => array('\uline{', '}')),
                   array('search'  => array('<em>', '</em>'),
                         'replace' => array('\textit{', '}')),
                   array('search'  => array('<i>', '</i>'),
                         'replace' => array('\textit{', '}')),
                   array('search'  => array('<code>', '</code>'),
                         'replace' => array('\begin{verbatim}', '\end{verbatim}')),
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
                   array('search'  => '</p>',
                         'replace' => "\n"),
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
    $out = preg_replace("/[\r\n]+/", "\n\n", $out);
#    $out = str_replace("\r\n", "\n", $out);
#    $out = str_replace("\n", "\\par\n", $out);
    
    return $out;
}


/************************************************************************************
 * LaTeX Template Functions
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
        $title = (empty($post->post_title) ? __('(no title)', 'cvtx') : $post->post_title);
        echo(cvtx_get_latex($title));
    }
}

function cvtx_antragstext($post) {
    global $cvtx_types;
    if ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex($post->post_content));
    }
}

function cvtx_has_begruendung($post) {
    $begruendung = '';
    if ($post->post_type == 'cvtx_antrag') {
        $begruendung = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_grund', true));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $begruendung = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true));
    }
    return !empty($begruendung);
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
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex(get_the_title($post->ID)));
    } else if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true))));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo(cvtx_get_latex(get_the_title($top_id)));
    } else if ($post->post_type == 'cvtx_application') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_application_top', true))));
    }
}

function cvtx_top_titel($post) {
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex($post->post_title));
    } else if ($post->post_type == 'cvtx_antrag') {
        $top   = get_post(get_post_meta($post->ID, 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top   = get_post(get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title));
    } else if ($post->post_type == 'cvtx_application') {
        $top   = get_post(get_post_meta($post->ID, 'cvtx_application_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title));
    }
}

function cvtx_top_kuerzel($post) {
    if ($post->post_type == 'cvtx_top') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta($post->ID, 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_antrag') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta($top_id, 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_application') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_application_top', true), 'cvtx_top_ord', true)));
    }
}

function cvtx_antrag($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $title = get_the_title(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        if (empty($title)) $title = __('(no title)', 'cvtx');
        echo(cvtx_get_latex($title));
    }
}

function cvtx_antrag_titel($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        $title  = (empty($antrag->post_title) ? __('(no title)', 'cvtx') : $antrag->post_title);
        echo(cvtx_get_latex($title));
    }
}

function cvtx_antrag_kuerzel($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        echo(cvtx_get_latex(cvtx_get_short($antrag)));
    }
}

function cvtx_amendment_procedure($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true)));
    }
}

function cvtx_amendment_procedure_has_details($post) {
    $details = '';
    if ($post->post_type == 'cvtx_aeantrag') {
        $details = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true));
    }
    return !empty($details);
}

function cvtx_amendment_procedure_details($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true)));
    }
}

function cvtx_has_info($post) {
    $info = '';
    if ($post->post_type == 'cvtx_antrag') {
        $info = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_info', true));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $info = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_info', true));
    }
    return !empty($info);
}

function cvtx_info($post) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_info', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_info', true)));
    }
}

function cvtx_application_file($post) {
    if ($post->post_type == 'cvtx_application') {
        $appl = get_post(get_post_meta($post->ID, 'cvtx_antrag_info', true));
        echo(cvtx_get_file($appl, 'pdf', 'dir'));
    }
}

?>
