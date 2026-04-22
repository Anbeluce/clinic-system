<?php
/**
 * VW Hospital Lite functions and definitions
 * @package VW Hospital Lite
 */

/* Breadcrumb Begin */
function vw_hospital_lite_the_breadcrumb() {
	if (!is_home()) {
		echo '<a href="';
			echo esc_url( home_url() );
		echo '">';
			bloginfo('name');
		echo "</a> ";
		if (is_category() || is_single()) {
			the_category(',');
			if (is_single()) {
				echo "<span> ";
					the_title();
				echo "</span> ";
			}
		} elseif (is_page()) {
			echo "<span> ";
				the_title();
		}
	}
}

/* Theme Setup */
if ( ! function_exists( 'vw_hospital_lite_setup' ) ) :

function vw_hospital_lite_setup() {
	
	$GLOBALS['content_width'] = apply_filters( 'vw_hospital_lite_content_width', 840 );

	load_theme_textdomain( 'vw-hospital-lite', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 240,
		'width'       => 240,
		'flex-height' => true,
	) );
	add_image_size('vw-hospital-lite-homepage-thumb',240,145,true);
	
       register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'vw-hospital-lite' ),
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'f1f1f1'
	) );

	//selective refresh for sidebar and widgets
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array('image','video','gallery','audio',) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', vw_hospital_lite_font_url() ) );

	// Theme Activation Notice
		global $pagenow;

		if (
			is_admin()
			&&
			('themes.php' == $pagenow)
			// &&
			// isset( $_GET['activated'] )
		) {
			add_action('admin_notices', 'vw_hospital_lite_activation_notice');
		}
}
endif;
add_action( 'after_setup_theme', 'vw_hospital_lite_setup' );

// Notice after Theme Activation
function vw_hospital_lite_activation_notice() {
	$vw_hospital_lite_meta = get_option( 'vw_hospital_lite_admin_notice' );

	if (!$vw_hospital_lite_meta) {
	echo '<div id="vw-hospital-lite-welcome-notice" class="notice notice-success is-dismissible welcome-notice">';;
		echo '<div class="notice-row">';
			echo '<div class="notice-text">';
				echo '<p class="welcome-text1">'. esc_html__( '🎉 Welcome to VW Themes,', 'vw-hospital-lite' ) .'</p>';
				echo '<p class="welcome-text2">'. esc_html__( 'You are now using the VW Hospital, a beautifully designed theme to kickstart your website.', 'vw-hospital-lite' ) .'</p>';
				echo '<p class="welcome-text3">'. esc_html__( 'To help you get started quickly, use the options below:', 'vw-hospital-lite' ) .'</p>';
				echo '<span class="import-btn"><a href="'. esc_url( admin_url( 'themes.php?page=vw_hospital_lite_guide' ) ) .'" class="button button-primary">'. esc_html__( 'DEMO IMPORT', 'vw-hospital-lite' ) .'</a></span>';
				echo '<span class="demo-btn"><a href="'. esc_url( 'https://www.vwthemes.net/vw-hospital-theme/' ) .'" class="button button-primary" target=_blank>'. esc_html__( 'VIEW DEMO', 'vw-hospital-lite' ) .'</a></span>';
				echo '<span class="upgrade-btn"><a href="'. esc_url( 'https://www.vwthemes.com/products/hospital-wordpress-theme' ) .'" class="button button-primary" target=_blank>'. esc_html__( 'UPGRADE TO PRO', 'vw-hospital-lite' ) .'</a></span>';
				echo '<span class="bundle-btn"><a href="'. esc_url( 'https://www.vwthemes.com/products/wp-theme-bundle' ) .'" class="button button-primary" target=_blank>'. esc_html__( 'BUNDLE OF 400+ THEMES', 'vw-hospital-lite' ) .'</a></span>';
			echo '</div>';
			echo '<div class="notice-img1">';
				echo '<img src="' . esc_url( get_template_directory_uri() . '/inc/getting-started/images/arrow-notice.png' ) . '" width="180" alt="' . esc_attr__( 'VW Hospital', 'vw-hospital-lite' ) . '" />';
			echo '</div>';
			echo '<div class="notice-img2">';
				echo '<img src="' . esc_url( get_template_directory_uri() . '/inc/getting-started/images/bundle-notice.png' ) . '" width="180" alt="' . esc_attr__( 'VW Hospital', 'vw-hospital-lite' ) . '" />';
			echo '</div>';	
		echo '</div>';	
	echo '</div>';
}
}

