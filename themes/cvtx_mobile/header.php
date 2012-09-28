<!DOCTYPE html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body>

<div data-role="page" data-add-back-btn="false" data-theme="<?php echo get_theme_mod('data_theme'); ?>">

	<div id="header"><div id="verlauf">
		<div id="headerimg"><h1><a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a></h1>
	    	<div class="description">
    			<?php bloginfo('description'); ?>
     		</div>
     	</div>	
	<div id="logo"></div></div></div>
	
	<div data-role="header" data-theme="<?php echo get_theme_mod('data_theme'); ?>">
		<div data-role="navbar">
			<ul>
				<?php wp_nav_menu(array('theme_location' => 'header-menu', 'container' => false, 'walker' => new cvtx_walker(), 'items_wrap' => '%3$s')); ?>
			</ul>
		</div>
	</div><!-- /header -->