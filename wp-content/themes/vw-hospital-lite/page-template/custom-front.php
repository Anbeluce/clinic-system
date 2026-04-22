<?php
/**
 * Template Name: Custom Home
 */

get_header(); ?>

<main id="maincontent" role="main">
  <?php do_action( 'vw_hospital_lite_above_slider' ); ?>

  <?php if( get_theme_mod( 'vw_hospital_lite_slider_hide_show', true) == 1 || get_theme_mod( 'vw_hospital_lite_resp_slider_hide_show', true) == 1) { ?>
    <section id="slider">
      <?php if(get_theme_mod('vw_hospital_lite_slider_type', 'Default slider') == 'Default slider' ){ ?>
      <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="<?php echo esc_attr(get_theme_mod( 'vw_hospital_lite_slider_speed',4000)) ?>">
        <?php $vw_hospital_lite_slider_pages = array();
          for ( $count = 1; $count <= 3; $count++ ) {
            $mod = intval( get_theme_mod( 'vw_hospital_lite_slider_page' . $count ));
            if ( 'page-none-selected' != $mod ) {
              $vw_hospital_lite_slider_pages[] = $mod;
            }
          }
          if( !empty($vw_hospital_lite_slider_pages) ) :
            $args = array(
              'post_type' => 'page',
              'post__in' => $vw_hospital_lite_slider_pages,
              'orderby' => 'post__in'
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              $i = 1;
        ?>     
        <div class="carousel-inner" role="listbox">
          <?php  while ( $query->have_posts() ) : $query->the_post(); ?>
            <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
              <?php if(has_post_thumbnail()){
                the_post_thumbnail();
              } else{?>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/block-patterns/images/slider.png" alt="" />
              <?php } ?>
              <div class="carousel-caption">
                <div class="inner_carousel">
                  <h1 class=" wow swing delay-1000" data-wow-duration="3s"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                  <p class=" wow swing delay-1000" data-wow-duration="3s"><?php $vw_hospital_lite_excerpt = get_the_excerpt(); echo esc_html( vw_hospital_lite_string_limit_words( $vw_hospital_lite_excerpt, esc_attr(get_theme_mod('vw_hospital_lite_slider_excerpt_number','30')))); ?></p>
                  <?php
                    $vw_hospital_lite_button_text = get_theme_mod('vw_hospital_lite_slider_button_text','READ MORE');
                    $vw_hospital_lite_button_link = get_theme_mod('vw_hospital_lite_slider_button_link', '');
                    if (empty($vw_hospital_lite_button_link)) {
                      $vw_hospital_lite_button_link = get_permalink();
                    }
                    if ($vw_hospital_lite_button_text || !empty($vw_hospital_lite_button_link)) { ?>
                    <div class=" more-btn wow swing delay-1000" data-wow-duration="3s">         
                      <?php if( get_theme_mod('vw_hospital_lite_slider_button_text','READ MORE') != ''){ ?>
                        <a href="<?php echo esc_url($vw_hospital_lite_button_link); ?>" class="button redmor">
                        <?php echo esc_html($vw_hospital_lite_button_text); ?><span class="screen-reader-text"><?php echo esc_html($vw_hospital_lite_button_text); ?></span>
                        </a>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          <?php $i++; endwhile; 
          wp_reset_postdata();?>
        </div>
        <?php else : ?>
            <div class="no-postfound"></div>
          <?php endif;
        endif;?>
      <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" role="button">
        <span class="carousel-control-prev-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
        <span class="screen-reader-text"><?php esc_html_e( 'Previous','vw-hospital-lite' );?></span>
      </a>
      <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" role="button">
        <span class="carousel-control-next-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
        <span class="screen-reader-text"><?php esc_html_e( 'Next','vw-hospital-lite' );?></span>
      </a>
      </div>  
      <div class="clearfix"></div>
        <?php } else if(get_theme_mod('vw_hospital_lite_slider_type', 'Advance slider') == 'Advance slider'){?>
          <?php echo do_shortcode(get_theme_mod('vw_hospital_lite_advance_slider_shortcode')); ?>
        <?php } ?>
    </section> 
  <?php } ?>

  <?php do_action( 'vw_hospital_lite_below_slider' ); ?>

  <?php if( get_theme_mod('vw_hospital_lite_sec1_title') != ''){ ?>
    <section class=" services wow zoomInDown delay-1000" data-wow-duration="2s">
      <div class="container">
        <?php if( get_theme_mod('vw_hospital_lite_sec1_title') != ''){ ?>     
          <h2><?php echo esc_html(get_theme_mod('vw_hospital_lite_sec1_title','vw-hospital-lite')); ?></h2>
        <?php }?>
        <div class="row">
    			<?php $vw_hospital_lite_service_page = array();
    			for ( $count = 0; $count <= 4; $count++ ) {
    				$mod = intval( get_theme_mod( 'vw_hospital_lite_servicesettings' . $count ));
    				if ( 'page-none-selected' != $mod ) {
    				  $vw_hospital_lite_service_page[] = $mod;
    				}
    			}
    			if( !empty($vw_hospital_lite_service_page) ) :
    			  $args = array(
    			    'post_type' => 'page',
    			    'post__in' => $vw_hospital_lite_service_page,
    			    'orderby' => 'post__in'
    			  );
    			  $query = new WP_Query( $args );
    			  if ( $query->have_posts() ) :
    			    $count = 0;
    					while ( $query->have_posts() ) : $query->the_post(); ?>
    						<div class="col-lg-3 col-md-6">
    							<div class="service-main-box">
  							    <div class="box-image">
  						        <?php the_post_thumbnail(); ?>
  							    </div>
  							    <div class="box-content text-center">
  						        <h3><?php the_title(); ?></h3>
  						        <p><?php $vw_hospital_lite_excerpt = get_the_excerpt(); echo esc_html( vw_hospital_lite_string_limit_words( $vw_hospital_lite_excerpt, esc_attr(get_theme_mod('vw_hospital_lite_service_excerpt_number','30')))); ?>
  						        <div class="clearfix"></div>
                      <?php if( get_theme_mod('vw_hospital_lite_services_button_text','READ MORE') != ''){ ?>
  						          <a class="r_button"  href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('vw_hospital_lite_services_button_text',__('READ MORE','vw-hospital-lite')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('vw_hospital_lite_services_button_text',__('READ MORE','vw-hospital-lite')));?></span></a>
                      <?php } ?>
  							    </div>
    							</div>
    						</div>
    					<?php $count++; endwhile; 
    					wp_reset_postdata();?>
    			  <?php else : ?>
    			      <div class="no-postfound"></div>
    			  <?php endif;
    			endif;?>
    		    <div class="clearfix"></div>
    		</div>
    	</div> 
    </section>
  <?php }?>

  <?php do_action( 'vw_hospital_lite_our_services' ); ?>

  <div class="container">
    <?php while ( have_posts() ) : the_post(); ?>
      <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
  </div>
</main>

<?php get_footer(); ?>