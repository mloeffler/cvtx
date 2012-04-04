<?php
/**
 * Functions-file for cvtx_theme
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */

add_action( 'after_setup_theme', 'cvtx_theme_setup' );
function cvtx_theme_setup() {
    load_theme_textdomain('cvtx_theme', TEMPLATEPATH . '/languages' );
}

// Register a cvtx_sidebar
if (function_exists('register_sidebar')) {
    register_sidebar(array('id' => 'cvtx',
    					   'after_title' => '</h2><div class="inner">',
    				 	   'after_widget' => '</div></li>'));
}

/**
 * Register cvtx_themes scripts
 */
add_action('wp_enqueue_scripts', 'cvtxtheme_script');
function cvtxtheme_script() {
	// include jquery
	wp_enqueue_script('jquery');
	// register theme-script
	wp_register_script('cvtx_script',
		get_template_directory_uri().'/scripts/script.js',
		array('jquery'),
		false,
		true);
	// include jquery.printElement
	wp_register_script('print_element',
		get_template_directory_uri().'/scripts/jquery.printElement.min.js',
		false,
		false,
		true);
    wp_enqueue_script('cvtx_script');
    wp_enqueue_script('print_element');
}

/**
 * Register menu-regions for cvtx_theme
 *
 * There are two menu-regions: the main menu (left) and
 * the cvtx-menu (right). The latter is supposed to hold
 * items like "new request"
 */
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Header Menu' ),
    	  'cvtx-menu' => __( 'Cvtx Menu') )
  );
}

/**
 * cvtx_walker is a Class, which extends the Walker_Nav_Menu-Class
 * and is instantiated in wp_nav_menu-calls in cvtx_theme. It adds
 * the actual depth to sub-menus and adds a HTML-element for theming.
 */
class cvtx_walker extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu depth-$depth\">\n";
		if($depth == 0) $output .= "\n<span class=\"arrow\"></span>\n";
	}
}
?>