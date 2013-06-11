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
\usepackage{floatflt}
\usepackage[strict]{changepage}
\usepackage{hyperref}
\DeclareUnicodeCharacter{A0}{ }

<?php $options = get_option('cvtx_options'); ?>

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
    \cline{1-2}
                                                            &                               \\
    \textbf{\LARGE <?php cvtx_kuerzel($post); ?>}           &                               \\
                                                            &                               \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($post); ?>   \\
                                                            &                               \\
    \cline{1-2}
\end{tabularx}

\begin{floatingtable}[r]{
    \begin{tabularx}{4.5cm}{X}
    \includegraphics[width=4.1cm,keepaspectratio]{<?php cvtx_application_photo($post); ?>}\\
    <?php cvtx_application_gender($post); ?>\smallskip \\
    <?php cvtx_application_birthdate($post); ?>\smallskip \\<?php if (!empty($options['cvtx_application_kvs_name'])) { cvtx_application_kv($post); ?>\smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_bvs_name'])) { cvtx_application_bv($post); ?>\smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_topics'])) { cvtx_application_topics_latex($post); ?>\smallskip \\ <?php } ?>
    <?php cvtx_application_website($post); ?>
    \end{tabularx}}
\end{floatingtable}

\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($post); ?>}

\begin{adjustwidth}{}{5cm}

<?php cvtx_text($post); ?>

\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($post); ?>

\end{adjustwidth}

\end{document}