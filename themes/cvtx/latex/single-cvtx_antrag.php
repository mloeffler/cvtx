\documentclass[paper=A4, fontsize=12pt, parskip]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
%\usepackage[onehalfspacing]{setspace}
\usepackage{lineno}

\sloppy
\pagestyle{myheadings}
\markright{<?php cvtx_top(); ?>}

\begin{document}

\begin{flushright}
 <?php cvtx_name(); ?>\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

\textbf{\Huge <?php cvtx_kuerzel(); ?>}

\textbf{\LARGE <?php cvtx_titel(); ?>}

\textbf{Gegenstand:} <?php cvtx_top_titel(); ?> (<?php cvtx_top_kuerzel(); ?>)\\
\textbf{Antragsteller:} <?php cvtx_antragsteller(); ?>\\[0em]

\begin{linenumbers}
\modulolinenumbers[5]
<?php cvtx_antragstext(); ?>\\[0em]
\end{linenumbers}

\textbf{Begründung:}\\
<?php cvtx_begruendung(); ?>

\end{document}