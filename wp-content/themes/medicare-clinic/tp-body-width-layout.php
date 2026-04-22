<?php

$medicare_clinic_tp_theme_css = '';

$medicare_clinic_theme_lay = get_theme_mod( 'medicare_clinic_tp_body_layout_settings','Full');
if($medicare_clinic_theme_lay == 'Container'){
$medicare_clinic_tp_theme_css .='body{';
$medicare_clinic_tp_theme_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
$medicare_clinic_tp_theme_css .='}';
$medicare_clinic_tp_theme_css .='@media screen and (max-width:575px){';
$medicare_clinic_tp_theme_css .='body{';
	$medicare_clinic_tp_theme_css .='max-width: 100%; padding-right:0px; padding-left: 0px';
$medicare_clinic_tp_theme_css .='} }';
$medicare_clinic_tp_theme_css .='.scrolled{';
$medicare_clinic_tp_theme_css .='width: auto; left:0; right:0;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_theme_lay == 'Container Fluid'){
$medicare_clinic_tp_theme_css .='body{';
$medicare_clinic_tp_theme_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
$medicare_clinic_tp_theme_css .='}';
$medicare_clinic_tp_theme_css .='@media screen and (max-width:575px){';
$medicare_clinic_tp_theme_css .='body{';
	$medicare_clinic_tp_theme_css .='max-width: 100%; padding-right:0px; padding-left:0px';
$medicare_clinic_tp_theme_css .='} }';
$medicare_clinic_tp_theme_css .='.scrolled{';
$medicare_clinic_tp_theme_css .='width: auto; left:0; right:0;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_theme_lay == 'Full'){
$medicare_clinic_tp_theme_css .='body{';
$medicare_clinic_tp_theme_css .='max-width: 100%;';
$medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_scroll_position = get_theme_mod( 'medicare_clinic_scroll_top_position','Right');
if($medicare_clinic_scroll_position == 'Right'){
$medicare_clinic_tp_theme_css .='#return-to-top{';
$medicare_clinic_tp_theme_css .='right: 20px;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_scroll_position == 'Left'){
$medicare_clinic_tp_theme_css .='#return-to-top{';
$medicare_clinic_tp_theme_css .='left: 20px;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_scroll_position == 'Center'){
$medicare_clinic_tp_theme_css .='#return-to-top{';
$medicare_clinic_tp_theme_css .='right: 50%;left: 50%;';
$medicare_clinic_tp_theme_css .='}';
}

// related post
$medicare_clinic_related_post_mob = get_theme_mod('medicare_clinic_related_post_mob', true);
$medicare_clinic_related_post = get_theme_mod('medicare_clinic_remove_related_post', true);
$medicare_clinic_tp_theme_css .= '.related-post-block {';
if ($medicare_clinic_related_post == false) {
    $medicare_clinic_tp_theme_css .= 'display: none;';
}
$medicare_clinic_tp_theme_css .= '}';
$medicare_clinic_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($medicare_clinic_related_post == false || $medicare_clinic_related_post_mob == false) {
    $medicare_clinic_tp_theme_css .= '.related-post-block { display: none; }';
}
$medicare_clinic_tp_theme_css .= '}';

// slider btn
$medicare_clinic_slider_buttom_mob = get_theme_mod('medicare_clinic_slider_buttom_mob', true);
$medicare_clinic_slider_button = get_theme_mod('medicare_clinic_slider_button', true);
$medicare_clinic_tp_theme_css .= '#main-slider .more-btn {';
if ($medicare_clinic_slider_button == false) {
    $medicare_clinic_tp_theme_css .= 'display: none;';
}
$medicare_clinic_tp_theme_css .= '}';
$medicare_clinic_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($medicare_clinic_slider_button == false || $medicare_clinic_slider_buttom_mob == false) {
    $medicare_clinic_tp_theme_css .= '#main-slider .more-btn { display: none; }';
}
$medicare_clinic_tp_theme_css .= '}';

//return to header mobile               
$medicare_clinic_return_to_header_mob = get_theme_mod('medicare_clinic_return_to_header_mob', true);
$medicare_clinic_return_to_header = get_theme_mod('medicare_clinic_return_to_header', true);
$medicare_clinic_tp_theme_css .= '.return-to-header{';
if ($medicare_clinic_return_to_header == false) {
    $medicare_clinic_tp_theme_css .= 'display: none;';
}
$medicare_clinic_tp_theme_css .= '}';
$medicare_clinic_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($medicare_clinic_return_to_header == false || $medicare_clinic_return_to_header_mob == false) {
    $medicare_clinic_tp_theme_css .= '.return-to-header{ display: none; }';
}
$medicare_clinic_tp_theme_css .= '}';

//blog description              
$medicare_clinic_mobile_blog_description = get_theme_mod('medicare_clinic_mobile_blog_description', true);
$medicare_clinic_tp_theme_css .= '@media screen and (max-width: 575px) {';
if ($medicare_clinic_mobile_blog_description == false) {
    $medicare_clinic_tp_theme_css .= '.blog-description{ display: none; }';
}
$medicare_clinic_tp_theme_css .= '}';


$medicare_clinic_footer_widget_image = get_theme_mod('medicare_clinic_footer_widget_image');
if($medicare_clinic_footer_widget_image != false){
$medicare_clinic_tp_theme_css .='#footer{';
$medicare_clinic_tp_theme_css .='background: url('.esc_attr($medicare_clinic_footer_widget_image).');';
$medicare_clinic_tp_theme_css .='}';
}

//Social icon Font size
$medicare_clinic_social_icon_fontsize = get_theme_mod('medicare_clinic_social_icon_fontsize');
$medicare_clinic_tp_theme_css .='.social-media a i{';
$medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_social_icon_fontsize).'px;';
$medicare_clinic_tp_theme_css .='}';

