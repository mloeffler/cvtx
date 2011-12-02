\documentclass[paper=A4, fontsize=12pt]{scrartcl}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage[utf8x]{inputenc}
\usepackage[ngerman]{babel}
\usepackage{fixltx2e}
\usepackage[onehalfspacing]{setspace}
\usepackage{lineno}

\begin{document}

\textbf{\huge <?php cvtx_titel(); ?> (<?php cvtx_kuerzel(); ?>)}

\textbf{Antragsteller:} <?php cvtx_antragsteller(); ?>\\[2em]

\begin{linenumbers}
\modulolinenumbers[5]
<?php cvtx_antragstext(); ?>\\[2em]
\end{linenumbers}

\textbf{Begründung:}\\
<?php cvtx_begruendung(); ?>

\end{document}