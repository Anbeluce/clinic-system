<?php
/**
 * Medicare Clinic Theme Page
 *
 * @package Medicare Clinic
 */

function medicare_clinic_admin_scripts() {
	wp_dequeue_script('medicare-clinic-custom-scripts');
}
add_action( 'admin_enqueue_scripts', 'medicare_clinic_admin_scripts' );

if ( ! defined( 'MEDICARE_CLINIC_FREE_THEME_URL' ) ) {
	define( 'MEDICARE_CLINIC_FREE_THEME_URL', 'https://www.themespride.com/products/medicare-clinic' );
}
if ( ! defined( 'MEDICARE_CLINIC_PRO_THEME_URL' ) ) {
	define( 'MEDICARE_CLINIC_PRO_THEME_URL', 'https://www.themespride.com/products/clinic-wordpress-theme' );
}
if ( ! defined( 'MEDICARE_CLINIC_DEMO_THEME_URL' ) ) {
	define( 'MEDICARE_CLINIC_DEMO_THEME_URL', 'https://page.themespride.com/medicare-clinic/' );
}
if ( ! defined( 'MEDICARE_CLINIC_DOCS_THEME_URL' ) ) {
    define( 'MEDICARE_CLINIC_DOCS_THEME_URL', 'https://page.themespride.com/demo/docs/medicare-clinic-lite/' );
}
if ( ! defined( 'MEDICARE_CLINIC_RATE_THEME_URL' ) ) {
    define( 'MEDICARE_CLINIC_RATE_THEME_URL', 'https://wordpress.org/support/theme/medicare-clinic/reviews/#new-post' );
}
if ( ! defined( 'MEDICARE_CLINIC_CHANGELOG_THEME_URL' ) ) {
    define( 'MEDICARE_CLINIC_CHANGELOG_THEME_URL', get_template_directory() . '/readme.txt' );
}
if ( ! defined( 'MEDICARE_CLINIC_SUPPORT_THEME_URL' ) ) {
    define( 'MEDICARE_CLINIC_SUPPORT_THEME_URL', 'https://wordpress.org/support/theme/medicare-clinic/' );
}
if ( ! defined( 'MEDICARE_CLINIC_THEME_BUNDLE' ) ) {
    define( 'MEDICARE_CLINIC_THEME_BUNDLE', 'https://www.themespride.com/products/wordpress-theme-bundle' );
}


/**
 * Add theme page
 */
function medicare_clinic_menu() {
	add_theme_page( esc_html__( 'About Theme', 'medicare-clinic' ), esc_html__( 'Begin Installation - Import Demo', 'medicare-clinic' ), 'edit_theme_options', 'medicare-clinic-about', 'medicare_clinic_about_display' );
}
add_action( 'admin_menu', 'medicare_clinic_menu' );

/**
 * Display About page
 */
