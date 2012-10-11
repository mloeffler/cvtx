<?php
/**
 * Returns latex formatted output
 *
 * @param $out input
 * @param $strip_nl convert newlines to spaces
 * @return formatted output
 */
function cvtx_get_latex($out, $strip_nl = false) {
    // Sanitize content using HTMLPurifier-plugin
    if (is_plugin_active('html-purified/html-purified.php')) {
        global $cvtx_purifier, $cvtx_purifier_config;
        // Purify resolution text and meta fields
        $out = $cvtx_purifier->purify($out,  $cvtx_purifier_config);
    }
    
    // strip html entities
    $out = html_entity_decode($out);
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
                   array('search'  => array('<ins>', '</ins>'),
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
                   array('search'  => array('</p>', '</span>'),
                         'replace' => array("\n", '}')),
                   array('search'  => array('/<span style="text\-decoration:[ ]*line\-through[;]?">/',
                                            '/<span style="text\-decoration:[ ]*underline[;]?">/',
                                            '/<br[ ]*[\/]?>/'),
                         'replace' => array('\sout{', '\uline{', "\n"),
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
    
    // Adjust line-through and underline-tags in a way that line breaks work.
    if ($res = preg_match_all('/(\\\sout\{|\\\uline\{|\\\subsection\*\{|\\\subsubsection\*\{)([0-9.\.,\*\?\s\r\w\pL:\n^\}]*)([\}]{1,1})/Uui', $out, $match)) {
        for ($i = 0; $i < $res; $i++) {
            $out = str_replace($match[0][$i], $match[1][$i].preg_replace("/[\r\n]+/", ($strip_nl || in_array($match[1][$i], array('\subsection*{', '\subsubsection*{')) ? ' ' : "}\n\n".$match[1][$i]), $match[2][$i])."}", $out);
        }
    }
    
    // add new lines
    $out = preg_replace("/[\r\n]+/", ($strip_nl ? ' ' : "\n\n"), $out);
/*    $out = str_replace("\r\n", "\n", $out);
    $out = str_replace("\n", "\\par\n", $out);*/
    
    return $out;
}


/************************************************************************************
 * LaTeX Template Functions
 ************************************************************************************/

function cvtx_name($strip_nl = false) {
    echo(cvtx_get_latex(get_bloginfo('name'), $strip_nl));
}

function cvtx_beschreibung($strip_nl = false) {
    echo(cvtx_get_latex(get_bloginfo('description'), $strip_nl));
}

function cvtx_kuerzel($post, $strip_nl = true) {
    global $cvtx_types;
    if (in_array($post->post_type, array_keys($cvtx_types))) {
        echo(cvtx_get_latex(cvtx_get_short($post), $strip_nl));
    }
}

function cvtx_titel($post, $strip_nl = true) {
    global $cvtx_types;
    if (in_array($post->post_type, array_keys($cvtx_types))) {
        $title = (empty($post->post_title) ? __('(no title)', 'cvtx') : $post->post_title);
        echo(cvtx_get_latex($title, $strip_nl));
    }
}

function cvtx_antragstext($post, $strip_nl = false) {
    global $cvtx_types;
    if ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex($post->post_content, $strip_nl));
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

function cvtx_begruendung($post, $strip_nl = false) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_grund', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true)));
    }
}

function cvtx_antragsteller($post, $strip_nl = false) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_steller', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_steller', true), $strip_nl));
    }
}

function cvtx_antragsteller_kurz($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_steller_short', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true), $strip_nl));
    }
}

function cvtx_top($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex(get_the_title($post->ID), $strip_nl));
    } else if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true)), $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo(cvtx_get_latex(get_the_title($top_id), $strip_nl));
    } else if ($post->post_type == 'cvtx_application') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_application_top', true)), $strip_nl));
    }
}

function cvtx_top_titel($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex($post->post_title, $strip_nl));
    } else if ($post->post_type == 'cvtx_antrag') {
        $top   = get_post(get_post_meta($post->ID, 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title, $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top   = get_post(get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title, $strip_nl));
    } else if ($post->post_type == 'cvtx_application') {
        $top   = get_post(get_post_meta($post->ID, 'cvtx_application_top', true));
        $title = (empty($top->post_title) ? __('(no title)', 'cvtx') : $top->post_title);
        echo(cvtx_get_latex($title, $strip_nl));
    }
}

function cvtx_top_kuerzel($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_top') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta($post->ID, 'cvtx_top_ord', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_antrag') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta($top_id, 'cvtx_top_ord', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_application') {
        printf(__('agenda_point_format', 'cvtx'), cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_application_top', true), 'cvtx_top_ord', true), $strip_nl));
    }
}

function cvtx_antrag($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $title = get_the_title(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        if (empty($title)) $title = __('(no title)', 'cvtx');
        echo(cvtx_get_latex($title, $strip_nl));
    }
}

function cvtx_antrag_titel($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        $title  = (empty($antrag->post_title) ? __('(no title)', 'cvtx') : $antrag->post_title);
        echo(cvtx_get_latex($title, $strip_nl));
    }
}

function cvtx_antrag_kuerzel($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        echo(cvtx_get_latex(cvtx_get_short($antrag), $strip_nl));
    }
}

function cvtx_amendment_procedure($post, $strip_nl = false) {
    if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true), $strip_nl));
    }
}

function cvtx_amendment_procedure_has_details($post) {
    $details = '';
    if ($post->post_type == 'cvtx_aeantrag') {
        $details = cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true));
    }
    return !empty($details);
}

function cvtx_amendment_procedure_details($post, $strip_nl = true) {
    if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_detail', true), $strip_nl));
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

function cvtx_info($post, $strip_nl = false) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_info', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_info', true), $strip_nl));
    }
}

function cvtx_application_file($post) {
    if ($post->post_type == 'cvtx_application') {
        $appl = get_post(get_post_meta($post->ID, 'cvtx_antrag_info', true));
        echo(cvtx_get_file($appl, 'pdf', 'dir'));
    }
}

?>
