\documentclass[paper=A4, fontsize=12pt, parskip]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
%\usepackage[onehalfspacing]{setspace}
\usepackage{lineno}
\usepackage{tabularx}
%\usepackage{calc}

\sloppy
\pagestyle{myheadings}
\markright{<?php cvtx_kuerzel(); ?> <?php cvtx_titel(); ?>}

\begin{document}

\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

%\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}
\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                &                                                                           \\
%    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel(); ?>} \textbf{\large <?php cvtx_top_titel(); ?>}}     \\
    \textbf{\LARGE <?php cvtx_kuerzel(); ?>}    &   \textbf{\large <?php cvtx_top_titel(); ?>}                              \\
                                                &                                                                           \\
    AntragstellerInnen:                         &   <?php cvtx_antragsteller(); ?>                                          \\
                                                &                                                                           \\
    Gegenstand:                                 &   <?php cvtx_top_titel(); ?> (<?php cvtx_top_kuerzel(); ?>)               \\
                                                &                                                                           \\
    \hline
\end{tabularx}

\textbf{\Large <?php cvtx_titel(); ?>}

\begin{linenumbers}
%\modulolinenumbers[5]
<?php cvtx_antragstext(); ?>
\end{linenumbers}

\textbf{Begründung:}\\
<?php cvtx_begruendung(); ?>

\end{document}