function medicare_clinic_about_display() {
	$medicare_clinic_theme = wp_get_theme();
	?>
	<div class="wrap about-wrap full-width-layout">
		<!-- top-detail -->
		<?php
		// Only show if NOT dismissed
		if ( ! get_option('dismissed-get_started-detail', false ) ) { 
		?>
		    <!-- top-detail -->
		    <div class="detail-theme" id="detail-theme-box">
		        <button type="button" class="close-btn" id="close-detail-theme">
		            <?php esc_html_e( 'Dismiss', 'medicare-clinic' ); ?>
		        </button>
		        <h2><?php echo esc_html__( 'Hey, Thank you for Installing Medicare Clinic Theme!', 'medicare-clinic' ); ?></h2>

		        <a href="<?php echo esc_url( admin_url( 'themes.php?page=medicare-clinic-about' ) ); ?>">
		            <?php esc_html_e( 'Get Started', 'medicare-clinic' ); ?>
		        </a>
		        <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="site-editor" target="_blank">
		            <?php esc_html_e( 'Site Editor', 'medicare-clinic' ); ?>
		        </a>

		        <a href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme" target="_blank">
		            <?php esc_html_e( 'Upgrade to Pro', 'medicare-clinic' ); ?>
		        </a>

		        <a href="<?php echo esc_url( MEDICARE_CLINIC_THEME_BUNDLE ); ?>" class="rate-theme" target="_blank">
		            <?php esc_html_e( 'Get Bundle', 'medicare-clinic' ); ?>
		        </a>
		    </div>
		<?php 
		} ?>
		
		<nav class="nav-tab-wrapper wp-clearfix medicare-clinic-tab-sec" aria-label="<?php esc_attr_e( 'Secondary menu', 'medicare-clinic' ); ?>">
		    <button class="nav-tab medicare-clinic-tablinks active"
		        onclick="medicare_clinic_open_tab(event, 'tp_demo_import')">
		        <?php esc_html_e( 'One Click Demo Import', 'medicare-clinic' ); ?>
		    </button>

		    <button class="nav-tab medicare-clinic-tablinks"
		        onclick="medicare_clinic_open_tab(event, 'tp_about_theme')">
		        <?php esc_html_e( 'About', 'medicare-clinic' ); ?>
		    </button>

		    <button class="nav-tab medicare-clinic-tablinks"
		        onclick="medicare_clinic_open_tab(event, 'tp_free_vs_pro')">
		        <?php esc_html_e( 'Compare Free Vs Pro', 'medicare-clinic' ); ?>
		    </button>

		    <button class="nav-tab medicare-clinic-tablinks"
		        onclick="medicare_clinic_open_tab(event, 'tp_changelog')">
		        <?php esc_html_e( 'Changelog', 'medicare-clinic' ); ?>
		    </button>

		    <button class="nav-tab medicare-clinic-tablinks blink wp-bundle"
		        onclick="medicare_clinic_open_tab(event, 'tp_get_bundle')">
		        <?php esc_html_e( 'Get WordPress Theme Bundle (120+ Themes)', 'medicare-clinic' ); ?>
		    </button>
		</nav>

		<?php
			medicare_clinic_demo_import();

			medicare_clinic_main_screen();

			medicare_clinic_changelog_screen();

			medicare_clinic_free_vs_pro();

			medicare_clinic_get_bundle();
		?>

		<p class="actions">
			<a target="_blank"href="<?php echo esc_url( MEDICARE_CLINIC_FREE_THEME_URL ); ?>" class="theme-info-btn" target="_blank" target="_blank"><?php esc_html_e( 'Theme Info', 'medicare-clinic' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_DEMO_THEME_URL ); ?>" class="view-demo" target="_blank"><?php esc_html_e( 'View Demo', 'medicare-clinic' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_DOCS_THEME_URL ); ?>" class="instruction-theme" target="_blank"><?php esc_html_e( 'Theme Documentation', 'medicare-clinic' ); ?></a>
			<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme" target="_blank"><?php esc_html_e( 'Upgrade to pro', 'medicare-clinic' ); ?></a>
		</p>

		<h1><?php echo esc_html( $medicare_clinic_theme ); ?></h1>
		<div class="about-theme">
			<div class="theme-description">
				<p class="about-text content">
					<?php
					// Remove last sentence of description.
					$medicare_clinic_description = explode( '. ', $medicare_clinic_theme->get( 'Description' ) );
					array_pop( $medicare_clinic_description );

					$medicare_clinic_description = implode( '. ', $medicare_clinic_description );

					echo esc_html( $medicare_clinic_description . '.' );
				?></p>
				
			</div>
			<div class="theme-screenshot">
				<img src="<?php echo esc_url( $medicare_clinic_theme->get_screenshot() ); ?>" />
			</div>
		</div>
	<?php
}


/**
 * Output the Demo Import screen (JS tab based).
 */
