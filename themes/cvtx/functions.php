<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array('id' => 'cvtx',
    					   'after_title' => '</h2><div class="inner">',
    				 	   'after_widget' => '</div></li>'));

add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Header Menu' ),
    	  'cvtx-menu' => __( 'Cvtx Menu') )
  );
}

function cvtxtheme_script() {
	wp_enqueue_script("jquery");
}
add_action('wp_enqueue_scripts', 'cvtxtheme_script');

add_filter('nav_menu_css_class' , 'cvtx_nav_class' , 10 , 2);
function cvtx_nav_class($classes, $item){
     if(is_single() && $item->title == "Antrag erstellen"){ 
         $classes[] = "special-class";
     }
     return $classes;
}

function add_first_and_last($output) {
    $output = substr_replace($output, 'class="last-menu-item menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
  return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');

class cvtx_walker extends Walker_Nav_Menu {
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu depth-$depth\">\n";
		if($depth == 0) $output .= "\n<span class=\"arrow\"></span>\n";
	}
}
?>