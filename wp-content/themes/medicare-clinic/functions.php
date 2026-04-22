<?php
/**
 * Medicare Clinic functions and definitions
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

function medicare_clinic_setup() {

	load_theme_textdomain( 'medicare-clinic', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'title-tag' );
	add_theme_support( "responsive-embeds" );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'medicare-clinic-featured-image', 2000, 1200, true );
	add_image_size( 'medicare-clinic-thumbnail-avatar', 100, 100, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary-menu'    => __( 'Primary Menu', 'medicare-clinic' ),
	) );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
    	'flex-height' => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array('image','video','gallery','audio',) );

	add_theme_support( 'html5', array('comment-form','comment-list','gallery','caption',) );

	add_theme_support( 'custom-header', apply_filters( 'medicare_clinic_custom_header_args', array(
        'default-text-color' => 'fff',
        'header-text'        => false,
        'width'              => 1600,
        'height'             => 400,
        'flex-width'         => true,
        'flex-height'        => true,
        'wp-head-callback'   => 'medicare_clinic_header_style',
        'default-image'      => get_template_directory_uri() . '/assets/images/sliderimage.png',
    ) ) );

	/**
	 * Implement the Custom Header feature.
	 */
	require get_parent_theme_file_path( '/inc/custom-header.php' );

}
add_action( 'after_setup_theme', 'medicare_clinic_setup' );

// Add function after setup:
function medicare_clinic_conditional_editor_styles() {
	
	add_editor_style( array( 'assets/css/editor-style.css', medicare_clinic_fonts_url() ) );
}
add_action( 'after_setup_theme', 'medicare_clinic_conditional_editor_styles', 11 );

/**
 * Register custom fonts.
 */
