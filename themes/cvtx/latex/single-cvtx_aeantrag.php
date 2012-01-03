\documentclass[a4paper, 12pt, pagesize, halfparskip]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{tabularx}
\usepackage{calc}
\usepackage{scrpage2}

\sloppy

\pagestyle{scrheadings}
\ohead{Änderungsantrag <?php cvtx_kuerzel($post); ?>}
\setheadsepline{0.4pt}

\begin{document}

\shorthandoff{"}

\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name($post); ?>}\\
 <?php cvtx_beschreibung($post); ?>
\end{flushright}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}
\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                &                                                               \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($post); ?>}}                               \\
                                                &                                                               \\
    AntragstellerInnen:                         &   <?php cvtx_antragsteller_kurz($post); ?>                    \\
                                                &                                                               \\
    Gegenstand:                                 &   <?php cvtx_antrag($post); ?> (<?php cvtx_top($post); ?>)    \\
                                                &                                                               \\
    Anmerkungen:                                &   <?php cvtx_info($post); ?>                                  \\
                                                &                                                               \\
    \hline
\end{tabularx}

\section*{Änderungsantrag <?php cvtx_kuerzel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

\subsection*{Begründung}
<?php cvtx_begruendung($post); ?>

\subsection*{AntragstellerInnen}
<?php cvtx_antragsteller($post); ?>

\end{document}
