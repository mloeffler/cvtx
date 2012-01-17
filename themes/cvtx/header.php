<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<noscript>
<style type="text/css">
nav ul li:hover ul {
	display: block;
}
</style>
</noscript>
</head>

<body>
<div id="overlay"></div>
<header>
	<div id="verlauf">
	<div class="wrapper">
		<div id="headerimg"><h1><a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a></h1>
	    	<div class="description">
    			<?php bloginfo('description'); ?>
     		</div>
     	</div>
		<div id="b90"></div>
	</div>
	</div>
</header>
<div id="c_wrap" class="wrapper">
	<div id="content">
		<nav><?php wp_nav_menu(array('theme_location' => 'header-menu', 'walker' => (has_nav_menu('header-menu') ? new cvtx_walker() : ''))); ?></nav><div class="filler"></div>