<?php

/**
 * Returns latex formatted output
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
    $out = str_replace(array('$', '%', '_', '{', '}', '&', '#', '–'),
                       array('\\$', '\\%', '\\_', '\\{', '\\}', '\\&', '\\#', '--'), $out);
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
        $title = (empty($post->post_title) ? __('(no title)') : $post->post_title);
        echo(cvtx_get_latex($title));
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
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex(get_the_title($post->ID)));
    } else if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true))));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo(cvtx_get_latex(get_the_title($top_id)));
    }
}

function cvtx_top_titel($post) {
    if ($post->post_type == 'cvtx_top') {
        echo(cvtx_get_latex($post->post_title));
    } else if ($post->post_type == 'cvtx_antrag') {
        $top   = get_post(get_post_meta($post->ID, 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)') : $top->post_title);
        echo(cvtx_get_latex($title));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top   = get_post(get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true));
        $title = (empty($top->post_title) ? __('(no title)') : $top->post_title);
        echo(cvtx_get_latex($title));
    }
}

function cvtx_top_kuerzel($post) {
    if ($post->post_type == 'cvtx_top') {
        echo('TOP '.cvtx_get_latex(get_post_meta($post->ID, 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_antrag') {
        echo('TOP '.cvtx_get_latex(get_post_meta(get_post_meta($post->ID, 'cvtx_antrag_top', true), 'cvtx_top_ord', true)));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        $top_id = get_post_meta(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true), 'cvtx_antrag_top', true);
        echo('TOP '.cvtx_get_latex(get_post_meta($top_id, 'cvtx_top_ord', true)));
    }
}

function cvtx_antrag($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $title = get_the_title(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        if (empty($title)) $title = __('(no title)');
        echo(cvtx_get_latex($title));
    }
}

function cvtx_antrag_titel($post) {
    if ($post->post_type == 'cvtx_aeantrag') {
        $antrag = get_post(get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true));
        $title  = (empty($antrag->post_title) ? __('(no title)') : $antrag->post_title);
        echo(cvtx_get_latex($title));
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