function medicare_clinic_fonts_url(){
	$medicare_clinic_font_url = '';
	$medicare_clinic_font_family = array();
	$medicare_clinic_font_family[] = 'Satisfy';
	$medicare_clinic_font_family[] = 'Outfit:wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,90';
	$medicare_clinic_font_family[] = 'Manrope:wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Oxanium:wght@200;300;400;500;600;700;800';
	$medicare_clinic_font_family[] = 'Oswald:200,300,400,500,600,700';
	$medicare_clinic_font_family[] = 'Roboto Serif:wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Bad Script';
	$medicare_clinic_font_family[] = 'Bebas Neue';
	$medicare_clinic_font_family[] = 'Fjalla One';
	$medicare_clinic_font_family[] = 'PT Sans:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'PT Serif:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900';
	$medicare_clinic_font_family[] = 'Roboto Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700';
	$medicare_clinic_font_family[] = 'Roboto+Flex:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Alex Brush';
	$medicare_clinic_font_family[] = 'Overpass:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Playball';
	$medicare_clinic_font_family[] = 'Alegreya:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Julius Sans One';
	$medicare_clinic_font_family[] = 'Arsenal:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Slabo 13px';
	$medicare_clinic_font_family[] = 'Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900';
	$medicare_clinic_font_family[] = 'Overpass Mono:wght@300;400;500;600;700';
	$medicare_clinic_font_family[] = 'Source Sans Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900';
	$medicare_clinic_font_family[] = 'Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900';
	$medicare_clinic_font_family[] = 'Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700';
	$medicare_clinic_font_family[] = 'Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Playfair Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Quicksand:wght@300;400;500;600;700';
	$medicare_clinic_font_family[] = 'Padauk:wght@400;700';
	$medicare_clinic_font_family[] = 'Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
	$medicare_clinic_font_family[] = 'Inconsolata:wght@200;300;400;500;600;700;800;900&family=Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
	$medicare_clinic_font_family[] = 'Bitter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000';
	$medicare_clinic_font_family[] = 'Pacifico';
	$medicare_clinic_font_family[] = 'Indie Flower';
	$medicare_clinic_font_family[] = 'VT323';
	$medicare_clinic_font_family[] = 'Dosis:wght@200;300;400;500;600;700;800';
	$medicare_clinic_font_family[] = 'Frank Ruhl Libre:wght@300;400;500;700;900';
	$medicare_clinic_font_family[] = 'Fjalla One';
	$medicare_clinic_font_family[] = 'Figtree:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Oxygen:wght@300;400;700';
	$medicare_clinic_font_family[] = 'Arvo:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Noto Serif:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Lobster';
	$medicare_clinic_font_family[] = 'Crimson Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700';
	$medicare_clinic_font_family[] = 'Yanone Kaffeesatz:wght@200;300;400;500;600;700';
	$medicare_clinic_font_family[] = 'Anton';
	$medicare_clinic_font_family[] = 'Libre Baskerville:ital,wght@0,400;0,700;1,400';
	$medicare_clinic_font_family[] = 'Bree Serif';
	$medicare_clinic_font_family[] = 'Gloria Hallelujah';
	$medicare_clinic_font_family[] = 'Abril Fatface';
	$medicare_clinic_font_family[] = 'Varela Round';
	$medicare_clinic_font_family[] = 'Vampiro One';
	$medicare_clinic_font_family[] = 'Shadows Into Light';
	$medicare_clinic_font_family[] = 'Cuprum:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Rokkitt:wght@100;200;300;400;500;600;700;800;900';
	$medicare_clinic_font_family[] = 'Vollkorn:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Francois One';
	$medicare_clinic_font_family[] = 'Orbitron:wght@400;500;600;700;800;900';
	$medicare_clinic_font_family[] = 'Patua One';
	$medicare_clinic_font_family[] = 'Acme';
	$medicare_clinic_font_family[] = 'Satisfy';
	$medicare_clinic_font_family[] = 'Josefin Slab:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Quattrocento Sans:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Architects Daughter';
	$medicare_clinic_font_family[] = 'Russo One';
	$medicare_clinic_font_family[] = 'Monda:wght@400;700';
	$medicare_clinic_font_family[] = 'Righteous';
	$medicare_clinic_font_family[] = 'Lobster Two:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Hammersmith One';
	$medicare_clinic_font_family[] = 'Courgette';
	$medicare_clinic_font_family[] = 'Permanent Marke';
	$medicare_clinic_font_family[] = 'Cherry Swash:wght@400;700';
	$medicare_clinic_font_family[] = 'Cormorant Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700';
	$medicare_clinic_font_family[] = 'Poiret One';
	$medicare_clinic_font_family[] = 'BenchNine:wght@300;400;700';
	$medicare_clinic_font_family[] = 'Economica:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Handlee';
	$medicare_clinic_font_family[] = 'Cardo:ital,wght@0,400;0,700;1,400';
	$medicare_clinic_font_family[] = 'Alfa Slab One';
	$medicare_clinic_font_family[] = 'Averia Serif Libre:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700';
	$medicare_clinic_font_family[] = 'Cookie';
	$medicare_clinic_font_family[] = 'Chewy';
	$medicare_clinic_font_family[] = 'Great Vibes';
	$medicare_clinic_font_family[] = 'Coming Soon';
	$medicare_clinic_font_family[] = 'Philosopher:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Days One';
	$medicare_clinic_font_family[] = 'Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Shrikhand';
	$medicare_clinic_font_family[] = 'Tangerine:wght@400;700';
	$medicare_clinic_font_family[] = 'IM Fell English SC';
	$medicare_clinic_font_family[] = 'Boogaloo';
	$medicare_clinic_font_family[] = 'Bangers';
	$medicare_clinic_font_family[] = 'Fredoka One';
	$medicare_clinic_font_family[] = 'Volkhov:ital,wght@0,400;0,700;1,400;1,700';
	$medicare_clinic_font_family[] = 'Shadows Into Light Two';
	$medicare_clinic_font_family[] = 'Marck Script';
	$medicare_clinic_font_family[] = 'Sacramento';
	$medicare_clinic_font_family[] = 'Unica One';
	$medicare_clinic_font_family[] = 'Dancing Script:wght@400;500;600;700';
	$medicare_clinic_font_family[] = 'Exo 2:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Archivo:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900';
	$medicare_clinic_font_family[] = 'DM Serif Display:ital@0;1';
	$medicare_clinic_font_family[] = 'Open Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800';
	$medicare_clinic_font_family[] = 'Karla:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800';

	$medicare_clinic_query_args = array(
		'family'	=> rawurlencode(implode('|',$medicare_clinic_font_family)),
	);
	$medicare_clinic_font_url = add_query_arg($medicare_clinic_query_args,'//fonts.googleapis.com/css');
	return $medicare_clinic_font_url;
	$contents = wptt_get_webfont_url( esc_url_raw( $medicare_clinic_font_url ) );
}

