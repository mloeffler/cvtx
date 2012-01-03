\documentclass[a4paper, 12pt, pagesize, halfparskip]{scrbook}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{tabularx}
\usepackage{scrpage2}
\usepackage{calc}

\sloppy

\pagestyle{scrheadings}
\ohead{<?php cvtx_titel($post); ?>}
\setheadsepline{0.4pt}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}

\subject{<?php cvtx_name(); ?>\\ <?php cvtx_beschreibung(); ?>}
\title{<?php cvtx_titel($post); ?>}
\date{Stand: \today}
\author{}

\begin{document}

\shorthandoff{"}

\maketitle

\tableofcontents


<?php
$top    = 0;
$antrag = 0;
$query  = new WP_Query(array('taxonomy' => 'cvtx_tax_reader',
                             'term'     => 'cvtx_reader_'.intval($post->ID),
                             'orderby'  => 'meta_value',
                             'meta_key' => 'cvtx_sort',
                             'order'    => 'ASC',
                             'nopaging' => true));
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

\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                    &                                                                           \\
    \textbf{\LARGE <?php cvtx_kuerzel($item); ?>}   &   \textbf{\large <?php cvtx_top_titel($item); ?>}                         \\
                                                    &                                                                           \\
    AntragstellerInnen:                             &   <?php cvtx_antragsteller_kurz($item); ?>                                \\
                                                    &                                                                           \\
    Gegenstand:                                     &   <?php cvtx_top_titel($item); ?> (<?php cvtx_top_kuerzel($item); ?>)     \\
                                                    &                                                                           \\
    Anmerkungen:                                    &   <?php cvtx_info($item); ?>                                              \\
                                                    &                                                                           \\
    \hline
\end{tabularx}

\section*{<?php cvtx_titel($item); ?>}
\addcontentsline{toc}{section}{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

\subsection*{Begründung}
<?php cvtx_begruendung($item); ?>

\subsection*{AntragstellerInnen}
<?php cvtx_antragsteller($item); ?>


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
\ohead{Änderungsantrag <?php cvtx_kuerzel($item); ?>}
\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                &                                                               \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($item); ?>}}                               \\
                                                &                                                               \\
    AntragstellerInnen:                         &   <?php cvtx_antragsteller_kurz($item); ?>                    \\
                                                &                                                               \\
    Gegenstand:                                 &   <?php cvtx_antrag($item); ?> (<?php cvtx_top($item); ?>)    \\
                                                &                                                               \\
    Anmerkungen:                                &   <?php cvtx_info($item); ?>                                  \\
                                                &                                                               \\
    \hline
\end{tabularx}

\section*{Änderungsantrag <?php cvtx_kuerzel($item); ?>}
\addcontentsline{toc}{subsection}{<?php cvtx_kuerzel($item); ?>}

\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

\subsection*{Begründung}
<?php cvtx_begruendung($item); ?>

\subsection*{AntragstellerInnen}
<?php cvtx_antragsteller($item); ?>
<?php
    }
}
?>


\end{document}