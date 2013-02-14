\documentclass[paper=a4, 10pt, pagesize, parskip=half]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8]{inputenc}
<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \usepackage[ngerman]{babel}
<?php } else { ?>
    \usepackage[english]{babel}
<?php } ?>
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{ltablex}
\usepackage{tabularx}
\usepackage{multirow}
\usepackage{scrpage2}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}
\usepackage{hyperref}
\usepackage[left=1.5cm,right=1.5cm,top=1.5cm,bottom=1.5cm,includehead,includefoot]{geometry}

\sloppy
\renewcommand\arraystretch{1.5}

\pagestyle{scrheadings}
\setheadsepline{0.4pt}

\subject{<?php cvtx_name(); ?>\\ <?php cvtx_beschreibung(); ?>}
\title{<?php cvtx_titel($post); ?>}
\date{<?php _e('This version', 'cvtx'); ?>: \today}
\author{}

\begin{document}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

\maketitle

\tableofcontents

\newpage

<?php
$reader = intval($post->ID);
$resos  = array();
// fetch all resolutions that should be included in this reader
$query  = new WP_Query(array('taxonomy'    => 'cvtx_tax_reader',
                             'term'        => 'cvtx_reader_'.$reader,
                             'post_type'   => 'cvtx_antrag',
                             'orderby'     => 'meta_value',
                             'meta_key'    => 'cvtx_sort',
                             'order'       => 'ASC',
                             'nopaging'    => true,
                             'post_status' => 'publish'));
// add them to the list
while ($query->have_posts()) {
    $query->the_post();
    $resos[] = get_the_ID();
}

$agenda_item = 0;
// loop through the resolutions
foreach ($resos as $resolution) {
    $reso = get_post($resolution);
    $loop = new WP_Query(array('post_type'   => 'cvtx_aeantrag',
                               'taxonomy'    => 'cvtx_tax_reader',
                               'term'        => 'cvtx_reader_'.$reader,
                               'orderby'     => 'meta_value',
                               'meta_key'    => 'cvtx_sort',
                               'order'       => 'ASC',
                               'nopaging'    => true,
                               'post_status' => 'publish',
                               'meta_query'  => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                            'value'   => $resolution,
                                                            'compare' => '='))));
    if ($loop->have_posts()) {
        /* update top if changed */
        $this_item = get_post_meta($resolution, 'cvtx_antrag_top', true);
        if ($agenda_item != $this_item) {
            $agenda_item = $this_item;
?>
\addcontentsline{toc}{section}{<?php cvtx_top($reso); ?>}
<?php
        }
?>
\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($reso); ?> <?php cvtx_titel($reso); ?>}
\addcontentsline{toc}{subsection}{<?php cvtx_kuerzel($reso); ?> <?php cvtx_titel($reso); ?>}

\begin{tabularx}{\textwidth}{p{1.5cm}p{3cm}>{\hsize=1.2\hsize}X>{\hsize=.8\hsize}X}
    \hline
    \multicolumn{4}{|l|}{}   \\
    \multicolumn{4}{|l|}{\textbf{\LARGE <?php cvtx_kuerzel($reso); ?> \large <?php cvtx_titel($reso); ?>}}   \\
    \multicolumn{4}{|l|}{<?php _e('Author(s)', 'cvtx'); echo(': '); cvtx_antragsteller_kurz($reso); ?>}   \\
    \multicolumn{4}{|l|}{}   \\
    \multicolumn{1}{|c}{\textbf{<?php _e('Line', 'cvtx'); ?>}}        &
    \multicolumn{1}{c} {\textbf{<?php _e('Author(s)', 'cvtx'); ?>}}   &
    \multicolumn{1}{c} {\textbf{<?php _e('Amendment', 'cvtx'); ?>}}   &
    \multicolumn{1}{c|}{\textbf{<?php _e('Procedure', 'cvtx'); ?>}}   \\
    \hline
    \hline
<?php
        while ($loop->have_posts()) {
            $loop->the_post();
            $amendment = get_post(get_the_ID());
?>
    <?php cvtx_kuerzel($amendment); ?>                       &
    <?php cvtx_antragsteller_kurz($amendment); ?>            &
    <?php cvtx_antragstext($amendment); ?>                   &
    <?php
    cvtx_amendment_procedure($amendment);
    if (cvtx_amendment_procedure_has_details($amendment)) {
        echo("\\vspace{2pt}\n");
        cvtx_amendment_procedure_details($amendment);
    }
    ?>   \\
    \hline
<?php   } ?>
\end{tabularx}

<?php
    }
}
?>

\end{document}
