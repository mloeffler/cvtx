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

\begin{tabularx}{\textwidth}{|lX|rr}
    \cline{1-2}
                                                            &                                           & & \multirow{7}{*}{\includegraphics[width=6cm,height=6cm,keepaspectratio]{<?php cvtx_application_photo($post); ?>}}    \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($post); ?>}}                       & & \\
                                                            &                                           & & \\
    <?php cvtx_print_latex(__('Name', 'cvtx')); ?>:         &   <?php cvtx_application_name($post); ?>  & & \\
                                                            &                                           & & \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($post); ?>               & & \\
                                                            &                                           & & \\
    \cline{1-2}
\end{tabularx}

\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($post); ?>}

<?php cvtx_text($post); ?>

\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($post); ?>

\end{document}