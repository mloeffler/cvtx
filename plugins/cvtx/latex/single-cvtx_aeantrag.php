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
\usepackage{calc}
\usepackage{scrpage2}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}

\sloppy

\pagestyle{scrheadings}
\ohead{<?php cvtx_get_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($post); ?>}
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

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}
\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                        &                                                                     \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($post); ?>}}                                             \\
                                                        &                                                                     \\
    <?php cvtx_get_latex(__('Author(s)', 'cvtx')); ?>:  &   <?php cvtx_antragsteller_kurz($post); ?>                          \\
                                                        &                                                                     \\
    <?php cvtx_get_latex(__('Concerning', 'cvtx')); ?>: &   <?php cvtx_antrag($post); ?> (<?php cvtx_top_titel($post); ?>)    \\
                                                        &                                                                     \\
<?php if (cvtx_has_info($post)) { ?>
    <?php cvtx_get_latex(__('Remarks', 'cvtx')); ?>:    &   <?php cvtx_info($post); ?>                                        \\
                                                        &                                                                     \\
<?php } ?>
    \hline
\end{tabularx}

\section*{<?php cvtx_get_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($post)) { ?>
    \subsection*{<?php cvtx_get_latex(__('Explanation', 'cvtx')); ?>}
    <?php cvtx_begruendung($post); ?>
<?php } ?>

\subsection*{<?php cvtx_get_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($post); ?>

\end{document}
