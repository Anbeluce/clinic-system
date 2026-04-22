<?php
/**
 * Template part for displaying the services section
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

// Get the courses setting.
$medicare_clinic_static_image = get_template_directory_uri() . '/assets/images/post-img1.png';
$medicare_clinic_courses = get_theme_mod('medicare_clinic_courses_setting', true);

if ($medicare_clinic_courses == '1') {
?>
<section id="our-services" class="pt-md-3 pb-md-5 py-2 px-md-0 px-3">
  <div class="container">
    <div class="text-center main-sec-title">
      <?php if (get_theme_mod('medicare_clinic_offer_section_tittle')) { ?>
        <h2 class="text-center text-uppercase mb-3"><?php echo esc_html(get_theme_mod('medicare_clinic_offer_section_tittle')); ?></h2>
      <?php } ?>
    </div>
    <?php if (get_theme_mod('medicare_clinic_offer_section_text')) { ?>
      <h3 class="text-center mb-lg-4 mb-3 serv-description"><?php echo esc_html(get_theme_mod('medicare_clinic_offer_section_text')); ?></h3>
    <?php } ?>
    <div class="owl-carousel owl-theme">
      <?php
      $medicare_clinic_post_category = get_theme_mod('medicare_clinic_offer_section_category','');
      if ($medicare_clinic_post_category) :
        $medicare_clinic_category_id = term_exists($medicare_clinic_post_category, 'category');
        if ($medicare_clinic_category_id !== 0 && $medicare_clinic_category_id !== null) :
          $medicare_clinic_posts_to_show = get_theme_mod('medicare_clinic_posts_to_show', 4);
          $medicare_clinic_page_query = new WP_Query(array(
            'category_name' => esc_attr($medicare_clinic_post_category),
            'posts_per_page' => $medicare_clinic_posts_to_show,
          ));

          $medicare_clinic_post_count = 0;
          if ($medicare_clinic_page_query->have_posts()) :
            while ($medicare_clinic_page_query->have_posts()) : $medicare_clinic_page_query->the_post(); 
              $medicare_clinic_post_count++;
      ?>
      <div class="item">
        <div class="serv-box">
            <div class="post-main-img">
                <?php if (has_post_thumbnail()) : ?>
                  <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title_attribute(); ?>" />
                <?php else : ?>
                  <img src="<?php echo esc_url($medicare_clinic_static_image); ?>" alt="<?php esc_attr_e('Post Image', 'medicare-clinic'); ?>" />
                <?php endif; ?>
                <div class="post-content">
                    <h4 class="text-capitalize mb-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php if ($medicare_clinic_team_role = get_theme_mod('medicare_clinic_team_role' . $medicare_clinic_post_count)) { ?>
                        <p class="team-role"><?php echo esc_html($medicare_clinic_team_role); ?></p>
                    <?php } ?>
                </div>
            </div>
            <div class="serv-social-media">
              <?php 
                  $medicare_clinic_serv_facebook_url = get_theme_mod('medicare_clinic_serv_facebook_url' . $medicare_clinic_post_count);
                      if ($medicare_clinic_serv_facebook_url != '') { ?>
                          <a target="_blank" href="<?php echo esc_url($medicare_clinic_serv_facebook_url); ?>">
                              <i class="fab fa-facebook-f"></i>
                          </a>
              <?php } ?>
              <?php 
                  $medicare_clinic_serv_insta_url = get_theme_mod('medicare_clinic_serv_insta_url' . $medicare_clinic_post_count);
                      if ($medicare_clinic_serv_insta_url != '') { ?>
                          <a target="_blank" href="<?php echo esc_url($medicare_clinic_serv_insta_url); ?>">
                              <i class="fab fa-instagram"></i>
                          </a>
              <?php } ?>
              <?php 
                  $medicare_clinic_serv_youtube_url = get_theme_mod('medicare_clinic_serv_youtube_url' . $medicare_clinic_post_count);
                      if ($medicare_clinic_serv_youtube_url != '') { ?>
                          <a target="_blank" href="<?php echo esc_url($medicare_clinic_serv_youtube_url); ?>">
                              <i class="fab fa-youtube"></i>
                          </a>
              <?php } ?>
              <?php 
                  $medicare_clinic_serv_snapchat_url = get_theme_mod('medicare_clinic_serv_snapchat_url' . $medicare_clinic_post_count);
                      if ($medicare_clinic_serv_snapchat_url != '') { ?>
                          <a target="_blank" href="<?php echo esc_url($medicare_clinic_serv_snapchat_url); ?>">
                            <i class="fa-brands fa-snapchat"></i>
                          </a>
              <?php } ?>
            </div>
        </div>
      </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else : ?>
        <div class="no-postfound"></div>
      <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
</div>
  </div>
</section>
<?php 
} // End of the if statement for courses.
?>