/**
 * Register widget area.
 */
function medicare_clinic_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'medicare-clinic' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'medicare-clinic' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your sidebar on pages.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar 3', 'medicare-clinic' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'medicare-clinic' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets here to appear in your footer.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'medicare-clinic' ),
		'id'            => 'footer-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'medicare-clinic' ),
		'id'            => 'footer-3',
		'description'   => __( 'Add widgets here to appear in your footer.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 4', 'medicare-clinic' ),
		'id'            => 'footer-4',
		'description'   => __( 'Add widgets here to appear in your footer.', 'medicare-clinic' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'medicare_clinic_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function medicare_clinic_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'medicare-clinic-fonts', medicare_clinic_fonts_url(), array(), null );

	// owl
	wp_enqueue_style( 'owl-carousel-css', get_theme_file_uri( '/assets/css/owl.carousel.css' ) );

	// Bootstrap
	wp_enqueue_style( 'bootstrap-css', get_theme_file_uri( '/assets/css/bootstrap.css' ) );

	// Theme stylesheet.
	wp_enqueue_style( 'medicare-clinic-style', get_stylesheet_uri() );
	require get_parent_theme_file_path( '/tp-theme-color.php' );
	wp_add_inline_style( 'medicare-clinic-style',$medicare_clinic_tp_theme_css );
	wp_style_add_data('medicare-clinic-style', 'rtl', 'replace');
	require get_parent_theme_file_path( '/tp-body-width-layout.php' );
	wp_add_inline_style( 'medicare-clinic-style',$medicare_clinic_tp_theme_css );
	wp_style_add_data('medicare-clinic-style', 'rtl', 'replace');

	// Theme block stylesheet.
	wp_enqueue_style( 'medicare-clinic-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'medicare-clinic-style' ), '1.0' );

	// Fontawesome
	wp_enqueue_style( 'fontawesome-css', get_theme_file_uri( '/assets/css/fontawesome-all.css' ) );
	

	wp_enqueue_script( 'medicare-clinic-custom-scripts', get_template_directory_uri() . '/assets/js/medicare-clinic-custom.js', array('jquery'), true );


	wp_enqueue_script( 'bootstrap-js', get_theme_file_uri( '/assets/js/bootstrap.js' ), array( 'jquery' ), true );

	wp_enqueue_script( 'owl-carousel-js', get_theme_file_uri( '/assets/js/owl.carousel.js' ), array( 'jquery' ), true );

	wp_enqueue_script( 'medicare-clinic-focus-nav', get_template_directory_uri() . '/assets/js/focus-nav.js', array('jquery'), true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$medicare_clinic_body_font_family = get_theme_mod('medicare_clinic_body_font_family', '');

	$medicare_clinic_heading_font_family = get_theme_mod('medicare_clinic_heading_font_family', '');

	$medicare_clinic_menu_font_family = get_theme_mod('medicare_clinic_menu_font_family', '');

	$medicare_clinic_tp_theme_css = '
		body, p.simplep, .more-btn a{
		    font-family: '.esc_html($medicare_clinic_body_font_family).';
		}
		h1,h2, h3, h4, h5, h6, .menubar,.logo h1, .logo p.site-title, p.simplep a, #main-slider p.slidertop-title, .more-btn a,.wc-block-checkout__actions_row .wc-block-components-checkout-place-order-button,.wc-block-cart__submit-container a,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, #theme-sidebar button[type="submit"],
#footer button[type="submit"]{
		    font-family: '.esc_html($medicare_clinic_heading_font_family).';
		}
	';
	wp_add_inline_style('medicare-clinic-style', $medicare_clinic_tp_theme_css);
}
add_action( 'wp_enqueue_scripts', 'medicare_clinic_scripts' );

/*radio button sanitization*/
function medicare_clinic_sanitize_choices( $input, $setting ) {
    global $wp_customize;
    $control = $wp_customize->get_control( $setting->id );
    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

// Sanitize Sortable control.
function medicare_clinic_sanitize_sortable( $val, $setting ) {
	if ( is_string( $val ) || is_numeric( $val ) ) {
		return array(
			esc_attr( $val ),
		);
	}
	$sanitized_value = array();
	foreach ( $val as $item ) {
		if ( isset( $setting->manager->get_control( $setting->id )->choices[ $item ] ) ) {
			$sanitized_value[] = esc_attr( $item );
		}
	}
	return $sanitized_value;
}
/* Excerpt Limit Begin */
function medicare_clinic_excerpt_function($excerpt_count = 35) {
    $medicare_clinic_excerpt = get_the_excerpt();

    $MEDICARE_CLINIC_TEXT_excerpt = wp_strip_all_tags($medicare_clinic_excerpt);

    $medicare_clinic_excerpt_limit = esc_attr(get_theme_mod('medicare_clinic_excerpt_count', $excerpt_count));

    $medicare_clinic_theme_excerpt = implode(' ', array_slice(explode(' ', $MEDICARE_CLINIC_TEXT_excerpt), 0, $medicare_clinic_excerpt_limit));

    return $medicare_clinic_theme_excerpt;
}

function medicare_clinic_sanitize_dropdown_pages( $page_id, $setting ) {
  // Ensure $input is an absolute integer.
  $page_id = absint( $page_id );
  // If $page_id is an ID of a published page, return it; otherwise, return the default.
  return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
}

// Change number or products per row to 3
add_filter('loop_shop_columns', 'medicare_clinic_loop_columns');
if (!function_exists('medicare_clinic_loop_columns')) {
	function medicare_clinic_loop_columns() {
		$columns = get_theme_mod( 'medicare_clinic_per_columns', 3 );
		return $columns;
	}
}

// Category count 
function medicare_clinic_display_post_category_count() {
    $medicare_clinic_category = get_the_category();
    $medicare_clinic_category_count = ($medicare_clinic_category) ? count($medicare_clinic_category) : 0;
    $medicare_clinic_category_text = ($medicare_clinic_category_count === 1) ? 'category' : 'categories'; // Check for pluralization
    echo $medicare_clinic_category_count . ' ' . $medicare_clinic_category_text;
}

//post tag
function medicare_clinic_custom_tags_filter($medicare_clinic_tag_list) {
    // Replace the comma (,) with an empty string
    $medicare_clinic_tag_list = str_replace(', ', '', $medicare_clinic_tag_list);

    return $medicare_clinic_tag_list;
}
add_filter('the_tags', 'medicare_clinic_custom_tags_filter');

function medicare_clinic_custom_output_tags() {
    $medicare_clinic_tags = get_the_tags();

    if ($medicare_clinic_tags) {
        $medicare_clinic_tags_output = '<div class="post_tag">Tags: ';

        $medicare_clinic_first_tag = reset($medicare_clinic_tags);

        foreach ($medicare_clinic_tags as $tag) {
            $medicare_clinic_tags_output .= '<a href="' . esc_url(get_tag_link($tag)) . '" rel="tag" class="me-2">' . esc_html($tag->name) . '</a>';
            if ($tag !== $medicare_clinic_first_tag) {
                $medicare_clinic_tags_output .= ' ';
            }
        }

        $medicare_clinic_tags_output .= '</div>';

        echo $medicare_clinic_tags_output;
    }
}
//Change number of products that are displayed per page (shop page)
add_filter( 'loop_shop_per_page', 'medicare_clinic_per_page', 20 );
function medicare_clinic_per_page( $medicare_clinic_cols ) {
  	$medicare_clinic_cols = get_theme_mod( 'medicare_clinic_product_per_page', 9 );
	return $medicare_clinic_cols;
}

function medicare_clinic_sanitize_number_range( $number, $setting ) {

	// Ensure input is an absolute integer.
	$number = absint( $number );

	// Get the input attributes associated with the setting.
	$atts = $setting->manager->get_control( $setting->id )->input_attrs;

	// Get minimum number in the range.
	$min = ( isset( $atts['min'] ) ? $atts['min'] : $number );

	// Get maximum number in the range.
	$max = ( isset( $atts['max'] ) ? $atts['max'] : $number );

	// Get step.
	$step = ( isset( $atts['step'] ) ? $atts['step'] : 1 );

	// If the number is within the valid range, return it; otherwise, return the default
	return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
}

function medicare_clinic_sanitize_checkbox( $input ) {
	// Boolean check
	return ( ( isset( $input ) && true == $input ) ? true : false );
}

function medicare_clinic_sanitize_phone_number( $phone ) {
	return preg_replace( '/[^\d+]/', '', $phone );
}

function medicare_clinic_sanitize_number_absint( $number, $setting ) {
	// Ensure $number is an absolute integer (whole number, zero or greater).
	$number = absint( $number );

	// If the input is an absolute integer, return it; otherwise, return the default
	return ( $number ? $number : $setting->default );
}

function medicare_clinic_string_limit_words($string, $word_limit) {
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $word_limit));
}

