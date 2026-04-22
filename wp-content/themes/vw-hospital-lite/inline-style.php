<?php

	/*-----------------------First highlight color-------------------*/

	$vw_hospital_lite_first_color = get_theme_mod('vw_hospital_lite_first_color');

	$vw_hospital_lite_custom_css = '';

	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='.top-bar, .menubar, slider .carousel-control-prev-icon i, #slider .carousel-control-next-icon i, #slider .more-btn a:hover, a.r_button:hover, input[type="submit"], input[type="submit"]:hover, .copyright, .services hr, nav.woocommerce-MyAccount-navigation ul li, .sidebar input[type="submit"], .woocommerce span.onsale, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, #comments input[type="submit"].submit, .sidebar .tagcloud a:hover, .footer-sec .tagcloud a:hover, .pagination .current, .pagination a:hover, #comments a.comment-reply-link, a.button, .sidebar a.custom_read_more:hover, .footer-content a.custom_read_more:hover, #slider .carousel-control-prev-icon i, #slider .carousel-control-next-icon i, .widget .woocommerce-product-search button[type="submit"], .sidebar .custom-social-icons i:hover, .footer-content .custom-social-icons i:hover, .nav-previous a, .nav-next a, .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:hover, #preloader, .footer-content .wp-block-search .wp-block-search__button, .sidebar .wp-block-search .wp-block-search__button,nav.navigation.posts-navigation .nav-previous a:hover,nav.navigation.posts-navigation .nav-next a:hover, .woocommerce a.added_to_cart.wc-forward, a.wc-block-components-checkout-return-to-cart-button, .wp-block-woocommerce-cart .wc-block-components-product-badge, a.wp-block-button__link.add_to_cart_button.ajax_add_to_cart{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_first_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='#comments input[type="submit"].submit, .scrollup i,.wc-block-components-order-summary-item__quantity,.wp-block-woocommerce-cart .wc-block-cart__submit-button,
 .wc-block-components-checkout-place-order-button, .wc-block-components-totals-coupon__button{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_first_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='a, .footer-sec h3, .sidebar h3, .logo h1 a, .logo p, .single-post h1, .sidebar .custom-social-icons a, .footer-sec .custom-social-icons a, .sidebar caption, .sidebar td, .sidebar th, .sidebar td#prev a, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title, .entry-content a, .main-navigation ul.sub-menu a:hover, .entry-content a, .sidebar .textwidget p a, .textwidget p a, #comments p a, .slider .inner_carousel p a, .logo h1 a, .logo p.site-title a, .sidebar ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a, .footer-sec li a:hover, .services-box:hover h2 a, .services-box:hover .metabox a, .single-post .metabox:hover a, .sidebar .wp-block-search .wp-block-search__label, .footer-sec .wp-block-search .wp-block-search__label{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_first_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='.services-box:hover h2 a, .services-box:hover .metabox a, .single-post .metabox:hover a{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_first_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='.services .service-main-box, input[type="submit"], .footer-content .custom-social-icons i:hover{';
			$vw_hospital_lite_custom_css .='border-color: '.esc_attr($vw_hospital_lite_first_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='.sidebar h3, .sidebar .wp-block-search .wp-block-search__label{';
			$vw_hospital_lite_custom_css .='border-left-color: '.esc_attr($vw_hospital_lite_first_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_first_color != false){
		$vw_hospital_lite_custom_css .='.footer-sec ul li{';
			$vw_hospital_lite_custom_css .='border-bottom-color: '.esc_attr($vw_hospital_lite_first_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	
	/*----------------Second highlight color-------------------*/

	$vw_hospital_lite_second_color = get_theme_mod('vw_hospital_lite_second_color');

	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='#slider .more-btn a, a.r_button, .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .sidebar .tagcloud a, .footer-sec .tagcloud a, .main-navigation ul li:hover, .hvr-sweep-to-right:before, .sidebar a.custom_read_more, .footer-content a.custom_read_more, .nav-previous a:hover, .nav-next a:hover,nav.navigation.posts-navigation .nav-previous a,nav.navigation.posts-navigation .nav-next a, .woocommerce a.added_to_cart.wc-forward:hover, a.wp-block-button__link.add_to_cart_button.ajax_add_to_cart:hover,.woocommerce-cart  .wc-block-grid .wc-block-grid__product-onsale{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_second_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_second_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='p.calling i, p.email i, .services h2, .services .box-content h3, .services-box h2 a, .page-box h2 a:hover, p.calling a:hover, p.email a:hover, #slider .inner_carousel h1 a:hover{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_second_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='#our-services .page-box a{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_second_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='.copyright.text-center, .main-navigation ul ul{';
			$vw_hospital_lite_custom_css .='border-top-color: '.esc_attr($vw_hospital_lite_second_color).';';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_second_color != false){
		$vw_hospital_lite_custom_css .='.top-bar, .main-navigation ul ul{';
			$vw_hospital_lite_custom_css .='border-bottom-color: '.esc_attr($vw_hospital_lite_second_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*---------------------------Width Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_hospital_lite_width_option','Full Width');
    if($theme_lay == 'Boxed'){
		$vw_hospital_lite_custom_css .='body{';
			$vw_hospital_lite_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto !important; margin-left: auto !important;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='right: 100px;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.scrollup.left i{';
			$vw_hospital_lite_custom_css .='left: 100px;';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Wide Width'){
		$vw_hospital_lite_custom_css .='body{';
			$vw_hospital_lite_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='right: 30px;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.scrollup.left i{';
			$vw_hospital_lite_custom_css .='left: 30px;';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Full Width'){
		$vw_hospital_lite_custom_css .='body{';
			$vw_hospital_lite_custom_css .='max-width: 100%;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------------- Slider Opacity -------------------*/

	$theme_lay = get_theme_mod( 'vw_hospital_lite_slider_opacity_color','0.5');
	if($theme_lay == '0'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.1'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.1';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.2'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.2';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.3'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.3';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.4'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.4';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.5'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.5';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.6'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.6';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.7'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.7';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.8'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.8';
		$vw_hospital_lite_custom_css .='}';
		}else if($theme_lay == '0.9'){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:0.9';
		$vw_hospital_lite_custom_css .='}';
		}

	/*---------------------- Slider Image Overlay ------------------------*/

	$vw_hospital_lite_slider_image_overlay = get_theme_mod('vw_hospital_lite_slider_image_overlay', true);
	if($vw_hospital_lite_slider_image_overlay == false){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='opacity:1;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_slider_image_overlay_color = get_theme_mod('vw_hospital_lite_slider_image_overlay_color', true);
	if($vw_hospital_lite_slider_image_overlay_color != false){
		$vw_hospital_lite_custom_css .='#slider{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_slider_image_overlay_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*-----------------Slider Content Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_hospital_lite_slider_content_option','Center');
    if($theme_lay == 'Left'){
		$vw_hospital_lite_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hospital_lite_custom_css .='text-align:left; left:15%; right:45%;';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Center'){
		$vw_hospital_lite_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hospital_lite_custom_css .='text-align:center; left:20%; right:20%;';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Right'){
		$vw_hospital_lite_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_hospital_lite_custom_css .='text-align:right; left:45%; right:15%;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------- Slider Content Padding Settings ------------------*/

	$vw_hospital_lite_slider_content_padding_top_bottom = get_theme_mod('vw_hospital_lite_slider_content_padding_top_bottom');
	$vw_hospital_lite_slider_content_padding_left_right = get_theme_mod('vw_hospital_lite_slider_content_padding_left_right');
	if($vw_hospital_lite_slider_content_padding_top_bottom != false || $vw_hospital_lite_slider_content_padding_left_right != false){
		$vw_hospital_lite_custom_css .='#slider .carousel-caption{';
			$vw_hospital_lite_custom_css .='top: '.esc_attr($vw_hospital_lite_slider_content_padding_top_bottom).'; bottom: '.esc_attr($vw_hospital_lite_slider_content_padding_top_bottom).';left: '.esc_attr($vw_hospital_lite_slider_content_padding_left_right).';right: '.esc_attr($vw_hospital_lite_slider_content_padding_left_right).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*-----------------Slider Height ------------*/

	$vw_hospital_lite_slider_height = get_theme_mod('vw_hospital_lite_slider_height');
	if($vw_hospital_lite_slider_height != false){
		$vw_hospital_lite_custom_css .='#slider img{';
			$vw_hospital_lite_custom_css .='height: '.esc_attr($vw_hospital_lite_slider_height).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*-----------------Blog Layout -------------------*/

	$theme_lay = get_theme_mod( 'vw_hospital_lite_blog_layout_option','Default');
    if($theme_lay == 'Default'){
		$vw_hospital_lite_custom_css .='.services-box{';
			$vw_hospital_lite_custom_css .='';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Center'){
		$vw_hospital_lite_custom_css .='.services-box, .mainpostbox .page-box h2, .services-box .metabox, .page-box p{';
			$vw_hospital_lite_custom_css .='text-align:center;';
		$vw_hospital_lite_custom_css .='}';
	}else if($theme_lay == 'Left'){
		$vw_hospital_lite_custom_css .='.services-box, .mainpostbox .page-box h2, .services-box .metabox, .page-box p{';
			$vw_hospital_lite_custom_css .='text-align:Left;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.post-info, .services-box h2{';
			$vw_hospital_lite_custom_css .='margin-top: 10px;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*--------------------- Blog Page Posts -------------------*/

	$vw_hospital_lite_blog_page_posts_settings = get_theme_mod( 'vw_hospital_lite_blog_page_posts_settings','Into Blocks');
    if($vw_hospital_lite_blog_page_posts_settings == 'Without Blocks'){
		$vw_hospital_lite_custom_css .='.our-services .page-box{';
			$vw_hospital_lite_custom_css .='box-shadow: none; border: none; margin:30px 0;';
		$vw_hospital_lite_custom_css .='}';
	}

	// featured image dimention
	$vw_hospital_lite_blog_post_featured_image_dimension = get_theme_mod('vw_hospital_lite_blog_post_featured_image_dimension', 'default');
	$vw_hospital_lite_blog_post_featured_image_custom_width = get_theme_mod('vw_hospital_lite_blog_post_featured_image_custom_width',250);
	$vw_hospital_lite_blog_post_featured_image_custom_height = get_theme_mod('vw_hospital_lite_blog_post_featured_image_custom_height',250);
	if($vw_hospital_lite_blog_post_featured_image_dimension == 'custom'){
		$vw_hospital_lite_custom_css .='.our-services img{';
			$vw_hospital_lite_custom_css .='width: '.esc_attr($vw_hospital_lite_blog_post_featured_image_custom_width).'; height: '.esc_attr($vw_hospital_lite_blog_post_featured_image_custom_height).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*----------------Responsive Media -----------------------*/

	$vw_hospital_lite_resp_topbar = get_theme_mod( 'vw_hospital_lite_resp_topbar_hide_show',true);
	if($vw_hospital_lite_resp_topbar == true && get_theme_mod( 'vw_hospital_lite_topbar_hide_show', true) == false){
    	$vw_hospital_lite_custom_css .='.top-bar{';
			$vw_hospital_lite_custom_css .='display:none;';
		$vw_hospital_lite_custom_css .='} ';
	}
    if($vw_hospital_lite_resp_topbar == true){
    	$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.top-bar{';
			$vw_hospital_lite_custom_css .='display:block;';
		$vw_hospital_lite_custom_css .='} }';
	}else if($vw_hospital_lite_resp_topbar == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.top-bar{';
			$vw_hospital_lite_custom_css .='display:none;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_resp_stickyheader = get_theme_mod( 'vw_hospital_lite_stickyheader_hide_show',false);
	if($vw_hospital_lite_resp_stickyheader == true && get_theme_mod( 'vw_hospital_lite_sticky_header',false) != true){
    	$vw_hospital_lite_custom_css .='.header-fixed{';
			$vw_hospital_lite_custom_css .='position:static;';
		$vw_hospital_lite_custom_css .='} ';
	}
    if($vw_hospital_lite_resp_stickyheader == true){
    	$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.header-fixed{';
			$vw_hospital_lite_custom_css .='position:fixed;';
		$vw_hospital_lite_custom_css .='} }';
	}else if($vw_hospital_lite_resp_stickyheader == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px){';
		$vw_hospital_lite_custom_css .='.header-fixed{';
			$vw_hospital_lite_custom_css .='position:static;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_resp_slider = get_theme_mod( 'vw_hospital_lite_resp_slider_hide_show',true);
	if($vw_hospital_lite_resp_slider == true && get_theme_mod( 'vw_hospital_lite_slider_hide_show', true) == false){
    	$vw_hospital_lite_custom_css .='#slider{';
			$vw_hospital_lite_custom_css .='display:none;';
		$vw_hospital_lite_custom_css .='} ';
	}
    if($vw_hospital_lite_resp_slider == true){
    	$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='#slider{';
			$vw_hospital_lite_custom_css .='display:block;';
		$vw_hospital_lite_custom_css .='} }';
	}else if($vw_hospital_lite_resp_slider == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='#slider{';
			$vw_hospital_lite_custom_css .='display:none;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_resp_sidebar = get_theme_mod( 'vw_hospital_lite_sidebar_hide_show',true);
    if($vw_hospital_lite_resp_sidebar == true){
    	$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.sidebar{';
			$vw_hospital_lite_custom_css .='display:block;';
		$vw_hospital_lite_custom_css .='} }';
	}else if($vw_hospital_lite_resp_sidebar == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.sidebar{';
			$vw_hospital_lite_custom_css .='display:none;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_resp_scroll_top = get_theme_mod( 'vw_hospital_lite_resp_scroll_top_hide_show',true);
	if($vw_hospital_lite_resp_scroll_top == true && get_theme_mod( 'vw_hospital_lite_hide_show_scroll',true) != true){
    	$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='visibility:hidden !important;';
		$vw_hospital_lite_custom_css .='} ';
	}
    if($vw_hospital_lite_resp_scroll_top == true){
    	$vw_hospital_lite_custom_css .='@media screen and (max-width:575px) {';
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='visibility:visible !important;';
		$vw_hospital_lite_custom_css .='} }';
	}else if($vw_hospital_lite_resp_scroll_top == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px){';
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='visibility:hidden !important;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_resp_menu_toggle_btn_bg_color = get_theme_mod('vw_hospital_lite_resp_menu_toggle_btn_bg_color');
	if($vw_hospital_lite_resp_menu_toggle_btn_bg_color != false){
		$vw_hospital_lite_custom_css .='.toggle-nav i{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_resp_menu_toggle_btn_bg_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------- Top Bar Settings ------------------*/

	$vw_hospital_lite_topbar_padding_top_bottom = get_theme_mod('vw_hospital_lite_topbar_padding_top_bottom');
	if($vw_hospital_lite_topbar_padding_top_bottom != false){
		$vw_hospital_lite_custom_css .='.top-bar{';
			$vw_hospital_lite_custom_css .='padding-top: '.esc_attr($vw_hospital_lite_topbar_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_hospital_lite_topbar_padding_top_bottom).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_navigation_menu_font_size = get_theme_mod('vw_hospital_lite_navigation_menu_font_size');
	if($vw_hospital_lite_navigation_menu_font_size != false){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_navigation_menu_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_navigation_menu_font_weight = get_theme_mod('vw_hospital_lite_navigation_menu_font_weight','500');
	if($vw_hospital_lite_navigation_menu_font_weight != false){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='font-weight: '.esc_attr($vw_hospital_lite_navigation_menu_font_weight).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_theme_lay = get_theme_mod( 'vw_hospital_lite_menu_text_transform','Uppercase');
	if($vw_hospital_lite_theme_lay == 'Capitalize'){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='text-transform:Capitalize;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_theme_lay == 'Lowercase'){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='text-transform:Lowercase;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_theme_lay == 'Uppercase'){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='text-transform:Uppercase;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_header_menus_color = get_theme_mod('vw_hospital_lite_header_menus_color');
	if($vw_hospital_lite_header_menus_color != false){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_header_menus_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_header_menus_hover_color = get_theme_mod('vw_hospital_lite_header_menus_hover_color');
	if($vw_hospital_lite_header_menus_hover_color != false){
		$vw_hospital_lite_custom_css .='.main-navigation a:hover{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_header_menus_hover_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_header_submenus_color = get_theme_mod('vw_hospital_lite_header_submenus_color');
	if($vw_hospital_lite_header_submenus_color != false){
		$vw_hospital_lite_custom_css .='.main-navigation ul ul a{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_header_submenus_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_header_submenus_hover_color = get_theme_mod('vw_hospital_lite_header_submenus_hover_color');
	if($vw_hospital_lite_header_submenus_hover_color != false){
		$vw_hospital_lite_custom_css .='.main-navigation ul.sub-menu a:hover{';
			$vw_hospital_lite_custom_css .='color: '.esc_attr($vw_hospital_lite_header_submenus_hover_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_menus_item = get_theme_mod( 'vw_hospital_lite_menus_item_style','None');
    if($vw_hospital_lite_menus_item == 'None'){
		$vw_hospital_lite_custom_css .='.main-navigation a{';
			$vw_hospital_lite_custom_css .='';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_menus_item == 'Zoom In'){
		$vw_hospital_lite_custom_css .='.main-navigation a:hover{';
			$vw_hospital_lite_custom_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important; color:#000;';
		$vw_hospital_lite_custom_css .='}';
	}



	/*-------------- Sticky Header Padding ----------------*/

	$vw_hospital_lite_sticky_header_padding = get_theme_mod('vw_hospital_lite_sticky_header_padding');
	if($vw_hospital_lite_sticky_header_padding != false){
		$vw_hospital_lite_custom_css .='.header-fixed{';
			$vw_hospital_lite_custom_css .='padding: '.esc_attr($vw_hospital_lite_sticky_header_padding).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/

	$vw_hospital_lite_search_border_radius = get_theme_mod('vw_hospital_lite_search_border_radius');
	if($vw_hospital_lite_search_border_radius != false){
		$vw_hospital_lite_custom_css .='.menubar form.search-form{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_search_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------- Single Blog Page------------------*/

	$vw_hospital_lite_featured_image_border_radius = get_theme_mod('vw_hospital_lite_featured_image_border_radius', 0);
	if($vw_hospital_lite_featured_image_border_radius != false){
		$vw_hospital_lite_custom_css .='.service-image img, .feature-box img{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_featured_image_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_featured_image_box_shadow = get_theme_mod('vw_hospital_lite_featured_image_box_shadow',0);
	if($vw_hospital_lite_featured_image_box_shadow != false){
		$vw_hospital_lite_custom_css .='.service-image img,  #content-vw img{';
			$vw_hospital_lite_custom_css .='box-shadow: '.esc_attr($vw_hospital_lite_featured_image_box_shadow).'px '.esc_attr($vw_hospital_lite_featured_image_box_shadow).'px '.esc_attr($vw_hospital_lite_featured_image_box_shadow).'px #cccccc;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_singlepost_image_box_shadow = get_theme_mod('vw_hospital_lite_singlepost_image_box_shadow',0);
	if($vw_hospital_lite_singlepost_image_box_shadow != false){
		$vw_hospital_lite_custom_css .='.feature-box img{';
			$vw_hospital_lite_custom_css .='box-shadow: '.esc_attr($vw_hospital_lite_singlepost_image_box_shadow).'px '.esc_attr($vw_hospital_lite_singlepost_image_box_shadow).'px '.esc_attr($vw_hospital_lite_singlepost_image_box_shadow).'px #cccccc;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_related_image_box_shadow = get_theme_mod('vw_hospital_lite_related_image_box_shadow',0);
	if($vw_hospital_lite_related_image_box_shadow != false){
		$vw_hospital_lite_custom_css .='.related-post .box-image img{';
			$vw_hospital_lite_custom_css .='box-shadow: '.esc_attr($vw_hospital_lite_related_image_box_shadow).'px '.esc_attr($vw_hospital_lite_related_image_box_shadow).'px '.esc_attr($vw_hospital_lite_related_image_box_shadow).'px #cccccc;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_single_blog_post_navigation_show_hide = get_theme_mod('vw_hospital_lite_single_blog_post_navigation_show_hide',true);
	if($vw_hospital_lite_single_blog_post_navigation_show_hide != true){
		$vw_hospital_lite_custom_css .='.post-navigation{';
			$vw_hospital_lite_custom_css .='display: none;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_comment_width = get_theme_mod('vw_hospital_lite_single_blog_comment_width');
	if($vw_hospital_lite_comment_width != false){
		$vw_hospital_lite_custom_css .='#comments textarea{';
			$vw_hospital_lite_custom_css .='width: '.esc_attr($vw_hospital_lite_comment_width).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/


	$vw_hospital_lite_copyright_background_color = get_theme_mod('vw_hospital_lite_copyright_background_color');
	if($vw_hospital_lite_copyright_background_color != false){
		$vw_hospital_lite_custom_css .='.copyright{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_copyright_background_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_background_color = get_theme_mod('vw_hospital_lite_footer_background_color');
	if($vw_hospital_lite_footer_background_color != false){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_footer_background_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_copyright_font_size = get_theme_mod('vw_hospital_lite_copyright_font_size');
	if($vw_hospital_lite_copyright_font_size != false){
		$vw_hospital_lite_custom_css .='.copyright p, .copyright p a{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_copyright_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_copyright_alingment = get_theme_mod('vw_hospital_lite_copyright_alingment');
	if($vw_hospital_lite_copyright_alingment != false){
		$vw_hospital_lite_custom_css .='.copyright p,#footer-2 p{';
			$vw_hospital_lite_custom_css .='text-align: '.esc_attr($vw_hospital_lite_copyright_alingment).';';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='
		@media screen and (max-width:720px) {
			.copyright p,#footer-2 p{';
			$vw_hospital_lite_custom_css .='text-align: center;} }';
	}

	$vw_hospital_lite_resp_stickycopyright = get_theme_mod( 'vw_hospital_lite_stickycopyright_hide_show',false);
	if($vw_hospital_lite_resp_stickycopyright == true && get_theme_mod( 'vw_hospital_lite_copyright_sticky',false) != true){
    	$vw_hospital_lite_custom_css .='.copyright-sticky{';
			$vw_hospital_lite_custom_css .='position:static;';
		$vw_hospital_lite_custom_css .='} ';
	}

	$vw_hospital_lite_footer_social_icons_font_size = get_theme_mod('vw_hospital_lite_footer_social_icons_font_size','16');
	$vw_hospital_lite_custom_css .='.copyright .widget i{';
		$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_footer_social_icons_font_size).'px;';
	$vw_hospital_lite_custom_css .='}';

	$vw_hospital_lite_align_footer_social_icon = get_theme_mod('vw_hospital_lite_align_footer_social_icon');
	if($vw_hospital_lite_align_footer_social_icon != false){
		$vw_hospital_lite_custom_css .='.copyright .widget{';
			$vw_hospital_lite_custom_css .='text-align: '.esc_attr($vw_hospital_lite_align_footer_social_icon).';';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='
		@media screen and (max-width:720px) {
			.copyright .widget{';
			$vw_hospital_lite_custom_css .='text-align: center;} }';
	}

	$vw_hospital_lite_copyright_padding_top_bottom = get_theme_mod('vw_hospital_lite_copyright_padding_top_bottom');
	if($vw_hospital_lite_copyright_padding_top_bottom != false){
		$vw_hospital_lite_custom_css .='.copyright.text-center{';
			$vw_hospital_lite_custom_css .='padding-top: '.esc_attr($vw_hospital_lite_copyright_padding_top_bottom).'; padding-bottom: '.esc_attr($vw_hospital_lite_copyright_padding_top_bottom).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_widgets_heading = get_theme_mod( 'vw_hospital_lite_footer_widgets_heading','Left');
    if($vw_hospital_lite_footer_widgets_heading == 'Left'){
		$vw_hospital_lite_custom_css .='.footer-content h3, .footer-content h3 .wp-block-search .wp-block-search__label{';
		$vw_hospital_lite_custom_css .='text-align: left;';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_footer_widgets_heading == 'Center'){
		$vw_hospital_lite_custom_css .='.footer-content h3, .footer-content h3 .wp-block-search .wp-block-search__label{';
			$vw_hospital_lite_custom_css .='text-align: center;';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_footer_widgets_heading == 'Right'){
		$vw_hospital_lite_custom_css .='.footer-content h3, .footer-content .wp-block-search .wp-block-search__label{';
			$vw_hospital_lite_custom_css .='text-align: right;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_widgets_content = get_theme_mod( 'vw_hospital_lite_footer_widgets_content','Left');
    if($vw_hospital_lite_footer_widgets_content == 'Left'){
		$vw_hospital_lite_custom_css .='.footer-content li{';
		$vw_hospital_lite_custom_css .='text-align: left;';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_footer_widgets_content == 'Center'){
		$vw_hospital_lite_custom_css .='.footer-content li{';
			$vw_hospital_lite_custom_css .='text-align: center;';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_footer_widgets_content == 'Right'){
		$vw_hospital_lite_custom_css .='.footer-content li{';
			$vw_hospital_lite_custom_css .='text-align: right;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_padding = get_theme_mod('vw_hospital_lite_footer_padding');
	if($vw_hospital_lite_footer_padding != false){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='padding: '.esc_attr($vw_hospital_lite_footer_padding).' 0;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_background_image = get_theme_mod('vw_hospital_lite_footer_background_image');
	if($vw_hospital_lite_footer_background_image != false){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background: url('.esc_attr($vw_hospital_lite_footer_background_image).'); background-size: cover;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_theme_lay = get_theme_mod( 'vw_hospital_lite_img_footer','scroll');
	if($vw_hospital_lite_theme_lay == 'fixed'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background-attachment: fixed !important;';
		$vw_hospital_lite_custom_css .='}';
	}elseif ($vw_hospital_lite_theme_lay == 'scroll'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background-attachment: scroll !important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_img_position = get_theme_mod('vw_hospital_lite_footer_img_position','center center');
	if($vw_hospital_lite_footer_img_position != false){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background-position: '.esc_attr($vw_hospital_lite_footer_img_position).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$vw_hospital_lite_scroll_to_top_font_size = get_theme_mod('vw_hospital_lite_scroll_to_top_font_size');
	if($vw_hospital_lite_scroll_to_top_font_size != false){
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_scroll_to_top_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_scroll_to_top_padding = get_theme_mod('vw_hospital_lite_scroll_to_top_padding');
	$vw_hospital_lite_scroll_to_top_padding = get_theme_mod('vw_hospital_lite_scroll_to_top_padding');
	if($vw_hospital_lite_scroll_to_top_padding != false){
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='padding-top: '.esc_attr($vw_hospital_lite_scroll_to_top_padding).';padding-bottom: '.esc_attr($vw_hospital_lite_scroll_to_top_padding).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_scroll_to_top_width = get_theme_mod('vw_hospital_lite_scroll_to_top_width');
	if($vw_hospital_lite_scroll_to_top_width != false){
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='width: '.esc_attr($vw_hospital_lite_scroll_to_top_width).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_scroll_to_top_height = get_theme_mod('vw_hospital_lite_scroll_to_top_height');
	if($vw_hospital_lite_scroll_to_top_height != false){
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='height: '.esc_attr($vw_hospital_lite_scroll_to_top_height).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_scroll_to_top_border_radius = get_theme_mod('vw_hospital_lite_scroll_to_top_border_radius');
	if($vw_hospital_lite_scroll_to_top_border_radius != false){
		$vw_hospital_lite_custom_css .='.scrollup i{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_scroll_to_top_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*----------------Social Icons Settings ------------------*/

	$vw_hospital_lite_social_icon_font_size = get_theme_mod('vw_hospital_lite_social_icon_font_size');
	if($vw_hospital_lite_social_icon_font_size != false){
		$vw_hospital_lite_custom_css .='.sidebar .custom-social-icons i, .footer-content .custom-social-icons i, .top-bar .custom-social-icons i{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_social_icon_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_social_icon_padding = get_theme_mod('vw_hospital_lite_social_icon_padding');
	if($vw_hospital_lite_social_icon_padding != false){
		$vw_hospital_lite_custom_css .='.sidebar .custom-social-icons i, .footer-content .custom-social-icons i{';
			$vw_hospital_lite_custom_css .='padding: '.esc_attr($vw_hospital_lite_social_icon_padding).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_social_icon_width = get_theme_mod('vw_hospital_lite_social_icon_width');
	if($vw_hospital_lite_social_icon_width != false){
		$vw_hospital_lite_custom_css .='.sidebar .custom-social-icons i, .footer-content .custom-social-icons i{';
			$vw_hospital_lite_custom_css .='width: '.esc_attr($vw_hospital_lite_social_icon_width).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_social_icon_height = get_theme_mod('vw_hospital_lite_social_icon_height');
	if($vw_hospital_lite_social_icon_height != false){
		$vw_hospital_lite_custom_css .='.sidebar .custom-social-icons i, .footer-content .custom-social-icons i{';
			$vw_hospital_lite_custom_css .='height: '.esc_attr($vw_hospital_lite_social_icon_height).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_social_icon_border_radius = get_theme_mod('vw_hospital_lite_social_icon_border_radius');
	if($vw_hospital_lite_social_icon_border_radius != false){
		$vw_hospital_lite_custom_css .='.sidebar .custom-social-icons i, .footer-content .custom-social-icons i{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_social_icon_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*----------------Woocommerce Products Settings ------------------*/

	$vw_hospital_lite_related_product_show_hide = get_theme_mod('vw_hospital_lite_related_product_show_hide',true);
	if($vw_hospital_lite_related_product_show_hide != true){
		$vw_hospital_lite_custom_css .='.related.products{';
			$vw_hospital_lite_custom_css .='display: none;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_padding_top_bottom = get_theme_mod('vw_hospital_lite_products_padding_top_bottom');
	if($vw_hospital_lite_products_padding_top_bottom != false){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hospital_lite_custom_css .='padding-top: '.esc_attr($vw_hospital_lite_products_padding_top_bottom).'!important; padding-bottom: '.esc_attr($vw_hospital_lite_products_padding_top_bottom).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_padding_left_right = get_theme_mod('vw_hospital_lite_products_padding_left_right');
	if($vw_hospital_lite_products_padding_left_right != false){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hospital_lite_custom_css .='padding-left: '.esc_attr($vw_hospital_lite_products_padding_left_right).'!important; padding-right: '.esc_attr($vw_hospital_lite_products_padding_left_right).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_box_shadow = get_theme_mod('vw_hospital_lite_products_box_shadow');
	if($vw_hospital_lite_products_box_shadow != false){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
				$vw_hospital_lite_custom_css .='box-shadow: '.esc_attr($vw_hospital_lite_products_box_shadow).'px '.esc_attr($vw_hospital_lite_products_box_shadow).'px '.esc_attr($vw_hospital_lite_products_box_shadow).'px #ddd;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_border_radius = get_theme_mod('vw_hospital_lite_products_border_radius', 0);
	if($vw_hospital_lite_products_border_radius != false){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_products_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_btn_padding_top_bottom = get_theme_mod('vw_hospital_lite_products_btn_padding_top_bottom');
	if($vw_hospital_lite_products_btn_padding_top_bottom != false){
		$vw_hospital_lite_custom_css .='.woocommerce a.button{';
			$vw_hospital_lite_custom_css .='padding-top: '.esc_attr($vw_hospital_lite_products_btn_padding_top_bottom).' !important; padding-bottom: '.esc_attr($vw_hospital_lite_products_btn_padding_top_bottom).' !important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_btn_padding_left_right = get_theme_mod('vw_hospital_lite_products_btn_padding_left_right');
	if($vw_hospital_lite_products_btn_padding_left_right != false){
		$vw_hospital_lite_custom_css .='.woocommerce a.button{';
			$vw_hospital_lite_custom_css .='padding-left: '.esc_attr($vw_hospital_lite_products_btn_padding_left_right).' !important; padding-right: '.esc_attr($vw_hospital_lite_products_btn_padding_left_right).' !important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_products_button_border_radius = get_theme_mod('vw_hospital_lite_products_button_border_radius', 0);
	if($vw_hospital_lite_products_button_border_radius != false){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product .button, a.checkout-button.button.alt.wc-forward,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_products_button_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_woocommerce_sale_position = get_theme_mod( 'vw_hospital_lite_woocommerce_sale_position','right');
    if($vw_hospital_lite_woocommerce_sale_position == 'left'){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product .onsale{';
			$vw_hospital_lite_custom_css .='left: -10px; right: auto;';
		$vw_hospital_lite_custom_css .='}';
	}else if($vw_hospital_lite_woocommerce_sale_position == 'right'){
		$vw_hospital_lite_custom_css .='.woocommerce ul.products li.product .onsale{';
			$vw_hospital_lite_custom_css .='left: auto; right: 0;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_woocommerce_sale_font_size = get_theme_mod('vw_hospital_lite_woocommerce_sale_font_size');
	if($vw_hospital_lite_woocommerce_sale_font_size != false){
		$vw_hospital_lite_custom_css .='.woocommerce span.onsale{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_woocommerce_sale_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_woocommerce_sale_border_radius = get_theme_mod('vw_hospital_lite_woocommerce_sale_border_radius', 100);
	if($vw_hospital_lite_woocommerce_sale_border_radius != false){
		$vw_hospital_lite_custom_css .='.woocommerce span.onsale{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_woocommerce_sale_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------------ Logo  -------------------*/

	// Site title Font Size
	$vw_hospital_lite_site_title_font_size = get_theme_mod('vw_hospital_lite_site_title_font_size');
	if($vw_hospital_lite_site_title_font_size != false){
		$vw_hospital_lite_custom_css .='.logo h1 a, .logo p.site-title a{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_site_title_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	// Site tagline Font Size
	$vw_hospital_lite_site_tagline_font_size = get_theme_mod('vw_hospital_lite_site_tagline_font_size');
	if($vw_hospital_lite_site_tagline_font_size != false){
		$vw_hospital_lite_custom_css .='.logo p.site-description{';
			$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_site_tagline_font_size).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_logo_padding = get_theme_mod('vw_hospital_lite_logo_padding');
	if($vw_hospital_lite_logo_padding != false){
		$vw_hospital_lite_custom_css .='.logo{';
			$vw_hospital_lite_custom_css .='padding: '.esc_attr($vw_hospital_lite_logo_padding).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_logo_margin = get_theme_mod('vw_hospital_lite_logo_margin');
	if($vw_hospital_lite_logo_margin != false){
		$vw_hospital_lite_custom_css .='.logo{';
			$vw_hospital_lite_custom_css .='margin: '.esc_attr($vw_hospital_lite_logo_margin).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*------------------ Preloader Background Color  -------------------*/

	$vw_hospital_lite_preloader_bg_color = get_theme_mod('vw_hospital_lite_preloader_bg_color');
	if($vw_hospital_lite_preloader_bg_color != false){
		$vw_hospital_lite_custom_css .='#preloader{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_preloader_bg_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_preloader_border_color = get_theme_mod('vw_hospital_lite_preloader_border_color');
	if($vw_hospital_lite_preloader_border_color != false){
		$vw_hospital_lite_custom_css .='.loader-line{';
			$vw_hospital_lite_custom_css .='border-color: '.esc_attr($vw_hospital_lite_preloader_border_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_preloader_bg_img = get_theme_mod('vw_hospital_lite_preloader_bg_img');
	if($vw_hospital_lite_preloader_bg_img != false){
		$vw_hospital_lite_custom_css .='#preloader{';
			$vw_hospital_lite_custom_css .='background: url('.esc_attr($vw_hospital_lite_preloader_bg_img).');-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;';
		$vw_hospital_lite_custom_css .='}';
	}

	// Header Background Color
	$vw_hospital_lite_header_background_color = get_theme_mod('vw_hospital_lite_header_background_color');
	if($vw_hospital_lite_header_background_color != false){
		$vw_hospital_lite_custom_css .='.header{';
			$vw_hospital_lite_custom_css .='background-color: '.esc_attr($vw_hospital_lite_header_background_color).';';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_header_img_position = get_theme_mod('vw_hospital_lite_header_img_position','center top');
	if($vw_hospital_lite_header_img_position != false){
		$vw_hospital_lite_custom_css .='.menubar{';
			$vw_hospital_lite_custom_css .='background-position: '.esc_attr($vw_hospital_lite_header_img_position).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*--------------------- Grid Posts Posts -------------------*/

	$vw_hospital_lite_display_grid_posts_settings = get_theme_mod( 'vw_hospital_lite_display_grid_posts_settings','Into Blocks');
    if($vw_hospital_lite_display_grid_posts_settings == 'Without Blocks'){
		$vw_hospital_lite_custom_css .='.grid-post-main-box {';
			$vw_hospital_lite_custom_css .='box-shadow: none; border: none; margin:30px 0;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_grid_featured_image_border_radius = get_theme_mod('vw_hospital_lite_grid_featured_image_border_radius', 0);
	if($vw_hospital_lite_grid_featured_image_border_radius != false){
		$vw_hospital_lite_custom_css .='.grid-post-main-box .box-image img, .grid-post-main-box .feature-box img{';
			$vw_hospital_lite_custom_css .='border-radius: '.esc_attr($vw_hospital_lite_grid_featured_image_border_radius).'px;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_grid_featured_image_box_shadow = get_theme_mod('vw_hospital_lite_grid_featured_image_box_shadow',0);
	if($vw_hospital_lite_grid_featured_image_box_shadow != false){
		$vw_hospital_lite_custom_css .='.grid-post-main-box .box-image img, .grid-post-main-box .feature-box img, #content-vw img{';
			$vw_hospital_lite_custom_css .='box-shadow: '.esc_attr($vw_hospital_lite_grid_featured_image_box_shadow).'px '.esc_attr($vw_hospital_lite_grid_featured_image_box_shadow).'px '.esc_attr($vw_hospital_lite_grid_featured_image_box_shadow).'px #cccccc;';
		$vw_hospital_lite_custom_css .='}';
	}

	/*---------------- Footer Settings ------------------*/

	$vw_hospital_lite_button_footer_heading_letter_spacing = get_theme_mod('vw_hospital_lite_button_footer_heading_letter_spacing',1);
	$vw_hospital_lite_custom_css .='.footer-content h3, a.rsswidget.rss-widget-title{';
		$vw_hospital_lite_custom_css .='letter-spacing: '.esc_attr($vw_hospital_lite_button_footer_heading_letter_spacing).'px;';
	$vw_hospital_lite_custom_css .='}';

	$vw_hospital_lite_button_footer_font_size = get_theme_mod('vw_hospital_lite_button_footer_font_size','25');
	$vw_hospital_lite_custom_css .='.footer-content h3, a.rsswidget.rss-widget-title{';
		$vw_hospital_lite_custom_css .='font-size: '.esc_attr($vw_hospital_lite_button_footer_font_size).'px;';
	$vw_hospital_lite_custom_css .='}';

	$vw_hospital_lite_theme_lay = get_theme_mod( 'vw_hospital_lite_button_footer_text_transform','Capitalize');
	if($vw_hospital_lite_theme_lay == 'Capitalize'){
		$vw_hospital_lite_custom_css .='.footer-content h3{';
			$vw_hospital_lite_custom_css .='text-transform:Capitalize;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_theme_lay == 'Lowercase'){
		$vw_hospital_lite_custom_css .='.footer-content h3, a.rsswidget.rss-widget-title{';
			$vw_hospital_lite_custom_css .='text-transform:Lowercase;';
		$vw_hospital_lite_custom_css .='}';
	}
	if($vw_hospital_lite_theme_lay == 'Uppercase'){
		$vw_hospital_lite_custom_css .='.footer-content h3, a.rsswidget.rss-widget-title{';
			$vw_hospital_lite_custom_css .='text-transform:Uppercase;';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_footer_heading_weight = get_theme_mod('vw_hospital_lite_footer_heading_weight','');
	if($vw_hospital_lite_footer_heading_weight != false){
		$vw_hospital_lite_custom_css .='.footer-content h3, a.rsswidget.rss-widget-title{';
			$vw_hospital_lite_custom_css .='font-weight: '.esc_attr($vw_hospital_lite_footer_heading_weight).';';
		$vw_hospital_lite_custom_css .='}';
	}

	/*---------------------------Footer Style -------------------*/

	$vw_hospital_lite_theme_lay = get_theme_mod( 'vw_hospital_lite_footer_template','vw-hospital-lite-footer-one');
    if($vw_hospital_lite_theme_lay == 'vw-hospital-lite-footer-one'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='';
		$vw_hospital_lite_custom_css .='}';

	}else if($vw_hospital_lite_theme_lay == 'vw-hospital-lite-footer-two'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background: linear-gradient(to right, #f9f8ff, #dedafa);';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content p, .footer-content li a, .footer-content, .footer-content h3, .footer-content a.rsswidget, .footer-content #wp-calendar a, .copyright a, .footer-content .custom_details, .footer-content ins span, .footer-content .tagcloud a, .main-inner-box span.entry-date a, nav.woocommerce-MyAccount-navigation ul li:hover a, .footer-content ul li a, .footer-content table, .footer-content th, .footer-content td, .footer-content caption, #sidebar caption,.footer-content nav.wp-calendar-nav a,.footer-content .search-form .search-field, .footer-sec{';
			$vw_hospital_lite_custom_css .='color:#000;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content ul li::before{';
			$vw_hospital_lite_custom_css .='background:#000;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content table, .footer-content th, .footer-content td,.footer-content .search-form .search-field,.footer-content .tagcloud a{';
			$vw_hospital_lite_custom_css .='border: 1px solid #000;';
		$vw_hospital_lite_custom_css .='}';

	}else if($vw_hospital_lite_theme_lay == 'vw-hospital-lite-footer-three'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background: #232524;';
		$vw_hospital_lite_custom_css .='}';
	}
	else if($vw_hospital_lite_theme_lay == 'vw-hospital-lite-footer-four'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background: #f7f7f7;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content p, .footer-content li a, .footer-content, .footer-content h3, .footer-content a.rsswidget, .footer-content #wp-calendar a, .copyright a, .footer-content .custom_details, .footer-content ins span, .footer-content .tagcloud a, .main-inner-box span.entry-date a, nav.woocommerce-MyAccount-navigation ul li:hover a, .footer-content ul li a, .footer-content table, .footer-content th, .footer-content td, .footer-content caption, #sidebar caption,.footer-content nav.wp-calendar-nav a,.footer-content .search-form .search-field, .footer-sec{';
			$vw_hospital_lite_custom_css .='color:#000;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content ul li::before{';
			$vw_hospital_lite_custom_css .='background:#000;';
		$vw_hospital_lite_custom_css .='}';
		$vw_hospital_lite_custom_css .='.footer-content table, .footer-content th, .footer-content td,.footer-content .search-form .search-field,.footer-content .tagcloud a{';
			$vw_hospital_lite_custom_css .='border: 1px solid #000;';
		$vw_hospital_lite_custom_css .='}';
	}
	else if($vw_hospital_lite_theme_lay == 'vw-hospital-lite-footer-five'){
		$vw_hospital_lite_custom_css .='.footer-content{';
			$vw_hospital_lite_custom_css .='background: linear-gradient(to right, #01093a, #2d0b00);';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_responsive_preloader_hide = get_theme_mod('vw_hospital_lite_responsive_preloader_hide',false);
	if($vw_hospital_lite_responsive_preloader_hide == true && get_theme_mod('vw_hospital_lite_loader_enable',false) == false){
		$vw_hospital_lite_custom_css .='@media screen and (min-width:575px){
			#preloader{';
			$vw_hospital_lite_custom_css .='display:none !important;';
		$vw_hospital_lite_custom_css .='} }';
	}

	if($vw_hospital_lite_responsive_preloader_hide == false){
		$vw_hospital_lite_custom_css .='@media screen and (max-width:575px){
			#preloader{';
			$vw_hospital_lite_custom_css .='display:none !important;';
		$vw_hospital_lite_custom_css .='} }';
	}

	$vw_hospital_lite_bradcrumbs_alignment = get_theme_mod( 'vw_hospital_lite_bradcrumbs_alignment','Left');
    if($vw_hospital_lite_bradcrumbs_alignment == 'Left'){
    	$vw_hospital_lite_custom_css .='@media screen and (min-width:768px) {';
		$vw_hospital_lite_custom_css .='.bradcrumbs{';
			$vw_hospital_lite_custom_css .='text-align:start;';
		$vw_hospital_lite_custom_css .='}}';
	}else if($vw_hospital_lite_bradcrumbs_alignment == 'Center'){
		$vw_hospital_lite_custom_css .='@media screen and (min-width:768px) {';
		$vw_hospital_lite_custom_css .='.bradcrumbs{';
			$vw_hospital_lite_custom_css .='text-align:center;';
		$vw_hospital_lite_custom_css .='}}';
	}else if($vw_hospital_lite_bradcrumbs_alignment == 'Right'){
		$vw_hospital_lite_custom_css .='@media screen and (min-width:768px) {';
		$vw_hospital_lite_custom_css .='.bradcrumbs{';
			$vw_hospital_lite_custom_css .='text-align:end;';
		$vw_hospital_lite_custom_css .='}}';
	}

	// sticky sidebar
		$vw_hospital_lite_resp_stickysidebar = get_theme_mod( 'vw_hospital_lite_stickysidebar_hide_show',false);
	if($vw_hospital_lite_resp_stickysidebar == true && get_theme_mod( 'vw_hospital_lite_sticky_sidebar',false) != true){
		$vw_hospital_lite_custom_css .= '@media (min-width: 768px) {';
    	$vw_hospital_lite_custom_css .='#sidebar{';
			$vw_hospital_lite_custom_css .='position:static;';
		$vw_hospital_lite_custom_css .='} ';
		$vw_hospital_lite_custom_css .= '}';
	}
    // First Letter Capital
	$vw_hospital_lite_show_first_caps = get_theme_mod('vw_hospital_lite_show_first_caps', false);
	if ($vw_hospital_lite_show_first_caps ) {
	    $vw_hospital_lite_custom_css .= '.service-text .entry-content p:nth-of-type(1)::first-letter {';
	    $vw_hospital_lite_custom_css .=' font-size: 55px;font-weight: 600;margin-right: 5px;';
	    $vw_hospital_lite_custom_css .='}';
	} else {
		$vw_hospital_lite_custom_css .= '.service-text .entry-content p:nth-of-type(1)::first-letter {';
	    $vw_hospital_lite_custom_css .= 'display: none;';
	    $vw_hospital_lite_custom_css .='}';
	}

	// Web Frame 
	$vw_hospital_lite_web_frame = get_theme_mod('vw_hospital_lite_web_frame',false);
	if($vw_hospital_lite_web_frame == false && get_theme_mod( 'vw_hospital_lite_web_frame',false) != true){
		$vw_hospital_lite_custom_css .='.web-frame{';
		$vw_hospital_lite_custom_css .='border: 1px !important';
		$vw_hospital_lite_custom_css .='}';
	}

	$vw_hospital_lite_web_frame_border_color = get_theme_mod('vw_hospital_lite_web_frame_border_color');
	if($vw_hospital_lite_web_frame_border_color != false){
		$vw_hospital_lite_custom_css .='.web-frame{';
			$vw_hospital_lite_custom_css .='border-color: '.esc_attr($vw_hospital_lite_web_frame_border_color).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}
    
	$vw_hospital_lite_web_frame_border_width = get_theme_mod('vw_hospital_lite_web_frame_border_width');
	if($vw_hospital_lite_web_frame_border_width != false){
		$vw_hospital_lite_custom_css .='.web-frame{';
			$vw_hospital_lite_custom_css .='border-width: '.esc_attr($vw_hospital_lite_web_frame_border_width).'!important;';
		$vw_hospital_lite_custom_css .='}';
	}

	// change the Category style //
	$vw_hospital_lite_single_post_styling = get_theme_mod( 'vw_hospital_lite_single_post_styling','Button');
		if($vw_hospital_lite_single_post_styling == 'Underline'){
		$vw_hospital_lite_custom_css .='.single-post-category .post-categories li a{';
			$vw_hospital_lite_custom_css .='text-decoration: underline; color: #000; background-color: transparent;';
		$vw_hospital_lite_custom_css .='}';
	}
	else if($vw_hospital_lite_single_post_styling == 'Default'){
		$vw_hospital_lite_custom_css .='.single-post-category .post-categories li a{';
			$vw_hospital_lite_custom_css .='text-decoration: none; color: #000; background-color: transparent;';
		$vw_hospital_lite_custom_css .='}';
	}