// site title and tagline font size option
$medicare_clinic_site_title_font_size = get_theme_mod('medicare_clinic_site_title_font_size', ''); {
$medicare_clinic_tp_theme_css .='.logo h1 a, .logo p a{';
$medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_site_title_font_size).'px !important;';
$medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_site_tagline_font_size = get_theme_mod('medicare_clinic_site_tagline_font_size', '');{
$medicare_clinic_tp_theme_css .='.logo p{';
$medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_site_tagline_font_size).'px;';
$medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_related_product = get_theme_mod('medicare_clinic_related_product',true);
if($medicare_clinic_related_product == false){
$medicare_clinic_tp_theme_css .='.related.products{';
	$medicare_clinic_tp_theme_css .='display: none;';
$medicare_clinic_tp_theme_css .='}';
}

//menu font size
$medicare_clinic_menu_font_size = get_theme_mod('medicare_clinic_menu_font_size', '');{
$medicare_clinic_tp_theme_css .='.main-navigation a, .main-navigation li.page_item_has_children:after, .main-navigation li.menu-item-has-children:after{';
	$medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_menu_font_size).'px;';
$medicare_clinic_tp_theme_css .='}';
}

// menu text transform
$medicare_clinic_menu_text_tranform = get_theme_mod( 'medicare_clinic_menu_text_tranform','');
if($medicare_clinic_menu_text_tranform == 'Uppercase'){
$medicare_clinic_tp_theme_css .='.main-navigation a {';
	$medicare_clinic_tp_theme_css .='text-transform: uppercase;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_text_tranform == 'Lowercase'){
$medicare_clinic_tp_theme_css .='.main-navigation a {';
	$medicare_clinic_tp_theme_css .='text-transform: lowercase;';
$medicare_clinic_tp_theme_css .='}';
}
else if($medicare_clinic_menu_text_tranform == 'Capitalize'){
$medicare_clinic_tp_theme_css .='.main-navigation a {';
	$medicare_clinic_tp_theme_css .='text-transform: capitalize;';
$medicare_clinic_tp_theme_css .='}';
}