/**
 * Use front-page.php when Front page displays is set to a static page.
 */
function medicare_clinic_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template','medicare_clinic_front_page_template' );

// logo
function medicare_clinic_logo_width(){

	$medicare_clinic_logo_width   = get_theme_mod( 'medicare_clinic_logo_width', 80 );

	echo "<style type='text/css' media='all'>"; ?>
		img.custom-logo{
		    width: <?php echo absint( $medicare_clinic_logo_width ); ?>px;
		    max-width: 100%;
		}
	<?php echo "</style>";
}

add_action( 'wp_head', 'medicare_clinic_logo_width' );

function medicare_clinic_theme_setup() {

	// footer link
	define('MEDICARE_CLINIC_CREDIT',__('https://www.themespride.com/products/medicare-clinic','medicare-clinic') );
	if ( ! function_exists( 'medicare_clinic_credit' ) ) {
		function medicare_clinic_credit(){
			echo "<a href=".esc_url(MEDICARE_CLINIC_CREDIT)." target='_blank'>".esc_html__(get_theme_mod('medicare_clinic_footer_text',__('Medicare Clinic Theme','medicare-clinic')))."</a>";
		}
	}

	/**
	 * Custom template tags for this theme.
	 */
	require get_parent_theme_file_path( '/inc/template-tags.php' );

	/**
	 * Additional features to allow styling of the templates.
	 */
	require get_parent_theme_file_path( '/inc/template-functions.php' );

	/**
	 * Customizer additions.
	 */
	require get_parent_theme_file_path( '/inc/customizer.php' );

	/**
	 * Load Theme Web File
	 */
	require get_parent_theme_file_path('/inc/wptt-webfont-loader.php' );
	/**
	 * Load Theme Web File
	 */
	require get_parent_theme_file_path( '/inc/controls/customize-control-toggle.php' );
	/**
	 * load sortable file
	 */
	require get_parent_theme_file_path( '/inc/controls/sortable-control.php' );

	/**
	 * About Theme Page
	 */
	require get_parent_theme_file_path( '/inc/about-theme.php' );

	/**
	 * TGM Recommendation
	 */
	require get_parent_theme_file_path( '/inc/TGM/tgm.php' );

}
add_action( 'after_setup_theme', 'medicare_clinic_theme_setup' );


