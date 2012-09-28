<?php
/**
 * Register cvtx_themes scripts
 */
add_action('wp_enqueue_scripts', 'cvtxmobile_script');
function cvtxmobile_script() {
	// register theme-script
	wp_register_script('jquery_mobile',
		get_template_directory_uri().'/jquery_mobile/jquery.mobile-1.1.1.min.js',
		false,
		false,
		false);
	// include jquery
	wp_register_script('script', get_template_directory_uri().'/scripts/script.js');
	wp_enqueue_script('jquery');
	wp_enqueue_script('script');
	wp_enqueue_script('jquery_mobile');
	wp_enqueue_style('jquery_mobile', get_template_directory_uri().'/jquery_mobile/jquery.mobile-1.1.1.min.css');
	wp_enqueue_style('jquery_mobile_theme', get_template_directory_uri().'/jquery_mobile/jquery.mobile.theme-1.1.1.min.css');
}

/**
 * Register menu-regions for cvtx_mobile
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

class cvtx_walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ' data-transition="slide"';
		$attributes .= $item->url != get_bloginfo('wpurl').'/' ? '' : ' data-direction="reverse" data-icon="home"';
		$attributes .= $item->title == 'Anträge' || $item->title == 'Antragsübersicht' ? ' data-icon="grid"' : '';
		$attributes .= strtolower($item->title) == 'antrag stellen' || strtolower($item->title == 'antrag erstellen') ? ' data-icon="plus"' : '';
		
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}


function cvtx_customize_register($wp_customize) {
	$wp_customize->add_setting('color1', array(
		'default' => '#6ab141',
	));
	$wp_customize->add_setting('color2', array(
		'default' => '#ffe500',
	));
	$wp_customize->add_setting('color3', array(
		'default' => '#ffe500',
	));
	$wp_customize->add_setting('logo', array(
		'default' => get_template_directory_uri() . '/images/b90.png',
	));
	$wp_customize->add_setting('data_theme', array(
		'default'	=> 'c',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_1', array(
		'label'      => __('Hintergrundfarbe Header', 'cvtx' ),
		'section'    => 'colors',
		'settings'   => 'color1'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_2', array(
		'label'			 => __('Link-Farbe Header', 'cvtx'),
		'section'		 => 'colors',
		'settings'	 => 'color2'
	)));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_3', array(
		'label'			 => __('Link-Farbe', 'cvtx'),
		'section'		 => 'colors',
		'settings'	 => 'color3'
	)));
	$wp_customize->add_section('cvtx_logo', array(
		'title' 		 => __('Parteilogo', 'cvtx'),
		'priority'	 => 30
	));
	$wp_customize->add_section('cvtx_data_theme', array(
		'title'			 => __('jQuery Mobile Data Theme', 'cvtx'),
		'priority'   => 30
	));
	$wp_customize->add_control('logo', array(
		'label'			 => __('jQuery Mobile Data Theme', 'cvtx'),
		'section'		 => 'cvtx_data_theme',
		'settings'	 => 'data_theme',
		'type'			 => 'select',
		'choices'		 => array(
			'a'	 => 'data-theme a',
			'b'	 => 'data-theme b',
			'c'	 => 'data-theme c',
			'd'	 => 'data-theme d',
			'e'	 => 'data-theme e',
		),
	));
}
add_action('customize_register', 'cvtx_customize_register');

function cvtx_customize_css() { ?>
  <style type="text/css">
  	#header { background-color: <?php echo get_theme_mod('color1'); ?>;}
		#logo { background: url(<?php echo get_theme_mod('logo'); ?>) no-repeat right top; }
		#header #headerimg h1 a { color: <?php echo get_theme_mod('color2'); ?> !important; }
		a { color: <?php echo get_theme_mod('color3'); ?> !important; }
   </style>
<?php }
add_action('wp_head', 'cvtx_customize_css');
?>