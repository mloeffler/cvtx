<?php
/**
 * Functions-file for cvtx_theme
 *
 * @package WordPress
 * @subpackage cvtx
 */

// Register a cvtx_sidebar
if (function_exists('register_sidebar')) {
    register_sidebar(array('id' => 'cvtx', 'name' => 'Sidebar'));
    register_sidebar(array('id' => 'cvtx_footer_first', 'name' => 'Fußbereich 1'));
    register_sidebar(array('id' => 'cvtx_footer_second', 'name' => 'Fußbereich 2'));
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

add_action('wp_head', 'js_header');

function js_header() {
	wp_enqueue_script('tiny_mce');
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
    array('header-menu' => __( 'Header Menu' )
  ));
}

/**
 * cvtx_walker is a Class, which extends the Walker_Nav_Menu-Class
 * and is instantiated in wp_nav_menu-calls in cvtx_theme. It adds
 * the actual depth to sub-menus and adds a HTML-element for theming.
 */
class cvtx_walker extends Walker_Nav_Menu {
  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"sub-menu depth-$depth\">\n";
		if($depth == 0) $output .= "\n<span class=\"arrow\"></span>\n";
  }
}

// Theme-Customization
function cvtx_customize_register($wp_customize) {
	$wp_customize->add_setting('color1', array(
		'default' => '#037f09',
		'transport' => 'refresh',
	));
	$wp_customize->add_setting('color2', array(
		'default' => '#89bd49',
		'transport' => 'refresh',
	));
	$wp_customize->add_setting('color3', array(
		'default' => '#faa619',
		'transport' => 'refresh',
	));
	$wp_customize->add_setting('color4', array(
		'default' => '#58ab27',
		'transport' => 'refresh',
	));
	$wp_customize->add_setting('h_image', array(
		'default' => get_template_directory_uri() . '/images/back_kongress.png',
		'transport' => 'refresh'
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_color', array(
		'label'      => __('Links und Überschriften', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color1'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_color_hover', array(
		'label'      => __('Link:hover, 3. Überschrift', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color2'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'menu_color', array(
		'label'      => __('Link im Menü', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color3'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'submenu_color', array(
		'label'      => __('Farbe 4, Untermenü', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color4'
	)));
	$wp_customize->add_control(new WP_Customize_Header_Image_Control($wp_customize, 'header_image1', array(
		'label' 		 => __('Header Image'),
		'section'		 => 'header_image',
		'settings'	 => 'h_image'
	)));
}
add_action('customize_register', 'cvtx_customize_register');

function cvtx_customize_css() { ?>
  <style type="text/css">
    a, h1, h2, h4, h5, h6 { color:<?php echo get_theme_mod('color1'); ?>; }
		a:hover,
		h3,
    #footer div.tb_tweet a,
    nav ul li a:hover,
    nav ul li.current_page_item a { color: <?php echo get_theme_mod('color2'); ?>; }
  	#footer .content a,
  	#footer div.tb_tweet a:hover,
  	nav ul li a { color: <?php echo get_theme_mod('color3'); ?>; }
  	ul#antraege li.top h3 { border-bottom: 1px solid <?php echo get_theme_mod('color3'); ?>; }
  	#ae_antraege table th { border-bottom: 2px solid <?php echo get_theme_mod('color3'); ?>; }
  	header, #header {	background: url(<?php header_image(); ?>) no-repeat top center; }
  	form input.submit, #searchsubmit, nav ul li.current_page_item ul li a,
  	nav ul li ul li.current_page_item ul li a, ul#antraege li.top ul li.antrag ul.ae_antraege li span
  	{ color: <?php echo get_theme_mod('color4'); ?>; }
  	nav ul li ul.depth-0 { border: 1px solid <?php echo get_theme_mod('color4'); ?>; }
  </style>
<?php }
add_action('wp_head', 'cvtx_customize_css');

$args = array(
	'width'         => 1200,
	'height'        => 298,
	'default-image' => get_template_directory_uri() . '/images/back_kongress.png',
	'uploads' 			=> true
);
add_theme_support('custom-header', $args);
?>