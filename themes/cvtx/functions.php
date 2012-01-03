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
?>