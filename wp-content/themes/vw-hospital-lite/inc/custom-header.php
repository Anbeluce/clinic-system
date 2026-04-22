<?php
/**
 * @package VW Hospital Lite
 * @subpackage vw-hospital-lite
 * @since vw-hospital-lite 1.0
 * Setup the WordPress core custom header feature.
 *
 * @uses vw_hospital_lite_header_style()
*/

function vw_hospital_lite_custom_header_setup() {

	add_theme_support( 'custom-header', apply_filters( 'vw_hospital_lite_custom_header_args', array(
		'default-text-color'     => 'fff',
		'header-text' 			 =>	false,
		'width'                  => 1600,
		'height'                 => 400,
		'flex-width'             => true,
		'flex-height'            => true,
		'admin-preview-callback' => 'vw_hospital_lite_admin_header_image',
	) ) );
}

add_action( 'after_setup_theme', 'vw_hospital_lite_custom_header_setup' );

if ( ! function_exists( 'vw_hospital_lite_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see vw_hospital_lite_custom_header_setup().
 */
add_action( 'wp_enqueue_scripts', 'vw_hospital_lite_header_style' );
function vw_hospital_lite_header_style() {
	//Check if user has defined any header image.
	if ( get_header_image() ) :
	$custom_css = "
        .menubar{
			background-image:url('".esc_url(get_header_image())."');
			background-position: center top;
			background-size: cover;
		}";
	   	wp_add_inline_style( 'vw-hospital-lite-basic-style', $custom_css );
	endif;
}
endif; // vw_hospital_lite_header_style