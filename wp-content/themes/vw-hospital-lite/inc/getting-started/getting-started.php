<?php
//about theme info
add_action( 'admin_menu', 'vw_hospital_lite_gettingstarted' );
function vw_hospital_lite_gettingstarted() {    	
	add_theme_page( esc_html__('About VW Hospital Theme', 'vw-hospital-lite'), esc_html__('Theme Demo Import', 'vw-hospital-lite'), 'edit_theme_options', 'vw_hospital_lite_guide', 'vw_hospital_lite_mostrar_guide');   
}

// Add a Custom CSS file to WP Admin Area
function vw_hospital_lite_admin_theme_style() {
   wp_enqueue_style('vw-hospital-lite-custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/getting-started/getting-started.css');
   wp_enqueue_script('vw-hospital-lite-tabs', esc_url(get_template_directory_uri()) . '/inc/getting-started/js/tab.js');

   // Admin notice code START
	wp_register_script('vw-hospital-lite-notice', esc_url(get_template_directory_uri()) . '/inc/getting-started/js/notice.js', array('jquery'), time(), true);
	wp_enqueue_script('vw-hospital-lite-notice');
	// Admin notice code END
}
add_action('admin_enqueue_scripts', 'vw_hospital_lite_admin_theme_style');

//guidline for about theme
function vw_hospital_lite_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'vw-hospital-lite' );
?>

