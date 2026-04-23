<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content-vw">
 *
 * @package VW Hospital Lite
 */

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width">
  <?php wp_head(); ?>
  <style id="auth-buttons-style">
    .header-right-wrap { display: flex; align-items: center; gap: 15px; justify-content: flex-end; }
    .auth-buttons { display: flex; gap: 8px; }
    .btn-auth { display: inline-block; padding: 7px 16px; border-radius: 4px; font-size: 12px; font-weight: 700; text-decoration: none !important; transition: all 0.2s ease; white-space: nowrap; text-transform: uppercase; }
    .btn-login { color: #005086 !important; border: 1.5px solid #005086; background: transparent; }
    .btn-login:hover { background: #eef6fc; }
    .btn-register { background: #005086; color: #fff !important; border: 1.5px solid #005086; }
    .btn-register:hover { background: #003d66; border-color: #003d66; transform: translateY(-1px); }
    .btn-logout { color: #666 !important; font-size: 11px; text-transform: none; border-bottom: 1px solid transparent; padding: 5px 0; border-radius: 0; }
    .btn-logout:hover { border-bottom-color: #666; }
    .user-welcome { font-size: 13px; color: #333; font-weight: 600; margin-right: 5px; }
    @media (max-width: 991px) { 
        .header-right-wrap { justify-content: center; margin-top: 10px; }
    }
  </style>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) 
  {
    wp_body_open();
  }else{
    do_action('wp_body_open');
  } 
?>

  <header role="banner">
    <a class="screen-reader-text skip-link" href="#maincontent"><?php esc_html_e( 'Skip to content', 'vw-hospital-lite' ); ?></a>
    <?php
      $vw_hospital_lite_searchbox = get_theme_mod( 'vw_hospital_lite_searchbox' );

        if ( 'Disable' == $vw_hospital_lite_searchbox ) {
          $colmd = 'col-lg-12 col-md-12';
        } else { 
          $colmd = 'col-lg-8 col-md-7';
        } 
    ?>
    <?php if( get_theme_mod( 'vw_hospital_lite_topbar_hide_show', true) == 1 || get_theme_mod( 'vw_hospital_lite_resp_topbar_hide_show', true) == 1) { ?>
      <div class="top-bar">
        <div class="container">
          <div class="row">
            <div class="col-lg-6 col-md-5">
              <?php if (is_active_sidebar('social-icon')) : ?>
                <?php dynamic_sidebar( 'social-icon' ); ?>
              <?php else : ?>
              <!-- Default Social Icons Widgets -->
                <div class="widget">
                    <ul class="custom-social-icons" >
                      <li><a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i></a></li> 
                      <li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a></li> 
                      <li><a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a></li> 
                      <li><a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a></li> 
                      <li><a href="https://rss.com" target="_blank"><i class="fa-solid fa-rss"></i></a></li>                                         
                    </ul>
                </div>
              <?php endif; ?>   
            </div>
            <div class="col-lg-6 col-md-7 appointment">
              <?php if ( get_theme_mod('vw_hospital_lite_appointment1','') != "" ) {?>
                <span><a href="<?php echo esc_url(get_theme_mod('vw_hospital_lite_appointment','')); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_appointments_icon','fas fa-plus-circle')); ?>"></i><?php echo esc_html(get_theme_mod('vw_hospital_lite_appointment1','')); ?></a></span>
              <?php }?>
              <?php if ( get_theme_mod('vw_hospital_lite_timing1','') != "" ) {?>
                <span><a href="<?php echo esc_url(get_theme_mod('vw_hospital_lite_timing','')); ?>"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_timing_icon','fas fa-ambulance')); ?>"></i><?php echo esc_html(get_theme_mod('vw_hospital_lite_timing1','')); ?></a></span>
              <?php }?>
            </div>
          </div>   
          <div class="clear"></div>
        </div>
      </div>
    <?php }?>
    <div class="header">
      <div class="container">
        <div class="row">
          <div class=" col-lg-6 col-md-4 align-self-center">
            <div class="logo">
            <?php if ( has_custom_logo() ) : ?>
              <div class="site-logo"><?php the_custom_logo(); ?></div>
            <?php endif; ?>
            <?php $blog_info = get_bloginfo( 'name' ); ?>
              <?php if ( ! empty( $blog_info ) ) : ?>
                <?php if ( is_front_page() && is_home() ) : ?>
                  <?php if( get_theme_mod('vw_hospital_lite_logo_title_hide_show',true) == 1){ ?>
                    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                  <?php } ?>
                <?php else : ?>
                  <?php if( get_theme_mod('vw_hospital_lite_logo_title_hide_show',true) == 1){ ?>
                    <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                  <?php } ?>
                <?php endif; ?>
              <?php endif; ?>
              <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) :
              ?>
              <?php if( get_theme_mod('vw_hospital_lite_tagline_hide_show',false) == 1){ ?>
                <p class="site-description">
                  <?php echo esc_html($description); ?>
                </p>
              <?php } ?>
            <?php endif; ?>
          </div>
        </div>
          <div class="col-lg-6 col-md-8 align-self-center">
            <div class="contact-call-Email row">
              <?php if( get_theme_mod('vw_hospital_lite_contact_call','') != '') { ?>
                <div class="col-lg-6 col-md-6">
                  <p class="calling"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_cont_phone_icon','fas fa-phone')); ?>"></i><a href="tel:<?php echo esc_attr( get_theme_mod('vw_hospital_lite_contact_call','') ); ?>"><?php echo esc_html(get_theme_mod('vw_hospital_lite_contact_call',''));?></a></p>
                </div>
              <?php } if( get_theme_mod('vw_hospital_lite_contact_email','') != '') { ?>
                <div class="col-lg-6 col-md-6">
                  <p class="email"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_cont_email_icon','fas fa-envelope')); ?>"></i><a href="mailto:<?php echo esc_attr(antispambot(get_theme_mod('vw_hospital_lite_contact_email',''))); ?>"><?php echo esc_html(antispambot(get_theme_mod('vw_hospital_lite_contact_email',''))); ?></a></p>
                </div>
              <?php } ?>
              </div>
            </div>
          <div class="clear"></div>
        </div>
      </div>
    </div>
    <div class="menubar <?php if( get_theme_mod( 'vw_hospital_lite_sticky_header', false) == 1 || get_theme_mod( 'vw_hospital_lite_stickyheader_hide_show', false) == 1) { ?> header-sticky"<?php } else { ?>close-sticky <?php } ?>">
      <div class="container">
        <div class="row">
          <div class="<?php echo esc_html( $colmd ); ?> align-self-center">
              <div class="toggle-nav mobile-menu">
                <button onclick="vw_hospital_lite_menu_open_nav()" class="responsivetoggle"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_res_open_menu_icon','fas fa-bars')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Open Button','vw-hospital-lite'); ?></span></button>
              </div>
            <div id="mySidenav" class="nav sidenav">
              <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'vw-hospital-lite' ); ?>">
                <?php 
                    wp_nav_menu( array( 
                      'theme_location' => 'primary',
                      'container_class' => 'main-menu clearfix' ,
                      'menu_class' => 'clearfix',
                      'items_wrap' => '<ul id="%1$s" class="%2$s mobile_nav">%3$s</ul>',
                      'fallback_cb' => 'wp_page_menu',
                    ) );
                ?>
                <a href="javascript:void(0)" class="closebtn mobile-menu" onclick="vw_hospital_lite_menu_close_nav()"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_res_close_menu_icon','fas fa-times')); ?>"></i><span class="screen-reader-text"><?php esc_html_e('Close Button','vw-hospital-lite'); ?></span></a>
              </nav>
            </div>
          </div>
          <?php if ( 'Disable' != $vw_hospital_lite_searchbox ) {?>
            <div class="col-lg-4 col-md-5 align-self-center">
              <div class="header-right-wrap">
                <div class="search-icon position-relative">
                  <?php get_search_form(); ?>
                </div>
                <div class="auth-buttons">
                  <?php if ( is_user_logged_in() ) : 
                    $current_user = wp_get_current_user();
                  ?>
                    <span class="user-welcome">Chào, <?php echo esc_html($current_user->display_name); ?></span>
                    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="btn-auth btn-logout">Đăng xuất</a>
                  <?php else : ?>
                    <a href="<?php echo home_url('/dang-nhap/'); ?>" class="btn-auth btn-login">Đăng nhập</a>
                    <a href="<?php echo home_url('/dang-ky/'); ?>" class="btn-auth btn-register">Đăng ký</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </header>

  <?php if(get_theme_mod('vw_hospital_lite_loader_enable',false) == 1 || get_theme_mod('vw_hospital_lite_responsive_preloader_hide',false) == 1) { ?>
    <div id="preloader">
      <div class="loader-inner">
        <div class="loader-line-wrap">
          <div class="loader-line"></div>
        </div>
        <div class="loader-line-wrap">
          <div class="loader-line"></div>
        </div>
        <div class="loader-line-wrap">
          <div class="loader-line"></div>
        </div>
        <div class="loader-line-wrap">
          <div class="loader-line"></div>
        </div>
        <div class="loader-line-wrap">
          <div class="loader-line"></div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php if ( is_singular() && has_post_thumbnail() ) : ?>
    <?php
      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'vw-hospital-lite-post-image-cover' );
      $post_image = $thumb['0'];
    ?>
    <div class="header-image bg-image" style="background-image: url(<?php echo esc_url( $post_image ); ?>)">

      <?php the_post_thumbnail( 'vw-hospital-lite-post-image' ); ?>

    </div>

  <?php elseif ( get_header_image() ) : ?>
  <div class="header-image bg-image" style="background-image: url(<?php header_image(); ?>)">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
      <img src="<?php header_image(); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php the_title(); ?> post thumbnail image">
    </a>
  </div>
  <?php endif; // End header image check. ?>