/* Theme Widgets Setup */
function vw_hospital_lite_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on blog page sidebar', 'vw-hospital-lite' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on page sidebar', 'vw-hospital-lite' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Third Column Sidebar', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on page sidebar', 'vw-hospital-lite' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on footer', 'vw-hospital-lite' ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on footer ', 'vw-hospital-lite' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on footer ', 'vw-hospital-lite' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 4', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on footer ', 'vw-hospital-lite' ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Social Icon', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on topbar', 'vw-hospital-lite' ),
		'id'            => 'social-icon',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Shop Page Sidebar', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on shop page', 'vw-hospital-lite' ),
		'id'            => 'woocommerce-shop-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Single Product Sidebar', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on shop page', 'vw-hospital-lite' ),
		'id'            => 'woocommerce-single-sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Social Icon', 'vw-hospital-lite' ),
		'description'   => __( 'Appears on right side footer', 'vw-hospital-lite' ),
		'id'            => 'footer-icon',
		'before_widget' => '<aside id="%1$s" class="widget mb-5 p-3 %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title px-3 py-2">',
		'after_title'   => '</h3>',
	) ); 
}
add_action( 'widgets_init', 'vw_hospital_lite_widgets_init' );

/* Theme Font URL */
function vw_hospital_lite_font_url() {
	$font_family   = array(
		'ABeeZee:ital@0;1',
		'Abril Fatfac',
		'Acme',
		'Allura',
		'Amatic SC:wght@400;700',
		'Anton',
		'Architects Daughter',
		'Archivo:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700',
		'Arsenal:ital,wght@0,400;0,700;1,400;1,700',
		'Arvo:ital,wght@0,400;0,700;1,400;1,700',
		'Alegreya:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900',
		'Asap:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Assistant:wght@200;300;400;500;600;700;800',
		'Alfa Slab One',
		'Averia Serif Libre:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700',
		'Bangers',
		'Boogaloo',
		'Bad Script',
		'Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Barlow Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Berkshire Swash',
		'Bitter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Bree Serif',
		'BenchNine:wght@300;400;700',
		'Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700',
		'Cardo:ital,wght@0,400;0,700;1,400',
		'Courgette',
		'Caveat:wght@400;500;600;700',
		'Caveat Brush',
		'Cherry Swash:wght@400;700',
		'Cormorant Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700',
		'Crimson Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700',
		'Cuprum:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700',
		'Cookie',
		'Coming Soon',
		'Charm:wght@400;700',
		'Chewy',
		'Days One',
		'DM Serif Display:ital@0;1',
		'Dosis:wght@200;300;400;500;600;700;800',
		'EB Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800',
		'Economica:ital,wght@0,400;0,700;1,400;1,700',
		'Epilogue:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Exo 2:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Familjen Grotesk:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700',
		'Fira Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Fredoka One',
		'Fjalla One',
		'Francois One',
		'Frank Ruhl Libre:wght@300;400;500;700;900',
		'Gabriela',
		'Gloria Hallelujah',
		'Great Vibes',
		'Handlee',
		'Hammersmith One',
		'Heebo:wght@100;200;300;400;500;600;700;800;900',
		'Hind:wght@300;400;500;600;700',
		'Inconsolata:wght@200;300;400;500;600;700;800;900',
		'Indie Flower',
		'IM Fell English SC',
		'Julius Sans One',
		'Jomhuria',
		'Josefin Slab:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700',
		'Josefin Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700',
		'Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Kaisei HarunoUmi:wght@400;500;700',
		'Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Kaushan Script',
		'Krub:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700',
		'Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900',
		'Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700',
		'Libre Baskerville:ital,wght@0,400;0,700;1,400',
		'Lobster',
		'Lobster Two:ital,wght@0,400;0,700;1,400;1,700',
		'Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900',
		'Monda:wght@400;700',
		'Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Mulish:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Marck Script',
		'Marcellus',
		'Merienda One',
		'Monda:wght@400;700',
		'Noto Serif:ital,wght@0,400;0,700;1,400;1,700',
		'Nunito Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900',
		'Open Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800',
		'Overpass:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Overpass Mono:wght@300;400;500;600;700',
		'Oxygen:wght@300;400;700',
		'Oswald:wght@200;300;400;500;600;700',
		'Orbitron:wght@400;500;600;700;800;900',
		'Patua One',
		'Pacifico',
		'Padauk:wght@400;700',
		'Playball',
		'Playfair Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900',
		'Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'PT Sans:ital,wght@0,400;0,700;1,400;1,700',
		'PT Serif:ital,wght@0,400;0,700;1,400;1,700',
		'Philosopher:ital,wght@0,400;0,700;1,400;1,700',
		'Permanent Marker',
		'Poiret One',
		'Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Prata',
		'Quicksand:wght@300;400;500;600;700',
		'Quattrocento Sans:ital,wght@0,400;0,700;1,400;1,700',
		'Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Roboto Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700',
		'Rokkitt:wght@100;200;300;400;500;600;700;800;900',
		'Ropa Sans:ital@0;1',
		'Russo One',
		'Righteous',
		'Saira:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Satisfy',
		'Sen:wght@400;700;800',
		'Slabo 13px',
		'Slabo 27px',
		'Source Sans Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900',
		'Shadows Into Light Two',
		'Shadows Into Light',
		'Sacramento',
		'Sail',
		'Shrikhand',
		'League Spartan:wght@100;200;300;400;500;600;700;800;900',
		'Staatliches',
		'Stylish',
		'Tangerine:wght@400;700',
		'Titillium Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700',
		'Trirong:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700',
		'Unica One',
		'VT323',
		'Varela Round',
		'Vampiro One',
		'Vollkorn:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900',
		'Volkhov:ital,wght@0,400;0,700;1,400;1,700',
		'Work Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900',
		'Yanone Kaffeesatz:wght@200;300;400;500;600;700',
		'Yeseva One',
		'ZCOOL XiaoWei'
	);
	$query_args = array(
		'family'	=> rawurlencode(implode('|',$font_family)),
	);
	$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
	return $font_url;
	$contents = vw_hospital_lite_wptt_get_webfont_url( esc_url_raw( $fonts_url ) );
}
 
