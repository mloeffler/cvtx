\documentclass[a4paper, 12pt, pagesize, halfparskip]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
%\usepackage[onehalfspacing]{setspace}
\usepackage{lineno}
\usepackage{tabularx}
%\usepackage{calc}
\usepackage{scrpage2}

\sloppy

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel(); ?> <?php cvtx_titel(); ?>}
\setheadsepline{0.4pt}

\begin{document}

\shorthandoff{"}

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
    Anmerkungen:                                &   <?php cvtx_info(); ?>                                                   \\
                                                &                                                                           \\
    \hline
\end{tabularx}

\section*{<?php cvtx_titel(); ?>}

\begin{linenumbers}
%\modulolinenumbers[5]
<?php cvtx_antragstext(); ?>
\end{linenumbers}

%\textbf{Begründung:}\\
\subsection*{Begründung}
<?php cvtx_begruendung(); ?>

\end{document}