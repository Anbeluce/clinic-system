<div class="theme-offer">
	<?php 
        // Check if the demo import has been completed
        $vw_hospital_lite_demo_import_completed = get_option('vw_hospital_lite_demo_import_completed', false);

        // If the demo import is completed, display the "View Site" button
        if ($vw_hospital_lite_demo_import_completed) {
        echo '<p class="notice-text">' . esc_html__('Your demo import has been completed successfully.', 'vw-hospital-lite') . '</p>';
        echo '<span><a href="' . esc_url(home_url()) . '" class="button button-primary site-btn" target="_blank">' . esc_html__('View Site', 'vw-hospital-lite') . '</a></span>';
    }

	// POST and update the customizer and other related data
    if (isset($_POST['submit'])) {


        // Check if ibtana visual editor is installed and activated
        if (!is_plugin_active('ibtana-visual-editor/plugin.php')) {
          // Install the plugin if it doesn't exist
          $vw_hospital_lite_plugin_slug = 'ibtana-visual-editor';
          $vw_hospital_lite_plugin_file = 'ibtana-visual-editor/plugin.php';

          // Check if plugin is installed
          $vw_hospital_lite_installed_plugins = get_plugins();
          if (!isset($vw_hospital_lite_installed_plugins[$vw_hospital_lite_plugin_file])) {
              include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
              include_once(ABSPATH . 'wp-admin/includes/file.php');
              include_once(ABSPATH . 'wp-admin/includes/misc.php');
              include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

              // Install the plugin
              $vw_hospital_lite_upgrader = new Plugin_Upgrader();
              $vw_hospital_lite_upgrader->install('https://downloads.wordpress.org/plugin/ibtana-visual-editor.latest-stable.zip');
          }
          // Activate the plugin
          activate_plugin($vw_hospital_lite_plugin_file);
        }


            // ------- Create Nav Menu --------
            $vw_hospital_lite_menuname = 'Main Menus';
            $vw_hospital_lite_bpmenulocation = 'primary';
            $vw_hospital_lite_menu_exists = wp_get_nav_menu_object($vw_hospital_lite_menuname);

            if (!$vw_hospital_lite_menu_exists) {
                $vw_hospital_lite_menu_id = wp_create_nav_menu($vw_hospital_lite_menuname);

                // Create Home Page
                $vw_hospital_lite_home_title = 'Home';
                $vw_hospital_lite_home = array(
                    'post_type' => 'page',
                    'post_title' => $vw_hospital_lite_home_title,
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_slug' => 'home'
                );
                $vw_hospital_lite_home_id = wp_insert_post($vw_hospital_lite_home);
                // Assign Home Page Template
                add_post_meta($vw_hospital_lite_home_id, '_wp_page_template', 'page-template/custom-front.php');
                // Update options to set Home Page as the front page
                update_option('page_on_front', $vw_hospital_lite_home_id);
                update_option('show_on_front', 'page');
                // Add Home Page to Menu
                wp_update_nav_menu_item($vw_hospital_lite_menu_id, 0, array(
                    'menu-item-title' => __('Home', 'vw-hospital-lite'),
                    'menu-item-classes' => 'home',
                    'menu-item-url' => home_url('/'),
                    'menu-item-status' => 'publish',
                    'menu-item-object-id' => $vw_hospital_lite_home_id,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type'
                ));

                // Create Pages Page with Dummy Content
                $vw_hospital_lite_pages_title = 'Pages';
                $vw_hospital_lite_pages_content = '
                Explore all the pages we have on our website. Find information about our services, company, and more.
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br>
                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
                $vw_hospital_lite_pages = array(
                    'post_type' => 'page',
                    'post_title' => $vw_hospital_lite_pages_title,
                    'post_content' => $vw_hospital_lite_pages_content,
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_slug' => 'pages'
                );
                $vw_hospital_lite_pages_id = wp_insert_post($vw_hospital_lite_pages);
                // Add Pages Page to Menu
                wp_update_nav_menu_item($vw_hospital_lite_menu_id, 0, array(
                    'menu-item-title' => __('Pages', 'vw-hospital-lite'),
                    'menu-item-classes' => 'pages',
                    'menu-item-url' => home_url('/pages/'),
                    'menu-item-status' => 'publish',
                    'menu-item-object-id' => $vw_hospital_lite_pages_id,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type'
                ));

                // Create About Us Page with Dummy Content
                $vw_hospital_lite_about_title = 'About Us';
                $vw_hospital_lite_about_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam...<br>
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.<br>
                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text.<br>
                All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.';
                $vw_hospital_lite_about = array(
                    'post_type' => 'page',
                    'post_title' => $vw_hospital_lite_about_title,
                    'post_content' => $vw_hospital_lite_about_content,
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_slug' => 'about-us'
                );
                $vw_hospital_lite_about_id = wp_insert_post($vw_hospital_lite_about);
                // Add About Us Page to Menu
                wp_update_nav_menu_item($vw_hospital_lite_menu_id, 0, array(
                    'menu-item-title' => __('About Us', 'vw-hospital-lite'),
                    'menu-item-classes' => 'about-us',
                    'menu-item-url' => home_url('/about-us/'),
                    'menu-item-status' => 'publish',
                    'menu-item-object-id' => $vw_hospital_lite_about_id,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type'
                ));

                // Set the menu location if it's not already set
                if (!has_nav_menu($vw_hospital_lite_bpmenulocation)) {
                    $locations = get_theme_mod('nav_menu_locations'); // Use 'nav_menu_locations' to get locations array
                    if (empty($locations)) {
                        $locations = array();
                    }
                    $locations[$vw_hospital_lite_bpmenulocation] = $vw_hospital_lite_menu_id;
                    set_theme_mod('nav_menu_locations', $locations);
                }
            }
        
            // Set the demo import completion flag
    		update_option('vw_hospital_lite_demo_import_completed', true);
    		// Display success message and "View Site" button
    		echo '<p class="notice-text">' . esc_html__('Your demo import has been completed successfully.', 'vw-hospital-lite') . '</p>';
    		echo '<span><a href="' . esc_url(home_url()) . '" class="button button-primary site-btn" target="_blank">' . esc_html__('View Site', 'vw-hospital-lite') . '</a></span>';
            //end 


            // Top Bar //
            set_theme_mod( 'vw_hospital_lite_cont_phone_icon', 'fas fa-phone' ); 
            set_theme_mod( 'vw_hospital_lite_contact_call', 'Call Us : 1234567890' );   
            set_theme_mod( 'vw_hospital_lite_cont_email_icon', 'fas fa-envelope' ); 
            set_theme_mod( 'vw_hospital_lite_contact_email', 'Email Us : support@example.com' );
            set_theme_mod( 'vw_hospital_lite_appointments_icon', 'fas fa-plus-circle' ); 
            set_theme_mod( 'vw_hospital_lite_appointment1', 'Book an appointment at 10am' );
            set_theme_mod( 'vw_hospital_lite_appointment', '#' ); 
            set_theme_mod( 'vw_hospital_lite_timing_icon', 'fas fa-ambulance' );
            set_theme_mod( 'vw_hospital_lite_timing1', 'Opening Timing : Monday to Saturday 10am - 7pm' );
            set_theme_mod( 'vw_hospital_lite_timing', '#' );

            // slider section start //  
            set_theme_mod( 'vw_hospital_lite_slider_button_text', 'READ MORE' );
            set_theme_mod( 'vw_hospital_lite_slider_button_link', '#' );

            for($vw_hospital_lite_i=1;$vw_hospital_lite_i<=3;$vw_hospital_lite_i++){
               $vw_hospital_lite_slider_title = 'LOREM IMPSUM IS SIMPLY DUMMY TEST OF THE PRINTING';
               $vw_hospital_lite_slider_content = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ';
                  // Create post object
               $my_post = array(
               'post_title'    => wp_strip_all_tags( $vw_hospital_lite_slider_title ),
               'post_content'  => $vw_hospital_lite_slider_content,
               'post_status'   => 'publish',
               'post_type'     => 'page',
               );

               // Insert the post into the database
               $vw_hospital_lite_post_id = wp_insert_post( $my_post );

               if ($vw_hospital_lite_post_id) {
                 // Set the theme mod for the slider page
                 set_theme_mod('vw_hospital_lite_slider_page' . $vw_hospital_lite_i, $vw_hospital_lite_post_id);

                  $vw_hospital_lite_image_url = get_template_directory_uri().'/images/slider'.$vw_hospital_lite_i.'.png';

                $vw_hospital_lite_image_id = media_sideload_image($vw_hospital_lite_image_url, $vw_hospital_lite_post_id, null, 'id');

                    if (!is_wp_error($vw_hospital_lite_image_id)) {
                        // Set the downloaded image as the post's featured image
                        set_post_thumbnail($vw_hospital_lite_post_id, $vw_hospital_lite_image_id);
                    }
                }
            } 


            // Service Section //
            set_theme_mod( 'vw_hospital_lite_sec1_title', 'OUR SERVICES' );
            set_theme_mod( 'vw_hospital_lite_services_button_text', 'READ MORE' );

            $vw_hospital_lite_service_title_array = array("SERVICE TITLE 1", "SERVICE TITLE 2", "SERVICE TITLE 3", "SERVICE TITLE 4");

            for($vw_hospital_lite_i=1;$vw_hospital_lite_i<=4;$vw_hospital_lite_i++){
                $vw_hospital_lite_service_title = $vw_hospital_lite_service_title_array[$vw_hospital_lite_i - 1];

               $vw_hospital_lite_service_content = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ';
                  // Create post object
               $my_post = array(
               'post_title'    => wp_strip_all_tags( $vw_hospital_lite_service_title ),
               'post_content'  => $vw_hospital_lite_service_content,
               'post_status'   => 'publish',
               'post_type'     => 'page',
               );

               // Insert the post into the database
               $vw_hospital_lite_post_id = wp_insert_post( $my_post );

               if ($vw_hospital_lite_post_id) {
                 // Set the theme mod for the service page
                 set_theme_mod('vw_hospital_lite_servicesettings' . $vw_hospital_lite_i, $vw_hospital_lite_post_id);

                  $vw_hospital_lite_image_url = get_template_directory_uri().'/images/service'.$vw_hospital_lite_i.'.png';

                $vw_hospital_lite_image_id = media_sideload_image($vw_hospital_lite_image_url, $vw_hospital_lite_post_id, null, 'id');

                    if (!is_wp_error($vw_hospital_lite_image_id)) {
                        // Set the downloaded image as the post's featured image
                        set_post_thumbnail($vw_hospital_lite_post_id, $vw_hospital_lite_image_id);
                    }
                }
            } 

        }
    ?>
  
	<p><?php esc_html_e('Please back up your website if it’s already live with data. This importer will overwrite your existing settings with the new customizer values for VW Hospital Lite', 'vw-hospital-lite'); ?></p>
    <form action="<?php echo esc_url(home_url()); ?>/wp-admin/themes.php?page=vw_hospital_lite_guide" method="POST" onsubmit="return validate(this);">
        <?php if (!get_option('vw_hospital_lite_demo_import_completed')) : ?>
            <input class="run-import" type="submit" name="submit" value="<?php esc_attr_e('Run Importer', 'vw-hospital-lite'); ?>" class="button button-primary button-large">
        <?php endif; ?>
        <div id="spinner" style="display:none;">         
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/spinner.png" alt="" />
        </div>
    </form>
    <script type="text/javascript">
        function validate(form) {
            if (confirm("Do you really want to import the theme demo content?")) {
                // Show the spinner
                document.getElementById('spinner').style.display = 'block';
                // Allow the form to be submitted
                return true;
            } 
            else {
                return false;
            }
        }
    </script>
</div>

