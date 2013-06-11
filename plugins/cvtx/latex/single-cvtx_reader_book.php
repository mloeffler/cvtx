\documentclass[paper=a4, 12pt, pagesize, parskip=half, DIV=calc]{scrbook}
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
\usepackage{calc}
\usepackage{pdfpages}
\usepackage{hyperref}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}
\usepackage{graphicx}
\usepackage{multirow}
\usepackage{hyperref}
\usepackage{floatflt}
\usepackage[strict]{changepage}
\DeclareUnicodeCharacter{A0}{ }

<?php $options = get_option('cvtx_options'); ?>

\sloppy

% Page Style Settings
\pagestyle{scrheadings}
\setheadsepline{0.4pt}
\setuptoc{toc}{totoc}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}

% Document Information
\subject{<?php cvtx_name(); ?>\\ <?php cvtx_beschreibung(); ?>}
\title{<?php cvtx_titel($post); ?>}
\date{<?php cvtx_print_latex(__('This version', 'cvtx')); ?>: \today}
\author{}

\begin{document}

\shorthandoff{"}

% Show Title Page
\maketitle

% Show Table of Contents
\tableofcontents

<?php
$top    = 0;
$antrag = 0;
$query  = new WP_Query(array('post_type'   => array('cvtx_antrag',
                                                    'cvtx_aeantrag',
                                                    'cvtx_application'),
                             'taxonomy'    => 'cvtx_tax_reader',
                             'term'        => 'cvtx_reader_'.intval($post->ID),
                             'orderby'     => 'meta_value',
                             'meta_key'    => 'cvtx_sort',
                             'order'       => 'ASC',
                             'nopaging'    => true,
                             'post_status' =>'publish'));
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
    
    /* Show Resolution */
    if ($item->post_type == 'cvtx_antrag') {
        $antrag = $item->ID;
?>
% Start New Page
\newpage

% Hide Headline and Show Page Number on This Page, Define Headline Text
\thispagestyle{plain}
\ohead{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Site Title and Subtitle
\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

% Info Box
\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                            &                                              \\
    \textbf{\LARGE <?php cvtx_kuerzel($item); ?>}           &                                              \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>:    &   <?php cvtx_antragsteller_kurz($item); ?>   \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($item); ?>                  \\
                                                            &                                              \\
<?php if (cvtx_has_info($item)) { ?>
    <?php cvtx_print_latex(__('Remarks', 'cvtx')); ?>:      &   <?php cvtx_info($item); ?>                 \\
                                                            &                                              \\
<?php } ?>
    \hline
\end{tabularx}

% Resolution title
\section*{<?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_top = get_post_meta($item->ID, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   } ?>
\addcontentsline{toc}{section}{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Resolution Text
\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

% Explanation
<?php if (cvtx_has_begruendung($item)) { ?>
   \subsection*{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}
   <?php cvtx_begruendung($item); ?>
<?php } ?>

% Author(s)
\subsection*{<?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($item); ?>

<?php
    }
    
    /* Show Application */
    else if ($item->post_type == 'cvtx_application') {
        // Include pdf or load latex file?
        $manually = (get_post_meta($item->ID, 'cvtx_application_manually', true) == 'on');
        
        // Include PDF
        if ($manually && cvtx_get_file($item)) {
?>
% Start New Page
\newpage

% Define Headline Text
\ohead{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php       // Update agenda item if changed
            $this_top = get_post_meta($item->ID, 'cvtx_application_top', true);
            if ($top != $this_top) {
                $top  = $this_top;
?>
                \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php       } ?>
\addcontentsline{toc}{section}{<?php cvtx_print_latex(__('Application ', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\includepdf[pages=-, pagecommand={\thispagestyle{scrheadings}}, offset=-1.5em 2em, width=1.15\textwidth]{<?php cvtx_application_file($item); ?>}

<?php
        }
        // Show latex inside
        else {
?>
% Start New Page
\newpage

% Define Headline Text
\ohead{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Site Title and Subtitle
\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

% Info Box
\begin{tabularx}{\textwidth}{|lX|r}
    \cline{1-2}
                                                            &                                           & \\
    \textbf{\LARGE <?php cvtx_kuerzel($item); ?>}           &                                           & \\
                                                            &                                           & \\
    <?php cvtx_print_latex(__('Name', 'cvtx')); ?>:         &   <?php cvtx_application_name($item); ?>  & \\
                                                            &                                           & \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($item); ?>               & \\
                                                            &                                           & \\
    \cline{1-2}
\end{tabularx}

% application fields
\begin{floatingtable}[r]{
    \begin{tabularx}{4.5cm}{X}
    \includegraphics[width=4.1cm,keepaspectratio]{<?php cvtx_application_photo($item); ?>}\\
    <?php cvtx_application_gender($item); ?>\smallskip \\
    <?php cvtx_application_birthdate($item); ?>\smallskip \\
    <?php if (!empty($options['cvtx_application_kvs_name'])) { cvtx_application_kv($item); ?>\smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_bvs_name'])) { cvtx_application_bv($item); ?>\smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_topics'])) { cvtx_application_topics_latex($item); ?>\smallskip \\ <?php } ?>
    <?php cvtx_application_website($item); ?>
    \end{tabularx}}
\end{floatingtable}

% Application title
\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_top = get_post_meta($item->ID, 'cvtx_application_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   } ?>
\addcontentsline{toc}{section}{<?php cvtx_print_latex(__('Application ', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\begin{adjustwidth}{}{5cm}

% Application text
<?php cvtx_text($item); ?>

% Biography
\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($item); ?>

\end{adjustwidth}


<?php
        }
    }

    /* Show Amendment */
    else if ($item->post_type == 'cvtx_aeantrag') {
?>
% Start New Page
\newpage
% Hide Headline and Show Page Number on This Page, Define Headline Text
\thispagestyle{plain}
\ohead{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Site Title and Subtitle
\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

% Info Box
\begin{tabularx}{\textwidth}{|lX|}
    \hline
                                                            &                                                                     \\
    \multicolumn{2}{|>{\adjust}X|}{\textbf{\LARGE <?php cvtx_kuerzel($item); ?>}}                                                 \\
                                                            &                                                                     \\
    <?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>:    &   <?php cvtx_antragsteller_kurz($item); ?>                          \\
                                                            &                                                                     \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_antrag($item); ?> (<?php cvtx_top_titel($item); ?>)    \\
                                                            &                                                                     \\
<?php if (cvtx_has_info($item)) { ?>
    <?php cvtx_print_latex(__('Remarks', 'cvtx')); ?>:      &   <?php cvtx_info($item); ?>                                        \\
                                                            &                                                                     \\
<?php } ?>
    \hline
\end{tabularx}

% Amendment Title
\section*{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_antrag = get_post_meta($item->ID, 'cvtx_aeantrag_antrag', true);
        $this_top    = get_post_meta($this_antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   }
        // Update resolution if changed
        if ($antrag != $this_antrag) {
            $antrag  = $this_antrag;
?>
            \addcontentsline{toc}{section}{<?php cvtx_antrag($item); ?>}
<?php   } ?>
\addcontentsline{toc}{subsection}{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Amendment Text
\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

% Explanation
<?php if (cvtx_has_begruendung($item)) { ?>
    \subsection*{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}
    <?php cvtx_begruendung($item); ?>
<?php } ?>

% Author(s)
\subsection*{<?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($item); ?>


<?php
    }
}
?>

\end{document}
