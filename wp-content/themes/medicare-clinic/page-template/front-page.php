<?php
/**
 * Template Name: Custom Home Page
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

get_header(); ?>

	<?php do_action( 'medicare_clinic_before_slider' ); ?>

	<?php get_template_part( 'template-parts/home/slider' ); ?>
	<?php do_action( 'medicare_clinic_after_slider' ); ?>

<main id="tp_content" role="main">
	<?php get_template_part( 'template-parts/home/our-services' ); ?>
	<?php do_action( 'medicare_clinic_after_our-services' ); ?>

	<?php get_template_part( 'template-parts/home/home-content' ); ?>
	<?php do_action( 'medicare_clinic_after_home_content' ); ?>
</main>

<?php get_footer();