//Admin Enqueue for Admin
function medicare_clinic_admin_enqueue_scripts(){
	wp_enqueue_style('medicare-clinic-admin-style', get_template_directory_uri() . '/assets/css/admin.css');
	wp_register_script( 'medicare-clinic-admin-script', get_template_directory_uri() . '/assets/js/medicare-clinic-admin.js', array( 'jquery' ), '', true );

	wp_localize_script(
		'medicare-clinic-admin-script',
		'medicare_clinic',
		array(
			'admin_ajax'	=>	admin_url('admin-ajax.php'),
			'wpnonce'			=>	wp_create_nonce('medicare_clinic_dismissed_notice_nonce')
		)
	);
	wp_enqueue_script('medicare-clinic-admin-script');

    wp_localize_script( 'medicare-clinic-admin-script', 'medicare_clinic_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
    );
}
add_action( 'admin_enqueue_scripts', 'medicare_clinic_admin_enqueue_scripts' );

// get started
add_action( 'wp_ajax_medicare_clinic_dismissed_notice_handler', 'medicare_clinic_ajax_notice_handler' );

function medicare_clinic_ajax_notice_handler() {
	if (!wp_verify_nonce($_POST['wpnonce'], 'medicare_clinic_dismissed_notice_nonce')) {
		exit;
	}
    if ( isset( $_POST['type'] ) ) {
        $type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
        update_option( 'dismissed-' . $type, TRUE );
    }
}

