\documentclass[paper=a4, 12pt, pagesize, parskip=half, DIV=calc]{scrartcl}
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
\usepackage{tabularx}
\usepackage{scrpage2}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}

\sloppy

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($post); ?> <?php cvtx_titel($post); ?>}
\setheadsepline{0.4pt}

\begin{document}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name($post); ?>}\\
 <?php cvtx_beschreibung($post); ?>
\end{flushright}

\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                     &                                              \\
    \textbf{\LARGE <?php cvtx_kuerzel($post); ?>}    &                                              \\
                                                     &                                              \\
    <?php _e('Author(s)', 'cvtx'); ?>:               &   <?php cvtx_antragsteller_kurz($post); ?>   \\
                                                     &                                              \\
    <?php _e('Concerning', 'cvtx'); ?>:              &   <?php cvtx_top($post); ?>                  \\
                                                     &                                              \\
<?php if (cvtx_has_info($post)) { ?>
    <?php _e('Remarks', 'cvtx'); ?>:                 &   <?php cvtx_info($post); ?>                 \\
                                                     &                                              \\
<?php } ?>
    \hline
\end{tabularx}

\section*{<?php cvtx_titel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($post)) { ?>
    \subsection*{<?php _e('Explanation', 'cvtx'); ?>}
    <?php cvtx_begruendung($post); ?>
<?php } ?>

\subsection*{<?php _e('Author(s)', 'cvtx'); ?>}
<?php cvtx_antragsteller($post); ?>

\end{document}