/* Theme enqueue scripts */
function vw_hospital_lite_scripts() {
	wp_enqueue_style( 'vw-hospital-lite-font', vw_hospital_lite_font_url(), array() );
	wp_enqueue_style( 'vw-hospital-lite-block-style', get_theme_file_uri('/css/blocks.css') );
	wp_enqueue_style( 'vw-hospital-lite-block-patterns-style-frontend', get_theme_file_uri('/inc/block-patterns/css/block-frontend.css') );
	wp_enqueue_style( 'bootstrap-style', get_template_directory_uri().'/css/bootstrap.css' );
	wp_enqueue_style( 'vw-hospital-lite-basic-style', get_stylesheet_uri() );
	require get_parent_theme_file_path( '/inline-style.php' );
	wp_add_inline_style( 'vw-hospital-lite-basic-style',$vw_hospital_lite_custom_css );
	wp_style_add_data( 'vw-hospital-lite-style', 'rtl', 'replace' );
	wp_enqueue_style( 'vw-hospital-lite-effect', get_template_directory_uri().'/css/effect.css' );
	wp_enqueue_style( 'fontawesome-css', get_template_directory_uri() . '/css/fontawesome-all.css' );
	wp_enqueue_script( 'vw-hospital-lite-customscripts', get_template_directory_uri() . '/js/custom.js', array('jquery') );
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.js', array('jquery') );
	wp_enqueue_script( 'jquery-superfish-js', get_template_directory_uri() . '/js/jquery.superfish.js', array('jquery') ,'',true);

	if (get_theme_mod('vw_hospital_lite_animation', true) == true){
		wp_enqueue_script( 'jquery-wow', get_template_directory_uri() . '/js/wow.js', array('jquery') );
		wp_enqueue_style( 'animate-css', get_template_directory_uri().'/css/animate.css' );
	}
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'vw_hospital_lite_scripts' );

/**
 * Enqueue block editor style
 */
function vw_hospital_lite_block_editor_styles() {
	wp_enqueue_style( 'vw-hospital-lite-font', vw_hospital_lite_font_url(), array() );
    wp_enqueue_style( 'vw-hospital-lite-block-patterns-style-editor', get_theme_file_uri( '/inc/block-patterns/css/block-editor.css' ), false, '1.0', 'all' );
    wp_enqueue_style( 'bootstrap-style', get_template_directory_uri().'/css/bootstrap.css' );
}
add_action( 'enqueue_block_editor_assets', 'vw_hospital_lite_block_editor_styles' );



/*radio button sanitization*/
function vw_hospital_lite_sanitize_choices( $input, $setting ) {
    global $wp_customize; 
    $control = $wp_customize->get_control( $setting->id ); 
    if ( array_key_exists( $input, $control->choices ) ) {
        return $input;
    } else {
        return $setting->default;
    }
}