function medicare_clinic_activation_notice() { 

	if ( ! get_option('dismissed-get_started', FALSE ) ) { ?>

    <div class="medicare-clinic-notice-wrapper updated notice notice-get-started-class is-dismissible" data-notice="get_started">
        <div class="medicare-clinic-getting-started-notice clearfix">
        	<div class="row-top">
	            <div class="medicare-clinic-theme-notice-content">
	                <h2 class="medicare-clinic-notice-h2">
	                    <?php
	                printf(
	                /* translators: 1: welcome page link starting html tag, 2: welcome page link ending html tag. */
	                    esc_html__( 'Install the Demo Import Plugin now to instantly set up your site like the live preview.', 'medicare-clinic' ), '<strong>'. wp_get_theme()->get('Name'). '</strong>' );
	                ?>
	                </h2>
	                <a class="medicare-clinic-btn-get-started button button-primary button-hero medicare-clinic-button-padding" href="<?php echo esc_url( admin_url( 'themes.php?page=medicare-clinic-about' )); ?>" ><?php esc_html_e( 'Get Started with Medicare Clinic Theme', 'medicare-clinic' ) ?></a>
	            </div>
	            <div class="image-box">
			    	<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/theme-notice.png' ); ?>" alt="<?php echo esc_attr__( 'Medicare Clinic', 'medicare-clinic' ); ?>" />
				</div>
	        </div>
        </div>
    </div>
<?php }

}
add_action( 'admin_notices', 'medicare_clinic_activation_notice' );

add_action('after_switch_theme', 'medicare_clinic_setup_options');
function medicare_clinic_setup_options () {
    update_option('dismissed-get_started', FALSE );
}

// Get Started Detail Notice - Dismiss permanently
function medicare_clinic_dismissed_get_started_detail_notice() {
    update_option( 'dismissed-get_started-detail', true );
    wp_send_json_success();
}
add_action( 'wp_ajax_medicare_clinic_dismissed_get_started_detail_notice', 'medicare_clinic_dismissed_get_started_detail_notice' );
add_action( 'wp_ajax_nopriv_medicare_clinic_dismissed_get_started_detail_notice', 'medicare_clinic_dismissed_get_started_detail_notice' );

// Reset on theme switch
add_action('after_switch_theme', 'medicare_clinic_setup_settings');
function medicare_clinic_setup_settings() {
    update_option('dismissed-get_started', false );
    update_option('dismissed-get_started-detail', false );
}

add_action( 'wp_ajax_medicare_clinic_popup_done', 'medicare_clinic_popup_done' );
function medicare_clinic_popup_done() {
	update_option( 'medicare_clinic_demo_popup_shown', true );
	wp_die();
}