function medicare_clinic_demo_import() {

	// Load whizzie demo importer
	$medicare_clinic_child_whizzie  = get_stylesheet_directory() . '/inc/whizzie.php';
	$medicare_clinic_parent_whizzie = get_template_directory() . '/inc/whizzie.php';

	if ( file_exists( $medicare_clinic_child_whizzie ) ) {
		require_once $medicare_clinic_child_whizzie;
	} elseif ( file_exists( $medicare_clinic_parent_whizzie ) ) {
		require_once $medicare_clinic_parent_whizzie;
	}

	/* ---------------------------------------------------------
	 * SAVE DEMO IMPORT STATUS
	 * --------------------------------------------------------- */
	if ( isset( $_GET['import-demo'] ) && $_GET['import-demo'] === 'true' ) {
		update_option( 'medicare_clinic_demo_imported', true );
		delete_option( 'medicare_clinic_demo_popup_shown' ); // allow popup once
	}

	/* ---------------------------------------------------------
	 * RESET DEMO (OPTIONAL)
	 * --------------------------------------------------------- */
	if ( isset( $_GET['reset-demo'] ) && $_GET['reset-demo'] === 'true' ) {
		delete_option( 'medicare_clinic_demo_imported' );
		delete_option( 'medicare_clinic_demo_popup_shown' );
		wp_safe_redirect( remove_query_arg( 'reset-demo' ) );
		exit;
	}

	$medicare_clinic_demo_imported  = get_option( 'medicare_clinic_demo_imported', false );
	$medicare_clinic_popup_shown    = get_option( 'medicare_clinic_demo_popup_shown', false );
	$medicare_clinic_show_popup_now = ( $medicare_clinic_demo_imported && ! $medicare_clinic_popup_shown );
	?>

	<div id="tp_demo_import" class="medicare-clinic-tabcontent">

	<?php if ( $medicare_clinic_demo_imported ) : ?>

		<!-- ================= SUCCESS STATE ================= -->
		<div class="content-row">
			<div class="col card success-demo text-center">
				<p class="imp-success">
					<?php esc_html_e( 'Demo Imported Successfully!', 'medicare-clinic' ); ?>
				</p><br>

				<div class="demo-button-three">
					<a class="button button-primary" href="<?php echo esc_url( home_url('/') ); ?>" target="_blank">
						<?php esc_html_e( 'View Site', 'medicare-clinic' ); ?>
					</a>

					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" target="_blank">
						<?php esc_html_e( 'Edit Site', 'medicare-clinic' ); ?>
					</a>

					<?php if ( defined( 'MEDICARE_CLINIC_DOCS_THEME_URL' ) ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( MEDICARE_CLINIC_DOCS_THEME_URL ); ?>" target="_blank">
							<?php esc_html_e( 'Documentation', 'medicare-clinic' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<h3><?php esc_html_e( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'medicare-clinic' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'medicare-clinic' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'medicare-clinic' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'medicare-clinic' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'medicare-clinic' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro" target="_blank"><?php esc_html_e( 'Upgrade To Premium Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></a>
				</div>
			</div>
		</div>

	<?php else : ?>

		<!-- ================= INSTALL STATE ================= -->
		<div class="content-row">
			<div class="col card demo-btn text-center">
				<form id="demo-importer-form" method="post">
					<p class="demo-title"><?php esc_html_e( 'Demo Importer', 'medicare-clinic' ); ?></p>
					<p class="demo-des">
						<?php esc_html_e( 'Import demo content with one click. You can customize everything later.', 'medicare-clinic' ); ?>
					</p>

					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Begin Installation – Import Demo', 'medicare-clinic' ); ?>
					</button>

					<div id="page-loader" style="display:none;margin-top:15px;">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/loader.png' ); ?>" width="40">
						<p><?php esc_html_e( 'Importing demo, please wait...', 'medicare-clinic' ); ?></p>
					</div>
				</form>
			</div>
			<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<h3><?php esc_html_e( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'medicare-clinic' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'medicare-clinic' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'medicare-clinic' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'medicare-clinic' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'medicare-clinic' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro" target="_blank"><?php esc_html_e( 'Upgrade To Premium Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></a>
				</div>
			</div>
		</div>

		<script>
		jQuery(function($){
			$('#demo-importer-form').on('submit', function(e){
				e.preventDefault();
				if(confirm('<?php esc_html_e( 'Are you sure you want to import demo content?', 'medicare-clinic' ); ?>')){
					$('#page-loader').show();
					let url = new URL(window.location.href);
					url.searchParams.set('import-demo','true');
					window.location.href = url;
				}
			});
		});
		</script>

	<?php endif; ?>

	</div>

	<?php if ( $medicare_clinic_show_popup_now ) : ?>
	<!-- ================= SUCCESS POPUP (ONLY ONCE) ================= -->
	<div id="demo-success-modal" class="modal-overlay">
		<div class="modal-content">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/demo-icon.png' ); ?>" alt="">
			<h2><?php esc_html_e( 'Demo Successfully Imported!', 'medicare-clinic' ); ?></h2>

			<div class="modal-buttons">
				<a class="button button-primary" href="<?php echo esc_url( home_url('/') ); ?>" target="_blank">
					<?php esc_html_e( 'View Site', 'medicare-clinic' ); ?>
				</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=medicare-clinic-about' ) ); ?>">
					<?php esc_html_e( 'Go To Dashboard', 'medicare-clinic' ); ?>
				</a>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const modal = document.getElementById("demo-success-modal");
			if (!modal) return;

			modal.style.display = "flex";

			// Mark popup as permanently shown (only once)
			fetch('<?php echo esc_url( admin_url( 'admin-ajax.php?action=medicare_clinic_popup_done' ) ); ?>');

			// Close popup on ANY button click
			modal.querySelectorAll('a.button').forEach(function(btn){
				btn.addEventListener('click', function(){
					modal.style.display = "none";
				});
			});
		});
	</script>

	<?php endif; ?>

	<?php
}


/**
 * Output the main about screen.
 */
function medicare_clinic_main_screen() {
	
	?>
	<div id="tp_about_theme" class="medicare-clinic-tabcontent">
		<div class="content-row">
			<div class="feature-section two-col">
				<div class="col card">
					<h2 class="title"><?php esc_html_e( 'Theme Customizer', 'medicare-clinic' ); ?></h2>
					<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'medicare-clinic' ) ?></p>
					<p><a target="_blank" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Customize', 'medicare-clinic' ); ?></a></p>
				</div>

				<div class="col card">
					<h2 class="title"><?php esc_html_e( 'Got theme support question?', 'medicare-clinic' ); ?></h2>
					<p><?php esc_html_e( 'Get genuine support from genuine people. Whether it\'s customization or compatibility, our seasoned developers deliver tailored solutions to your queries.', 'medicare-clinic' ) ?></p>
					<p><a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_SUPPORT_THEME_URL ); ?>" class="button button-primary"><?php esc_html_e( 'Support Forum', 'medicare-clinic' ); ?></a></p>
				</div>
			</div>
			<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<h3><?php esc_html_e( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'medicare-clinic' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'medicare-clinic' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'medicare-clinic' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'medicare-clinic' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'medicare-clinic' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro" target="_blank"><?php esc_html_e( 'Upgrade To Premium Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Output the changelog screen.
 */
function medicare_clinic_changelog_screen() {
		global $wp_filesystem;
	?>
	<div id="tp_changelog" class="medicare-clinic-tabcontent">
	<div class="content-row">
		<div class="wrap about-wrap change-log">
			<?php
				$changelog_file = apply_filters( 'medicare_clinic_changelog_file', MEDICARE_CLINIC_CHANGELOG_THEME_URL );
				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = medicare_clinic_parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<div class="theme-price col card">
				<div class="price-flex">
					<div class="price-content">
						<h3><?php esc_html_e( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></h3>
						<p class="main-flash"><?php 
						  printf(
						    /* translators: 1: bold FLASH DEAL text, 2: discount code */
						    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'medicare-clinic' ),
						    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'medicare-clinic' ) . '</strong>',
						    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'medicare-clinic' ) . '</strong>'
						  ); 
						  ?></p>
						 <p>
						  <del><?php echo esc_html__( '$59', 'medicare-clinic' ); ?></del>
						  <strong class="bold-price"><?php echo esc_html__( '$39', 'medicare-clinic' ); ?></strong>
						</p>
					</div>
					<div class="price-img">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
					</div>
				</div>
				<div class="main-pro-price">
					<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro" target="_blank"><?php esc_html_e( 'Upgrade To Premium Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></a>
				</div>
			</div>
	</div>
</div>
	<?php
}

