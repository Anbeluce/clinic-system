<?php
/**
 * Template part for displaying slider section
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

$medicare_clinic_static_image = get_template_directory_uri() . '/assets/images/slider-img.png';
$medicare_clinic_slider_arrows = get_theme_mod('medicare_clinic_slider_arrows', true);

?>
<?php if ($medicare_clinic_slider_arrows) : ?>
  <section id="slider" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/sliderbg.png');">
    <div class="container">
      <div class="owl-carousel owl-theme">
        <?php 
        $medicare_clinic_slide_pages = array();
        for ($medicare_clinic_count = 1; $medicare_clinic_count <= 4; $medicare_clinic_count++) {
          $mod = absint(get_theme_mod('medicare_clinic_slider_page' . $medicare_clinic_count));
          if ($mod != 0) {
            $medicare_clinic_slide_pages[] = $mod;
          }
        }

        if (!empty($medicare_clinic_slide_pages)) :
          $medicare_clinic_args = array(
            'post_type' => 'page',
            'post__in' => $medicare_clinic_slide_pages,
            'orderby' => 'post__in'
          );
          $medicare_clinic_query = new WP_Query($medicare_clinic_args);
          if ($medicare_clinic_query->have_posts()) :
            while ($medicare_clinic_query->have_posts()) : $medicare_clinic_query->the_post(); ?>
              <div class="item">
                <div class="row m-0">
                  <div class="col-lg-6 col-md-6 col-12 slider-content-col align-self-center">
                    <div class="carousel-caption">
                      <div class="inner_carousel">
                        <?php if (get_theme_mod('medicare_clinic_slider_short_heading')) : ?>
                          <p class="slidetop-text mb-2 text-uppercase"><?php echo esc_html(get_theme_mod('medicare_clinic_slider_short_heading')); ?></p>
                        <?php endif; ?>
                        <?php if (get_theme_mod('medicare_clinic_show_slider_title', true)) : ?>
                          <h1 class="mb-md-2 mb-0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                        <?php endif; ?>
                        <?php if (get_theme_mod('medicare_clinic_show_slider_content', true)) : ?>
                          <p class="mb-0 slide-content"><?php $medicare_clinic_excerpt = get_the_excerpt(); echo esc_html( medicare_clinic_string_limit_words( $medicare_clinic_excerpt, esc_attr(get_theme_mod('medicare_clinic_slider_excerpt_length','27')))); ?></p>
                        <?php endif; ?>
                        <div class="more-btn mt-4 d-flex align-items-stretch">
                          <div class="contact call my-0 d-flex">
                            <?php if ( get_theme_mod( 'medicare_clinic_about_call_text' ) || get_theme_mod( 'medicare_clinic_about_call' ) ) : ?>
                              <span class="main-abt-contact-box">
                                <span class="about-call-icon"><i class="fas fa-phone-volume"></i></span>
                                <span class="about-contact-text">
                                  <p class="call-text mb-0 text-start"><?php echo esc_html(get_theme_mod('medicare_clinic_about_call_text', 'Emergency Call')); ?></p>
                                  <p class="call-simplep mb-0 text-start"><a href="tel:<?php echo esc_html(get_theme_mod('medicare_clinic_about_call')); ?>">
                                    <?php echo esc_html(get_theme_mod('medicare_clinic_about_call')); ?></a></p>
                                </span>
                              </span>
                            <?php endif; ?>
                          </div>
                         <?php 
                        $medicare_clinic_slider_add_time = get_theme_mod('medicare_clinic_slider_add_time');
                        if ( $medicare_clinic_slider_add_time ) : ?>
                          <div class="topbar-text m-0">
                            <span class="toptext"><?php echo esc_html($medicare_clinic_slider_add_time); ?></span>
                          </div>
                        <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12 slider-img-col">
                    <?php if (has_post_thumbnail()) : ?>
                      <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title_attribute(); ?>" />
                    <?php else : ?>
                      <img src="<?php echo esc_url($medicare_clinic_static_image); ?>" alt="<?php esc_attr_e('Slider Image', 'medicare-clinic'); ?>" />
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endwhile;
            wp_reset_postdata();
          else : ?>
            <div class="no-postfound"><?php esc_html_e('No posts found', 'medicare-clinic'); ?></div>
          <?php endif;
        endif; ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </section>
<?php endif; ?>