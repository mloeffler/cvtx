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
    AntragstellerInnen:                             &   <?php cvtx_antragsteller_kurz($post); ?>                                \\
                                                    &                                                                           \\
    Gegenstand:                                     &   <?php cvtx_top_titel($post); ?> (<?php cvtx_top_kuerzel($post); ?>)     \\
                                                    &                                                                           \\
    Anmerkungen:                                    &   <?php cvtx_info($post); ?>                                              \\
                                                    &                                                                           \\
    \hline
\end{tabularx}

\section*{<?php cvtx_titel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

\subsection*{Begründung}
<?php cvtx_begruendung($post); ?>

\subsection*{AntragstellerInnen}
<?php cvtx_antragsteller($post); ?>

\end{document}