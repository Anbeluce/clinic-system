<?php
/**
 * Custom header implementation
 *
 * @link https://codex.wordpress.org/Custom_Headers
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

function medicare_clinic_custom_header_setup() {
    register_default_headers( array(
        'default-image' => array(
            'url'           => get_template_directory_uri() . '/assets/images/sliderimage.png',
            'thumbnail_url' => get_template_directory_uri() . '/assets/images/sliderimage.png',
            'description'   => __( 'Default Header Image', 'medicare-clinic' ),
        ),
    ) );
}
add_action( 'after_setup_theme', 'medicare_clinic_custom_header_setup' );

/**
 * Styles the header image based on Customizer settings.
 */
function medicare_clinic_header_style() {
    $medicare_clinic_header_image = get_header_image() ? get_header_image() : get_template_directory_uri() . '/assets/images/sliderimage.png';

    $medicare_clinic_height     = get_theme_mod( 'medicare_clinic_header_image_height', 400 );
    $medicare_clinic_position   = get_theme_mod( 'medicare_clinic_header_background_position', 'center' );
    $medicare_clinic_attachment = get_theme_mod( 'medicare_clinic_header_background_attachment', 1 ) ? 'fixed' : 'scroll';

    $medicare_clinic_custom_css = "
        .header-img, .single-page-img, .external-div .box-image-page img, .external-div {
            background-image: url('" . esc_url( $medicare_clinic_header_image ) . "');
            background-size: cover;
            height: " . esc_attr( $medicare_clinic_height ) . "px;
            background-position: " . esc_attr( $medicare_clinic_position ) . ";
            background-attachment: " . esc_attr( $medicare_clinic_attachment ) . ";
        }

        @media (max-width: 1000px) {
            .header-img, .single-page-img, .external-div .box-image-page img,.external-div,.featured-image{
                height: 250px !important;
            }
            .box-text h2{
                font-size: 27px;
            }
        }
    ";

    wp_add_inline_style( 'medicare-clinic-style', $medicare_clinic_custom_css );
}
add_action( 'wp_enqueue_scripts', 'medicare_clinic_header_style' );

/**
 * Enqueue the main theme stylesheet.
 */
function medicare_clinic_enqueue_styles() {
    wp_enqueue_style( 'medicare-clinic-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'medicare_clinic_enqueue_styles' );