<div class="wrapper-info">
    <div class="col-left sshot-section">
    	<h2><?php esc_html_e( 'Welcome to VW Hospital Theme', 'vw-hospital-lite' ); ?> <span class="version">Version: <?php echo esc_html($theme['Version']);?></span></h2>
    	<p><?php esc_html_e('All our WordPress themes are modern, minimalist, 100% responsive, seo-friendly,feature-rich, and multipurpose that best suit designers, bloggers and other professionals who are working in the creative fields.','vw-hospital-lite'); ?></p>
    </div>
    <div class="col-right coupen-section">
    	<div class="logo-section">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/screenshot.png" alt="" />
		</div>
		<div class="logo-right">			
			<div class="update-now">
				<div class="theme-info">
					<div class="theme-info-left">
						<h2><?php esc_html_e('TRY PREMIUM','vw-hospital-lite'); ?></h2>
						<h4><?php esc_html_e('VW HOSPITAL LITE THEME','vw-hospital-lite'); ?></h4>
					</div>	
					<div class="theme-info-right"></div>
				</div>	
				<div class="dicount-row">
					<div class="disc-sec">	
						<h5 class="disc-text"><?php esc_html_e('GET THE FLAT DISCOUNT OF','vw-hospital-lite'); ?></h5>
						<h1 class="disc-per"><?php esc_html_e('20%','vw-hospital-lite'); ?></h1>	
					</div>
					<div class="coupen-info">
						<h5 class="coupen-code"><?php esc_html_e('"VWPRO20"','vw-hospital-lite'); ?></h5>
						<h5 class="coupen-text"><?php esc_html_e('USE COUPON CODE','vw-hospital-lite'); ?></h5>
						<div class="info-link">						
							<a href="<?php echo esc_url( VW_HOSPITAL_LITE_BUY_NOW ); ?>" target="_blank"> <?php esc_html_e( 'UPGRADE TO PRO', 'vw-hospital-lite' ); ?></a>
						</div>	
					</div>	
				</div>				
			</div>
		</div> 
		
    </div>

    <div class="tab-sec">
		<div class="tab">
			<button class="tablinks" onclick="vw_hospital_lite_open_tab(event, 'theme_offer')"><?php esc_html_e( 'Demo Importer', 'vw-hospital-lite' ); ?></button>
			<button class="tablinks" onclick="vw_hospital_lite_open_tab(event, 'lite_theme')"><?php esc_html_e( 'Setup With Customizer', 'vw-hospital-lite' ); ?></button>
			
		  	<button class="tablinks" onclick="vw_hospital_lite_open_tab(event, 'pro_theme')"><?php esc_html_e( 'Get Premium', 'vw-hospital-lite' ); ?></button>
		  	<button class="tablinks" onclick="vw_hospital_lite_open_tab(event, 'free_pro')"><?php esc_html_e( 'Free Vs Premium', 'vw-hospital-lite' ); ?></button>
		  	<button class="tablinks" onclick="vw_hospital_lite_open_tab(event, 'get_bundle')"><?php esc_html_e( 'Get 400+ Themes Bundle at $99', 'vw-hospital-lite' ); ?></button>
		</div>

		<!-- Tab content -->
		<?php 
			$vw_hospital_lite_plugin_custom_css = '';
			if(class_exists('Ibtana_Visual_Editor_Menu_Class')){
				$vw_hospital_lite_plugin_custom_css ='display: block';
			}
		?>

		<div id="theme_offer" class="tabcontent open">
			<div class="demo-content">
				<h3><?php esc_html_e( 'Click the below run importer button to import demo content', 'vw-hospital-lite' ); ?></h3>
				<?php 
				/* Get Started. */ 
				require get_parent_theme_file_path( '/inc/getting-started/demo-content.php' );
				 ?>
			</div>
		</div>

		<div id="lite_theme" class="tabcontent">
			<?php  if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = VW_Hospital_Lite_Plugin_Activation_Settings::get_instance();
				$vw_hospital_lite_actions = $plugin_ins->recommended_actions;
				?>
				<div class="vw-hospital-lite-recommended-plugins">
				    <div class="vw-hospital-lite-action-list">
				        <?php if ($vw_hospital_lite_actions): foreach ($vw_hospital_lite_actions as $key => $vw_hospital_lite_actionValue): ?>
				                <div class="vw-hospital-lite-action" id="<?php echo esc_attr($vw_hospital_lite_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($vw_hospital_lite_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($vw_hospital_lite_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($vw_hospital_lite_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" get-start-tab-id="lite-theme-tab" href="javascript:void(0);"><?php esc_html_e('Skip','vw-hospital-lite'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="lite-theme-tab" style="<?php echo esc_attr($vw_hospital_lite_plugin_custom_css); ?>">
				<h3><?php esc_html_e( 'Lite Theme Information', 'vw-hospital-lite' ); ?></h3>
				<hr class="h3hr">
			  	<p><?php esc_html_e('Hospital WordPress theme is meant for doctors, surgeons, dentists, health centres, Paramedic, Audiologist, Pharmacy technician, Dental assistant, Prosthetist, Psychologist, Surgical technologist, health club, medicals, Pediatrics, Language Therapist, Sonographer, nursing homes, veterinary clinics, Optometrist, Radiographer, medical stores, orthopedics, medical personnel, general physicians, General practitioner, Genetic counselor, Respiratory therapist, Dietitian, Healthcare, Clinics, Hospitals, Medical Centers, Wellness, Paediatrician, Chiropractor, Primary care physician, gynaecologist, Gastroenterology General Surgery, Emergency Medicine, Obstetrics & Gynaecology, health consultant, physiotherapists, orthopaedics, medical practitioners, paediatricians, vet for his medicine, Clinical, Hospitals, Health Clinics, Medical Practices, Pediatrics, ambulance, psychiatry, pharmacies, therapy and health care centres. This responsive theme offers innumerous features for medical personnels and everyone else involved in health services. VW Themes’ WP Theme Bundle delivers 420+ premium WordPress themes crafted for diverse industries, startups, and online businesses. Some of it’s amazing features include Call to Action Button (CTA), Appointment form section and testimonial section. This theme has several personalization options that makes it highly user-friendly and interactive. Team, banner, search bar, breadcrumbs, advanced typography, post formats, footer widgets, price widgets, Custom Header, Block-Enabled, plans & timing, services are some of the useful sections available on it’s homepage. It is built on Bootstrap and is compatible with WooCommerce. Furthermore, it is SEO friendly with optimized codes making your site rank high on Google and other search engines. It has a secure and clean code and is optimized for speed and faster page load time. Strong shortcodes expands what you can do with your pages and posts. With the social media integration, it helps you to make your presence available on social platforms as well. It instantly gives a professional look to your online existence. Start creating ideal website with this beautiful Free Multipurpose Hospital WordPress Theme right now.','vw-hospital-lite'); ?></p>
			  	<div class="col-left-inner">
			  		<h4><?php esc_html_e( 'Theme Documentation', 'vw-hospital-lite' ); ?></h4>
					<p><?php esc_html_e( 'If you need any assistance regarding setting up and configuring the Theme, our documentation is there.', 'vw-hospital-lite' ); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOSPITAL_LITE_FREE_THEME_DOC ); ?>" target="_blank"> <?php esc_html_e( 'Documentation', 'vw-hospital-lite' ); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Theme Customizer', 'vw-hospital-lite'); ?></h4>
					<p> <?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'vw-hospital-lite'); ?></p>
					<div class="info-link">
						<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'vw-hospital-lite'); ?></a>
					</div>
					<hr>				
					<h4><?php esc_html_e('Having Trouble, Need Support?', 'vw-hospital-lite'); ?></h4>
					<p> <?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'vw-hospital-lite'); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOSPITAL_LITE_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'vw-hospital-lite'); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Reviews & Testimonials', 'vw-hospital-lite'); ?></h4>
					<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'vw-hospital-lite'); ?>  </p>
					<div class="info-link">
						<a href="<?php echo esc_url( VW_HOSPITAL_LITE_REVIEW ); ?>" target="_blank"><?php esc_html_e('Reviews', 'vw-hospital-lite'); ?></a>
					</div>
			  	  	<div class="link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'vw-hospital-lite' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','vw-hospital-lite'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-admin-customizer"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=vw_hospital_lite_typography') ); ?>" target="_blank"><?php esc_html_e('Typography','vw-hospital-lite'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','vw-hospital-lite'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-editor-table"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_our_services') ); ?>" target="_blank"><?php esc_html_e('Our Services','vw-hospital-lite'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','vw-hospital-lite'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','vw-hospital-lite'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','vw-hospital-lite'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','vw-hospital-lite'); ?></a>
								</div> 
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','vw-hospital-lite'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=vw_hospital_lite_footer_section') ); ?>" target="_blank"><?php esc_html_e('Footer Text','vw-hospital-lite'); ?></a>
								</div>
							</div>
						</div>
					</div>
			  	</div>
				<div class="col-right-inner">
					<h3 class="page-template"><?php esc_html_e('How to set up Home Page Template','vw-hospital-lite'); ?></h3>
				  	<hr class="h3hr">
					<p><?php esc_html_e('Follow these instructions to setup Home page.','vw-hospital-lite'); ?></p>
	                <ul>
	                  	<p><span class="strong"><?php esc_html_e('1. Create a new page :','vw-hospital-lite'); ?></span><?php esc_html_e(' Go to ','vw-hospital-lite'); ?>
					  	<b><?php esc_html_e(' Dashboard >> Pages >> Add New Page','vw-hospital-lite'); ?></b></p>

	                  	<p><?php esc_html_e('Name it as "Home" then select the template "Custom Home Page".','vw-hospital-lite'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/home-page-template.png" alt="" />
	                  	<p><span class="strong"><?php esc_html_e('2. Set the front page:','vw-hospital-lite'); ?></span><?php esc_html_e(' Go to ','vw-hospital-lite'); ?>
					  	<b><?php esc_html_e(' Settings >> Reading ','vw-hospital-lite'); ?></b></p>
					  	<p><?php esc_html_e('Select the option of Static Page, now select the page you created to be the homepage, while another page to be your default page.','vw-hospital-lite'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/set-front-page.png" alt="" />
	                  	<p><?php esc_html_e(' Once you are done with this, then follow the','vw-hospital-lite'); ?> <a class="doc-links" href="https://preview.vwthemesdemo.com/docs/free-vw-hospital-lite/" target="_blank"><?php esc_html_e('Documentation','vw-hospital-lite'); ?></a></p>
	                </ul>
			  	</div>
			</div>
		</div>


		<div id="pro_theme" class="tabcontent">
		  	<h3><?php esc_html_e( 'Premium Theme Information', 'vw-hospital-lite' ); ?></h3>
			<hr class="h3hr">
		    <div class="col-left-pro">
		    	<p><?php esc_html_e('Hospital WordPress theme has a unique design, its loaded with features filled up to the brim. Your final stop in pursuit of the best hospital WordPress theme has arrived. You dont have to look any further. We have built this theme to be the embodiment of the medical service and hospital segment. It has a sophisticated & professional design. The clean and compact look is made for the medical industry professionals. This theme will fit like a glove for Hospitals, Clinics, Health Care, Veterinarian Hospitals, doctors, dentists, health clinics, surgeons and so on and so forth. This feature allows your users to establish a direct connection with your expert doctors.','vw-hospital-lite'); ?></p>
		    	
		    </div>
		    <div class="col-right-pro">
		    	<div class="pro-links">
			    	<a href="<?php echo esc_url( VW_HOSPITAL_LITE_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'vw-hospital-lite'); ?></a>
					<a href="<?php echo esc_url( VW_HOSPITAL_LITE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'vw-hospital-lite'); ?></a>
					<a href="<?php echo esc_url( VW_HOSPITAL_LITE_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'vw-hospital-lite'); ?></a>
					<a href="<?php echo esc_url( VW_HOSPITAL_LITE_THEME_BUNDLE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Get 400+ Themes Bundle at $99', 'vw-hospital-lite'); ?></a>
				</div>
		    	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/vw-hospital-theme.png" alt="" />
		    </div>
		    
		</div>

		<div id="free_pro" class="tabcontent">
		  	<div class="featurebox">
			    <h3><?php esc_html_e( 'Theme Features', 'vw-hospital-lite' ); ?></h3>
				<hr class="h3hr">
				<div class="table-image">
					<table class="tablebox">
						<thead>
							<tr>
								<th><?php esc_html_e('Features', 'vw-hospital-lite'); ?></th>
								<th><?php esc_html_e('Free Themes', 'vw-hospital-lite'); ?></th>
								<th><?php esc_html_e('Premium Themes', 'vw-hospital-lite'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Theme Customization', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Responsive Design', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Logo Upload', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Social Media Links', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Slider Settings', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Number of Slides', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('4', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('Unlimited', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Template Pages', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('3', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('6', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Home Page Template', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Theme sections', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('2', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><?php esc_html_e('12', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Contact us Page Template', 'vw-hospital-lite'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('1', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Blog Templates & Layout', 'vw-hospital-lite'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('3(Full width/Left/Right Sidebar)', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Page Templates & Layout', 'vw-hospital-lite'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('2(Left/Right Sidebar)', 'vw-hospital-lite'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Color Pallete For Particular Sections', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Global Color Option', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Reordering', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Demo Importer', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Allow To Set Site Title, Tagline, Logo', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Enable Disable Options On All Sections, Logo', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Full Documentation', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Latest WordPress Compatibility', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Woo-Commerce Compatibility', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Support 3rd Party Plugins', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Secure and Optimized Code', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Exclusive Functionalities', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Enable / Disable', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Section Google Font Choices', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Simple & Mega Menu Option', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Support to add custom CSS / JS ', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Shortcodes', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Background, Colors, Header, Logo & Menu', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Premium Membership', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Budget Friendly Value', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Priority Error Fixing', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Feature Addition', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('All Access Theme Pass', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Seamless Customer Support', 'vw-hospital-lite'); ?></td>
								<td class="table-img"><span class="dashicons dashicons-no"></span></td>
								<td class="table-img"><span class="dashicons dashicons-saved"></span></td>
							</tr>
							<tr>
								<td></td>
								<td class="table-img"></td>
								<td class="update-link"><a href="<?php echo esc_url( VW_HOSPITAL_LITE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'vw-hospital-lite'); ?></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="get_bundle" class="tabcontent">		  	
		   <div class="col-left-pro">
		   	<h3><?php esc_html_e( 'WP Theme Bundle', 'vw-hospital-lite' ); ?></h3>
		    	<p><?php esc_html_e('Enhance your website effortlessly with our WP Theme Bundle. Get access to 400+ premium WordPress themes and 5+ powerful plugins, all designed to meet diverse business needs. Enjoy seamless integration with any plugins, ultimate customization flexibility, and regular updates to keep your site current and secure. Plus, benefit from our dedicated customer support, ensuring a smooth and professional web experience.','vw-hospital-lite'); ?></p>
		    	<div class="feature">
		    		<h4><?php esc_html_e( 'Features:', 'vw-hospital-lite' ); ?></h4>
		    		<p><?php esc_html_e('400+ Premium Themes & 5+ Plugins.', 'vw-hospital-lite'); ?></p>
		    		<p><?php esc_html_e('Seamless Integration.', 'vw-hospital-lite'); ?></p>
		    		<p><?php esc_html_e('Customization Flexibility.', 'vw-hospital-lite'); ?></p>
		    		<p><?php esc_html_e('Regular Updates.', 'vw-hospital-lite'); ?></p>
		    		<p><?php esc_html_e('Dedicated Support.', 'vw-hospital-lite'); ?></p>
		    	</div>
		    	<p><?php esc_html_e('Upgrade now and give your website the professional edge it deserves, all at an unbeatable price of $99!', 'vw-hospital-lite'); ?></p>
		    	<div class="pro-links">
					<a href="<?php echo esc_url( VW_HOSPITAL_LITE_THEME_BUNDLE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Now', 'vw-hospital-lite'); ?></a>
					<a href="<?php echo esc_url( VW_HOSPITAL_LITE_THEME_BUNDLE_DOC ); ?>" target="_blank"><?php esc_html_e('Documentation', 'vw-hospital-lite'); ?></a>
				</div>
		   </div>
		   <div class="col-right-pro">
		    	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getting-started/images/bundle.png" alt="" />
		   </div>		    
		</div>

	</div>
</div>
<?php } ?>