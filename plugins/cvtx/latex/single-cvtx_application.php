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
\usepackage{graphicx}
\usepackage{multirow}
\usepackage{wrapfig}

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

\begin{tabularx}{\textwidth}{|lX|r}
    \cline{1-2}
                                                            &                                           & \\
    \textbf{\LARGE <?php cvtx_kuerzel($post); ?>}           &                                           & \\
                                                            &                                           & \\
    <?php cvtx_print_latex(__('Name', 'cvtx')); ?>:         &   <?php cvtx_application_name($post); ?>  & \\
                                                            &                                           & \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($post); ?>               & \\
                                                            &                                           & \\
    \cline{1-2}
\end{tabularx}

\begin{wrapfigure}{r}{4cm}
    \vspace{-1cm}
    \begin{small}\begin{flushleft}
    \includegraphics[width=4cm,keepaspectratio]{<?php cvtx_application_photo($post); ?>}\\
    <?php cvtx_application_gender($post); ?>\vspace{3pt} \\
    <?php cvtx_application_birthdate($post); ?>\vspace{3pt} \\
    <?php cvtx_application_kv($post); ?>\vspace{3pt} \\
    <?php cvtx_application_bv($post); ?>\vspace{3pt} \\
    <?php cvtx_application_topics_latex($post); ?>\vspace{3pt} \\
    <?php cvtx_application_website($post); ?>\vspace{3pt} \\
    \end{flushleft}\end{small}
\end{wrapfigure}

\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($post); ?>}

<?php cvtx_text($post); ?>

\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($post); ?>

\end{document}