/**
 * Parse changelog from readme file.
 * @param  string $content
 * @return string
 */
function medicare_clinic_parse_changelog( $content ) {
	// Explode content with ==  to juse separate main content to array of headings.
	$content = explode ( '== ', $content );

	$changelog_isolated = '';

	// Get element with 'Changelog ==' as starting string, i.e isolate changelog.
	foreach ( $content as $key => $value ) {
		if (strpos( $value, 'Changelog ==') === 0) {
	    	$changelog_isolated = str_replace( 'Changelog ==', '', $value );
	    }
	}

	// Now Explode $changelog_isolated to manupulate it to add html elements.
	$changelog_array = explode( '= ', $changelog_isolated );

	// Unset first element as it is empty.
	unset( $changelog_array[0] );

	$changelog = '<pre class="changelog">';

	foreach ( $changelog_array as $value) {
		// Replace all enter (\n) elements with </span><span> , opening and closing span will be added in next process.
		$value = preg_replace( '/\n+/', '</span><span>', $value );

		// Add openinf and closing div and span, only first span element will have heading class.
		$value = '<div class="block"><span class="heading">= ' . $value . '</span></div>';

		// Remove empty <span></span> element which newr formed at the end.
		$changelog .= str_replace( '<span></span>', '', $value );
	}

	$changelog .= '</pre>';

	return wp_kses_post( $changelog );
}

