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
    					   'before_title' => '<h2 class="sidebar_title">',
    				 	   'after_title' => '</h2><br/>'));
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
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/smoothness/jquery-ui.css');
}
add_action('wp_enqueue_scripts', 'add_thickbox');

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
 * Register image size for applications
 */
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size('cvtx_application_theme', 160, 250);
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
		'default' => '#ffe500',
	));
	$wp_customize->add_setting('color2', array(
		'default' => '#58ab27',
	));
	$wp_customize->add_setting('color3', array(
		'default' => '#6ab141',
	));
	$wp_customize->add_setting('color4', array(
		'default' => '#ffe500',
	));
	$wp_customize->add_setting('color5', array(
		'default' => '#ffe500',
	));
	$wp_customize->add_setting('color6', array(
		'default' => '#3C751C',
	));
	$wp_customize->add_setting('logo', array(
		'default' => get_template_directory_uri() . '/images/b90.png',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_1', array(
		'label'      => __('Seitenleiste, Menü, etc.', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color2'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_2', array(
		'label'      => __('2. Menü', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color1'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color', array(
		'label'      => __('Hintergrund Kopfbereich', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color3'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_title_color', array(
		'label'      => __('Farbe Titel', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color4'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_color', array(
		'label'      => __('Farbe Links/Überschriften', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color5'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'menu_back', array(
		'label'      => __('Inaktive Menü-Items', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color6'
	)));
	$wp_customize->add_section('cvtx_logo', array(
		'title' 		 => __('Parteilogo', 'cvtx'),
		'priority'	 => 30
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'logo', array(
		'label'			 => __('Parteilogo', 'cvtx'),
		'section'		 => 'cvtx_logo',
		'settings'	 => 'logo'
	)));
}
add_action('customize_register', 'cvtx_customize_register');

function cvtx_customize_css() { ?>
  <style type="text/css">
    nav ul li.current_page_item,
    div.filler, #sidebar ul.side li h2 { background: <?php echo get_theme_mod('color1'); ?>; }
    nav ul li { background: <?php echo get_theme_mod('color6'); ?>; }
    nav ul li ul li.current_page_item a,  #header span.right,
    header h1 a, #header h1 a { color: <?php echo get_theme_mod('color4'); ?>; }
    ul#antraege li.top h3 { border-bottom: 1px solid <?php echo get_theme_mod('color1'); ?>; }
    #ae_antraege table th { border-bottom: 2px solid <?php echo get_theme_mod('color1'); ?>; }
    #ae_antraege td.verfahren span.procedure { border: 1px solid <?php echo get_theme_mod('color1'); ?>; }
    nav ul li a, nav ul li.current_page_item a,
    nav ul li.current_page_item ul li a, nav ul li ul li.current_page_item ul li a,
    #sidebar ul.side li h2 { color: <?php echo get_theme_mod('color2'); ?>; }
    nav ul li ul.depth-0 { border: 1px solid <?php echo get_theme_mod('color2'); ?>; }
		header, #header { background: <?php echo get_theme_mod('color3'); ?>; }
		#content h2, h3, a, form input.submit, #searchsubmit,
		ul#antraege li.top ul.ae_antraege li span { color: <?php echo get_theme_mod('color5'); ?>; }
		#b90 { background: url(<?php echo get_theme_mod('logo'); ?>) no-repeat right top; }
   </style>
<?php }
add_action('wp_head', 'cvtx_customize_css');
?>