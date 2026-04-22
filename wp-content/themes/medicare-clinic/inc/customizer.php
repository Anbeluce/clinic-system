<?php
/**
 * Medicare Clinic: Customizer
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function medicare_clinic_customize_register( $wp_customize ) {

	// Pro Version
    class Medicare_Clinic_Customize_Pro_Version extends WP_Customize_Control {
        public $type = 'pro_options';

        public function render_content() {
            echo '<span>Unlock Premium <strong>'. esc_html( $this->label ) .'</strong>? </span>';
            echo '<a href="'. esc_url($this->description) .'" target="_blank">';
                echo '<span class="dashicons dashicons-info"></span>';
                echo '<strong> '. esc_html( MEDICARE_CLINIC_BUY_TEXT,'medicare-clinic' ) .'<strong></a>';
            echo '</a>';
        }
    }

    // Custom Controls
    function medicare_clinic_sanitize_custom_control( $input ) {
        return $input;
    }

	require get_parent_theme_file_path('/inc/controls/range-slider-control.php');

	require get_parent_theme_file_path('/inc/controls/icon-changer.php');
	
	// Register the custom control type.
	$wp_customize->register_control_type( 'Medicare_Clinic_Toggle_Control' );
	
	//Register the sortable control type.
	$wp_customize->register_control_type( 'Medicare_Clinic_Control_Sortable' );

	//add home page setting pannel
	$wp_customize->add_panel( 'medicare_clinic_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Custom Home page', 'medicare-clinic' ),
	    'description' => __( 'Description of what this panel does.', 'medicare-clinic' ),
	) );
	
	//TP GENRAL OPTION
	$wp_customize->add_section('medicare_clinic_tp_general_settings',array(
        'title' => __('TP General Option', 'medicare-clinic'),
        'priority' => 1,
        'panel' => 'medicare_clinic_panel_id'
    ) );

    $wp_customize->add_setting('medicare_clinic_tp_body_layout_settings',array(
        'default' => 'Full',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
    $wp_customize->add_control('medicare_clinic_tp_body_layout_settings',array(
        'type' => 'radio',
        'label'     => __('Body Layout Setting', 'medicare-clinic'),
        'description'   => __('This option work for complete body, if you want to set the complete website in container.', 'medicare-clinic'),
        'section' => 'medicare_clinic_tp_general_settings',
        'choices' => array(
            'Full' => __('Full','medicare-clinic'),
            'Container' => __('Container','medicare-clinic'),
            'Container Fluid' => __('Container Fluid','medicare-clinic')
        ),
	) );

    // Add Settings and Controls for Post Layout
	$wp_customize->add_setting('medicare_clinic_sidebar_post_layout',array(
        'default' => 'right',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_sidebar_post_layout',array(
        'type' => 'radio',
        'label'     => __('Post Sidebar Position', 'medicare-clinic'),
        'description'   => __('This option work for blog page, blog single page, archive page and search page.', 'medicare-clinic'),
        'section' => 'medicare_clinic_tp_general_settings',
        'choices' => array(
            'full' => __('Full','medicare-clinic'),
            'left' => __('Left','medicare-clinic'),
            'right' => __('Right','medicare-clinic'),
            'three-column' => __('Three Columns','medicare-clinic'),
            'four-column' => __('Four Columns','medicare-clinic'),
            'grid' => __('Grid Layout','medicare-clinic')
        ),
	) );

	// Add Settings and Controls for post sidebar Layout
	$wp_customize->add_setting('medicare_clinic_sidebar_single_post_layout',array(
        'default' => 'right',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_sidebar_single_post_layout',array(
        'type' => 'radio',
        'label'     => __('Single Post Sidebar Position', 'medicare-clinic'),
        'description'   => __('This option work for single blog page', 'medicare-clinic'),
        'section' => 'medicare_clinic_tp_general_settings',
        'choices' => array(
            'full' => __('Full','medicare-clinic'),
            'left' => __('Left','medicare-clinic'),
            'right' => __('Right','medicare-clinic'),
        ),
	) );

	// Add Settings and Controls for Page Layout
	$wp_customize->add_setting('medicare_clinic_sidebar_page_layout',array(
        'default' => 'right',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_sidebar_page_layout',array(
        'type' => 'radio',
        'label'     => __('Page Sidebar Position', 'medicare-clinic'),
        'description'   => __('This option work for pages.', 'medicare-clinic'),
        'section' => 'medicare_clinic_tp_general_settings',
        'choices' => array(
            'full' => __('Full','medicare-clinic'),
            'left' => __('Left','medicare-clinic'),
            'right' => __('Right','medicare-clinic')
        ),
	) );

	$wp_customize->add_setting( 'medicare_clinic_sticky', array(
		'default'           => false,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_sticky', array(
		'label'       => esc_html__( 'Show Sticky Header', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_tp_general_settings',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_sticky',
	) ) );

	//tp typography option
	$medicare_clinic_font_array = array(
		''                       => 'No Fonts',
		'Abril Fatface'          => 'Abril Fatface',
		'Acme'                   => 'Acme',
		'Anton'                  => 'Anton',
		'Architects Daughter'    => 'Architects Daughter',
		'Arimo'                  => 'Arimo',
		'Arsenal'                => 'Arsenal',
		'Arvo'                   => 'Arvo',
		'Alegreya'               => 'Alegreya',
		'Alfa Slab One'          => 'Alfa Slab One',
		'Averia Serif Libre'     => 'Averia Serif Libre',
		'Bangers'                => 'Bangers',
		'Boogaloo'               => 'Boogaloo',
		'Bad Script'             => 'Bad Script',
		'Bitter'                 => 'Bitter',
		'Bree Serif'             => 'Bree Serif',
		'BenchNine'              => 'BenchNine',
		'Cabin'                  => 'Cabin',
		'Cardo'                  => 'Cardo',
		'Courgette'              => 'Courgette',
		'Cherry Swash'           => 'Cherry Swash',
		'Cormorant Garamond'     => 'Cormorant Garamond',
		'Crimson Text'           => 'Crimson Text',
		'Cuprum'                 => 'Cuprum',
		'Cookie'                 => 'Cookie',
		'Chewy'                  => 'Chewy',
		'Days One'               => 'Days One',
		'Dosis'                  => 'Dosis',
		'Droid Sans'             => 'Droid Sans',
		'Economica'              => 'Economica',
		'Fredoka One'            => 'Fredoka One',
		'Fjalla One'             => 'Fjalla One',
		'Francois One'           => 'Francois One',
		'Frank Ruhl Libre'       => 'Frank Ruhl Libre',
		'Gloria Hallelujah'      => 'Gloria Hallelujah',
		'Great Vibes'            => 'Great Vibes',
		'Handlee'                => 'Handlee',
		'Hammersmith One'        => 'Hammersmith One',
		'Inconsolata'            => 'Inconsolata',
		'Indie Flower'           => 'Indie Flower',
		'Inter'                  => 'Inter',
		'IM Fell English SC'     => 'IM Fell English SC',
		'Julius Sans One'        => 'Julius Sans One',
		'Josefin Slab'           => 'Josefin Slab',
		'Josefin Sans'           => 'Josefin Sans',
		'Kanit'                  => 'Kanit',
		'Karla'                  => 'Karla',
		'Lobster'                => 'Lobster',
		'Lato'                   => 'Lato',
		'Lora'                   => 'Lora',
		'Libre Baskerville'      => 'Libre Baskerville',
		'Lobster Two'            => 'Lobster Two',
		'Manrope'           	 => 'Manrope',
		'Merriweather'           => 'Merriweather',
		'Monda'                  => 'Monda',
		'Montserrat'             => 'Montserrat',
		'Muli'                   => 'Muli',
		'Marck Script'           => 'Marck Script',
		'Noto Serif'             => 'Noto Serif',
		'Open Sans'              => 'Open Sans',
		'Overpass'               => 'Overpass',
		'Overpass Mono'          => 'Overpass Mono',
		'Oxygen'                 => 'Oxygen',
		'Oxanium'                => 'Oxanium',
		'Orbitron'               => 'Orbitron',
		'Patua One'              => 'Patua One',
		'Pacifico'               => 'Pacifico',
		'Padauk'                 => 'Padauk',
		'Playball'               => 'Playball',
		'Playfair Display'       => 'Playfair Display',
		'PT Sans'                => 'PT Sans',
		'Philosopher'            => 'Philosopher',
		'Permanent Marker'       => 'Permanent Marker',
		'Poiret One'             => 'Poiret One',
		'Quicksand'              => 'Quicksand',
		'Quattrocento Sans'      => 'Quattrocento Sans',
		'Raleway'                => 'Raleway',
		'Rubik'                  => 'Rubik',
		'Rokkitt'                => 'Rokkitt',
		'Roboto Serif'           => 'Roboto Serif',
		'Russo One'              => 'Russo One',
		'Righteous'              => 'Righteous',
		'Satisfy'                => 'Satisfy',
		'Slabo'                  => 'Slabo',
		'Source Sans Pro'        => 'Source Sans Pro',
		'Shadows Into Light Two' => 'Shadows Into Light Two',
		'Shadows Into Light'     => 'Shadows Into Light',
		'Sacramento'             => 'Sacramento',
		'Shrikhand'              => 'Shrikhand',
		'Tangerine'              => 'Tangerine',
		'Ubuntu'                 => 'Ubuntu',
		'VT323'                  => 'VT323',
		'Varela Round'           => 'Varela Round',
		'Vampiro One'            => 'Vampiro One',
		'Vollkorn'               => 'Vollkorn',
		'Volkhov'                => 'Volkhov',
		'Yanone Kaffeesatz'      => 'Yanone Kaffeesatz'
	);

	$wp_customize->add_section('medicare_clinic_typography_option',array(
		'title'         => __('TP Typography Option', 'medicare-clinic'),
		'priority' => 1,
		'panel' => 'medicare_clinic_panel_id'
   	));

   	$wp_customize->add_setting('medicare_clinic_heading_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'medicare_clinic_sanitize_choices',
	));
	$wp_customize->add_control(	'medicare_clinic_heading_font_family', array(
		'section' => 'medicare_clinic_typography_option',
		'label'   => __('heading Fonts', 'medicare-clinic'),
		'type'    => 'select',
		'choices' => $medicare_clinic_font_array,
	));

	$wp_customize->add_setting('medicare_clinic_body_font_family', array(
		'default'           => '',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'medicare_clinic_sanitize_choices',
	));
	$wp_customize->add_control(	'medicare_clinic_body_font_family', array(
		'section' => 'medicare_clinic_typography_option',
		'label'   => __('Body Fonts', 'medicare-clinic'),
		'type'    => 'select',
		'choices' => $medicare_clinic_font_array,
	));

	//TP Preloader Option
	$wp_customize->add_section('medicare_clinic_prelaoder_option',array(
		'title'         => __('TP Preloader Option', 'medicare-clinic'),
		'priority' => 1,
		'panel' => 'medicare_clinic_panel_id'
	) );

	$wp_customize->add_setting( 'medicare_clinic_preloader_show_hide', array(
		'default'           => false,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_preloader_show_hide', array(
		'label'       => esc_html__( 'Show / Hide Preloader Option', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_prelaoder_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_preloader_show_hide',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_tp_preloader_color1_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_tp_preloader_color1_option', array(
			'label'     => __('Preloader First Ring Color', 'medicare-clinic'),
	    'description' => __('It will change the complete theme preloader ring 1 color in one click.', 'medicare-clinic'),
	    'section' => 'medicare_clinic_prelaoder_option',
	    'settings' => 'medicare_clinic_tp_preloader_color1_option',
  	)));

  	$wp_customize->add_setting( 'medicare_clinic_tp_preloader_color2_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_tp_preloader_color2_option', array(
			'label'     => __('Preloader Second Ring Color', 'medicare-clinic'),
	    'description' => __('It will change the complete theme preloader ring 2 color in one click.', 'medicare-clinic'),
	    'section' => 'medicare_clinic_prelaoder_option',
	    'settings' => 'medicare_clinic_tp_preloader_color2_option',
  	)));

  	$wp_customize->add_setting( 'medicare_clinic_tp_preloader_bg_color_option', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_tp_preloader_bg_color_option', array(
			'label'     => __('Preloader Background Color', 'medicare-clinic'),
	    'description' => __('It will change the complete theme preloader bg color in one click.', 'medicare-clinic'),
	    'section' => 'medicare_clinic_prelaoder_option',
	    'settings' => 'medicare_clinic_tp_preloader_bg_color_option',
  	)));

  	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_preloader_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_preloader_pro_version_logo', array(
        'section'     => 'medicare_clinic_prelaoder_option',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//TP Color Option
	$wp_customize->add_section('medicare_clinic_color_option',array(
     'title'         => __('TP Color Option', 'medicare-clinic'),
     'priority' => 1,
     'panel' => 'medicare_clinic_panel_id'
    ) );
    
	$wp_customize->add_setting( 'medicare_clinic_tp_color_option_first', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_tp_color_option_first', array(
			'label'     => __('Theme First Color', 'medicare-clinic'),
	    'description' => __('It will change the complete theme color in one click.', 'medicare-clinic'),
	    'section' => 'medicare_clinic_color_option',
	    'settings' => 'medicare_clinic_tp_color_option_first',
  	)));

	//TP Blog Option
	$wp_customize->add_section('medicare_clinic_blog_option',array(
        'title' => __('TP Blog Option', 'medicare-clinic'),
        'priority' => 1,
        'panel' => 'medicare_clinic_panel_id'
    ) );

    $wp_customize->add_setting('medicare_clinic_edit_blog_page_title',array(
		'default'=> __('Home','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_edit_blog_page_title',array(
		'label'	=> __('Change Blog Page Title','medicare-clinic'),
		'section'=> 'medicare_clinic_blog_option',
		'type'=> 'text'
	));

	$wp_customize->add_setting('medicare_clinic_edit_blog_page_description',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_edit_blog_page_description',array(
		'label'	=> __('Add Blog Page Description','medicare-clinic'),
		'section'=> 'medicare_clinic_blog_option',
		'type'=> 'text'
	));

	/** Meta Order */
    $wp_customize->add_setting('blog_meta_order', array(
        'default' => array('date', 'author', 'comment','category', 'time'),
        'sanitize_callback' => 'medicare_clinic_sanitize_sortable',
    ));
    $wp_customize->add_control(new Medicare_Clinic_Control_Sortable($wp_customize, 'blog_meta_order', array(
    	'label' => esc_html__('Meta Order', 'medicare-clinic'),
        'description' => __('Drag & Drop post items to re-arrange the order and also hide and show items as per the need by clicking on the eye icon.', 'medicare-clinic') ,
        'section' => 'medicare_clinic_blog_option',
        'choices' => array(
            'date' => __('date', 'medicare-clinic') ,
            'author' => __('author', 'medicare-clinic') ,
            'comment' => __('comment', 'medicare-clinic') ,
            'category' => __('category', 'medicare-clinic') ,
            'time' => __('time', 'medicare-clinic') ,
        ) ,
    )));

    $wp_customize->add_setting( 'medicare_clinic_excerpt_count', array(
		'default'              => 35,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'medicare_clinic_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'medicare_clinic_excerpt_count', array(
		'label'       => esc_html__( 'Edit Excerpt Limit','medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting('medicare_clinic_show_first_caps',array(
        'default' => false,
        'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
    ));
	$wp_customize->add_control( 'medicare_clinic_show_first_caps',array(
		'label' => esc_html__('First Cap (First Capital Letter)', 'medicare-clinic'),
		'type' => 'checkbox',
		'section' => 'medicare_clinic_blog_option',
	));

    $wp_customize->add_setting('medicare_clinic_read_more_text',array(
		'default'=> __('Read More','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_read_more_text',array(
		'label'	=> __('Edit Button Text','medicare-clinic'),
		'section'=> 'medicare_clinic_blog_option',
		'type'=> 'text'
	));

	$wp_customize->add_setting('medicare_clinic_post_image_round', array(
	  'default' => '0',
      'sanitize_callback' => 'medicare_clinic_sanitize_number_range',
	));
	$wp_customize->add_control(new Medicare_Clinic_Range_Slider($wp_customize, 'medicare_clinic_post_image_round', array(
       'section' => 'medicare_clinic_blog_option',
      'label' => esc_html__('Edit Post Image Border Radius', 'medicare-clinic'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 180,
        'step' => 1
    )
	)));

	$wp_customize->add_setting('medicare_clinic_post_image_width', array(
	  'default' => '',
      'sanitize_callback' => 'medicare_clinic_sanitize_number_range',
	));
	$wp_customize->add_control(new Medicare_Clinic_Range_Slider($wp_customize, 'medicare_clinic_post_image_width', array(
       'section' => 'medicare_clinic_blog_option',
      'label' => esc_html__('Edit Post Image Width', 'medicare-clinic'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 367,
        'step' => 1
    )
	)));

	$wp_customize->add_setting('medicare_clinic_post_image_length', array(
	  'default' => '',
      'sanitize_callback' => 'medicare_clinic_sanitize_number_range',
	));
	$wp_customize->add_control(new Medicare_Clinic_Range_Slider($wp_customize, 'medicare_clinic_post_image_length', array(
       'section' => 'medicare_clinic_blog_option',
      'label' => esc_html__('Edit Post Image height', 'medicare-clinic'),
      'input_attrs' => array(
        'min' => 0,
        'max' => 900,
        'step' => 1
    )
	)));
	
	$wp_customize->add_setting( 'medicare_clinic_remove_read_button', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_remove_read_button', array(
		'label'       => esc_html__( 'Show / Hide Read More Button', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_remove_read_button',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_remove_tags', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_remove_tags', array(
		'label'       => esc_html__( 'Show / Hide Tags Option', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_remove_tags',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_remove_category', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_remove_category', array(
		'label'       => esc_html__( 'Show / Hide Category Option', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_remove_category',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_remove_comment', array(
	 'default'           => true,
	 'transport'         => 'refresh',
	 'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
 	) );

	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_remove_comment', array(
	 'label'       => esc_html__( 'Show / Hide Comment Form', 'medicare-clinic' ),
	 'section'     => 'medicare_clinic_blog_option',
	 'type'        => 'toggle',
	 'settings'    => 'medicare_clinic_remove_comment',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_remove_related_post', array(
	 'default'           => true,
	 'transport'         => 'refresh',
	 'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
 	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_remove_related_post', array(
	 'label'       => esc_html__( 'Show / Hide Related Post', 'medicare-clinic' ),
	 'section'     => 'medicare_clinic_blog_option',
	 'type'        => 'toggle',
	 'settings'    => 'medicare_clinic_remove_related_post',
	) ) );

	$wp_customize->add_setting('medicare_clinic_related_post_heading',array(
		'default'=> __('Related Posts','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_related_post_heading',array(
		'label'	=> __('Edit Section Title','medicare-clinic'),
		'section'=> 'medicare_clinic_blog_option',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'medicare_clinic_related_post_per_page', array(
		'default'              => 3,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'medicare_clinic_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'medicare_clinic_related_post_per_page', array(
		'label'       => esc_html__( 'Related Post Per Page','medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 3,
			'max'              => 9,
		),
	) );

	$wp_customize->add_setting( 'medicare_clinic_related_post_per_columns', array(
		'default'              => 3,
		'type'                 => 'theme_mod',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'medicare_clinic_sanitize_number_range',
		'sanitize_js_callback' => 'absint',
	) );
	$wp_customize->add_control( 'medicare_clinic_related_post_per_columns', array(
		'label'       => esc_html__( 'Related Post Per Row','medicare-clinic' ),
		'section'     => 'medicare_clinic_blog_option',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 4,
		),
	) );

	$wp_customize->add_setting('medicare_clinic_post_layout',array(
        'default' => 'image-content',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_post_layout',array(
        'type' => 'radio',
        'label'     => __('Post Layout', 'medicare-clinic'),
        'section' => 'medicare_clinic_blog_option',
        'choices' => array(
            'image-content' => __('Media-Content','medicare-clinic'),
            'content-image' => __('Content-Media','medicare-clinic'),
        ),
	) );

	//TP Single Blog Option
	$wp_customize->add_section('medicare_clinic_single_blog_option',array(
        'title' => __('Single Post Option', 'medicare-clinic'),
        'priority' => 1,
        'panel' => 'medicare_clinic_panel_id'
    ) );

    /** Meta Order */
    $wp_customize->add_setting('medicare_clinic_single_blog_meta_order', array(
        'default' => array('date', 'author', 'comment','category', 'time'),
        'sanitize_callback' => 'medicare_clinic_sanitize_sortable',
    ));
    $wp_customize->add_control(new medicare_clinic_Control_Sortable($wp_customize, 'medicare_clinic_single_blog_meta_order', array(
    	'label' => esc_html__('Meta Order', 'medicare-clinic'),
        'description' => __('Drag & Drop post items to re-arrange the order and also hide and show items as per the need by clicking on the eye icon.', 'medicare-clinic') ,
        'section' => 'medicare_clinic_single_blog_option',
        'choices' => array(
            'date' => __('date', 'medicare-clinic') ,
            'author' => __('author', 'medicare-clinic') ,
            'comment' => __('comment', 'medicare-clinic') ,
            'category' => __('category', 'medicare-clinic') ,
            'time' => __('time', 'medicare-clinic') ,
        ) ,
    )));

    $wp_customize->add_setting('medicare_clinic_single_post_date_icon',array(
		'default'	=> 'far fa-calendar-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_single_post_date_icon',array(
		'label'	=> __('Change Date Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_single_blog_option',
		'type'		=> 'medicare-clinic-icon'
	)));

	$wp_customize->add_setting('medicare_clinic_single_post_author_icon',array(
		'default'	=> 'fas fa-user',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_single_post_author_icon',array(
		'label'	=> __('Change Author Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_single_blog_option',
		'type'		=> 'medicare-clinic-icon'
	)));

	$wp_customize->add_setting('medicare_clinic_single_post_comment_icon',array(
		'default'	=> 'fas fa-comments',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_single_post_comment_icon',array(
		'label'	=> __('Change Comment Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_single_blog_option',
		'type'		=> 'medicare-clinic-icon'
	)));

	$wp_customize->add_setting('medicare_clinic_single_post_category_icon',array(
		'default'	=> 'fas fa-list',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_single_post_category_icon',array(
		'label'	=> __('Change Category Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_single_blog_option',
		'type'		=> 'medicare-clinic-icon'
	)));

	$wp_customize->add_setting('medicare_clinic_single_post_time_icon',array(
		'default'	=> 'fas fa-clock',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_single_post_time_icon',array(
		'label'	=> __('Change Time Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_single_blog_option',
		'type'		=> 'medicare-clinic-icon'
	)));

	//MENU TYPOGRAPHY
	$wp_customize->add_section( 'medicare_clinic_menu_typography', array(
    	'title'      => __( 'Menu Typography', 'medicare-clinic' ),
    	'priority' => 2,
		'panel' => 'medicare_clinic_panel_id'
	) );

	$wp_customize->add_setting('medicare_clinic_menu_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_menu_font_weight',array(
     'type' => 'radio',
     'label'     => __('Font Weight', 'medicare-clinic'),
     'section' => 'medicare_clinic_menu_typography',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','medicare-clinic'),
         '200' => __('200','medicare-clinic'),
         '300' => __('300','medicare-clinic'),
         '400' => __('400','medicare-clinic'),
         '500' => __('500','medicare-clinic'),
         '600' => __('600','medicare-clinic'),
         '700' => __('700','medicare-clinic'),
         '800' => __('800','medicare-clinic'),
         '900' => __('900','medicare-clinic')
     ),
	) );

	$wp_customize->add_setting('medicare_clinic_menu_text_tranform',array(
		'default' => '',
		'sanitize_callback' => 'medicare_clinic_sanitize_choices'
 	));
 	$wp_customize->add_control('medicare_clinic_menu_text_tranform',array(
		'type' => 'select',
		'label' => __('Menu Text Transform','medicare-clinic'),
		'section' => 'medicare_clinic_menu_typography',
		'choices' => array(
		   'Uppercase' => __('Uppercase','medicare-clinic'),
		   'Lowercase' => __('Lowercase','medicare-clinic'),
		   'Capitalize' => __('Capitalize','medicare-clinic'),
		),
	) );

	$wp_customize->add_setting('medicare_clinic_menu_font_size', array(
	  'default' => '',
      'sanitize_callback' => 'medicare_clinic_sanitize_number_range',
	));
	$wp_customize->add_control(new Medicare_Clinic_Range_Slider($wp_customize, 'medicare_clinic_menu_font_size', array(
        'section' => 'medicare_clinic_menu_typography',
        'label' => esc_html__('Font Size', 'medicare-clinic'),
        'input_attrs' => array(
          'min' => 0,
          'max' => 20,
          'step' => 1
    )
	)));

	$wp_customize->add_setting('medicare_clinic_menus_item_style',array(
		'default' => '',
		'transport' => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_menus_item_style',array(
		'type' => 'select',
		'section' => 'medicare_clinic_menu_typography',
		'label' => __('Menu Hover Effect','medicare-clinic'),
		'choices' => array(
			'None' => __('None','medicare-clinic'),
			'Zoom In' => __('Zoom In','medicare-clinic'),
		),
	) );

	$wp_customize->add_setting( 'medicare_clinic_menu_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_menu_color', array(
			'label'     => __('Change Menu Color', 'medicare-clinic'),
	    'section' => 'medicare_clinic_menu_typography',
	    'settings' => 'medicare_clinic_menu_color',
  	)));

  	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_menu_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_menu_pro_version_logo', array(
        'section'     => 'medicare_clinic_menu_typography',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

  	// header detail
	$wp_customize->add_section( 'medicare_clinic_header_sec', array(
    	'title'      => __( 'Header Details', 'medicare-clinic' ),
    	'description' => __( 'Add your Contact details here', 'medicare-clinic' ),
		'panel' => 'medicare_clinic_panel_id',
      'priority' => 2,
	) );

	$wp_customize->add_setting('medicare_clinic_header_button',array(
		'default'=> 'APPOINTMENT',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_header_button',array(
		'label'	=> __('Change Header Button Text','medicare-clinic'),
		'section'=> 'medicare_clinic_header_sec',
		'type'=> 'text'
	));

	$wp_customize->add_setting('medicare_clinic_header_link',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('medicare_clinic_header_link',array(
		'label'	=> __('Add Header Button Link','medicare-clinic'),
		'section'=> 'medicare_clinic_header_sec',
		'type'=> 'url'
	));

	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_header_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_header_pro_version_logo', array(
        'section'     => 'medicare_clinic_header_sec',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//home page slider
	$wp_customize->add_section( 'medicare_clinic_slider_section' , array(
    	'title'      => __( 'Slider Section', 'medicare-clinic' ),
    	'priority' => 2,
		'panel' => 'medicare_clinic_panel_id'
	) );

	$wp_customize->add_setting( 'medicare_clinic_slider_arrows', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_slider_arrows', array(
		'label'       => esc_html__( 'Show / Hide slider', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_slider_section',
		'priority' => 1,
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_slider_arrows',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_show_slider_title', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_show_slider_title', array(
		'label'       => esc_html__( 'Show / Hide Slider Heading', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_slider_section',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_show_slider_title',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_show_slider_content', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_show_slider_content', array(
		'label'       => esc_html__( 'Show / Hide Slider Content', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_slider_section',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_show_slider_content',
	) ) );

	for ( $medicare_clinic_count = 1; $medicare_clinic_count <= 4; $medicare_clinic_count++ ) {
		$wp_customize->add_setting( 'medicare_clinic_slider_page' . $medicare_clinic_count, array(
			'default'           => '',
			'sanitize_callback' => 'medicare_clinic_sanitize_dropdown_pages'
		) );

		$wp_customize->add_control( 'medicare_clinic_slider_page' . $medicare_clinic_count, array(
			'label'    => __( 'Select Slide Image Page', 'medicare-clinic' ),
			'section'  => 'medicare_clinic_slider_section',
			'type'     => 'dropdown-pages'
		) );
	}

	$wp_customize->add_setting('medicare_clinic_slider_short_heading',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_slider_short_heading',array(
		'label'	=> __('Add short Heading','medicare-clinic'),
		'section'=> 'medicare_clinic_slider_section',
		'type'=> 'text'
	));

	$wp_customize->add_setting(
		'medicare_clinic_about_call_text',array(
			'default'=> '',
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		'medicare_clinic_about_call_text',array(
			'label'	=> __('Add Call Text','medicare-clinic'),
			'section'=> 'medicare_clinic_slider_section',
			'type'=> 'text'
	));

	$wp_customize->add_setting(
		'medicare_clinic_about_call',
		array(
			'default'=> '',
			'sanitize_callback'	=> 'medicare_clinic_sanitize_phone_number'
	));
	$wp_customize->add_control(
		'medicare_clinic_about_call',array(
			'label'	=> __('Add Phone Number','medicare-clinic'),
			'section'=> 'medicare_clinic_slider_section',
			'type'=> 'text'
	));

	$wp_customize->add_setting('medicare_clinic_slider_add_time',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_slider_add_time',array(
		'label'	=> __('Add Hours','medicare-clinic'),
		'section'=> 'medicare_clinic_slider_section',
		'type'=> 'text'
	));

	//Slider excerpt
	$wp_customize->add_setting( 'medicare_clinic_slider_excerpt_length', array(
		'default'              => 27,
		'sanitize_callback'	=> 'absint',
	) );
	$wp_customize->add_control( 'medicare_clinic_slider_excerpt_length', array(
		'label'       => esc_html__( 'Slider Content length','medicare-clinic' ),
		'section'     => 'medicare_clinic_slider_section',
		'type'        => 'number',
		'settings'    => 'medicare_clinic_slider_excerpt_length',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	) );

	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_slider_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_slider_pro_version_logo', array(
        'section'     => 'medicare_clinic_slider_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

	/*=========================================
	service Section
	=========================================*/
	$wp_customize->add_section( 
		'medicare_clinic_service_section' , 
		array(
	        'title'      => __( 'Our Services Section', 'medicare-clinic' ),
	        'priority' => 3,
	        'panel' => 'medicare_clinic_panel_id',
    	) 
    );

    $wp_customize->add_setting( 'medicare_clinic_courses_setting', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_courses_setting', array(
		'label'       => esc_html__( 'Show / Hide Section', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_service_section',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_courses_setting',
	) ) );

    $wp_customize->add_setting(
    	'medicare_clinic_offer_section_tittle',
    	array(
	        'default'   => '',
	        'sanitize_callback' => 'sanitize_text_field'
    	)
    );
    $wp_customize->add_control(
    	'medicare_clinic_offer_section_tittle',
    	array(
	        'label' => __('Section Top Title','medicare-clinic'),
	        'section'   => 'medicare_clinic_service_section',
	        'type'      => 'text'
    	)
    );

    $wp_customize->add_setting(
    	'medicare_clinic_offer_section_text',
    	array(
	        'default'   => '',
	        'sanitize_callback' => 'sanitize_text_field'
    	)
    );
    $wp_customize->add_control(
    	'medicare_clinic_offer_section_text',
    	array(
	        'label' => __('Section Heading','medicare-clinic'),
	        'section'   => 'medicare_clinic_service_section',
	        'type'      => 'text'
    	)
    );

    $categories = get_categories();
    $cats = array();
    $i = 0;
    $offer_cat[]= 'select';
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        }
        $offer_cat[$category->slug] = $category->name;
    }

    $wp_customize->add_setting(
    	'medicare_clinic_offer_section_category',
    	array(
	        'default'   => '',
	        'sanitize_callback' => 'medicare_clinic_sanitize_choices',
    	)
    );
    $wp_customize->add_control(
    	'medicare_clinic_offer_section_category',
    	array(
	        'type'    => 'select',
	        'choices' => $offer_cat,
	        'label' => __('Select Category','medicare-clinic'),
	        'section' => 'medicare_clinic_service_section',
    	)
    );

    // Setting for number of posts to show
    $wp_customize->add_setting('medicare_clinic_posts_to_show', array(
        'default'           => 4, // Default number of posts to show
        'sanitize_callback' => 'absint', // Sanitization callback
    ));

    // Add control for number of posts to show
    $wp_customize->add_control('medicare_clinic_posts_to_show', array(
        'label'       => __('Number of Post to Show', 'medicare-clinic'),
        'section'     => 'medicare_clinic_service_section',
        'type'        => 'number',
        'input_attrs' => array(
            'step' => 1,
            'min'  => 0,
            'max'  => 50,
        ),
    ));

    // Get the number of posts to show
    $medicare_clinic_posts_to_show = get_theme_mod('medicare_clinic_posts_to_show', 4);
    
    // Loop to create settings and controls for each post's price and star rating
    for ($medicare_clinic_i = 1; $medicare_clinic_i <= $medicare_clinic_posts_to_show; $medicare_clinic_i++) {

    	$wp_customize->add_setting('medicare_clinic_team_role' . $medicare_clinic_i, array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field'
        ));

        $wp_customize->add_control('medicare_clinic_team_role' . $medicare_clinic_i, array(
            'label'    => __('Add Specialty for Doctor ', 'medicare-clinic') . $medicare_clinic_i,
            'section'  => 'medicare_clinic_service_section',
            'type'     => 'text',
        ));

    	$wp_customize->add_setting('medicare_clinic_serv_facebook_url' . $medicare_clinic_i, array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ));

        $wp_customize->add_control('medicare_clinic_serv_facebook_url' . $medicare_clinic_i, array(
            'label'    => __('Add Facebook Link for Post ', 'medicare-clinic') . $medicare_clinic_i,
            'section'  => 'medicare_clinic_service_section',
            'type'     => 'text',
        ));

        $wp_customize->add_setting('medicare_clinic_serv_insta_url' . $medicare_clinic_i, array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ));

        $wp_customize->add_control('medicare_clinic_serv_insta_url' . $medicare_clinic_i, array(
            'label'    => __('Add Instagram Link for Post', 'medicare-clinic') . $medicare_clinic_i,
            'section'  => 'medicare_clinic_service_section',
            'type'     => 'text',
        ));

        $wp_customize->add_setting('medicare_clinic_serv_youtube_url' . $medicare_clinic_i, array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ));

        $wp_customize->add_control('medicare_clinic_serv_youtube_url' . $medicare_clinic_i, array(
            'label'    => __('Add Youtube Link for Post', 'medicare-clinic') . $medicare_clinic_i,
            'section'  => 'medicare_clinic_service_section',
            'type'     => 'text',
        ));

        $wp_customize->add_setting('medicare_clinic_serv_snapchat_url' . $medicare_clinic_i, array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw'
        ));

        $wp_customize->add_control('medicare_clinic_serv_snapchat_url' . $medicare_clinic_i, array(
            'label'    => __('Add Snapchat Link for Post', 'medicare-clinic') . $medicare_clinic_i,
            'section'  => 'medicare_clinic_service_section',
            'type'     => 'text',
        ));
  
    }

    // Pro Version
    $wp_customize->add_setting( 'medicare_clinic_about_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_about_pro_version_logo', array(
        'section'     => 'medicare_clinic_service_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
    )));

	//footer
	$wp_customize->add_section('medicare_clinic_footer_section',array(
		'title'	=> __('Footer Widget Settings','medicare-clinic'),
		'panel' => 'medicare_clinic_panel_id',
		'priority' => 4,
	));

	$wp_customize->add_setting('medicare_clinic_footer_columns',array(
		'default'	=> 4,
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_footer_columns',array(
		'label'	=> __('Footer Widget Columns','medicare-clinic'),
		'section'	=> 'medicare_clinic_footer_section',
		'setting'	=> 'medicare_clinic_footer_columns',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 4,
		),
	));
	$wp_customize->add_setting( 'medicare_clinic_tp_footer_bg_color_option', array(
		'default' => '#151515',
		'sanitize_callback' => 'sanitize_hex_color'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_tp_footer_bg_color_option', array(
		'label'     => __('Footer Widget Background Color', 'medicare-clinic'),
		'description' => __('It will change the complete footer widget backgorund color.', 'medicare-clinic'),
		'section' => 'medicare_clinic_footer_section',
		'settings' => 'medicare_clinic_tp_footer_bg_color_option',
	)));

	$wp_customize->add_setting('medicare_clinic_footer_widget_image',array(
		'default'	=> '',
		'sanitize_callback'	=> 'esc_url_raw',
	));
	$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'medicare_clinic_footer_widget_image',array(
       'label' => __('Footer Widget Background Image','medicare-clinic'),
       'section' => 'medicare_clinic_footer_section'
	)));

	//footer widget title font size
	$wp_customize->add_setting('medicare_clinic_footer_widget_title_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_footer_widget_title_font_size',array(
		'label'	=> __('Change Footer Widget Title Font Size in PX','medicare-clinic'),
		'section'	=> 'medicare_clinic_footer_section',
	    'setting'	=> 'medicare_clinic_footer_widget_title_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	$wp_customize->add_setting( 'medicare_clinic_footer_widget_title_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_footer_widget_title_color', array(
			'label'     => __('Change Footer Widget Title Color', 'medicare-clinic'),
	    'section' => 'medicare_clinic_footer_section',
	    'settings' => 'medicare_clinic_footer_widget_title_color',
  	)));

  	$wp_customize->add_setting('medicare_clinic_footer_widget_title_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_footer_widget_title_font_weight',array(
     'type' => 'radio',
     'label'     => __('Change Footer Widget Title Font Weight', 'medicare-clinic'),
     'section' => 'medicare_clinic_footer_section',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','medicare-clinic'),
         '200' => __('200','medicare-clinic'),
         '300' => __('300','medicare-clinic'),
         '400' => __('400','medicare-clinic'),
         '500' => __('500','medicare-clinic'),
         '600' => __('600','medicare-clinic'),
         '700' => __('700','medicare-clinic'),
         '800' => __('800','medicare-clinic'),
         '900' => __('900','medicare-clinic')
     ),
	) );

	$wp_customize->add_setting('medicare_clinic_footer_widget_title_text_tranform',array(
		'default' => '',
		'sanitize_callback' => 'medicare_clinic_sanitize_choices'
 	));
 	$wp_customize->add_control('medicare_clinic_footer_widget_title_text_tranform',array(
		'type' => 'select',
		'label' => __('Change Footer Widget Title Letter Case','medicare-clinic'),
		'section' => 'medicare_clinic_footer_section',
		'choices' => array(
		   'Uppercase' => __('Uppercase','medicare-clinic'),
		   'Lowercase' => __('Lowercase','medicare-clinic'),
		   'Capitalize' => __('Capitalize','medicare-clinic'),
		),
	) );

	// Add Settings and Controls for position
	$wp_customize->add_setting('medicare_clinic_footer_widget_title_position',array(
        'default' => '',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_footer_widget_title_position',array(
        'type' => 'radio',
        'label'     => __('Change Footer Widget Position', 'medicare-clinic'),
        'description'   => __('This option work for Footer Widget', 'medicare-clinic'),
        'section' => 'medicare_clinic_footer_section',
        'choices' => array(
            'Right' => __('Right','medicare-clinic'),
            'Left' => __('Left','medicare-clinic'),
            'Center' => __('Center','medicare-clinic')
        ),
	) );
  	
	$wp_customize->add_setting( 'medicare_clinic_return_to_header', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_return_to_header', array(
		'label'       => esc_html__( 'Show / Hide Return to header', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_footer_section',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_return_to_header',
	) ) );

	$wp_customize->add_setting('medicare_clinic_return_icon',array(
		'default'	=> 'fas fa-arrow-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new Medicare_Clinic_Icon_Changer(
       $wp_customize,'medicare_clinic_return_icon',array(
		'label'	=> __('Return to header Icon','medicare-clinic'),
		'transport' => 'refresh',
		'section'	=> 'medicare_clinic_footer_section',
		'type'		=> 'medicare-clinic-icon'
	)));


    // Add Settings and Controls for Scroll top
	$wp_customize->add_setting('medicare_clinic_scroll_top_position',array(
        'default' => 'Right',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_scroll_top_position',array(
        'type' => 'radio',
        'label'     => __('Scroll to top Position', 'medicare-clinic'),
        'description'   => __('This option work for scroll to top', 'medicare-clinic'),
        'section' => 'medicare_clinic_footer_section',
        'choices' => array(
            'Right' => __('Right','medicare-clinic'),
            'Left' => __('Left','medicare-clinic'),
            'Center' => __('Center','medicare-clinic')
        ),
	) );

	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_footer_widget_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_footer_widget_pro_version_logo', array(
        'section'     => 'medicare_clinic_footer_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//footer
	$wp_customize->add_section('medicare_clinic_footer_copyright_section',array(
		'title'	=> __('Footer Copyright Settings','medicare-clinic'),
		'description'	=> __('Add copyright text.','medicare-clinic'),
		'panel' => 'medicare_clinic_panel_id',
		'priority' => 5,
	));

	$wp_customize->add_setting('medicare_clinic_footer_text',array(
		'default' => __( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_footer_text',array(
		'label'	=> __('Copyright Text','medicare-clinic'),
		'section'	=> 'medicare_clinic_footer_copyright_section',
		'type'		=> 'text'
	));

	$wp_customize->add_setting('medicare_clinic_footer_copyright_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_footer_copyright_font_size',array(
		'label'	=> __('Change Footer Copyright Font Size in PX','medicare-clinic'),
		'section'	=> 'medicare_clinic_footer_copyright_section',
	    'setting'	=> 'medicare_clinic_footer_copyright_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	$wp_customize->add_setting('medicare_clinic_footer_copyright_title_font_weight',array(
        'default' => '',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_footer_copyright_title_font_weight',array(
     'type' => 'radio',
     'label'     => __('Change Footer Copyright Text Font Weight', 'medicare-clinic'),
     'section' => 'medicare_clinic_footer_copyright_section',
     'type' => 'select',
     'choices' => array(
         '100' => __('100','medicare-clinic'),
         '200' => __('200','medicare-clinic'),
         '300' => __('300','medicare-clinic'),
         '400' => __('400','medicare-clinic'),
         '500' => __('500','medicare-clinic'),
         '600' => __('600','medicare-clinic'),
         '700' => __('700','medicare-clinic'),
         '800' => __('800','medicare-clinic'),
         '900' => __('900','medicare-clinic')
     ),
	) );

	$wp_customize->add_setting( 'medicare_clinic_footer_copyright_text_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_footer_copyright_text_color', array(
			'label'     => __('Change Footer Copyright Text Color', 'medicare-clinic'),
	    'section' => 'medicare_clinic_footer_copyright_section',
	    'settings' => 'medicare_clinic_footer_copyright_text_color',
  	)));

  	$wp_customize->add_setting('medicare_clinic_footer_copyright_top_bottom_padding',array(
		'default'	=> '',
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_footer_copyright_top_bottom_padding',array(
		'label'	=> __('Change Footer Copyright Padding in PX','medicare-clinic'),
		'section'	=> 'medicare_clinic_footer_copyright_section',
	    'setting'	=> 'medicare_clinic_footer_copyright_top_bottom_padding',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 50,
		),
	));

	// Add Settings and Controls for Scroll top
	$wp_customize->add_setting('medicare_clinic_copyright_text_position',array(
        'default' => 'Center',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_copyright_text_position',array(
        'type' => 'radio',
        'label'     => __('Copyright Text Position', 'medicare-clinic'),
        'description'   => __('This option work for Copyright', 'medicare-clinic'),
        'section' => 'medicare_clinic_footer_copyright_section',
        'choices' => array(
            'Right' => __('Right','medicare-clinic'),
            'Left' => __('Left','medicare-clinic'),
            'Center' => __('Center','medicare-clinic')
        ),
	) );

	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_copyright_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_copyright_pro_version_logo', array(
        'section'     => 'medicare_clinic_footer_copyright_section',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));

	//Mobile resposnsive
	$wp_customize->add_section('medicare_clinic_mobile_media_option',array(
		'title'         => __('Mobile Responsive media', 'medicare-clinic'),
		'description' => __('Control will not function if the toggle in the main settings is off.', 'medicare-clinic'),
		'priority' => 5,
		'panel' => 'medicare_clinic_panel_id'
	) );

	$wp_customize->add_setting( 'medicare_clinic_mobile_blog_description', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_mobile_blog_description', array(
		'label'       => esc_html__( 'Show / Hide Blog Page Description', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_mobile_blog_description',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_return_to_header_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_return_to_header_mob', array(
		'label'       => esc_html__( 'Show / Hide Return to header', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_return_to_header_mob',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_slider_buttom_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_slider_buttom_mob', array(
		'label'       => esc_html__( 'Show / Hide Slider Button', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_slider_buttom_mob',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_related_post_mob', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_related_post_mob', array(
		'label'       => esc_html__( 'Show / Hide Related Post', 'medicare-clinic' ),
		'section'     => 'medicare_clinic_mobile_media_option',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_related_post_mob',
	) ) );

	// Pro Version
    $wp_customize->add_setting( 'medicare_clinic_responsive_pro_version_logo', array(
        'sanitize_callback' => 'medicare_clinic_sanitize_custom_control'
    ));
    $wp_customize->add_control( new medicare_clinic_Customize_Pro_Version ( $wp_customize,'medicare_clinic_responsive_pro_version_logo', array(
        'section'     => 'medicare_clinic_mobile_media_option',
        'type'        => 'pro_options',
        'label'       => esc_html__( 'Features ', 'medicare-clinic' ),
        'description' => esc_url( MEDICARE_CLINIC_PRO_THEME_URL ),
        'priority'    => 100
    )));
	
	$wp_customize->get_setting( 'blogname' )->transport          = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport   = 'postMessage';

	//site Title
	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'Medicare_Clinic_Customize_partial_blogname',
	) );

	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'Medicare_Clinic_Customize_partial_blogdescription',
	) );

	$wp_customize->add_setting( 'medicare_clinic_site_title', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_site_title', array(
		'label'       => esc_html__( 'Show / Hide Site Title', 'medicare-clinic' ),
		'section'     => 'title_tagline',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_site_title',
	) ) );

	// logo site title size
	$wp_customize->add_setting('medicare_clinic_site_title_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_site_title_font_size',array(
		'label'	=> __('Site Title Font Size in PX','medicare-clinic'),
		'section'	=> 'title_tagline',
		'setting'	=> 'medicare_clinic_site_title_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
		    'step'             => 1,
			'min'              => 0,
			'max'              => 30,
			),
	));

	$wp_customize->add_setting( 'medicare_clinic_site_tagline_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_site_tagline_color', array(
			'label'     => __('Change Site Title Color', 'medicare-clinic'),
	    'section' => 'title_tagline',
	    'settings' => 'medicare_clinic_site_tagline_color',
  	)));

	$wp_customize->add_setting( 'medicare_clinic_site_tagline', array(
		'default'           => false,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_site_tagline', array(
		'label'       => esc_html__( 'Show / Hide Site Tagline', 'medicare-clinic' ),
		'section'     => 'title_tagline',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_site_tagline',
	) ) );

	// logo site tagline size
	$wp_customize->add_setting('medicare_clinic_site_tagline_font_size',array(
		'default'	=> '',
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_site_tagline_font_size',array(
		'label'	=> __('Site Tagline Font Size in PX','medicare-clinic'),
		'section'	=> 'title_tagline',
		'setting'	=> 'medicare_clinic_site_tagline_font_size',
		'type'	=> 'number',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 0,
			'max'              => 30,
		),
	));

	$wp_customize->add_setting( 'medicare_clinic_logo_tagline_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_logo_tagline_color', array(
			'label'     => __('Change Site Tagline Color', 'medicare-clinic'),
	    'section' => 'title_tagline',
	    'settings' => 'medicare_clinic_logo_tagline_color',
  	)));

    $wp_customize->add_setting('medicare_clinic_logo_width',array(
	   'default' => 80,
	   'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_logo_width',array(
		'label'	=> esc_html__('Here You Can Customize Your Logo Size','medicare-clinic'),
		'section'	=> 'title_tagline',
		'type'		=> 'number'
	));

	$wp_customize->add_setting('medicare_clinic_per_columns',array(
		'default'=> 3,
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_per_columns',array(
		'label'	=> __('Product Per Row','medicare-clinic'),
		'section'=> 'woocommerce_product_catalog',
		'type'=> 'number'
	));

	$wp_customize->add_setting('medicare_clinic_product_per_page',array(
		'default'=> 9,
		'sanitize_callback'	=> 'medicare_clinic_sanitize_number_absint'
	));
	$wp_customize->add_control('medicare_clinic_product_per_page',array(
		'label'	=> __('Product Per Page','medicare-clinic'),
		'section'=> 'woocommerce_product_catalog',
		'type'=> 'number'
	));

	$wp_customize->add_setting( 'medicare_clinic_product_sidebar', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_product_sidebar', array(
		'label'       => esc_html__( 'Show / Hide Shop Page Sidebar', 'medicare-clinic' ),
		'section'     => 'woocommerce_product_catalog',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_product_sidebar',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_single_product_sidebar', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_single_product_sidebar', array(
		'label'       => esc_html__( 'Show / Hide Product Page Sidebar', 'medicare-clinic' ),
		'section'     => 'woocommerce_product_catalog',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_single_product_sidebar',
	) ) );

	$wp_customize->add_setting( 'medicare_clinic_related_product', array(
		'default'           => true,
		'transport'         => 'refresh',
		'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	) );
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_related_product', array(
		'label'       => esc_html__( 'Show / Hide related product', 'medicare-clinic' ),
		'section'     => 'woocommerce_product_catalog',
		'type'        => 'toggle',
		'settings'    => 'medicare_clinic_related_product',
	) ) );

	
	//Page template settings
	$wp_customize->add_panel( 'medicare_clinic_page_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Page Template Settings', 'medicare-clinic' ),
	    'description' => __( 'Description of what this panel does.', 'medicare-clinic' ),
	) );

	// 404 PAGE
	$wp_customize->add_section('medicare_clinic_404_page_section',array(
		'title'         => __('404 Page', 'medicare-clinic'),
		'description'   => __('Here you can customize 404 Page content.', 'medicare-clinic'),
		'panel' => 'medicare_clinic_page_panel_id'
	) );

	$wp_customize->add_setting('medicare_clinic_edit_404_title',array(
		'default'=> __('Oops! That page cant be found.','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	$wp_customize->add_control('medicare_clinic_edit_404_title',array(
		'label'	=> __('Edit Title','medicare-clinic'),
		'section'=> 'medicare_clinic_404_page_section',
		'type'=> 'text',
	));

	$wp_customize->add_setting('medicare_clinic_edit_404_text',array(
		'default'=> __('It looks like nothing was found at this location. Maybe try a search?','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_edit_404_text',array(
		'label'	=> __('Edit Text','medicare-clinic'),
		'section'=> 'medicare_clinic_404_page_section',
		'type'=> 'text'
	));

	// Search Results
	$wp_customize->add_section('medicare_clinic_no_result_section',array(
		'title'         => __('Search Results', 'medicare-clinic'),
		'description'  => __('Here you can customize Search Result content.', 'medicare-clinic'),
		'panel' => 'medicare_clinic_page_panel_id'
	) );

	$wp_customize->add_setting('medicare_clinic_edit_no_result_title',array(
		'default'=> __('Nothing Found','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field',
	));
	$wp_customize->add_control('medicare_clinic_edit_no_result_title',array(
		'label'	=> __('Edit Title','medicare-clinic'),
		'section'=> 'medicare_clinic_no_result_section',
		'type'=> 'text',
	));

	$wp_customize->add_setting('medicare_clinic_edit_no_result_text',array(
		'default'=> __('Sorry, but nothing matched your search terms. Please try again with some different keywords.','medicare-clinic'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('medicare_clinic_edit_no_result_text',array(
		'label'	=> __('Edit Text','medicare-clinic'),
		'section'=> 'medicare_clinic_no_result_section',
		'type'=> 'text'
	));

	 // Header Image Height
    $wp_customize->add_setting(
        'medicare_clinic_header_image_height',
        array(
            'default'           => 500,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'medicare_clinic_header_image_height',
        array(
            'label'       => esc_html__( 'Header Image Height', 'medicare-clinic' ),
            'section'     => 'header_image',
            'type'        => 'number',
            'description' => esc_html__( 'Control the height of the header image. Default is 350px.', 'medicare-clinic' ),
            'input_attrs' => array(
                'min'  => 220,
                'max'  => 1000,
                'step' => 1,
            ),
        )
    );

    // Header Background Position
    $wp_customize->add_setting(
        'medicare_clinic_header_background_position',
        array(
            'default'           => 'center',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'medicare_clinic_header_background_position',
        array(
            'label'       => esc_html__( 'Header Background Position', 'medicare-clinic' ),
            'section'     => 'header_image',
            'type'        => 'select',
            'choices'     => array(
                'top'    => esc_html__( 'Top', 'medicare-clinic' ),
                'center' => esc_html__( 'Center', 'medicare-clinic' ),
                'bottom' => esc_html__( 'Bottom', 'medicare-clinic' ),
            ),
            'description' => esc_html__( 'Choose how you want to position the header image.', 'medicare-clinic' ),
        )
    );

    // Header Image Parallax Effect
    $wp_customize->add_setting(
        'medicare_clinic_header_background_attachment',
        array(
            'default'           => 1,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'medicare_clinic_header_background_attachment',
        array(
            'label'       => esc_html__( 'Header Image Parallax', 'medicare-clinic' ),
            'section'     => 'header_image',
            'type'        => 'checkbox',
            'description' => esc_html__( 'Add a parallax effect on page scroll.', 'medicare-clinic' ),
        )
    );

        //Opacity
	$wp_customize->add_setting('medicare_clinic_header_banner_opacity_color',array(
       'default'              => '0.5',
       'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
    $wp_customize->add_control( 'medicare_clinic_header_banner_opacity_color', array(
		'label'       => esc_html__( 'Header Image Opacity','medicare-clinic' ),
		'section'     => 'header_image',
		'type'        => 'select',
		'settings'    => 'medicare_clinic_header_banner_opacity_color',
		'choices' => array(
           '0' =>  esc_attr(__('0','medicare-clinic')),
           '0.1' =>  esc_attr(__('0.1','medicare-clinic')),
           '0.2' =>  esc_attr(__('0.2','medicare-clinic')),
           '0.3' =>  esc_attr(__('0.3','medicare-clinic')),
           '0.4' =>  esc_attr(__('0.4','medicare-clinic')),
           '0.5' =>  esc_attr(__('0.5','medicare-clinic')),
           '0.6' =>  esc_attr(__('0.6','medicare-clinic')),
           '0.7' =>  esc_attr(__('0.7','medicare-clinic')),
           '0.8' =>  esc_attr(__('0.8','medicare-clinic')),
           '0.9' =>  esc_attr(__('0.9','medicare-clinic'))
		), 
	) );

   $wp_customize->add_setting( 'medicare_clinic_header_banner_image_overlay', array(
	    'default'   => true,
	    'transport' => 'refresh',
	    'sanitize_callback' => 'medicare_clinic_sanitize_checkbox',
	));
	$wp_customize->add_control( new Medicare_Clinic_Toggle_Control( $wp_customize, 'medicare_clinic_header_banner_image_overlay', array(
	    'label'   => esc_html__( 'Show / Hide Header Image Overlay', 'medicare-clinic' ),
	    'section' => 'header_image',
	)));

    $wp_customize->add_setting('medicare_clinic_header_banner_image_ooverlay_color', array(
		'default'           => '#000',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'medicare_clinic_header_banner_image_ooverlay_color', array(
		'label'    => __('Header Image Overlay Color', 'medicare-clinic'),
		'section'  => 'header_image',
	)));

    $wp_customize->add_setting(
        'medicare_clinic_header_image_title_font_size',
        array(
            'default'           => 40,
            'sanitize_callback' => 'absint',
        )
    );
    $wp_customize->add_control(
        'medicare_clinic_header_image_title_font_size',
        array(
            'label'       => esc_html__( 'Change Header Image Title Font Size', 'medicare-clinic' ),
            'section'     => 'header_image',
            'type'        => 'number',
            'description' => esc_html__( 'Control the font Size of the header image title. Default is 40px.', 'medicare-clinic' ),
            'input_attrs' => array(
                'min'  => 10,
                'max'  => 200,
                'step' => 1,
            ),
        )
    );

	$wp_customize->add_setting( 'medicare_clinic_header_image_title_text_color', array(
	    'default' => '',
	    'sanitize_callback' => 'sanitize_hex_color'
  	));
  	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_clinic_header_image_title_text_color', array(
			'label'     => __('Change Header Image Title Color', 'medicare-clinic'),
	    'section' => 'header_image',
	    'settings' => 'medicare_clinic_header_image_title_text_color',
  	)));

  	//Woocommerce settings
	$wp_customize->add_section('medicare_clinic_woocommerce_section', array(
		'title'    => __('WooCommerce Options', 'medicare-clinic'),
		'priority' => null,
		'panel'    => 'woocommerce',
	));

	$wp_customize->add_setting('medicare_clinic_sale_tag_position',array(
        'default' => 'right',
        'sanitize_callback' => 'medicare_clinic_sanitize_choices'
	));
	$wp_customize->add_control('medicare_clinic_sale_tag_position',array(
        'type' => 'radio',
        'label'     => __('Sale Badge Position', 'medicare-clinic'),
        'description'   => __('This option work for Archieve Products', 'medicare-clinic'),
        'section' => 'medicare_clinic_woocommerce_section',
        'choices' => array(
            'left' => __('Left','medicare-clinic'),
            'right' => __('Right','medicare-clinic'),
        ),
	) );

  	$wp_customize->add_setting('medicare_clinic_woocommerce_sale_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('medicare_clinic_woocommerce_sale_font_size',array(
		'label'	=> __('Sale Font Size','medicare-clinic'),

		'section'=> 'medicare_clinic_woocommerce_section',
		'settings'    => 'medicare_clinic_woocommerce_sale_font_size',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting('medicare_clinic_woocommerce_sale_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('medicare_clinic_woocommerce_sale_padding_top_bottom',array(
		'label'	=> __('Sale Padding Top Bottom','medicare-clinic'),
		'section'=> 'medicare_clinic_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting('medicare_clinic_woocommerce_sale_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'absint'
	));
	$wp_customize->add_control('medicare_clinic_woocommerce_sale_padding_left_right',array(
		'label'	=> __('Sale Padding Left Right','medicare-clinic'),
		'section'=> 'medicare_clinic_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	));

	$wp_customize->add_setting( 'medicare_clinic_woocommerce_sale_border_radius', array(
		'default'              => '100',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'absint'
	) );
	$wp_customize->add_control( 'medicare_clinic_woocommerce_sale_border_radius', array(
		'label'       => esc_html__( 'Sale Border Radius','medicare-clinic' ),
		'section'     => 'medicare_clinic_woocommerce_section',
		'type'        => 'number',
		'input_attrs' => array(
			'step'             => 2,
			'min'              => 0,
			'max'              => 100,
		),
	) );

}
add_action( 'customize_register', 'medicare_clinic_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Medicare Clinic 1.0
 * @see medicare_clinic_customize_register()
 *
 * @return void
 */
function Medicare_Clinic_Customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Medicare Clinic 1.0
 * @see medicare_clinic_customize_register()
 *
 * @return void
 */
function Medicare_Clinic_Customize_partial_blogdescription() {
	bloginfo( 'description' );
}

if ( ! defined( 'MEDICARE_CLINIC_PRO_THEME_NAME' ) ) {
	define( 'MEDICARE_CLINIC_PRO_THEME_NAME', esc_html__( 'Medicare Clinic Pro', 'medicare-clinic'));
}
if ( ! defined( 'MEDICARE_CLINIC_PRO_THEME_URL' ) ) {
	define( 'MEDICARE_CLINIC_PRO_THEME_URL', esc_url('https://www.themespride.com/products/clinic-wordpress-theme', 'medicare-clinic'));
}


if ( ! defined( 'MEDICARE_CLINIC_DOCS_URL' ) ) {
	define( 'MEDICARE_CLINIC_DOCS_URL', esc_url('https://page.themespride.com/demo/docs/medicare-clinic-lite/'));
}
if ( ! defined( 'MEDICARE_CLINIC_TEXT' ) ) {
    define( 'MEDICARE_CLINIC_TEXT', __( 'Medicare Clinic Pro','medicare-clinic' ));
}
if ( ! defined( 'MEDICARE_CLINIC_BUY_TEXT' ) ) {
    define( 'MEDICARE_CLINIC_BUY_TEXT', __( 'Upgrade Pro','medicare-clinic' ));
}


add_action( 'customize_register', function( $manager ) {

// Load custom sections.
load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

    $manager->register_section_type( medicare_clinic_Button::class );

    $manager->add_section(
        new medicare_clinic_Button( $manager, 'medicare_clinic_pro', [
            'title'       => esc_html( MEDICARE_CLINIC_TEXT,'medicare-clinic' ),
            'priority'    => 0,
            'button_text' => __( 'GET PREMIUM', 'medicare-clinic' ),
            'button_url'  => esc_url( MEDICARE_CLINIC_PRO_THEME_URL )
        ] )
    );

} );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Medicare_Clinic_Customize {

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
		$manager->register_section_type( 'Medicare_Clinic_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Medicare_Clinic_Customize_Section_Pro(
				$manager,
				'medicare_clinic_section_pro',
				array(
					'priority'   => 9,
					'title'    => MEDICARE_CLINIC_PRO_THEME_NAME,
					'pro_text' => esc_html__( 'Upgrade Pro', 'medicare-clinic' ),
					'pro_url'  => esc_url( MEDICARE_CLINIC_PRO_THEME_URL, 'medicare-clinic' ),
				)
			)
		);

		// Register sections.
		$manager->add_section(
			new medicare_clinic_Customize_Section_Pro(
				$manager,
				'medicare_clinic_documentation',
				array(
					'priority'   => 500,
					'title'    => esc_html__( 'Theme Documentation', 'medicare-clinic' ),
					'pro_text' => esc_html__( 'Click Here', 'medicare-clinic' ),
					'pro_url'  => esc_url( MEDICARE_CLINIC_DOCS_URL, 'medicare-clinic'),
				)
			)
		);

	}
	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'medicare-clinic-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'medicare-clinic-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
Medicare_Clinic_Customize::get_instance();