//sale position
$medicare_clinic_scroll_position = get_theme_mod( 'medicare_clinic_sale_tag_position','right');
if($medicare_clinic_scroll_position == 'right'){
$medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale{';
    $medicare_clinic_tp_theme_css .='right: 25px !important;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_scroll_position == 'left'){
$medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale{';
    $medicare_clinic_tp_theme_css .='left: 25px !important; right: auto !important;';
$medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_woocommerce_sale_font_size = get_theme_mod('medicare_clinic_woocommerce_sale_font_size');
if($medicare_clinic_woocommerce_sale_font_size != false){
    $medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_woocommerce_sale_font_size).'px;';
    $medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_woocommerce_sale_padding_top_bottom = get_theme_mod('medicare_clinic_woocommerce_sale_padding_top_bottom');
if($medicare_clinic_woocommerce_sale_padding_top_bottom != false){
    $medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $medicare_clinic_tp_theme_css .='padding-top: '.esc_attr($medicare_clinic_woocommerce_sale_padding_top_bottom).'px; padding-bottom: '.esc_attr($medicare_clinic_woocommerce_sale_padding_top_bottom).'px;';
    $medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_woocommerce_sale_padding_left_right = get_theme_mod('medicare_clinic_woocommerce_sale_padding_left_right');
if($medicare_clinic_woocommerce_sale_padding_left_right != false){
    $medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $medicare_clinic_tp_theme_css .='padding-left: '.esc_attr($medicare_clinic_woocommerce_sale_padding_left_right).'px !Important; padding-right: '.esc_attr($medicare_clinic_woocommerce_sale_padding_left_right).'px !important;';
    $medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_woocommerce_sale_border_radius = get_theme_mod('medicare_clinic_woocommerce_sale_border_radius', 100);
if($medicare_clinic_woocommerce_sale_border_radius != false){
    $medicare_clinic_tp_theme_css .='.woocommerce ul.products li.product .onsale, .woocommerce span.onsale{';
        $medicare_clinic_tp_theme_css .='border-radius: '.esc_attr($medicare_clinic_woocommerce_sale_border_radius).'% !important;';
    $medicare_clinic_tp_theme_css .='}';
}

//Font Weight
$medicare_clinic_menu_font_weight = get_theme_mod( 'medicare_clinic_menu_font_weight','');
if($medicare_clinic_menu_font_weight == '100'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 100;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '200'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 200;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '300'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 300;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '400'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 400;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '500'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 500;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '600'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 600;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '700'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 700;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '800'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 800;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_menu_font_weight == '900'){
$medicare_clinic_tp_theme_css .='.main-navigation a{';
    $medicare_clinic_tp_theme_css .='font-weight: 900;';
$medicare_clinic_tp_theme_css .='}';
}

/*------------- Blog Page------------------*/
$medicare_clinic_post_image_round = get_theme_mod('medicare_clinic_post_image_round', 0);
if($medicare_clinic_post_image_round != false){
    $medicare_clinic_tp_theme_css .='.blog .box-image img{';
        $medicare_clinic_tp_theme_css .='border-radius: '.esc_attr($medicare_clinic_post_image_round).'px;';
    $medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_post_image_width = get_theme_mod('medicare_clinic_post_image_width', '');
if($medicare_clinic_post_image_width != false){
    $medicare_clinic_tp_theme_css .='.blog .box-image img{';
        $medicare_clinic_tp_theme_css .='Width: '.esc_attr($medicare_clinic_post_image_width).'px;';
    $medicare_clinic_tp_theme_css .='}';
}

$medicare_clinic_post_image_length = get_theme_mod('medicare_clinic_post_image_length', '');
if($medicare_clinic_post_image_length != false){
    $medicare_clinic_tp_theme_css .='.blog .box-image img{';
        $medicare_clinic_tp_theme_css .='height: '.esc_attr($medicare_clinic_post_image_length).'px;';
    $medicare_clinic_tp_theme_css .='}';
}

// footer widget title font size
$medicare_clinic_footer_widget_title_font_size = get_theme_mod('medicare_clinic_footer_widget_title_font_size', '');{
$medicare_clinic_tp_theme_css .='#footer h3, #footer h2.wp-block-heading{';
    $medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_footer_widget_title_font_size).'px;';
$medicare_clinic_tp_theme_css .='}';
}

// Copyright text font size
$medicare_clinic_footer_copyright_font_size = get_theme_mod('medicare_clinic_footer_copyright_font_size', '');{
$medicare_clinic_tp_theme_css .='#footer .site-info p{';
    $medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_footer_copyright_font_size).'px;';
$medicare_clinic_tp_theme_css .='}';
}

// copyright padding
$medicare_clinic_footer_copyright_top_bottom_padding = get_theme_mod('medicare_clinic_footer_copyright_top_bottom_padding', '');
if ($medicare_clinic_footer_copyright_top_bottom_padding !== '') { 
    $medicare_clinic_tp_theme_css .= '.site-info {';
    $medicare_clinic_tp_theme_css .= 'padding-top: ' . esc_attr($medicare_clinic_footer_copyright_top_bottom_padding) . 'px;';
    $medicare_clinic_tp_theme_css .= 'padding-bottom: ' . esc_attr($medicare_clinic_footer_copyright_top_bottom_padding) . 'px;';
    $medicare_clinic_tp_theme_css .= '}';
}

// copyright position
$medicare_clinic_copyright_text_position = get_theme_mod( 'medicare_clinic_copyright_text_position','Center');
if($medicare_clinic_copyright_text_position == 'Center'){
$medicare_clinic_tp_theme_css .='#footer .site-info p{';
$medicare_clinic_tp_theme_css .='text-align:center;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_copyright_text_position == 'Left'){
$medicare_clinic_tp_theme_css .='#footer .site-info p{';
$medicare_clinic_tp_theme_css .='text-align:left;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_copyright_text_position == 'Right'){
$medicare_clinic_tp_theme_css .='#footer .site-info p{';
$medicare_clinic_tp_theme_css .='text-align:right;';
$medicare_clinic_tp_theme_css .='}';
}

// Header Image title font size
$medicare_clinic_header_image_title_font_size = get_theme_mod('medicare_clinic_header_image_title_font_size', '40');{
$medicare_clinic_tp_theme_css .='.box-text h2{';
    $medicare_clinic_tp_theme_css .='font-size: '.esc_attr($medicare_clinic_header_image_title_font_size).'px;';
$medicare_clinic_tp_theme_css .='}';
}

/*--------------------------- banner image Opacity -------------------*/
    $medicare_clinic_theme_lay = get_theme_mod( 'medicare_clinic_header_banner_opacity_color','0.5');
        if($medicare_clinic_theme_lay == '0'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.1'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.1';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.2'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.2';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.3'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.3';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.4'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.4';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.5'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.5';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.6'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.6';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.7'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.7';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.8'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.8';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '0.9'){
            $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
                $medicare_clinic_tp_theme_css .='opacity:0.9';
            $medicare_clinic_tp_theme_css .='}';
        }else if($medicare_clinic_theme_lay == '1'){
            $medicare_clinic_tp_theme_css .='#main-slider img{';
                $medicare_clinic_tp_theme_css .='opacity:1';
            $medicare_clinic_tp_theme_css .='}';
        }

    $medicare_clinic_header_banner_image_overlay = get_theme_mod('medicare_clinic_header_banner_image_overlay', true);
    if($medicare_clinic_header_banner_image_overlay == false){
        $medicare_clinic_tp_theme_css .='.single-page-img, .featured-image{';
            $medicare_clinic_tp_theme_css .='opacity:1;';
        $medicare_clinic_tp_theme_css .='}';
    }

    $medicare_clinic_header_banner_image_ooverlay_color = get_theme_mod('medicare_clinic_header_banner_image_ooverlay_color', true);
    if($medicare_clinic_header_banner_image_ooverlay_color != false){
        $medicare_clinic_tp_theme_css .='.box-image-page{';
            $medicare_clinic_tp_theme_css .='background-color: '.esc_attr($medicare_clinic_header_banner_image_ooverlay_color).';';
        $medicare_clinic_tp_theme_css .='}';
    }

    // header
    $medicare_clinic_slider_arrows = get_theme_mod('medicare_clinic_slider_arrows', true);
    if($medicare_clinic_slider_arrows == false){
    $medicare_clinic_tp_theme_css .='.page-template-front-page .headerbox{';
        $medicare_clinic_tp_theme_css .='position:static; border-bottom: 1px solid #ccc';
    $medicare_clinic_tp_theme_css .='}';
    }

    //First Cap ( Blog Post )
    $medicare_clinic_show_first_caps = get_theme_mod('medicare_clinic_show_first_caps', 'false');
    if($medicare_clinic_show_first_caps == 'true' ){
    $medicare_clinic_tp_theme_css .='.blog .page-box p:nth-of-type(1)::first-letter{';
    $medicare_clinic_tp_theme_css .=' font-size: 55px; font-weight: 600;';
    $medicare_clinic_tp_theme_css .=' margin-right: 6px;';
    $medicare_clinic_tp_theme_css .=' line-height: 1;';
    $medicare_clinic_tp_theme_css .='}';
    }elseif($medicare_clinic_show_first_caps == 'false' ){
    $medicare_clinic_tp_theme_css .='.blog .page-box p:nth-of-type(1)::first-letter {';
    $medicare_clinic_tp_theme_css .='display: none;';
    $medicare_clinic_tp_theme_css .='}';
    }

    // Menu hover effect
    $medicare_clinic_menus_item = get_theme_mod( 'medicare_clinic_menus_item_style','None');
    if($medicare_clinic_menus_item == 'None'){
        $medicare_clinic_tp_theme_css .='.main-navigation a:hover{';
            $medicare_clinic_tp_theme_css .='';
        $medicare_clinic_tp_theme_css .='}';
    }else if($medicare_clinic_menus_item == 'Zoom In'){
        $medicare_clinic_tp_theme_css .='.main-navigation a:hover{';
            $medicare_clinic_tp_theme_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important;';
        $medicare_clinic_tp_theme_css .='}';
    }

    
// footer widget letter case
$medicare_clinic_footer_widget_title_text_tranform = get_theme_mod( 'medicare_clinic_footer_widget_title_text_tranform','');
if($medicare_clinic_footer_widget_title_text_tranform == 'Uppercase'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='text-transform: uppercase;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_text_tranform == 'Lowercase'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='text-transform: lowercase;';
$medicare_clinic_tp_theme_css .='}';
}
else if($medicare_clinic_footer_widget_title_text_tranform == 'Capitalize'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='text-transform: capitalize;';
$medicare_clinic_tp_theme_css .='}';
}

//Footer Font Weight
$medicare_clinic_footer_widget_title_font_weight = get_theme_mod( 'medicare_clinic_footer_widget_title_font_weight','');
if($medicare_clinic_footer_widget_title_font_weight == '100'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 100;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '200'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 200;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '300'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 300;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '400'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 400;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '500'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 500;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '600'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 600;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '700'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 700;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '800'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 800;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_font_weight == '900'){
$medicare_clinic_tp_theme_css .='#footer h2, #footer h3, #footer h1.wp-block-heading, #footer h2.wp-block-heading, #footer h3.wp-block-heading, #footer h4.wp-block-heading, #footer h5.wp-block-heading, #footer h6.wp-block-heading {';
    $medicare_clinic_tp_theme_css .='font-weight: 900;';
$medicare_clinic_tp_theme_css .='}';
}

// footer widget position
$medicare_clinic_footer_widget_title_position = get_theme_mod( 'medicare_clinic_footer_widget_title_position','');
if($medicare_clinic_footer_widget_title_position == 'Right'){
$medicare_clinic_tp_theme_css .='#footer aside.widget-area{';
$medicare_clinic_tp_theme_css .='text-align: right;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_position == 'Left'){
$medicare_clinic_tp_theme_css .='#footer aside.widget-area{';
$medicare_clinic_tp_theme_css .='text-align: left;';
$medicare_clinic_tp_theme_css .='}';
}else if($medicare_clinic_footer_widget_title_position == 'Center'){
$medicare_clinic_tp_theme_css .='#footer aside.widget-area{';
$medicare_clinic_tp_theme_css .='text-align: center;';
$medicare_clinic_tp_theme_css .='}';
}