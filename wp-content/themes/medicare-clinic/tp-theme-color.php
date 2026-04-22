<?php
	
	$medicare_clinic_tp_theme_css = '';

	// 1st color
	$medicare_clinic_tp_color_option_first = get_theme_mod('medicare_clinic_tp_color_option_first', '#EA7F4A');
	if ($medicare_clinic_tp_color_option_first) {
		$medicare_clinic_tp_theme_css .= ':root {';
		$medicare_clinic_tp_theme_css .= '--color-primary1: ' . esc_attr($medicare_clinic_tp_color_option_first) . ';';
		$medicare_clinic_tp_theme_css .= '}';
	}

	// preloader
	$medicare_clinic_tp_preloader_color1_option = get_theme_mod('medicare_clinic_tp_preloader_color1_option');
	if($medicare_clinic_tp_preloader_color1_option != false){
	$medicare_clinic_tp_theme_css .='.center1{';
		$medicare_clinic_tp_theme_css .='border-color: '.esc_attr($medicare_clinic_tp_preloader_color1_option).' !important;';
	$medicare_clinic_tp_theme_css .='}';
	}
	if($medicare_clinic_tp_preloader_color1_option != false){
	$medicare_clinic_tp_theme_css .='.center1 .ring::before{';
		$medicare_clinic_tp_theme_css .='background: '.esc_attr($medicare_clinic_tp_preloader_color1_option).' !important;';
	$medicare_clinic_tp_theme_css .='}';
	}

	$medicare_clinic_tp_preloader_color2_option = get_theme_mod('medicare_clinic_tp_preloader_color2_option');

	if($medicare_clinic_tp_preloader_color2_option != false){
	$medicare_clinic_tp_theme_css .='.center2{';
		$medicare_clinic_tp_theme_css .='border-color: '.esc_attr($medicare_clinic_tp_preloader_color2_option).' !important;';
	$medicare_clinic_tp_theme_css .='}';
	}
	if($medicare_clinic_tp_preloader_color2_option != false){
	$medicare_clinic_tp_theme_css .='.center2 .ring::before{';
		$medicare_clinic_tp_theme_css .='background: '.esc_attr($medicare_clinic_tp_preloader_color2_option).' !important;';
	$medicare_clinic_tp_theme_css .='}';
	}

	$medicare_clinic_tp_preloader_bg_color_option = get_theme_mod('medicare_clinic_tp_preloader_bg_color_option');

	if($medicare_clinic_tp_preloader_bg_color_option != false){
	$medicare_clinic_tp_theme_css .='.loader{';
		$medicare_clinic_tp_theme_css .='background: '.esc_attr($medicare_clinic_tp_preloader_bg_color_option).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	$medicare_clinic_tp_footer_bg_color_option = get_theme_mod('medicare_clinic_tp_footer_bg_color_option');


	if($medicare_clinic_tp_footer_bg_color_option != false){
	$medicare_clinic_tp_theme_css .='#footer{';
		$medicare_clinic_tp_theme_css .='background: '.esc_attr($medicare_clinic_tp_footer_bg_color_option).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	// logo tagline color
	$medicare_clinic_site_tagline_color = get_theme_mod('medicare_clinic_site_tagline_color');

	if($medicare_clinic_site_tagline_color != false){
	$medicare_clinic_tp_theme_css .='.logo h1 a, .logo p a, .logo p.site-title a{';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_site_tagline_color).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	$medicare_clinic_logo_tagline_color = get_theme_mod('medicare_clinic_logo_tagline_color');
	if($medicare_clinic_logo_tagline_color != false){
	$medicare_clinic_tp_theme_css .='p.site-description{';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_logo_tagline_color).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	// footer widget title color
	$medicare_clinic_footer_widget_title_color = get_theme_mod('medicare_clinic_footer_widget_title_color');
	if($medicare_clinic_footer_widget_title_color != false){
	$medicare_clinic_tp_theme_css .='#footer h3, #footer h2.wp-block-heading{';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_footer_widget_title_color).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	// copyright text color
	$medicare_clinic_footer_copyright_text_color = get_theme_mod('medicare_clinic_footer_copyright_text_color');
	if($medicare_clinic_footer_copyright_text_color != false){
	$medicare_clinic_tp_theme_css .='#footer .site-info p, #footer .site-info a {';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_footer_copyright_text_color).'!important;';
	$medicare_clinic_tp_theme_css .='}';
	}

	// header image title color
	$medicare_clinic_header_image_title_text_color = get_theme_mod('medicare_clinic_header_image_title_text_color');
	if($medicare_clinic_header_image_title_text_color != false){
	$medicare_clinic_tp_theme_css .='.box-text h2{';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_header_image_title_text_color).';';
	$medicare_clinic_tp_theme_css .='}';
	}

	// menu color
	$medicare_clinic_menu_color = get_theme_mod('medicare_clinic_menu_color');
	if($medicare_clinic_menu_color != false){
	$medicare_clinic_tp_theme_css .='.main-navigation a{';
	$medicare_clinic_tp_theme_css .='color: '.esc_attr($medicare_clinic_menu_color).';';
	$medicare_clinic_tp_theme_css .='}';
}


//Footer Font Weight
$medicare_clinic_footer_copyright_title_font_weight = get_theme_mod( 'medicare_clinic_footer_copyright_title_font_weight','');
if($medicare_clinic_footer_copyright_title_font_weight == '100'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 100;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '200'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 200;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '300'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 300;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '400'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 400;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '500'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 500;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '600'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 600;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '700'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 700;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '800'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 800;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_copyright_title_font_weight == '900'){
$medicare_clinic_tp_theme_css .='#footer .site-info p {';
    $medicare_clinic_tp_theme_css .='font-weight: 900;';
$medicare_clinic_tp_theme_css .='}';
}