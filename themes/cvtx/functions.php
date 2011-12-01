<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array('after_title' => '</h2><div class="inner">',
    				 	   'after_widget' => '</div></li>'));

add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array('header-menu' => __( 'Header Menu' ) )
  );
}
?>