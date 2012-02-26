\documentclass[a4paper, 12pt, pagesize, halfparskip]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{tabularx}
\usepackage{scrpage2}

\sloppy

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($post); ?> <?php cvtx_titel($post); ?>}
\setheadsepline{0.4pt}

\begin{document}

\shorthandoff{"}

\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name($post); ?>}\\
 <?php cvtx_beschreibung($post); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                    &                                                                           \\
    \textbf{\LARGE <?php cvtx_kuerzel($post); ?>}   &   \textbf{\large <?php cvtx_top_titel($post); ?>}                         \\
                                                    &                                                                           \\
    <?php _e('AntragstellerInnen', 'cvtx'); ?>:     &   <?php cvtx_antragsteller_kurz($post); ?>                                \\
                                                    &                                                                           \\
    <?php _e('Gegenstand', 'cvtx'); ?>:             &   <?php cvtx_top_titel($post); ?> (<?php cvtx_top_kuerzel($post); ?>)     \\
                                                    &                                                                           \\
<?php if (cvtx_has_info($post)) { ?>
    <?php _e('Anmerkungen', 'cvtx'); ?>:            &   <?php cvtx_info($post); ?>                                              \\
                                                    &                                                                           \\
<?php } ?>
    \hline
\end{tabularx}

\section*{<?php cvtx_titel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($post)) { ?>
    \subsection*{<?php _e('Begründung', 'cvtx'); ?>}
    <?php cvtx_begruendung($post); ?>
<?php } ?>

\subsection*{<?php _e('AntragstellerInnen', 'cvtx'); ?>}
<?php cvtx_antragsteller($post); ?>

\end{document}