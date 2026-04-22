<?php
/**
 * VW Hospital Lite Theme Customizer
 *
 * @package VW Hospital Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vw_hospital_lite_custom_controls() {

    load_template( trailingslashit( get_template_directory() ) . '/inc/custom-controls.php' );
}
add_action( 'customize_register', 'vw_hospital_lite_custom_controls' );

function vw_hospital_lite_customize_register( $wp_customize ) {

    load_template( trailingslashit( get_template_directory() ) . '/inc/icon-picker.php' );

    $wp_customize->get_setting( 'blogname' )->transport = 'postMessage'; 
    $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial( 'blogname', array( 
        'selector' => '.logo .site-title a', 
        'render_callback' => 'vw_hospital_lite_customize_partial_blogname', 
    )); 

    $wp_customize->selective_refresh->add_partial( 'blogdescription', array( 
        'selector' => 'p.site-description', 
        'render_callback' => 'vw_hospital_lite_customize_partial_blogdescription', 
    ));

    //add home page setting pannel
    $VWHospitalLiteParentPanel = new VW_Hospital_Lite_WP_Customize_Panel( $wp_customize, 'vw_hospital_lite_panel_id', array(
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => esc_html__( 'VW Settings', 'vw-hospital-lite' ),
        'priority' => 10,
    ));

    $wp_customize->add_panel( $VWHospitalLiteParentPanel );

    $HomePageParentPanel = new VW_Hospital_Lite_WP_Customize_Panel( $wp_customize, 'vw_hospital_lite_homepage_panel', array(
        'title' => __( 'Homepage Settings', 'vw-hospital-lite' ),
        'panel' => 'vw_hospital_lite_panel_id',
    ));

    $wp_customize->add_panel( $HomePageParentPanel );

    //Topbar
    $wp_customize->add_section('vw_hospital_lite_top',array(
        'title' => __('Contact Us','vw-hospital-lite'),
        'description'   => __('This section is used to display address and contact','vw-hospital-lite'),
        'priority'   => 30,
        'panel' => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_topbar_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_topbar_hide_show',array(
        'label' => esc_html__( 'Show / Hide Topbar','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_top'
    )));

    // Header Background color
    $wp_customize->add_setting('vw_hospital_lite_header_background_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_header_background_color', array(
        'label'    => __('Header Background Color', 'vw-hospital-lite'),
        'section'  => 'header_image',
    )));

    $wp_customize->add_setting('vw_hospital_lite_header_img_position',array(
      'default' => 'center top',
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_header_img_position',array(
        'type' => 'select',
        'label' => __('Header Image Position','vw-hospital-lite'),
        'section' => 'header_image',
        'choices'   => array(
            'left top'      => esc_html__( 'Top Left', 'vw-hospital-lite' ),
            'center top'   => esc_html__( 'Top', 'vw-hospital-lite' ),
            'right top'   => esc_html__( 'Top Right', 'vw-hospital-lite' ),
            'left center'   => esc_html__( 'Left', 'vw-hospital-lite' ),
            'center center'   => esc_html__( 'Center', 'vw-hospital-lite' ),
            'right center'   => esc_html__( 'Right', 'vw-hospital-lite' ),
            'left bottom'   => esc_html__( 'Bottom Left', 'vw-hospital-lite' ),
            'center bottom'   => esc_html__( 'Bottom', 'vw-hospital-lite' ),
            'right bottom'   => esc_html__( 'Bottom Right', 'vw-hospital-lite' ),
        ),
    ));

    $wp_customize->add_setting('vw_hospital_lite_topbar_padding_top_bottom',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_topbar_padding_top_bottom',array(
        'label' => __('Topbar Padding Top Bottom','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_top',
        'type'=> 'text'
    ));

    //Sticky Header
    $wp_customize->add_setting( 'vw_hospital_lite_sticky_header',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_sticky_header',array(
        'label' => esc_html__( 'Show / Hide Sticky Header','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_top'
    )));

    $wp_customize->add_setting('vw_hospital_lite_sticky_header_padding',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_sticky_header_padding',array(
        'label' => __('Sticky Header Padding','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_top',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_searchbox',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_searchbox',array(
        'label' => esc_html__( 'Search Box','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_top'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_search_border_radius', array(
        'default'              => "",
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_search_border_radius', array(
        'label'       => esc_html__( 'Search Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_top',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_contact_call', array( 
        'selector' => 'p.calling', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_contact_call', 
    ));

    $wp_customize->add_setting('vw_hospital_lite_cont_phone_icon',array(
        'default'   => 'fas fa-phone',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_cont_phone_icon',array(
        'label' => __('Add Contact Number Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_cont_phone_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_contact_call',array(
        'default'   => '',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_phone_number',
    ));
    
    $wp_customize->add_control('vw_hospital_lite_contact_call',array(
        'label' => __('Contact No ','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_contact_call',
        'type'  => 'text'
    ) );

    $wp_customize->add_setting('vw_hospital_lite_cont_email_icon',array(
        'default'   => 'fas fa-envelope',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_cont_email_icon',array(
        'label' => __('Add Email address Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_cont_email_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_contact_email',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('vw_hospital_lite_contact_email',array(
        'label' => __('Email Address','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_contact_email',
        'type'  => 'text'
    ) );

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_appointment1', array( 
        'selector' => '.top-bar a', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_appointment1', 
    ));

    $wp_customize->add_setting('vw_hospital_lite_appointments_icon',array(
        'default'   => 'fas fa-plus-circle',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_appointments_icon',array(
        'label' => __('Add Appointment Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_appointments_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_appointment1',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control('vw_hospital_lite_appointment1',array(
        'label' => __('Add Appointment text','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_top',
        'type'  => 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_appointment',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('vw_hospital_lite_appointment',array(
        'label' => __('Add Appointment Link','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_appointment',
        'type'  => 'url'
    ));

    $wp_customize->add_setting('vw_hospital_lite_timing_icon',array(
        'default'   => 'fas fa-ambulance',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_timing_icon',array(
        'label' => __('Add Timing Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_timing_icon',
        'type'      => 'icon'
    )));

     $wp_customize->add_setting('vw_hospital_lite_timing1',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control('vw_hospital_lite_timing1',array(
        'label' => __('Add Timing text','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_top',
        'type'  => 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_timing',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('vw_hospital_lite_timing',array(
        'label' => __('Add Timing Url','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_top',
        'setting'   => 'vw_hospital_lite_timing',
        'type'  => 'url'
    ));

    //Menus Settings
    $wp_customize->add_section( 'vw_hospital_lite_menu_section' , array(
        'title' => __( 'Menus Settings', 'vw-hospital-lite' ),
        'priority'   => 30,
        'panel' => 'vw_hospital_lite_homepage_panel'
    ) );

    $wp_customize->add_setting('vw_hospital_lite_navigation_menu_font_size',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_navigation_menu_font_size',array(
        'label' => __('Menus Font Size','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_menu_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_navigation_menu_font_weight',array(
        'default' => 500,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_navigation_menu_font_weight',array(
        'type' => 'select',
        'label' => __('Menus Font Weight','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_menu_section',
        'choices' => array(
            '100' => __('100','vw-hospital-lite'),
            '200' => __('200','vw-hospital-lite'),
            '300' => __('300','vw-hospital-lite'),
            '400' => __('400','vw-hospital-lite'),
            '500' => __('500','vw-hospital-lite'),
            '600' => __('600','vw-hospital-lite'),
            '700' => __('700','vw-hospital-lite'),
            '800' => __('800','vw-hospital-lite'),
            '900' => __('900','vw-hospital-lite'),
        ),
    ) );

    // text trasform
    $wp_customize->add_setting('vw_hospital_lite_menu_text_transform',array(
        'default'=> 'Uppercase',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_menu_text_transform',array(
        'type' => 'radio',
        'label' => __('Menu Text Transform','vw-hospital-lite'),
        'choices' => array(
            'Uppercase' => __('Uppercase','vw-hospital-lite'),
            'Capitalize' => __('Capitalize','vw-hospital-lite'),
            'Lowercase' => __('Lowercase','vw-hospital-lite'),
        ),
        'section'=> 'vw_hospital_lite_menu_section',
    ));

    $wp_customize->add_setting('vw_hospital_lite_menus_item_style',array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_menus_item_style',array(
        'type' => 'select',
        'section' => 'vw_hospital_lite_menu_section',
        'label' => __('Menu Item Hover Style','vw-hospital-lite'),
        'choices' => array(
            'None' => __('None','vw-hospital-lite'),
            'Zoom In' => __('Zoom In','vw-hospital-lite'),
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_header_menus_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_header_menus_color', array(
        'label'    => __('Menus Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_menu_section',
    )));

    $wp_customize->add_setting('vw_hospital_lite_header_menus_hover_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_header_menus_hover_color', array(
        'label'    => __('Menus Hover Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_menu_section',
    )));

    $wp_customize->add_setting('vw_hospital_lite_header_submenus_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_header_submenus_color', array(
        'label'    => __('Sub Menus Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_menu_section',
    )));

    $wp_customize->add_setting('vw_hospital_lite_header_submenus_hover_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_header_submenus_hover_color', array(
        'label'    => __('Sub Menus Hover Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_menu_section',
    )));

    //Social
    $wp_customize->add_section(
        'vw_hospital_lite_social_links', array(
            'title'     =>  __('Social Links', 'vw-hospital-lite'),
            'priority'   => 30,
            'panel'     =>  'vw_hospital_lite_homepage_panel'
        )
    );

    $wp_customize->add_setting('vw_hospital_lite_social_icons',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icons',array(
        'label' =>  __('Steps to setup social icons','vw-hospital-lite'),
        'description' => __('<p>1. Go to Dashboard >> Appearance >> Widgets</p>
            <p>2. Add Vw Social Icon Widget in Social Icon Widget area.</p>
            <p>3. Add social icons url and save.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_social_links',
        'type'=> 'hidden'
    ));
    $wp_customize->add_setting('vw_hospital_lite_social_icon_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icon_btn',array(
        'description' => "<a target='_blank' href='". admin_url('widgets.php') ." '>Setup Social Icons</a>",
        'section'=> 'vw_hospital_lite_social_links',
        'type'=> 'hidden'
    ));

    //home page slider
    $wp_customize->add_section( 'vw_hospital_lite_slidersettings' , array(
      'title'      => __( 'Slider Settings', 'vw-hospital-lite' ),
      'description' => __('Free theme has 3 slides options, For unlimited slides and more options </br> <a class="go-pro-btn" target="blank" href="https://www.vwthemes.com/products/hospital-wordpress-theme">GET PRO</a>','vw-hospital-lite'),
      'priority'   => 30,
      'panel' => 'vw_hospital_lite_homepage_panel'
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_slider_hide_show',
       array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_slider_hide_show',
       array(
      'label' => esc_html__( 'Show / Hide Slider','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_slidersettings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_slider_type',array(
        'default' => 'Default slider',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ) );
    $wp_customize->add_control('vw_hospital_lite_slider_type', array(
        'type' => 'select',
        'label' => __('Slider Type','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_slidersettings',
        'choices' => array(
            'Default slider' => __('Default slider','vw-hospital-lite'),
            'Advance slider' => __('Advance slider','vw-hospital-lite'),
        ),
    ));

    $wp_customize->add_setting('vw_hospital_lite_advance_slider_shortcode',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_advance_slider_shortcode',array(
        'label' => __('Add Slider Shortcode','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'text',
        'active_callback' => 'vw_hospital_lite_advance_slider'
    ));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_slider_hide_show',array(
        'selector'        => '#slider .inner_carousel h1',
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_slider_hide_show',
    ));

    for ( $count = 1; $count <= 3; $count++ ) {
        // Add color scheme setting and control.
        $wp_customize->add_setting( 'vw_hospital_lite_slider_page' . $count, array(
            'default'           => '',
            'sanitize_callback' => 'vw_hospital_lite_sanitize_dropdown_pages'
        ) );
        $wp_customize->add_control( 'vw_hospital_lite_slider_page' . $count, array(
            'label'    => __( 'Select Slide Image Page', 'vw-hospital-lite' ),
            'description' => __('Slider image size (1500 x 765)','vw-hospital-lite'),
            'section'  => 'vw_hospital_lite_slidersettings',
            'type'     => 'dropdown-pages',
            'active_callback' => 'vw_hospital_lite_default_slider'
        ) );
    
    }

    $wp_customize->add_setting('vw_hospital_lite_slider_button_text',array(
        'default'=> 'READ MORE',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_slider_button_text',array(
        'label' => __('Add Slider Button Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'text',
        'active_callback' => 'vw_hospital_lite_default_slider'
    ));

    $wp_customize->add_setting('vw_hospital_lite_slider_button_link',array(
        'default'=> '',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control('vw_hospital_lite_slider_button_link',array(
        'label' => esc_html__('Add Button Link','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => esc_html__( 'www.example-info.com', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'url'
    ));

    //content layout
    $wp_customize->add_setting('vw_hospital_lite_slider_content_option',array(
        'default' => 'Center',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Image_Radio_Control($wp_customize, 'vw_hospital_lite_slider_content_option', array(
        'type' => 'select',
        'label' => __('Slider Content Layouts','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_slidersettings',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/images/slider-content1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/slider-content2.png',
            'Right' => esc_url(get_template_directory_uri()).'/images/slider-content3.png',
    ),
        'active_callback' => 'vw_hospital_lite_default_slider'
    )));

    //Slider content padding
    $wp_customize->add_setting('vw_hospital_lite_slider_content_padding_top_bottom',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_slider_content_padding_top_bottom',array(
        'label' => __('Slider Content Padding Top Bottom','vw-hospital-lite'),
        'description'   => __('Enter a value in %. Example:20%','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '50%', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'text',
        'active_callback' => 'vw_hospital_lite_default_slider'
    ));

    $wp_customize->add_setting('vw_hospital_lite_slider_content_padding_left_right',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_slider_content_padding_left_right',array(
        'label' => __('Slider Content Padding Left Right','vw-hospital-lite'),
        'description'   => __('Enter a value in %. Example:20%','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '50%', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'text',
        'active_callback' => 'vw_hospital_lite_default_slider'
    ));

    //Slider excerpt
    $wp_customize->add_setting( 'vw_hospital_lite_slider_excerpt_number', array(
        'default'              => 30,
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_slider_excerpt_number', array(
        'label'       => esc_html__( 'Slider Excerpt length','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_slidersettings',
        'type'        => 'range',
        'settings'    => 'vw_hospital_lite_slider_excerpt_number',
        'input_attrs' => array(
            'step'             => 5,
            'min'              => 0,
            'max'              => 50,
        ),
        'active_callback' => 'vw_hospital_lite_default_slider'
    ) );

    //Slider height
    $wp_customize->add_setting('vw_hospital_lite_slider_height',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_slider_height',array(
        'label' => __('Slider Height','vw-hospital-lite'),
        'description'   => __('Specify the slider height (px).','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '500px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_slidersettings',
        'type'=> 'text',
        'active_callback' => 'vw_hospital_lite_default_slider'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_slider_speed', array(
        'default'  => 4000,
        'sanitize_callback' => 'vw_hospital_lite_sanitize_float'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_slider_speed', array(
        'label' => esc_html__('Slider Transition Speed','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_slidersettings',
        'type'  => 'number',
        'active_callback' => 'vw_hospital_lite_default_slider'
    ) );

    //Opacity
    $wp_customize->add_setting('vw_hospital_lite_slider_opacity_color',array(
      'default'              => 0.5,
      'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));

    $wp_customize->add_control( 'vw_hospital_lite_slider_opacity_color', array(
        'label'       => esc_html__( 'Slider Image Opacity','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_slidersettings',
        'type'        => 'select',
        'settings'    => 'vw_hospital_lite_slider_opacity_color',
        'choices' => array(
          '0' =>  esc_attr(__('0','vw-hospital-lite')),
          '0.1' =>  esc_attr(__('0.1','vw-hospital-lite')),
          '0.2' =>  esc_attr(__('0.2','vw-hospital-lite')),
          '0.3' =>  esc_attr(__('0.3','vw-hospital-lite')),
          '0.4' =>  esc_attr(__('0.4','vw-hospital-lite')),
          '0.5' =>  esc_attr(__('0.5','vw-hospital-lite')),
          '0.6' =>  esc_attr(__('0.6','vw-hospital-lite')),
          '0.7' =>  esc_attr(__('0.7','vw-hospital-lite')),
          '0.8' =>  esc_attr(__('0.8','vw-hospital-lite')),
          '0.9' =>  esc_attr(__('0.9','vw-hospital-lite'))
        ),
        'active_callback' => 'vw_hospital_lite_default_slider'
    ));
        
    $wp_customize->add_setting( 'vw_hospital_lite_slider_image_overlay',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_slider_image_overlay',array(
        'label' => esc_html__( 'Show / Hide Slider Image Overlay','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_slidersettings',
        'active_callback' => 'vw_hospital_lite_default_slider'
    )));

    $wp_customize->add_setting('vw_hospital_lite_slider_image_overlay_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_slider_image_overlay_color', array(
        'label'    => __('Slider Image Overlay Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_slidersettings',
        'active_callback' => 'vw_hospital_lite_default_slider'
    )));

    //Hospital search Section
    $wp_customize->add_section('vw_hospital_lite_hospitalsearch', array(
        'title'       => __('Hospital Search Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_hospitalsearch_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_hospitalsearch_text',array(
        'description' => __('<p>1. More options for hospital search section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for hospital search section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_hospitalsearch',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_hospitalsearch_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_hospitalsearch_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_hospitalsearch',
        'type'=> 'hidden'
    ));

    //about Section
    $wp_customize->add_section('vw_hospital_lite_about', array(
        'title'       => __('About Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_about_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_about_text',array(
        'description' => __('<p>1. More options for about section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for about section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_about',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_about_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_about_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_about',
        'type'=> 'hidden'
    ));

    //OUR services
    $wp_customize->add_section('vw_hospital_lite_our_services',array(
        'title' => __('Our Services','vw-hospital-lite'),
        'description' => __('For more options of the Our Services section </br> <a class="go-pro-btn" target="blank" href="https://www.vwthemes.com/products/hospital-wordpress-theme">GET PRO</a>','vw-hospital-lite'),
        'panel' => 'vw_hospital_lite_homepage_panel',
    )); 

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial( 'vw_hospital_lite_sec1_title', array( 
        'selector' => '.services h2', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_sec1_title',
    ));

    $wp_customize->add_setting('vw_hospital_lite_sec1_title',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    
    $wp_customize->add_control('vw_hospital_lite_sec1_title',array(
        'label' => __('Section Title','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_our_services',
        'setting'=> 'vw_hospital_lite_sec1_title',
        'type'=> 'text'
    )); 

    for ( $count = 0; $count <= 3; $count++ ) {
        $wp_customize->add_setting( 'vw_hospital_lite_servicesettings' . $count, array(
            'default'           => '',
            'sanitize_callback' => 'vw_hospital_lite_sanitize_dropdown_pages'
        ));
        $wp_customize->add_control( 'vw_hospital_lite_servicesettings' . $count, array(
            'label'    => __( 'Select Service Page', 'vw-hospital-lite' ),
            'section'  => 'vw_hospital_lite_our_services',
            'type'     => 'dropdown-pages'
        ));
    }

    $wp_customize->add_setting( 'vw_hospital_lite_service_excerpt_number', array(
        'default'              => 30,
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_service_excerpt_number', array(
        'label'       => esc_html__( 'Services Excerpt length','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_our_services',
        'type'        => 'range',
        'settings'    => 'vw_hospital_lite_service_excerpt_number',
        'input_attrs' => array(
            'step'             => 5,
            'min'              => 0,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_services_button_text',array(
        'default'=> 'READ MORE',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_services_button_text',array(
        'label' => __('Add Services Button Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_our_services',
        'type'=> 'text'
    ));

    //timing Section
    $wp_customize->add_section('vw_hospital_lite_timing', array(
        'title'       => __('Timing Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_timing_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_timing_text',array(
        'description' => __('<p>1. More options for timing section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for timing section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_timing',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_timing_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_timing_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_timing',
        'type'=> 'hidden'
    ));

    //Products Section
    $wp_customize->add_section('vw_hospital_lite_products', array(
        'title'       => __('Products Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_products_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_text',array(
        'description' => __('<p>1. More options for products section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for products section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_products',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_products_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_products',
        'type'=> 'hidden'
    ));

    //gallery Section
    $wp_customize->add_section('vw_hospital_lite_gallery_box', array(
        'title'       => __('Gallery Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_gallery_box_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_gallery_box_text',array(
        'description' => __('<p>1. More options for gallery_box section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for gallery_box section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_gallery_box',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_gallery_box_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_gallery_box_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_gallery_box',
        'type'=> 'hidden'
    ));

    //testimonial Section
    $wp_customize->add_section('vw_hospital_lite_testimonial', array(
        'title'       => __('Testimonial Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_testimonial_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_testimonial_text',array(
        'description' => __('<p>1. More options for testimonial section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for testimonial section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_testimonial',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_testimonial_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_testimonial_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_testimonial',
        'type'=> 'hidden'
    ));

    //hospital facility Section
    $wp_customize->add_section('vw_hospital_lite_hospital_facility', array(
        'title'       => __('Hospital Facility Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_hospital_facility_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_hospital facility_text',array(
        'description' => __('<p>1. More options for hospital facility section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for hospital_facility section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_hospital_facility',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_hospital_facility_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_hospital_facility_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_hospital_facility',
        'type'=> 'hidden'
    ));

    //post Section
    $wp_customize->add_section('vw_hospital_lite_post', array(
        'title'       => __('Post Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_post_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_post_text',array(
        'description' => __('<p>1. More options for post section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for post section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_post',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_post_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_post_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_post',
        'type'=> 'hidden'
    ));

    //tips depart Section
    $wp_customize->add_section('vw_hospital_lite_tips_depart', array(
        'title'       => __('Tips Depart Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_tips_depart_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_tips_depart_text',array(
        'description' => __('<p>1. More options for tips depart section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for tips depart section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_tips_depart',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_tips_depart_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_tips_depart_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_tips_depart',
        'type'=> 'hidden'
    ));

    //about hospital Section
    $wp_customize->add_section('vw_hospital_lite_about_hospital', array(
        'title'       => __('About Hospital Section', 'vw-hospital-lite'),
        'description' => __('<p class="premium-opt">Premium Theme Features</p>','vw-hospital-lite'),
        'priority'    => null,
        'panel'       => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_about_hospital_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_about_hospital_text',array(
        'description' => __('<p>1. More options for about hospital section.</p>
            <p>2. Unlimited images options.</p>
            <p>3. Color options for about hospital section.</p>','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_about_hospital',
        'type'=> 'hidden'
    ));

    $wp_customize->add_setting('vw_hospital_lite_about_hospital_btn',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_about_hospital_btn',array(
        'description' => "<a class='go-pro' target='_blank' href=".esc_url(VW_HOSPITAL_LITE_BUY_NOW).">More Info</a>",
        'section'=> 'vw_hospital_lite_about_hospital',
        'type'=> 'hidden'
    ));

    // Footer
    $wp_customize->add_section('vw_hospital_lite_footer_section',array(
        'title' => __('Copyright','vw-hospital-lite'),
        'description' => __('For more options of the footer section </br> <a class="go-pro-btn" target="blank" href="https://www.vwthemes.com/products/hospital-wordpress-theme">GET PRO</a>','vw-hospital-lite'),
        'priority'  => null,
        'panel' => 'vw_hospital_lite_homepage_panel',
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_footer_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new vw_hospital_lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_footer_hide_show',array(
      'label' => esc_html__( 'Show / Hide Footer','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_footer_section'
    )));

     // font size
    $wp_customize->add_setting('vw_hospital_lite_button_footer_font_size',array(
        'default'=> 25,
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_button_footer_font_size',array(
        'label' => __('Footer Heading Font Size','vw-hospital-lite'),
        'type'        => 'number',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
        'section'=> 'vw_hospital_lite_footer_section',
    ));

    $wp_customize->add_setting('vw_hospital_lite_button_footer_heading_letter_spacing',array(
        'default'=> 1,
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_button_footer_heading_letter_spacing',array(
        'label' => __('Heading Letter Spacing','vw-hospital-lite'),
        'type'        => 'number',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
    ),
        'section'=> 'vw_hospital_lite_footer_section',
    ));

    // text trasform
    $wp_customize->add_setting('vw_hospital_lite_button_footer_text_transform',array(
        'default'=> 'Capitalize',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_button_footer_text_transform',array(
        'type' => 'radio',
        'label' => __('Heading Text Transform','vw-hospital-lite'),
        'choices' => array(
      'Uppercase' => __('Uppercase','vw-hospital-lite'),
      'Capitalize' => __('Capitalize','vw-hospital-lite'),
      'Lowercase' => __('Lowercase','vw-hospital-lite'),
    ),
        'section'=> 'vw_hospital_lite_footer_section',
    ));

    $wp_customize->add_setting('vw_hospital_lite_footer_heading_weight',array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_heading_weight',array(
        'type' => 'select',
        'label' => __('Heading Font Weight','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'choices' => array(
            '100' => __('100','vw-hospital-lite'),
            '200' => __('200','vw-hospital-lite'),
            '300' => __('300','vw-hospital-lite'),
            '400' => __('400','vw-hospital-lite'),
            '500' => __('500','vw-hospital-lite'),
            '600' => __('600','vw-hospital-lite'),
            '700' => __('700','vw-hospital-lite'),
            '800' => __('800','vw-hospital-lite'),
            '900' => __('900','vw-hospital-lite'),
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_footer_template',array(
        'default'   => esc_html('vw-hospital-lite-footer-one'),
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_template',array(
        'label' => esc_html__('Footer style','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_footer_section',
        'setting'   => 'vw_hospital_lite_footer_template',
        'type' => 'select',
        'choices' => array(
            'vw-hospital-lite-footer-one' => esc_html__('Style 1', 'vw-hospital-lite'),
            'vw-hospital-lite-footer-two' => esc_html__('Style 2', 'vw-hospital-lite'),
            'vw-hospital-lite-footer-three' => esc_html__('Style 3', 'vw-hospital-lite'),
            'vw-hospital-lite-footer-four' => esc_html__('Style 4', 'vw-hospital-lite'),
            'vw-hospital-lite-footer-five' => esc_html__('Style 5', 'vw-hospital-lite'),
        )
    ));

    $wp_customize->add_setting('vw_hospital_lite_footer_background_color', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_footer_background_color', array(
        'label'    => __('Footer Background Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_footer_section',
    )));

    $wp_customize->add_setting('vw_hospital_lite_footer_background_image',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'vw_hospital_lite_footer_background_image',array(
        'label' => __('Footer Background Image','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section'
    )));

    $wp_customize->add_setting('vw_hospital_lite_footer_img_position',array(
      'default' => 'center center',
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_img_position',array(
        'type' => 'select',
        'label' => __('Footer Image Position','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'choices'   => array(
            'left top'      => esc_html__( 'Top Left', 'vw-hospital-lite' ),
            'center top'   => esc_html__( 'Top', 'vw-hospital-lite' ),
            'right top'   => esc_html__( 'Top Right', 'vw-hospital-lite' ),
            'left center'   => esc_html__( 'Left', 'vw-hospital-lite' ),
            'center center'   => esc_html__( 'Center', 'vw-hospital-lite' ),
            'right center'   => esc_html__( 'Right', 'vw-hospital-lite' ),
            'left bottom'   => esc_html__( 'Bottom Left', 'vw-hospital-lite' ),
            'center bottom'   => esc_html__( 'Bottom', 'vw-hospital-lite' ),
            'right bottom'   => esc_html__( 'Bottom Right', 'vw-hospital-lite' ),
        ), 
    ));
    
    // Footer
    $wp_customize->add_setting('vw_hospital_lite_img_footer',array(
        'default'=> 'scroll',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_img_footer',array(
        'type' => 'select',
        'label' => __('Footer Background Attatchment','vw-hospital-lite'),
        'choices' => array(
            'fixed' => __('fixed','vw-hospital-lite'),
            'scroll' => __('scroll','vw-hospital-lite'),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
    ));

    // footer padding
    $wp_customize->add_setting('vw_hospital_lite_footer_padding',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_padding',array(
        'label' => __('Footer Top Bottom Padding','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
      'placeholder' => __( '10px', 'vw-hospital-lite' ),
    ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_footer_widgets_heading',array(
        'default' => 'Left',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_widgets_heading',array(
        'type' => 'select',
        'label' => __('Footer Widget Heading','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'choices' => array(
            'Left' => __('Left','vw-hospital-lite'),
            'Center' => __('Center','vw-hospital-lite'),
            'Right' => __('Right','vw-hospital-lite')
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_footer_widgets_content',array(
        'default' => 'Left',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_widgets_content',array(
        'type' => 'select',
        'label' => __('Footer Widget Content','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'choices' => array(
            'Left' => __('Left','vw-hospital-lite'),
            'Center' => __('Center','vw-hospital-lite'),
            'Right' => __('Right','vw-hospital-lite')
        ),
    ) );

    // footer social icon
    $wp_customize->add_setting( 'vw_hospital_lite_footer_icon',array(
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_footer_icon',array(
        'label' => esc_html__( 'Show / Hide Footer Social Icon','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_footer_section'
    )));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_footer_copy', array( 
        'selector' => '.copyright p', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_footer_copy', 
    ));
    
    $wp_customize->add_setting( 'vw_hospital_lite_copyright_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new vw_hospital_lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_copyright_hide_show',array(
      'label' => esc_html__( 'Show / Hide Copyright','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_footer_section'
    )));

    $wp_customize->add_setting('vw_hospital_lite_copyright_background_color', array(
        'default'           => '#3ca6d4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_copyright_background_color', array(
        'label'    => __('Copyright Background Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_footer_section',
    )));
    
    $wp_customize->add_setting('vw_hospital_lite_footer_copy',array(
        'default'   => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vw_hospital_lite_footer_copy',array(
        'label' => __('Copyright Text','vw-hospital-lite'),
        'section'   => 'vw_hospital_lite_footer_section',
        'type'      => 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_copyright_font_size',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_copyright_font_size',array(
        'label' => __('Copyright Font Size','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_copyright_alingment',array(
        'default' => 'center',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Image_Radio_Control($wp_customize, 'vw_hospital_lite_copyright_alingment', array(
        'type' => 'select',
        'label' => __('Copyright Alignment','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'settings' => 'vw_hospital_lite_copyright_alingment',
        'choices' => array(
            'left' => esc_url(get_template_directory_uri()).'/images/copyright1.png',
            'center' => esc_url(get_template_directory_uri()).'/images/copyright2.png',
            'right' => esc_url(get_template_directory_uri()).'/images/copyright3.png'
    ))));

    $wp_customize->add_setting('vw_hospital_lite_copyright_padding_top_bottom',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_copyright_padding_top_bottom',array(
        'label' => __('Copyright Padding Top Bottom','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_hide_show_scroll',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_hide_show_scroll',array(
        'label' => esc_html__( 'Show / Hide Scroll To Top','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_footer_section'
    )));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_scroll_top_icon', array( 
        'selector' => '.scrollup i', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_scroll_top_icon', 
    ));

    $wp_customize->add_setting('vw_hospital_lite_scroll_top_icon',array(
        'default'   => 'fas fa-long-arrow-alt-up',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_scroll_top_icon',array(
        'label' => __('Add Scroll to Top Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_footer_section',
        'setting'   => 'vw_hospital_lite_scroll_top_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_scroll_to_top_font_size',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_scroll_to_top_font_size',array(
        'label' => __('Icon Font Size','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_scroll_to_top_padding',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_scroll_to_top_padding',array(
        'label' => __('Icon Top Bottom Padding','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_scroll_to_top_width',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_scroll_to_top_width',array(
        'label' => __('Icon Width','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_scroll_to_top_height',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_scroll_to_top_height',array(
        'label' => __('Icon Height','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_footer_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_scroll_to_top_border_radius', array(
        'default'              => '',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_scroll_to_top_border_radius', array(
        'label'       => esc_html__( 'Icon Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_footer_section',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_scroll_top_alignment',array(
        'default' => 'Right',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Image_Radio_Control($wp_customize, 'vw_hospital_lite_scroll_top_alignment', array(
        'type' => 'select',
        'label' => __('Scroll To Top','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'settings' => 'vw_hospital_lite_scroll_top_alignment',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/images/layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/layout2.png',
            'Right' => esc_url(get_template_directory_uri()).'/images/layout3.png'
    ))));

    $wp_customize->add_setting('vw_hospital_lite_align_footer_social_icon',array(
        'default' => 'center',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_align_footer_social_icon',array(
        'type' => 'select',
        'label' => __('Social Icon Alignment ','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_footer_section',
        'choices' => array(
            'left' => __('Left','vw-hospital-lite'),
            'right' => __('Right','vw-hospital-lite'),
            'center' => __('Center','vw-hospital-lite'),
        ),
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_copyright_sticky',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_copyright_sticky',array(
      'label' => esc_html__( 'Show / Hide Sticky Copyright','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_footer_section'
    )));

   $wp_customize->add_setting('vw_hospital_lite_footer_social_icons_font_size',array(
       'default'=> 16,
       'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_footer_social_icons_font_size',array(
    'label' => __('Social Icon Font Size','vw-hospital-lite'),
        'type'        => 'number',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
        'section'=> 'vw_hospital_lite_footer_section',
     ));

    //Blog Post
    $BlogPostParentPanel = new VW_Hospital_Lite_WP_Customize_Panel( $wp_customize, 'blog_post_parent_panel', array(
        'title' => __( 'Blog Post Settings', 'vw-hospital-lite' ),
        'panel' => 'vw_hospital_lite_panel_id',
    ));

    $wp_customize->add_panel( $BlogPostParentPanel );

    // Add example section and controls to the middle (second) panel
    $wp_customize->add_section( 'vw_hospital_lite_post_settings', array(
        'title' => __( 'Post Settings', 'vw-hospital-lite' ),
        'panel' => 'blog_post_parent_panel',
    ));

    //Blog layout
    $wp_customize->add_setting('vw_hospital_lite_blog_layout_option',array(
        'default' => 'Default',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Image_Radio_Control($wp_customize, 'vw_hospital_lite_blog_layout_option', array(
        'type' => 'select',
        'label' => __('Blog Layouts','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_post_settings',
        'choices' => array(
            'Default' => esc_url(get_template_directory_uri()).'/images/blog-layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/images/blog-layout2.png',
            'Left' => esc_url(get_template_directory_uri()).'/images/blog-layout3.png',
    ))));

    // Add Settings and Controls for Layout
    $wp_customize->add_setting('vw_hospital_lite_theme_options',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'          
    ) );
    $wp_customize->add_control('vw_hospital_lite_theme_options', array(
        'type' => 'select',
        'label' => __('Post Sidebar Layout','vw-hospital-lite'),
        'description' => __('Here you can change the sidebar layout for posts. ','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_post_settings',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hospital-lite'),
            'Right Sidebar' => __('Right Sidebar','vw-hospital-lite'),
            'One Column' => __('One Column','vw-hospital-lite'),
            'Three Columns' => __('Three Columns','vw-hospital-lite'),
            'Four Columns' => __('Four Columns','vw-hospital-lite'),
            'Grid Layout' => __('Grid Layout','vw-hospital-lite')
        ),
    ));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_toggle_postdate', array( 
        'selector' => '.services-box h2 a', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_toggle_postdate', 
    ));

    $wp_customize->add_setting('vw_hospital_lite_toggle_postdate_icon',array(
        'default'   => 'fas fa-calendar-alt',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_toggle_postdate_icon',array(
        'label' => __('Add Post Date Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_post_settings',
        'setting'   => 'vw_hospital_lite_toggle_postdate_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_toggle_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_toggle_postdate',array(
        'label' => esc_html__( 'Show / Hide Post Date','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_toggle_author_icon',array(
        'default'   => 'fas fa-user',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_toggle_author_icon',array(
        'label' => __('Add Author Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_post_settings',
        'setting'   => 'vw_hospital_lite_toggle_author_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_toggle_author',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_toggle_author',array(
        'label' => esc_html__( 'Show / Hide Author','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_toggle_comments_icon',array(
        'default'   => 'fa fa-comments',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_toggle_comments_icon',array(
        'label' => __('Add Comments Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_post_settings',
        'setting'   => 'vw_hospital_lite_toggle_comments_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_toggle_comments',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_toggle_comments',array(
        'label' => esc_html__( 'Show / Hide Comments','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_toggle_time_icon',array(
        'default'   => 'fas fa-clock',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_toggle_time_icon',array(
        'label' => __('Add Time Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_post_settings',
        'setting'   => 'vw_hospital_lite_toggle_time_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_toggle_time',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_toggle_time',array(
        'label' => esc_html__( 'Show / Hide Time','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_category_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_category_hide_show',
       array(
      'label' => esc_html__( 'Show / Hide Category','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_featured_image_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_featured_image_hide_show', array(
        'label' => esc_html__( 'Show / Hide Featured Image','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_post_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_featured_image_border_radius', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_featured_image_border_radius', array(
        'label'       => esc_html__( 'Featured Image Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_post_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_featured_image_box_shadow', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_featured_image_box_shadow', array(
        'label'       => esc_html__( 'Featured Image Box Shadow','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_post_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    //Featured Image
    $wp_customize->add_setting('vw_hospital_lite_blog_post_featured_image_dimension',array(
           'default' => 'default',
           'sanitize_callback'  => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_blog_post_featured_image_dimension',array(
         'type' => 'select',
         'label'    => __('Blog Post Featured Image Dimension','vw-hospital-lite'),
         'section'  => 'vw_hospital_lite_post_settings',
         'choices' => array(
          'default' => __('Default','vw-hospital-lite'),
          'custom' => __('Custom Image Size','vw-hospital-lite'),
      ),
    ));

    $wp_customize->add_setting('vw_hospital_lite_blog_post_featured_image_custom_width',array(
            'default'=> '',
            'sanitize_callback' => 'sanitize_text_field'
        ));
    $wp_customize->add_control('vw_hospital_lite_blog_post_featured_image_custom_width',array(
            'label' => __('Featured Image Custom Width','vw-hospital-lite'),
            'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
            'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),),
            'section'=> 'vw_hospital_lite_post_settings',
            'type'=> 'text',
            'active_callback' => 'vw_hospital_lite_blog_post_featured_image_dimension'
        ));

    $wp_customize->add_setting('vw_hospital_lite_blog_post_featured_image_custom_height',array(
            'default'=> '',
            'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_blog_post_featured_image_custom_height',array(
            'label' => __('Featured Image Custom Height','vw-hospital-lite'),
            'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
            'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),),
            'section'=> 'vw_hospital_lite_post_settings',
            'type'=> 'text',
            'active_callback' => 'vw_hospital_lite_blog_post_featured_image_dimension'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_excerpt_number', array(
        'default'              => 30,
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_excerpt_number', array(
        'label'       => esc_html__( 'Excerpt length','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_post_settings',
        'type'        => 'range',
        'settings'    => 'vw_hospital_lite_excerpt_number',
        'input_attrs' => array(
            'step'             => 5,
            'min'              => 0,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_meta_field_separator',array(
        'default'=> '|',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_meta_field_separator',array(
        'label' => __('Add Meta Separator','vw-hospital-lite'),
        'description' => __('Add the seperator for meta box. Example: "|", "/", etc.','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_post_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_blog_page_posts_settings',array(
        'default' => 'Into Blocks',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_blog_page_posts_settings',array(
        'type' => 'select',
        'label' => __('Display Blog Posts','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_post_settings',
        'choices' => array(
            'Into Blocks' => __('Into Blocks','vw-hospital-lite'),
            'Without Blocks' => __('Without Blocks','vw-hospital-lite')
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_excerpt_settings',array(
        'default' => 'Excerpt',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_excerpt_settings',array(
        'type' => 'select',
        'label' => __('Post Content','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_post_settings',
        'choices' => array(
            'Content' => __('Content','vw-hospital-lite'),
            'Excerpt' => __('Excerpt','vw-hospital-lite'),
            'No Content' => __('No Content','vw-hospital-lite')
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_excerpt_suffix',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_excerpt_suffix',array(
        'label' => __('Add Excerpt Suffix','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '[...]', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_post_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_blog_pagination_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_blog_pagination_hide_show',array(
      'label' => esc_html__( 'Show / Hide Blog Pagination','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_post_settings'
    )));

    // Web Frame // 
	
	$wp_customize->add_setting( 'vw_hospital_lite_web_frame',array(
		'default' => 0,
		'transport' => 'refresh',
		'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new Vw_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_web_frame',array(
		'label' => esc_html__( 'Show / Hide Blog Page Border','vw-hospital-lite' ),
		'section' => 'vw_hospital_lite_post_settings'
    )));

	$wp_customize->add_setting('vw_hospital_lite_web_frame_border_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_web_frame_border_color', array(
		'label'    => __('Blog Page Border Color', 'vw-hospital-lite'),
		'section'  => 'vw_hospital_lite_post_settings',
	)));

	$wp_customize->add_setting('vw_hospital_lite_web_frame_border_width',array(
		'default'=> '',
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control('vw_hospital_lite_web_frame_border_width',array(
		'label' => __('Blog Page Width','vw-hospital-lite'),
		'description' => __('Enter a value in pixels Example:20px','vw-hospital-lite'),
		'input_attrs' => array(
		'placeholder' => __( '10px', 'vw-hospital-lite' ),
	),
		'section'=> 'vw_hospital_lite_post_settings',
		'type'=> 'text'
   ));

    $wp_customize->add_setting( 'vw_hospital_lite_blog_pagination_type', array(
        'default'           => 'blog-page-numbers',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control( 'vw_hospital_lite_blog_pagination_type', array(
        'section' => 'vw_hospital_lite_post_settings',
        'type' => 'select',
        'label' => __( 'Blog Pagination', 'vw-hospital-lite' ),
        'choices'       => array(
            'blog-page-numbers'  => __( 'Numeric', 'vw-hospital-lite' ),
            'next-prev' => __( 'Older Posts/Newer Posts', 'vw-hospital-lite' ),
    )));
    $wp_customize->add_setting('vw_hospital_lite_show_first_caps', array(
	    'default'           => false,
	    'transport'         => 'refresh',
	    'sanitize_callback' => 'vw_hospital_lite_switch_sanitization',
	));

	$wp_customize->add_control(new Vw_Hospital_Lite_Toggle_Switch_Custom_Control(
	    $wp_customize,
	    'vw_hospital_lite_show_first_caps',
	    array(
	        'label'   => esc_html__('First Cap (First Capital Letter)', 'vw-hospital-lite'),
	        'section' => 'vw_hospital_lite_post_settings',
	    )
	));

    // Related Post Settings
    $wp_customize->add_section( 'vw_hospital_lite_related_posts_settings', array(
        'title' => __( 'Related Posts Settings', 'vw-hospital-lite' ),
        'panel' => 'blog_post_parent_panel',
    ));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('vw_hospital_lite_related_post_title', array( 
        'selector' => '.related-post h3', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_related_post_title', 
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_related_post',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_related_post',array(
        'label' => esc_html__( 'Show / Hide Related Post','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_post_title',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_related_post_title',array(
        'label' => __('Add Related Post Title','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'Related Post', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_related_posts_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_related_posts_count',array(
        'default'=> 3,
        'sanitize_callback' => 'vw_hospital_lite_sanitize_float'
    ));
    $wp_customize->add_control('vw_hospital_lite_related_posts_count',array(
        'label' => __('Add Related Post Count','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '3', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_related_posts_settings',
        'type'=> 'number'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_related_posts_excerpt_number', array(
        'default'              => 20,
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_related_posts_excerpt_number', array(
        'label'       => esc_html__( 'Related Posts Excerpt length','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_related_posts_settings',
        'type'        => 'range',
        'settings'    => 'vw_hospital_lite_related_posts_excerpt_number',
        'input_attrs' => array(
            'step'             => 5,
            'min'              => 0,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_related_toggle_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_related_toggle_postdate',array(
        'label' => esc_html__( 'Show / Hide Post Date','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_postdate_icon',array(
        'default' => 'fas fa-calendar-alt',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
    $wp_customize,'vw_hospital_lite_related_postdate_icon',array(
        'label' => __('Add Post Date Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section' => 'vw_hospital_lite_related_posts_settings',
        'setting' => 'vw_hospital_lite_related_postdate_icon',
        'type'    => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_related_toggle_author',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_related_toggle_author',array(
        'label' => esc_html__( 'Show / Hide Author','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_author_icon',array(
        'default' => 'fas fa-user',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
    $wp_customize,'vw_hospital_lite_related_author_icon',array(
        'label' => __('Add Author Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section' => 'vw_hospital_lite_related_posts_settings',
        'setting' => 'vw_hospital_lite_related_author_icon',
        'type'    => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_related_toggle_comments',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_related_toggle_comments',array(
        'label' => esc_html__( 'Show / Hide Comments','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_comments_icon',array(
        'default' => 'fa fa-comments',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
    $wp_customize,'vw_hospital_lite_related_comments_icon',array(
        'label' => __('Add Comments Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section' => 'vw_hospital_lite_related_posts_settings',
        'setting' => 'vw_hospital_lite_related_comments_icon',
        'type'    => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_related_toggle_time',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_related_toggle_time',array(
        'label' => esc_html__( 'Show / Hide Time','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_time_icon',array(
        'default' => 'fas fa-clock',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
    $wp_customize,'vw_hospital_lite_related_time_icon',array(
        'label' => __('Add Time Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section' => 'vw_hospital_lite_related_posts_settings',
        'setting' => 'vw_hospital_lite_related_time_icon',
        'type'    => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_related_post_meta_field_separator',array(
        'default'=> '|',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_related_post_meta_field_separator',array(
        'label' => __('Add Meta Separator','vw-hospital-lite'),
        'description' => __('Add the seperator for meta box. Example: "|", "/", etc.','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_related_posts_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_related_image_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_related_image_hide_show', array(
        'label' => esc_html__( 'Show / Hide Featured Image','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_related_posts_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_related_image_box_shadow', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_related_image_box_shadow', array(
        'label'       => esc_html__( 'Related post Image Box Shadow','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_related_posts_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    // Single Posts Settings
    $wp_customize->add_section( 'vw_hospital_lite_single_blog_settings', array(
        'title' => __( 'Single Post Settings', 'vw-hospital-lite' ),
        'panel' => 'blog_post_parent_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_single_postdate_icon',array(
        'default'   => 'fas fa-calendar-alt',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_single_postdate_icon',array(
        'label' => __('Add Post Date Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_single_blog_settings',
        'setting'   => 'vw_hospital_lite_single_postdate_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_single_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_postdate',array(
        'label' => esc_html__( 'Show / Hide Date','vw-hospital-lite' ),
       'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_single_author_icon',array(
        'default'   => 'far fa-user',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_single_author_icon',array(
        'label' => __('Add Author Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_single_blog_settings',
        'setting'   => 'vw_hospital_lite_single_author_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_single_author',array(
    'default' => 1,
    'transport' => 'refresh',
    'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_author',array(
        'label' => esc_html__( 'Show / Hide Author','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_single_comments_icon',array(
        'default'   => 'fas fa-comments',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_single_comments_icon',array(
        'label' => __('Add Comments Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_single_blog_settings',
        'setting'   => 'vw_hospital_lite_single_comments_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_single_comments',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_comments',array(
        'label' => esc_html__( 'Show / Hide Comments','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_single_time_icon',array(
        'default'   => 'fas fa-clock',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_single_time_icon',array(
        'label' => __('Add Time Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_single_blog_settings',
        'setting'   => 'vw_hospital_lite_single_time_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_single_time',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_time',array(
        'label' => esc_html__( 'Show / Hide Time','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_toggle_tags',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_toggle_tags', array(
        'label' => esc_html__( 'Show / Hide Tags','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_single_post_breadcrumb',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_post_breadcrumb',array(
        'label' => esc_html__( 'Show / Hide Breadcrumb','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    // Single Posts Category
    $wp_customize->add_setting( 'vw_hospital_lite_single_post_category',array(
        'default' => true,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_post_category',array(
        'label' => esc_html__( 'Show / Hide Category','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));
    $wp_customize->add_setting('vw_hospital_lite_single_post_styling',array(
    'default' => 'Button',
    'transport' => 'refresh',
    'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
	));
	$wp_customize->add_control('vw_hospital_lite_single_post_styling',array(
	'description' => __('Change the styling of Category .','vw-hospital-lite'),
	'type' => 'select',
    'section' => 'vw_hospital_lite_single_blog_settings',
    'choices' => array(
        'Button' => __('Button','vw-hospital-lite'),
		'Underline' => __('Underline','vw-hospital-lite'),
		'Default' => __('Default','vw-hospital-lite'),
      ),
	) );

    $wp_customize->add_setting( 'vw_hospital_lite_single_blog_post_navigation_show_hide',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_blog_post_navigation_show_hide', array(
        'label' => esc_html__( 'Show / Hide Post Navigation','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_single_blog_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_singlepost_image_box_shadow', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_singlepost_image_box_shadow', array(
        'label'       => esc_html__( 'Single post Image Box Shadow','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_single_blog_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_single_post_meta_field_separator',array(
        'default'=> '|',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_single_post_meta_field_separator',array(
        'label' => __('Add Meta Separator','vw-hospital-lite'),
        'description' => __('Add the seperator for meta box. Example: "|", "/", etc.','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_single_blog_settings',
        'type'=> 'text'
    ));

    //navigation text
    $wp_customize->add_setting('vw_hospital_lite_single_blog_prev_navigation_text',array(
        'default'=> 'PREVIOUS PAGE',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_single_blog_prev_navigation_text',array(
        'label' => __('Post Navigation Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'PREVIOUS PAGE', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_single_blog_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_single_blog_next_navigation_text',array(
        'default'=> 'NEXT PAGE',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_single_blog_next_navigation_text',array(
        'label' => __('Post Navigation Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'NEXT PAGE', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_single_blog_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_single_blog_comment_width',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_single_blog_comment_width',array(
        'label' => __('Comment Form Width','vw-hospital-lite'),
        'description'   => __('Enter a value in %. Example:50%','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '100%', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_single_blog_settings',
        'type'=> 'text'
    ));

    // Grid layout setting
    $wp_customize->add_section( 'vw_hospital_lite_grid_layout_settings', array(
        'title' => __( 'Grid Layout Settings', 'vw-hospital-lite' ),
        'panel' => 'blog_post_parent_panel',
    ));

    $wp_customize->add_setting('vw_hospital_lite_grid_postdate_icon',array(
        'default'   => 'fas fa-calendar-alt',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_grid_postdate_icon',array(
        'label' => __('Add Post Date Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_grid_layout_settings',
        'setting'   => 'vw_hospital_lite_grid_postdate_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_grid_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_grid_postdate',array(
        'label' => esc_html__( 'Show / Hide Post Date','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_grid_layout_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_grid_author_icon',array(
        'default'   => 'far fa-user',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_grid_author_icon',array(
        'label' => __('Add Author Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_grid_layout_settings',
        'setting'   => 'vw_hospital_lite_grid_author_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_grid_author',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_grid_author',array(
        'label' => esc_html__( 'Show / Hide Author','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_grid_layout_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_grid_comments_icon',array(
        'default'   => 'fas fa-comments',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control(new vw_hospital_lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_grid_comments_icon',array(
        'label' => __('Add Comments Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_grid_layout_settings',
        'setting'   => 'vw_hospital_lite_grid_comments_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_grid_comments',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_grid_comments',array(
        'label' => esc_html__( 'Show / Hide Comments','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_grid_layout_settings'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_grid_image_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_grid_image_hide_show', array(
        'label' => esc_html__( 'Show / Hide Featured Image','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_grid_layout_settings'
    )));

    $wp_customize->add_setting('vw_hospital_lite_grid_post_meta_field_separator',array(
        'default'=> '|',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_grid_post_meta_field_separator',array(
        'label' => __('Add Meta Separator','vw-hospital-lite'),
        'description' => __('Add the seperator for meta box. Example: "|", "/", etc.','vw-hospital-lite'),
        'section'=> 'vw_hospital_lite_grid_layout_settings',
        'type'=> 'text'
    )); 

    $wp_customize->add_setting('vw_hospital_lite_display_grid_posts_settings',array(
    'default' => 'Into Blocks',
    'transport' => 'refresh',
    'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_display_grid_posts_settings',array(
    'type' => 'select',
    'label' => __('Display Grid Posts','vw-hospital-lite'),
    'section' => 'vw_hospital_lite_grid_layout_settings',
    'choices' => array(
        'Into Blocks' => __('Into Blocks','vw-hospital-lite'),
      'Without Blocks' => __('Without Blocks','vw-hospital-lite')
      ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_grid_excerpt_suffix',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_grid_excerpt_suffix',array(
        'label' => __('Add Excerpt Suffix','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '[...]', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_grid_layout_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_grid_excerpt_settings',array(
        'default' => 'Excerpt',
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_grid_excerpt_settings',array(
        'type' => 'select',
        'label' => esc_html__('Grid Post Content','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_grid_layout_settings',
        'choices' => array(
            'Content' => esc_html__('Content','vw-hospital-lite'),
            'Excerpt' => esc_html__('Excerpt','vw-hospital-lite'),
            'No Content' => esc_html__('No Content','vw-hospital-lite')
        ),
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_grid_featured_image_border_radius', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_grid_featured_image_border_radius', array(
        'label'       => esc_html__( 'Grid Featured Image Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_grid_layout_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting( 'vw_hospital_lite_grid_featured_image_box_shadow', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_grid_featured_image_box_shadow', array(
        'label'       => esc_html__( 'Grid Featured Image Box Shadow','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_grid_layout_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

   // other settings
    $OtherParentPanel = new VW_Hospital_Lite_WP_Customize_Panel( $wp_customize, 'vw_hospital_lite_other_panel_id', array(
        'title' => __( 'Others Settings', 'vw-hospital-lite' ),
        'panel' => 'vw_hospital_lite_panel_id',
    ));

    $wp_customize->add_panel( $OtherParentPanel );

	//Layouts
	$wp_customize->add_section( 'vw_hospital_lite_left_right', array(
    	'title'      => esc_html__( 'General Settings', 'vw-hospital-lite' ),
		'priority'   => 30,
		'panel' => 'vw_hospital_lite_other_panel_id'
	) );

	$wp_customize->add_setting('vw_hospital_lite_width_option',array(
        'default' => 'Full Width',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
	));
	$wp_customize->add_control(new VW_Hospital_Lite_Image_Radio_Control($wp_customize, 'vw_hospital_lite_width_option', array(
        'type' => 'select',
        'label' => __('Width Layouts','vw-hospital-lite'),
        'description' => __('Here you can change the width layout of Website.','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_left_right',
        'choices' => array(
            'Full Width' => esc_url(get_template_directory_uri()).'/images/full-width.png',
            'Wide Width' => esc_url(get_template_directory_uri()).'/images/wide-width.png',
            'Boxed' => esc_url(get_template_directory_uri()).'/images/boxed-width.png',
    ))));

	$wp_customize->add_setting('vw_hospital_lite_page_layout',array(
        'default' => 'One Column',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
	));
	$wp_customize->add_control('vw_hospital_lite_page_layout',array(
        'type' => 'select',
        'label' => __('Page Sidebar Layout','vw-hospital-lite'),
        'description' => __('Here you can change the sidebar layout for pages. ','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hospital-lite'),
            'Right Sidebar' => __('Right Sidebar','vw-hospital-lite'),
            'One Column' => __('One Column','vw-hospital-lite')
        ),
	) );

    $wp_customize->add_setting( 'vw_hospital_lite_single_page_breadcrumb',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new vw_hospital_lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_single_page_breadcrumb',array(
        'label' => esc_html__( 'Show / Hide Page Breadcrumb','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_left_right'
    )));

    $wp_customize->add_setting('vw_hospital_lite_bradcrumbs_alignment',array(
        'default' => 'Left',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_bradcrumbs_alignment',array(
        'type' => 'select',
        'label' => __('Bradcrumbs Alignment','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_left_right',
        'choices' => array(
            'Left' => __('Left','vw-hospital-lite'),
            'Right' => __('Right','vw-hospital-lite'),
            'Center' => __('Center','vw-hospital-lite'),
        ),
    ) );

    //Wow Animation
    $wp_customize->add_setting( 'vw_hospital_lite_animation',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_animation',array(
        'label' => esc_html__( 'Show / Hide Animation ','vw-hospital-lite' ),
        'description' => __('Here you can disable overall site animation effect','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_left_right'
    )));

    $wp_customize->add_setting('vw_hospital_lite_reset_all_settings',array(
      'sanitize_callback'   => 'sanitize_text_field',
    ));
    $wp_customize->add_control(new VW_Hospital_Lite_Reset_Custom_Control($wp_customize, 'vw_hospital_lite_reset_all_settings',array(
      'type' => 'reset_control',
      'label' => __('Reset All Settings', 'vw-hospital-lite'),
      'description' => 'vw_hospital_lite_reset_all_settings',
      'section' => 'vw_hospital_lite_left_right'
    )));

	//Pre-Loader
	$wp_customize->add_setting( 'vw_hospital_lite_loader_enable',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_loader_enable',array(
        'label' => esc_html__( 'Show / Hide Pre-Loader','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_left_right'
    )));

    $wp_customize->add_setting('vw_hospital_lite_preloader_bg_color', array(
        'default'           => '#3ca6d4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_preloader_bg_color', array(
        'label'    => __('Pre-Loader Background Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_left_right',
    )));

    $wp_customize->add_setting('vw_hospital_lite_preloader_border_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_preloader_border_color', array(
        'label'    => __('Pre-Loader Border Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_left_right',
    )));

    $wp_customize->add_setting('vw_hospital_lite_preloader_bg_img',array(
        'default'   => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'vw_hospital_lite_preloader_bg_img',array(
        'label' => __('Preloader Background Image','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_left_right'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_sticky_sidebar',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new vw_hospital_lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_sticky_sidebar',array(
      'label' => esc_html__( 'Show / Hide Sticky Sidebar','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_left_right'
    )));

    //404 Page Setting
    $wp_customize->add_section('vw_hospital_lite_404_page',array(
        'title' => __('404 Page Settings','vw-hospital-lite'),
        'panel' => 'vw_hospital_lite_other_panel_id',
    )); 

    $wp_customize->add_setting('vw_hospital_lite_404_page_title',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_404_page_title',array(
        'label' => __('Add Title','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '404 Not Found', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_404_page',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_404_page_content',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_404_page_content',array(
        'label' => __('Add Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'Looks like you have taken a wrong turn, Dont worry, it happens to the best of us.', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_404_page',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_404_page_button_text',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_404_page_button_text',array(
        'label' => __('Add Button Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'Return to Home Page', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_404_page',
        'type'=> 'text'
    ));

    //No Result Page Setting
    $wp_customize->add_section('vw_hospital_lite_no_results_page',array(
        'title' => __('No Results Page Settings','vw-hospital-lite'),
        'panel' => 'vw_hospital_lite_other_panel_id',
    )); 

    $wp_customize->add_setting('vw_hospital_lite_no_results_page_title',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_control('vw_hospital_lite_no_results_page_title',array(
        'label' => __('Add Title','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'Nothing Found', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_no_results_page',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_no_results_page_content',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_control('vw_hospital_lite_no_results_page_content',array(
        'label' => __('Add Text','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_no_results_page',
        'type'=> 'text'
    ));

    //Social Icon Setting
    $wp_customize->add_section('vw_hospital_lite_social_icon_settings',array(
        'title' => __('Social Icons Settings','vw-hospital-lite'),
        'panel' => 'vw_hospital_lite_other_panel_id',
    )); 

    $wp_customize->add_setting('vw_hospital_lite_social_icon_font_size',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icon_font_size',array(
        'label' => __('Icon Font Size','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_social_icon_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_social_icon_padding',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icon_padding',array(
        'label' => __('Icon Padding','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_social_icon_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_social_icon_width',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icon_width',array(
        'label' => __('Icon Width','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_social_icon_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_social_icon_height',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_social_icon_height',array(
        'label' => __('Icon Height','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_social_icon_settings',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_social_icon_border_radius', array(
        'default'              => '',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_social_icon_border_radius', array(
        'label'       => esc_html__( 'Icon Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_social_icon_settings',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    //Responsive Media Settings
    $wp_customize->add_section('vw_hospital_lite_responsive_media',array(
        'title' => __('Responsive Media','vw-hospital-lite'),
        'panel' => 'vw_hospital_lite_other_panel_id',
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_responsive_preloader_hide',array(
        'default' => false,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_Control( $wp_customize, 'vw_hospital_lite_responsive_preloader_hide',array(
        'label' => esc_html__( 'Show / Hide Preloader','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_resp_topbar_hide_show',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_resp_topbar_hide_show',array(
      'label' => esc_html__( 'Show / Hide Topbar','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_stickyheader_hide_show',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_stickyheader_hide_show',array(
      'label' => esc_html__( 'Sticky Header','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_resp_slider_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_resp_slider_hide_show',array(
      'label' => esc_html__( 'Show / Hide Slider','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_sidebar_hide_show',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_sidebar_hide_show',array(
      'label' => esc_html__( 'Show / Hide Sidebar','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting( 'vw_hospital_lite_resp_scroll_top_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ));  
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_resp_scroll_top_hide_show',array(
      'label' => esc_html__( 'Show / Hide Scroll To Top','vw-hospital-lite' ),
      'section' => 'vw_hospital_lite_responsive_media'
    )));

    $wp_customize->add_setting('vw_hospital_lite_resp_menu_toggle_btn_bg_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vw_hospital_lite_resp_menu_toggle_btn_bg_color', array(
        'label'    => __('Toggle Button Bg Color', 'vw-hospital-lite'),
        'section'  => 'vw_hospital_lite_responsive_media',
    )));

    $wp_customize->add_setting('vw_hospital_lite_res_open_menu_icon',array(
        'default'   => 'fas fa-bars',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_res_open_menu_icon',array(
        'label' => __('Add Open Menu Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_responsive_media',
        'setting'   => 'vw_hospital_lite_res_open_menu_icon',
        'type'      => 'icon'
    )));

    $wp_customize->add_setting('vw_hospital_lite_res_close_menu_icon',array(
        'default'   => 'fas fa-times',
        'sanitize_callback' => 'sanitize_text_field'
    )); 
    $wp_customize->add_control(new VW_Hospital_Lite_Fontawesome_Icon_Chooser(
        $wp_customize,'vw_hospital_lite_res_close_menu_icon',array(
        'label' => __('Add Close Menu Icon','vw-hospital-lite'),
        'transport' => 'refresh',
        'section'   => 'vw_hospital_lite_responsive_media',
        'setting'   => 'vw_hospital_lite_res_close_menu_icon',
        'type'      => 'icon'
    )));

    //Woocommerce settings
    $wp_customize->add_section('vw_hospital_lite_woocommerce_section', array(
        'title'    => __('WooCommerce Layout', 'vw-hospital-lite'),
        'priority' => null,
        'panel'    => 'woocommerce',
    ));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial( 'vw_hospital_lite_woocommerce_shop_page_sidebar', array( 'selector' => '.post-type-archive-product .sidebar', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_woocommerce_shop_page_sidebar', ) );

    //Woocommerce Shop Page Sidebar
    $wp_customize->add_setting( 'vw_hospital_lite_woocommerce_shop_page_sidebar',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_woocommerce_shop_page_sidebar',array(
        'label' => esc_html__( 'Show / Hide Shop Page Sidebar','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_woocommerce_section'
    )));

    $wp_customize->add_setting('vw_hospital_lite_shop_page_layout',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_shop_page_layout',array(
        'type' => 'select',
        'label' => __('Shop Page Sidebar Layout','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_woocommerce_section',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hospital-lite'),
            'Right Sidebar' => __('Right Sidebar','vw-hospital-lite'),
        ),
    ) );

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial( 'vw_hospital_lite_woocommerce_single_product_page_sidebar', array( 'selector' => '.single-product .sidebar', 
        'render_callback' => 'vw_hospital_lite_customize_partial_vw_hospital_lite_woocommerce_single_product_page_sidebar', ) );

    //Woocommerce Single Product page Sidebar
    $wp_customize->add_setting( 'vw_hospital_lite_woocommerce_single_product_page_sidebar',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_woocommerce_single_product_page_sidebar',array(
        'label' => esc_html__( 'Show / Hide Single Product Sidebar','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_woocommerce_section'
    )));

    $wp_customize->add_setting('vw_hospital_lite_single_product_layout',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_single_product_layout',array(
        'type' => 'select',
        'label' => __('Single Product Sidebar Layout','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_woocommerce_section',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','vw-hospital-lite'),
            'Right Sidebar' => __('Right Sidebar','vw-hospital-lite'),
        ),
    ) );

    //Products per page
    $wp_customize->add_setting('vw_hospital_lite_products_per_page',array(
        'default'=> '9',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_float'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_per_page',array(
        'label' => __('Products Per Page','vw-hospital-lite'),
        'description' => __('Display on shop page','vw-hospital-lite'),
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 0,
            'max'              => 50,
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'number',
    ));

    //Products per row
    $wp_customize->add_setting('vw_hospital_lite_products_per_row',array(
        'default'=> 3,
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_per_row',array(
        'label' => __('Products Per Row','vw-hospital-lite'),
        'description' => __('Display on shop page','vw-hospital-lite'),
        'choices' => array(
            2 => 2,
            3 => 3,
            4 => 4,
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'select',
    ));

    //Products padding
    $wp_customize->add_setting('vw_hospital_lite_products_padding_top_bottom',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_padding_top_bottom',array(
        'label' => __('Products Padding Top Bottom','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_products_padding_left_right',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_padding_left_right',array(
        'label' => __('Products Padding Left Right','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'text'
    ));

    //Products box shadow
    $wp_customize->add_setting( 'vw_hospital_lite_products_box_shadow', array(
        'default'              => '',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_products_box_shadow', array(
        'label'       => esc_html__( 'Products Box Shadow','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_woocommerce_section',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    //Products border radius
    $wp_customize->add_setting( 'vw_hospital_lite_products_border_radius', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_products_border_radius', array(
        'label'       => esc_html__( 'Products Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_woocommerce_section',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_products_btn_padding_top_bottom',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_btn_padding_top_bottom',array(
        'label' => __('Products Button Padding Top Bottom','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting('vw_hospital_lite_products_btn_padding_left_right',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_products_btn_padding_left_right',array(
        'label' => __('Products Button Padding Left Right','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_products_button_border_radius', array(
        'default'              => '0',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_products_button_border_radius', array(
        'label'       => esc_html__( 'Products Button Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_woocommerce_section',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    //Products Sale Badge
    $wp_customize->add_setting('vw_hospital_lite_woocommerce_sale_position',array(
        'default' => 'right',
        'sanitize_callback' => 'vw_hospital_lite_sanitize_choices'
    ));
    $wp_customize->add_control('vw_hospital_lite_woocommerce_sale_position',array(
        'type' => 'select',
        'label' => __('Sale Badge Position','vw-hospital-lite'),
        'section' => 'vw_hospital_lite_woocommerce_section',
        'choices' => array(
            'left' => __('Left','vw-hospital-lite'),
            'right' => __('Right','vw-hospital-lite'),
        ),
    ) );

    $wp_customize->add_setting('vw_hospital_lite_woocommerce_sale_font_size',array(
        'default'=> '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('vw_hospital_lite_woocommerce_sale_font_size',array(
        'label' => __('Sale Font Size','vw-hospital-lite'),
        'description'   => __('Enter a value in pixels. Example:20px','vw-hospital-lite'),
        'input_attrs' => array(
            'placeholder' => __( '10px', 'vw-hospital-lite' ),
        ),
        'section'=> 'vw_hospital_lite_woocommerce_section',
        'type'=> 'text'
    ));

    $wp_customize->add_setting( 'vw_hospital_lite_woocommerce_sale_border_radius', array(
        'default'              => '100',
        'transport'            => 'refresh',
        'sanitize_callback'    => 'vw_hospital_lite_sanitize_number_range'
    ) );
    $wp_customize->add_control( 'vw_hospital_lite_woocommerce_sale_border_radius', array(
        'label'       => esc_html__( 'Sale Border Radius','vw-hospital-lite' ),
        'section'     => 'vw_hospital_lite_woocommerce_section',
        'type'        => 'range',
        'input_attrs' => array(
            'step'             => 1,
            'min'              => 1,
            'max'              => 50,
        ),
    ) );

    // Related Product
    $wp_customize->add_setting( 'vw_hospital_lite_related_product_show_hide',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'vw_hospital_lite_switch_sanitization'
    ) );
    $wp_customize->add_control( new VW_Hospital_Lite_Toggle_Switch_Custom_control( $wp_customize, 'vw_hospital_lite_related_product_show_hide',array(
        'label' => esc_html__( 'Show / Hide Related product','vw-hospital-lite' ),
        'section' => 'vw_hospital_lite_woocommerce_section'
    ))); 


	// Has to be at the top
    $wp_customize->register_panel_type( 'VW_Hospital_Lite_WP_Customize_Panel' );
    $wp_customize->register_section_type( 'VW_Hospital_Lite_WP_Customize_Section' );
	
}
add_action( 'customize_register', 'vw_hospital_lite_customize_register' );

load_template( trailingslashit( get_template_directory() ) . '/inc/logo-resizer.php' );

if ( class_exists( 'WP_Customize_Panel' ) ) {
    class VW_Hospital_Lite_WP_Customize_Panel extends WP_Customize_Panel {
        public $panel;
        public $type = 'vw_hospital_lite_panel';
        public function json() {

          $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type', 'panel', ) );
          $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
          $array['content'] = $this->get_content();
          $array['active'] = $this->active();
          $array['instanceNumber'] = $this->instance_number;
          return $array;
        }
    }
}

if ( class_exists( 'WP_Customize_Section' ) ) {
    class VW_Hospital_Lite_WP_Customize_Section extends WP_Customize_Section {
        public $section;
        public $type = 'vw_hospital_lite_section';
        public function json() {

          $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'panel', 'type', 'description_hidden', 'section', ) );
          $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
          $array['content'] = $this->get_content();
          $array['active'] = $this->active();
          $array['instanceNumber'] = $this->instance_number;

          if ( $this->panel ) {
            $array['customizeAction'] = sprintf( 'Customizing &#9656; %s', esc_html( $this->manager->get_panel( $this->panel )->title ) );
          } else {
            $array['customizeAction'] = 'Customizing';
          }
          return $array;
        }
    }
}

// Enqueue our scripts and styles
function vw_hospital_lite_customize_controls_scripts() {
  wp_enqueue_script( 'customizer-controls', get_theme_file_uri( '/js/customizer-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'vw_hospital_lite_customize_controls_scripts' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class VW_Hospital_Lite_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'VW_Hospital_Lite_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(new VW_Hospital_Lite_Customize_Section_Pro($manager,'vw_hospital_lite_upgrade_pro_link',array(
			'priority'	=> 1,
			'title'    => esc_html__( 'VW Hospital Pro', 'vw-hospital-lite' ),
			'pro_text' => esc_html__( 'Upgrade Pro','vw-hospital-lite' ),
			'pro_url'  => esc_url('https://www.vwthemes.com/products/hospital-wordpress-theme')
		)));

		// Register sections.
		$manager->add_section(new VW_Hospital_Lite_Customize_Section_Pro($manager,'vw_hospital_lite_get_started_link',array(
			'priority'   => 1,
			'title'    => esc_html__( 'Documentation', 'vw-hospital-lite' ),
			'pro_text' => esc_html__( 'Docs', 'vw-hospital-lite' ),
			'pro_url'  => esc_url('https://preview.vwthemesdemo.com/docs/free-vw-hospital-lite/')
		)));
        
        $manager->add_section(new VW_Hospital_Lite_Customize_Section_Pro($manager,'vw_hospital_lite_get_bundle_link',array(
            'priority'   => 1,
            'title'    => esc_html__( 'Get 250+ Themes Bundle at $99', 'vw-hospital-lite' ),
            'pro_text' => esc_html__( 'Get Bundle', 'vw-hospital-lite' ),
            'pro_url'  => esc_url('https://www.vwthemes.com/products/wp-theme-bundle')
        )));
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'vw-hospital-lite-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'vw-hospital-lite-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/css/customize-controls.css' );

        wp_localize_script(
        'vw-hospital-lite-customize-controls',
        'vw_hospital_lite_customizer_params',
        array(
            'ajaxurl' =>    admin_url( 'admin-ajax.php' )
        ));
	}
}

// Doing this customizer thang!
VW_Hospital_Lite_Customize::get_instance();