/**
 * Import Demo data for theme using catch themes demo import plugin
 */
function medicare_clinic_free_vs_pro() {
	?>
	<div id="tp_free_vs_pro" class="medicare-clinic-tabcontent">
	<div class="content-row">
		<div class="wrap about-wrap change-log">
			<p class="about-description"><?php esc_html_e( 'View Free vs Pro Table below:', 'medicare-clinic' ); ?></p>
			<div class="vs-theme-table">
				<table>
					<thead>
						<tr><th scope="col"></th>
							<th class="head" scope="col"><?php esc_html_e( 'Free Theme', 'medicare-clinic' ); ?></th>
							<th class="head" scope="col"><?php esc_html_e( 'Pro Theme', 'medicare-clinic' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><span><?php esc_html_e( 'Theme Demo Set Up', 'medicare-clinic' ); ?></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Templates, Color options and Fonts', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Included Demo Content', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Section Ordering', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Multiple Sections', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Additional Plugins', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Premium Technical Support', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Access to Support Forums', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-no-alt"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Free updates', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Unlimited Domains', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Responsive Design', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td headers="features" class="feature"><?php esc_html_e( 'Live Customizer', 'medicare-clinic' ); ?></td>
							<td><span class="dashicons dashicons-saved"></span></td>
							<td><span class="dashicons dashicons-saved"></span></td>
						</tr>
						<tr class="odd" scope="row">
							<td class="feature feature--empty"></td>
							<td class="feature feature--empty"></td>
							<td headers="comp-2" class="td-btn-2"><a class="sidebar-button single-btn" href="<?php echo esc_url(MEDICARE_CLINIC_PRO_THEME_URL);?>" target="_blank"><?php esc_html_e( 'Go For Premium', 'medicare-clinic' ); ?></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="theme-price col card">
			<div class="price-flex">
				<div class="price-content">
					<h3><?php esc_html_e( 'Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></h3>
					<p class="main-flash"><?php 
					  printf(
					    /* translators: 1: bold FLASH DEAL text, 2: discount code */
					    esc_html__( '%1$s - Get 20%% Discount on All Themes, Use code %2$s', 'medicare-clinic' ),
					    '<strong class="bold-text">' . esc_html__( 'FLASH DEAL', 'medicare-clinic' ) . '</strong>',
					    '<strong class="bold-text">' . esc_html__( 'QBSALE20', 'medicare-clinic' ) . '</strong>'
					  ); 
					  ?></p>
					 <p>
					  <del><?php echo esc_html__( '$59', 'medicare-clinic' ); ?></del>
					  <strong class="bold-price"><?php echo esc_html__( '$39', 'medicare-clinic' ); ?></strong>
					</p>
				</div>
				<div class="price-img">
					<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-img.png" alt="theme-img" />
				</div>
			</div>
			<div class="main-pro-price">
				<a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_PRO_THEME_URL ); ?>" class="pro-btn-theme price-pro" target="_blank"><?php esc_html_e( 'Upgrade To Premium Medicare Clinic WordPress Theme', 'medicare-clinic' ); ?></a>
			</div>
		</div>
	</div>
</div>
	<?php
}

function medicare_clinic_get_bundle() {
	?>
	<div id="tp_get_bundle" class="medicare-clinic-tabcontent">
		<div class="wrap about-wrap theme-main-bundle">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/theme-bundle.png" alt="theme-bundle" width="300" height="300" />
			<p class="bundle-link"><a target="_blank" href="<?php echo esc_url( MEDICARE_CLINIC_THEME_BUNDLE ); ?>" class="button button-primary bundle-btn"><?php esc_html_e( 'Buy WordPress Theme Bundle (120+ Themes)', 'medicare-clinic' ); ?></a></p>
		</div>
	</div>
	<?php
}