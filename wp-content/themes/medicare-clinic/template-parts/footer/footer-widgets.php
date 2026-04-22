<?php
/**
 * Displays footer widgets if assigned
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */
?>
<?php

// Determine the number of columns dynamically for the footer (you can replace this with your logic).
$medicare_clinic_no_of_footer_col = get_theme_mod('medicare_clinic_footer_columns', 4); // Change this value as needed.

// Calculate the Bootstrap class for large screens (col-lg-X) for footer.
$medicare_clinic_col_lg_footer_class = 'col-lg-' . (12 / $medicare_clinic_no_of_footer_col);

// Calculate the Bootstrap class for medium screens (col-md-X) for footer.
$medicare_clinic_col_md_footer_class = 'col-md-' . (12 / $medicare_clinic_no_of_footer_col);
?>
<div class="container">
    <aside class="widget-area row py-2 pt-3" role="complementary" aria-label="<?php esc_attr_e( 'Footer', 'medicare-clinic' ); ?>">
        <?php
        $medicare_clinic_default_widgets = array(
            1 => 'search',
            2 => 'archives',
            3 => 'meta',
            4 => 'categories'
        );

        for ($medicare_clinic_i = 1; $medicare_clinic_i <= $medicare_clinic_no_of_footer_col; $medicare_clinic_i++) :
            $medicare_clinic_lg_class = esc_attr($medicare_clinic_col_lg_footer_class);
            $medicare_clinic_md_class = esc_attr($medicare_clinic_col_md_footer_class);
            echo '<div class="col-12 ' . $medicare_clinic_lg_class . ' ' . $medicare_clinic_md_class . '">';

            if (is_active_sidebar('footer-' . $medicare_clinic_i)) {
                dynamic_sidebar('footer-' . $medicare_clinic_i);
            } else {
                // Display default widget content if not active.
                switch ($medicare_clinic_default_widgets[$medicare_clinic_i] ?? '') {
                    case 'search':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Search', 'medicare-clinic'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Search', 'medicare-clinic'); ?></h3>
                            <?php get_search_form(); ?>
                        </aside>
                        <?php
                        break;

                    case 'archives':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Archives', 'medicare-clinic'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Archives', 'medicare-clinic'); ?></h3>
                            <ul><?php wp_get_archives(['type' => 'monthly']); ?></ul>
                        </aside>
                        <?php
                        break;

                    case 'meta':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Meta', 'medicare-clinic'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Meta', 'medicare-clinic'); ?></h3>
                            <ul>
                                <?php wp_register(); ?>
                                <li><?php wp_loginout(); ?></li>
                                <?php wp_meta(); ?>
                            </ul>
                        </aside>
                        <?php
                        break;

                    case 'categories':
                        ?>
                        <aside class="widget" role="complementary" aria-label="<?php esc_attr_e('Categories', 'medicare-clinic'); ?>">
                            <h3 class="widget-title"><?php esc_html_e('Categories', 'medicare-clinic'); ?></h3>
                            <ul><?php wp_list_categories(['title_li' => '']); ?></ul>
                        </aside>
                        <?php
                        break;
                }
            }

            echo '</div>';
        endfor;
        ?>
    </aside>
</div>