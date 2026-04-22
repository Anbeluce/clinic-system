<?php

require get_template_directory() . '/inc/TGM/class-tgm-plugin-activation.php';
/**
 * Recommended plugins.
 */
function medicare_clinic_register_recommended_plugins() {
	$plugins = array(
		array(
            'name'             => __( 'Advanced Appointment Booking & Scheduling', 'medicare-clinic' ),
            'slug'             => 'advanced-appointment-booking-scheduling',
            'required'         => false,
            'force_activation' => false,
        ),
	);
	$config = array();
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'medicare_clinic_register_recommended_plugins' );
