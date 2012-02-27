\documentclass[a4paper, 12pt, pagesize, halfparskip, DIV=calc]{scrbook}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \usepackage[ngerman]{babel}
<?php } else { ?>
    \usepackage[english]{babel}
<?php } ?>
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{tabularx}
\usepackage{scrpage2}
\usepackage{calc}
\usepackage{pdfpages}
\usepackage{hyperref}

\sloppy

\pagestyle{scrheadings}
\setheadsepline{0.4pt}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}

\subject{<?php cvtx_name(); ?>\\ <?php cvtx_beschreibung(); ?>}
\title{<?php cvtx_titel($post); ?>}
\date{<?php _e('Stand', 'cvtx'); ?>: \today}
\author{}

\begin{document}

\shorthandoff{"}

\maketitle

\tableofcontents


<?php
$top    = 0;
$antrag = 0;
$query  = new WP_Query(array('taxonomy'    => 'cvtx_tax_reader',
                             'term'        => 'cvtx_reader_'.intval($post->ID),
                             'orderby'     => 'meta_value',
                             'meta_key'    => 'cvtx_sort',
                             'order'       => 'ASC',
                             'nopaging'    => true,
                             'post_status' =>'publish'));
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
    
    /* show antrag */
    if ($item->post_type == 'cvtx_antrag') {
        $antrag   = $item->ID;
?>
\newpage
<?php
        /* update top if changed */
        $this_top = get_post_meta($item->ID, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top = $this_top;
?>
\addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php
        }
?>

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}
\thispagestyle{empty}
\addcontentsline{toc}{section}{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                    &                                                                           \\
    \textbf{\LARGE <?php cvtx_kuerzel($item); ?>}   &   \textbf{\large <?php cvtx_top_titel($item); ?>}                         \\
                                                    &                                                                           \\
    <?php _e('AntragstellerInnen', 'cvtx'); ?>:     &   <?php cvtx_antragsteller_kurz($item); ?>                                \\
                                                    &                                                                           \\
    <?php _e('Gegenstand', 'cvtx'); ?>:             &   <?php cvtx_top_titel($item); ?> (<?php cvtx_top_kuerzel($item); ?>)     \\
                                                    &                                                                           \\
<?php if (cvtx_has_info($item)) { ?>
    <?php _e('Anmerkungen', 'cvtx'); ?>:            &   <?php cvtx_info($item); ?>                                              \\
                                                    &                                                                           \\
<?php } ?>
    \hline
\end{tabularx}

\section*{<?php cvtx_titel($item); ?>}

\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($item)) { ?>
   \subsection*{<?php _e('Begründung', 'cvtx'); ?>}
   <?php cvtx_begruendung($item); ?>
<?php } ?>

\subsection*{<?php _e('AntragstellerInnen', 'cvtx'); ?>}
<?php cvtx_antragsteller($item); ?>


<?php
    }
    /* show application */
    else if ($item->post_type == 'cvtx_application') {
?>
\newpage
<?php
        /* update top if changed */
        $this_top = get_post_meta($item->ID, 'cvtx_application_top', true);
        if ($top != $this_top) {
            $top = $this_top;
?>
\addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php
        }
?>

\pagestyle{scrheadings}
\ohead{<?php _e('Application', 'cvtx'); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}
\addcontentsline{toc}{section}{<?php _e('Application ', 'cvtx'); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\includepdf[pages=-, pagecommand={\thispagestyle{scrheadings}}, offset=-1.5em 2em, width=1.15\textwidth]{<?php cvtx_application_file($item); ?>}

<?php
    }
    /* show aeantrag */
    else if ($item->post_type == 'cvtx_aeantrag') {
?>
\newpage
<?php
        /* update top if changed */
        $this_antrag = get_post_meta($item->ID, 'cvtx_aeantrag_antrag', true);
        $this_top    = get_post_meta($this_antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top = $this_top;
?>
\addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php
        }
        /* update antrag if changed */
        if ($antrag != $this_antrag) {
            $antrag = $this_antrag;
?>
\addcontentsline{toc}{section}{<?php cvtx_antrag($item); ?>}
<?php
        }
?>

\pagestyle{scrheadings}
\ohead{<?php _e('Amendment', 'cvtx'); ?> <?php cvtx_kuerzel($item); ?>}
\thispagestyle{empty}
\addcontentsline{toc}{subsection}{<?php _e('Amendment', 'cvtx'); ?> <?php cvtx_kuerzel($item); ?>}

\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                &                                                               \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($item); ?>}}                               \\
                                                &                                                               \\
    <?php _e('AntragstellerInnen', 'cvtx'); ?>: &   <?php cvtx_antragsteller_kurz($item); ?>                    \\
                                                &                                                               \\
    <?php _e('Gegenstand', 'cvtx'); ?>:         &   <?php cvtx_antrag($item); ?> (<?php cvtx_top($item); ?>)    \\
                                                &                                                               \\
<?php if (cvtx_has_info($item)) { ?>
    <?php _e('Anmerkungen', 'cvtx'); ?>:        &   <?php cvtx_info($item); ?>                                  \\
                                                &                                                               \\
<?php } ?>
    \hline
\end{tabularx}

\section*{<?php _e('Amendment', 'cvtx'); ?> <?php cvtx_kuerzel($item); ?>}

\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($item)) { ?>
    \subsection*{<?php _e('Begründung', 'cvtx'); ?>}
    <?php cvtx_begruendung($item); ?>
<?php } ?>

\subsection*{<?php _e('AntragstellerInnen', 'cvtx'); ?>}
<?php cvtx_antragsteller($item); ?>
<?php
    }
}
?>


\end{document}