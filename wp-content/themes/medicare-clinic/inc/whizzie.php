<?php 
if (isset($_GET['import-demo']) && $_GET['import-demo'] == true) {

    function medicare_clinic_import_demo_content() {
        
        // Display the preloader only for plugin installation
        echo '<div id="plugin-loader" style="display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999;">
                <img src="' . esc_url(get_template_directory_uri()) . '/assets/images/loader.png" alt="Loading..." width="60" height="60" />
              </div>';

        // Define the plugins you want to install and activate
        $plugins = array(
            array(
                'slug' => 'advanced-appointment-booking-scheduling',
                'file' => 'advanced-appointment-booking-scheduling/advanced-appointment-booking.php',
                'url'  => 'https://downloads.wordpress.org/plugin/advanced-appointment-booking-scheduling.zip'
            ),
        );

        // Include required files for plugin installation
        include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
        include_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/misc.php');
        include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

        // Loop through each plugin
        foreach ($plugins as $plugin) {
            $plugin_file = WP_PLUGIN_DIR . '/' . $plugin['file'];

            // Check if the plugin is installed
            if (!file_exists($plugin_file)) {
                // If the plugin is not installed, download and install it
                $upgrader = new Plugin_Upgrader();
                $result = $upgrader->install($plugin['url']);

                // Check for installation errors
                if (is_wp_error($result)) {
                    error_log('Plugin installation failed: ' . $plugin['slug'] . ' - ' . $result->get_error_message());
                    echo 'Error installing plugin: ' . esc_html($plugin['slug']) . ' - ' . esc_html($result->get_error_message());
                    continue;
                }
            }

            // If the plugin exists but is not active, activate it
            if (file_exists($plugin_file) && !is_plugin_active($plugin['file'])) {
                $result = activate_plugin($plugin['file']);

                // Check for activation errors
                if (is_wp_error($result)) {
                    error_log('Plugin activation failed: ' . $plugin['slug'] . ' - ' . $result->get_error_message());
                    echo 'Error activating plugin: ' . esc_html($plugin['slug']) . ' - ' . esc_html($result->get_error_message());
                }
            }
        }

        // Hide the preloader after the process is complete
        echo '<script type="text/javascript">
                document.getElementById("plugin-loader").style.display = "none";
              </script>';

        // Add filter to skip WooCommerce setup wizard after activation
        add_filter('woocommerce_prevent_automatic_wizard_redirect', '__return_true');
    }

    // Call the import function
    medicare_clinic_import_demo_content();

    // ------- Create Nav Menu --------
$medicare_clinic_menuname = 'Main Menus';
$medicare_clinic_bpmenulocation = 'primary-menu';
$medicare_clinic_menu_exists = wp_get_nav_menu_object($medicare_clinic_menuname);

if (!$medicare_clinic_menu_exists) {
    $medicare_clinic_menu_id = wp_create_nav_menu($medicare_clinic_menuname);

    // Create Home Page
    $medicare_clinic_home_title = 'Home';
    $medicare_clinic_home = array(
        'post_type' => 'page',
        'post_title' => $medicare_clinic_home_title,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'home'
    );
    $medicare_clinic_home_id = wp_insert_post($medicare_clinic_home);

    // Assign Home Page Template
    add_post_meta($medicare_clinic_home_id, '_wp_page_template', 'page-template/front-page.php');

    // Update options to set Home Page as the front page
    update_option('page_on_front', $medicare_clinic_home_id);
    update_option('show_on_front', 'page');

    // Add Home Page to Menu
    wp_update_nav_menu_item($medicare_clinic_menu_id, 0, array(
        'menu-item-title' => __('Home', 'medicare-clinic'),
        'menu-item-classes' => 'home',
        'menu-item-url' => home_url('/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $medicare_clinic_home_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create About Us Page with Dummy Content
    $medicare_clinic_about_title = 'About Us';
    $medicare_clinic_about_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $medicare_clinic_about = array(
        'post_type' => 'page',
        'post_title' => $medicare_clinic_about_title,
        'post_content' => $medicare_clinic_about_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'about-us'
    );
    $medicare_clinic_about_id = wp_insert_post($medicare_clinic_about);

    // Add About Us Page to Menu
    wp_update_nav_menu_item($medicare_clinic_menu_id, 0, array(
        'menu-item-title' => __('About Us', 'medicare-clinic'),
        'menu-item-classes' => 'about-us',
        'menu-item-url' => home_url('/about-us/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $medicare_clinic_about_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Services Page with Dummy Content
    $medicare_clinic_services_title = 'Services';
    $medicare_clinic_services_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $medicare_clinic_services = array(
        'post_type' => 'page',
        'post_title' => $medicare_clinic_services_title,
        'post_content' => $medicare_clinic_services_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'services'
    );
    $medicare_clinic_services_id = wp_insert_post($medicare_clinic_services);

    // Add Services Page to Menu
    wp_update_nav_menu_item($medicare_clinic_menu_id, 0, array(
        'menu-item-title' => __('Services', 'medicare-clinic'),
        'menu-item-classes' => 'services',
        'menu-item-url' => home_url('/services/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $medicare_clinic_services_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Pages Page with Dummy Content
    $medicare_clinic_pages_title = 'Pages';
    $medicare_clinic_pages_content = '<h2>Our Pages</h2>
    <p>Explore all the pages we have on our website. Find information about our services, company, and more.</p>';
    $medicare_clinic_pages = array(
        'post_type' => 'page',
        'post_title' => $medicare_clinic_pages_title,
        'post_content' => $medicare_clinic_pages_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'pages'
    );
    $medicare_clinic_pages_id = wp_insert_post($medicare_clinic_pages);

    // Add Pages Page to Menu
    wp_update_nav_menu_item($medicare_clinic_menu_id, 0, array(
        'menu-item-title' => __('Pages', 'medicare-clinic'),
        'menu-item-classes' => 'pages',
        'menu-item-url' => home_url('/pages/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $medicare_clinic_pages_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Create Contact Page with Dummy Content
    $medicare_clinic_contact_title = 'Contact';
    $medicare_clinic_contact_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>

             Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br> 

                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br> 

                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
    $medicare_clinic_contact = array(
        'post_type' => 'page',
        'post_title' => $medicare_clinic_contact_title,
        'post_content' => $medicare_clinic_contact_content,
        'post_status' => 'publish',
        'post_author' => 1,
        'post_slug' => 'contact'
    );
    $medicare_clinic_contact_id = wp_insert_post($medicare_clinic_contact);

    // Add Contact Page to Menu
    wp_update_nav_menu_item($medicare_clinic_menu_id, 0, array(
        'menu-item-title' => __('Contact', 'medicare-clinic'),
        'menu-item-classes' => 'contact',
        'menu-item-url' => home_url('/contact/'),
        'menu-item-status' => 'publish',
        'menu-item-object-id' => $medicare_clinic_contact_id,
        'menu-item-object' => 'page',
        'menu-item-type' => 'post_type'
    ));

    // Set the menu location if it's not already set
    if (!has_nav_menu($medicare_clinic_bpmenulocation)) {
        $locations = get_theme_mod('nav_menu_locations'); // Use 'nav_menu_locations' to get locations array
        if (empty($locations)) {
            $locations = array();
        }
        $locations[$medicare_clinic_bpmenulocation] = $medicare_clinic_menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

        //---Header--//
        set_theme_mod('medicare_clinic_header_button', 'APPOINTMENT');
        set_theme_mod('medicare_clinic_header_link', '#');

        // Slider Section
        set_theme_mod('medicare_clinic_slider_arrows', true);
        set_theme_mod('medicare_clinic_slider_short_heading', 'Healthcare');


        set_theme_mod('medicare_clinic_about_call_text', 'Emergency call');
        set_theme_mod('medicare_clinic_about_call', '1800-818-6767');
        set_theme_mod('medicare_clinic_slider_add_time', '24 Hours');

        for ($i = 1; $i <= 4; $i++) {
            $medicare_clinic_title = 'Take Care of your Health with us!';
            $medicare_clinic_content = 'Our team of experienced doctors and healthcare professionals is dedicated to providing personalized care tailored to your unique needs...';

            // Create post object
            $my_post = array(
                'post_title'    => wp_strip_all_tags($medicare_clinic_title),
                'post_content'  => $medicare_clinic_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            );

            /// Insert the post into the database
            $post_id = wp_insert_post($my_post);

            if ($post_id) {
                // Set the theme mod for the slider page
                set_theme_mod('medicare_clinic_slider_page' . $i, $post_id);

                $image_url = get_template_directory_uri() . '/assets/images/slider-img.png';
                $image_id = media_sideload_image($image_url, $post_id, null, 'id');

                if (!is_wp_error($image_id)) {
                    // Set the downloaded image as the post's featured image
                    set_post_thumbnail($post_id, $image_id);
                }
            }
        }

        // Our Services Section //
        set_theme_mod('medicare_clinic_offer_section_tittle', 'Our Services');
        set_theme_mod('medicare_clinic_offer_section_text', 'Our Mediax specialties Technical service');
        set_theme_mod('medicare_clinic_offer_section_category', 'postcategory1');

        set_theme_mod('medicare_clinic_team_role1', 'Cardiology');
        set_theme_mod('medicare_clinic_team_role2', 'Urology');
        set_theme_mod('medicare_clinic_team_role3', 'Surgery');
        set_theme_mod('medicare_clinic_team_role4', 'Neurosurgery');

        set_theme_mod('medicare_clinic_serv_facebook_url1', '#');
        set_theme_mod('medicare_clinic_serv_facebook_url2', '#');
        set_theme_mod('medicare_clinic_serv_facebook_url3', '#');
        set_theme_mod('medicare_clinic_serv_facebook_url4', '#');

        set_theme_mod('medicare_clinic_serv_insta_url1', '#');
        set_theme_mod('medicare_clinic_serv_insta_url2', '#');
        set_theme_mod('medicare_clinic_serv_insta_url3', '#');
        set_theme_mod('medicare_clinic_serv_insta_url4', '#');

        set_theme_mod('medicare_clinic_serv_youtube_url1', '#');
        set_theme_mod('medicare_clinic_serv_youtube_url2', '#');
        set_theme_mod('medicare_clinic_serv_youtube_url3', '#');
        set_theme_mod('medicare_clinic_serv_youtube_url4', '#');

        set_theme_mod('medicare_clinic_serv_snapchat_url1', '#');
        set_theme_mod('medicare_clinic_serv_snapchat_url2', '#');
        set_theme_mod('medicare_clinic_serv_snapchat_url3', '#');
        set_theme_mod('medicare_clinic_serv_snapchat_url4', '#');

        // Define post category names and post titles
        $medicare_clinic_category_names = array('postcategory1');
        $medicare_clinic_title_array = array(
            array(
                "Dr. Vera Hasson",
                "Dr. Philip Bailey",
                "Dr. Jeanette Hoff",
                "Dr.Matthew Hill"
            )
        );

        foreach ($medicare_clinic_category_names as $medicare_clinic_index => $medicare_clinic_category_name) {
            // Create or retrieve the post category term ID
            $medicare_clinic_term = term_exists($medicare_clinic_category_name, 'category');
            if ($medicare_clinic_term === 0 || $medicare_clinic_term === null) {
                // If the term does not exist, create it
                $medicare_clinic_term = wp_insert_term($medicare_clinic_category_name, 'category');
            }
            if (is_wp_error($medicare_clinic_term)) {
                error_log('Error creating category: ' . $medicare_clinic_term->get_error_message());
                continue; // Skip to the next iteration if category creation fails
            }

            // Ensure we get the term ID
            $medicare_clinic_term_id = is_array($medicare_clinic_term) ? $medicare_clinic_term['term_id'] : $medicare_clinic_term;

            // Get titles for this category
            $medicare_clinic_titles = $medicare_clinic_title_array[$medicare_clinic_index];

            for ($medicare_clinic_i = 0; $medicare_clinic_i < count($medicare_clinic_titles); $medicare_clinic_i++) {
                // Create post content
                $medicare_clinic_title = $medicare_clinic_titles[$medicare_clinic_i];

                // Create post post object
                $medicare_clinic_my_post = array(
                    'post_title'    => wp_strip_all_tags($medicare_clinic_title),
                    'post_content'  => $medicare_clinic_content,
                    'post_status'   => 'publish',
                    'post_type'     => 'post', // Post type set to 'post'
                );

                // Insert the post into the database
                $medicare_clinic_post_id = wp_insert_post($medicare_clinic_my_post);

                if (is_wp_error($medicare_clinic_post_id)) {
                    error_log('Error creating post: ' . $medicare_clinic_post_id->get_error_message());
                    continue; // Skip to the next post if creation fails
                }

                // Assign the category to the post
                wp_set_post_categories($medicare_clinic_post_id, array((int)$medicare_clinic_term_id));

                // Handle the featured image using media_sideload_image
                $medicare_clinic_image_url = get_stylesheet_directory_uri() . '/assets/images/post-img' . ($medicare_clinic_i + 1) . '.png';
                $medicare_clinic_image_id = media_sideload_image($medicare_clinic_image_url, $medicare_clinic_post_id, null, 'id');

                if (is_wp_error($medicare_clinic_image_id)) {
                    error_log('Error downloading image: ' . $medicare_clinic_image_id->get_error_message());
                    continue; // Skip to the next post if image download fails
                }

                // Assign featured image to post
                set_post_thumbnail($medicare_clinic_post_id, $medicare_clinic_image_id);
            }
        }

    }
?>