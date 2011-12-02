<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
<?php if(isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1):?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/ae_antraege.css" type="text/css" media="screen" />
<?php endif; ?>
<?php wp_head(); ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/jquery.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/script.js"></script>
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
		<nav><?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?></nav><div class="filler"></div>