function vw_hospital_lite_sanitize_float( $input ) {
	return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function vw_hospital_lite_sanitize_number_range( $number, $setting ) {
	$number = absint( $number );
	$atts = $setting->manager->get_control( $setting->id )->input_attrs;
	$min = ( isset( $atts['min'] ) ? $atts['min'] : $number );
	$max = ( isset( $atts['max'] ) ? $atts['max'] : $number );
	$step = ( isset( $atts['step'] ) ? $atts['step'] : 1 );
	return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
}

function vw_hospital_lite_sanitize_phone_number( $phone ) {
	return preg_replace( '/[^\d+]/', '', $phone );
}

/* Excerpt Limit Begin */
function vw_hospital_lite_string_limit_words($string, $word_limit) {
	$words = explode(' ', $string, ($word_limit + 1));
	if(count($words) > $word_limit)
	array_pop($words);
	return implode(' ', $words);
}

// Change number or products per row to 3
add_filter('loop_shop_columns', 'vw_hospital_lite_loop_columns');
	if (!function_exists('vw_hospital_lite_loop_columns')) {
	function vw_hospital_lite_loop_columns() {
		return get_theme_mod( 'vw_hospital_lite_products_per_row', '3' ); 
		// 3 products per row
	}
}

//Change number of products that are displayed per page (shop page)
add_filter( 'loop_shop_per_page', 'vw_hospital_lite_products_per_page' );
function vw_hospital_lite_products_per_page( $cols ) {
  	return  get_theme_mod( 'vw_hospital_lite_products_per_page',9);
}

function vw_hospital_lite_sanitize_dropdown_pages( $page_id, $setting ) {
  // Ensure $input is an absolute integer.
  $page_id = absint( $page_id );
  // If $page_id is an ID of a published page, return it; otherwise, return the default.
  return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
}

function vw_hospital_lite_logo_title_hide_show(){
	if(get_theme_mod('vw_hospital_lite_logo_title_hide_show') == '1' ) {
		return true;
	}
	return false;
}

function vw_hospital_lite_tagline_hide_show(){
	if(get_theme_mod('vw_hospital_lite_tagline_hide_show',0) == '1' ) {
		return true;
	}
	return false;
}

add_action( 'wp_ajax_vw_hospital_lite_reset_all_settings', 'vw_hospital_lite_reset_all_settings' );
function vw_hospital_lite_reset_all_settings() {
	remove_theme_mod( 'vw_hospital_lite_slider_hide_show' );
	remove_theme_mod( 'vw_hospital_lite_slider_page' );
	remove_theme_mod( 'vw_hospital_lite_slider_button_text' );
	remove_theme_mod( 'vw_hospital_lite_slider_content_option' );
	remove_theme_mod( 'vw_hospital_lite_slider_content_padding_top_bottom' );
	remove_theme_mod( 'vw_hospital_lite_slider_content_padding_left_right' );
	remove_theme_mod( 'vw_hospital_lite_slider_excerpt_number' );
	remove_theme_mod( 'vw_hospital_lite_slider_opacity_color' );
	remove_theme_mod( 'vw_hospital_lite_slider_height' );
	remove_theme_mod( 'vw_hospital_lite_slider_speed' );
	remove_theme_mod( 'vw_hospital_lite_footer_background_color' );
	remove_theme_mod( 'vw_hospital_lite_footer_copy' );
	remove_theme_mod( 'vw_hospital_lite_copyright_font_size' );
	remove_theme_mod( 'vw_hospital_lite_copyright_alingment' );
	remove_theme_mod( 'vw_hospital_lite_copyright_padding_top_bottom' );
	remove_theme_mod( 'vw_hospital_lite_hide_show_scroll' );
	remove_theme_mod( 'vw_hospital_lite_scroll_top_icon' );
	remove_theme_mod( 'vw_hospital_lite_scroll_to_top_font_size' );
	remove_theme_mod( 'vw_hospital_lite_scroll_to_top_padding' );
	remove_theme_mod( 'vw_hospital_lite_scroll_to_top_width' );
	remove_theme_mod( 'vw_hospital_lite_scroll_to_top_height' );
	remove_theme_mod( 'vw_hospital_lite_scroll_to_top_border_radius' );
	remove_theme_mod( 'vw_hospital_lite_scroll_top_alignment' );
	wp_send_json_success(
		array(
			'success' => true,
			'message' => "Reset Completed",
		)
	);
}
//Active Callback
function vw_hospital_lite_default_slider(){
	if(get_theme_mod('vw_hospital_lite_slider_type', 'Default slider') == 'Default slider' ) {
		return true;
	}
	return false;
}

function vw_hospital_lite_advance_slider(){
	if(get_theme_mod('vw_hospital_lite_slider_type', 'Default slider') == 'Advance slider' ) {
		return true;
	}
	return false;
}

function vw_hospital_lite_blog_post_featured_image_dimension(){
	if(get_theme_mod('vw_hospital_lite_blog_post_featured_image_dimension') == 'custom' ) {
		return true;
	}
	return false;
} 


// edit

if (!function_exists('vw_hospital_lite_edit_link')) :

    function vw_hospital_lite_edit_link($view = 'default')
    {
        global $post;
            edit_post_link(
                sprintf(
                    wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Edit <span class="screen-reader-text">%s</span>', 'vw-hospital-lite'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ),
                '<span class="edit-link"><i class="fas fa-edit"></i>',
                '</span>'
            );

    }
endif;

/* Implement the Custom Header feature. */
require get_template_directory() . '/inc/custom-header.php';

function vw_hospital_lite_init_setup() {
	/* Custom template tags for this theme. */
	require get_template_directory() . '/inc/template-tags.php';

	/* Customizer additions. */
	require get_template_directory() . '/inc/customizer.php';

	/* Plugin Activation */
	require get_template_directory() . '/inc/getting-started/plugin-activation.php';

	/* Implement the About theme page */
	require get_template_directory() . '/inc/getting-started/getting-started.php';

	/* Social Social Widgets */
	require get_template_directory() . '/inc/themes-widgets/social-icon.php';

	/* Customizer additions. */
	require get_template_directory() . '/inc/themes-widgets/about-us-widget.php';

	/* Customizer additions. */
	require get_template_directory() . '/inc/themes-widgets/contact-us-widget.php';

	/* typography */
	require get_template_directory() . '/inc/typography/ctypo.php';

	/* Block Pattern */
	require get_template_directory() . '/inc/block-patterns/block-patterns.php';

	/* TGM Plugin Activation */
	require get_template_directory() . '/inc/tgm/tgm.php';

	/* Webfonts */
	require get_template_directory() . '/inc/wptt-webfont-loader.php';

	define('VW_HOSPITAL_LITE_FREE_THEME_DOC',__('https://preview.vwthemesdemo.com/docs/free-vw-hospital-lite/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_REVIEW',__('https://wordpress.org/support/theme/vw-hospital-lite/reviews/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_BUY_NOW',__('https://www.vwthemes.com/products/hospital-wordpress-theme','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_LIVE_DEMO',__('https://vwthemes.net/vw-hospital-theme/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_PRO_DOC',__('https://preview.vwthemesdemo.com/docs/vw-hospital-pro/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_FAQ',__('https://www.vwthemes.com/faqs/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_CHILD_THEME',__('https://developer.wordpress.org/themes/advanced-topics/child-themes/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_CONTACT',__('https://www.vwthemes.com/contact/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_SUPPORT',__('https://wordpress.org/support/theme/vw-hospital-lite/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_COPYRIGHT_CREDIT',__('https://www.vwthemes.com/','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_CREDIT',__('https://www.vwthemes.com/products/wp-hospital-wordpress-theme','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_THEME_BUNDLE_BUY_NOW',__('https://www.vwthemes.com/products/wp-theme-bundle','vw-hospital-lite'));
	define('VW_HOSPITAL_LITE_THEME_BUNDLE_DOC',__('https://preview.vwthemesdemo.com/docs/theme-bundle/','vw-hospital-lite'));

	if ( ! function_exists( 'vw_hospital_lite_credit' ) ) {
		function vw_hospital_lite_credit(){
			echo "<a href=".esc_url(VW_HOSPITAL_LITE_CREDIT)." target='_blank'>". esc_html__('Hospital WordPress Theme','vw-hospital-lite')."</a>";
		}
	}
}
add_action( 'after_setup_theme', 'vw_hospital_lite_init_setup' );	

// Admin notice code START
function vw_hospital_lite_dismissed_notice() {
	update_option( 'vw_hospital_lite_admin_notice', true );
}
add_action( 'wp_ajax_vw_hospital_lite_dismissed_notice', 'vw_hospital_lite_dismissed_notice' );

//After Switch theme function
add_action('after_switch_theme', 'vw_hospital_lite_getstart_setup_options');
function vw_hospital_lite_getstart_setup_options () {
    update_option('vw_hospital_lite_admin_notice', false );
}
// Admin notice code END
