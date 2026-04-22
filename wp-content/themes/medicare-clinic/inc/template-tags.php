<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function medicare_clinic_categorized_blog() {
	$medicare_clinic_category_count = get_transient( 'medicare_clinic_categories' );

	if ( false === $medicare_clinic_category_count ) {
		// Create an array of all the categories that are attached to posts.
		$medicare_clinic_categories = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$medicare_clinic_category_count = count( $medicare_clinic_categories );

		set_transient( 'medicare_clinic_categories', $medicare_clinic_category_count );
	}

	// Allow viewing case of 0 or 1 categories in post preview.
	if ( is_preview() ) {
		return true;
	}

	return $medicare_clinic_category_count > 1;
}

if ( ! function_exists( 'medicare_clinic_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since Medicare Clinic
 */
function medicare_clinic_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

/**
 * Flush out the transients used in medicare_clinic_categorized_blog.
 */
function medicare_clinic_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'medicare_clinic_categories' );
}
add_action( 'edit_category', 'medicare_clinic_category_transient_flusher' );
add_action( 'save_post',     'medicare_clinic_category_transient_flusher' );