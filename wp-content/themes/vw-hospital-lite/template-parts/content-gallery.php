<?php
/**
 * The template part for displaying gallery post
 *
 * @package VW Hospital Lite
 * @subpackage vw-hospital-lite
 * @since VW Hospital Lite 1.0
 */
?>
<?php 
  $vw_hospital_lite_archive_year  = get_the_time('Y'); 
  $vw_hospital_lite_archive_month = get_the_time('m'); 
  $vw_hospital_lite_archive_day   = get_the_time('d'); 
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('inner-service'); ?>>
  <div class=" services-box wow zoomInDown delay-1000" data-wow-duration="2s">
    <div class="page-box">
      <div class="box-image">
        <?php
          if ( ! is_single() ) {
            // If not a single post, highlight the gallery.
            if ( get_post_gallery() ) {
              echo '<div class="entry-gallery">';
                echo ( get_post_gallery() );
              echo '</div>';
            };
          };
        ?>
      </div>
      <h2><a href="<?php echo the_permalink(); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title();?><span class="screen-reader-text"><?php the_title(); ?></span></a></h2> 
      <?php if( get_theme_mod( 'vw_hospital_lite_toggle_postdate',

true) == 1 || get_theme_mod( 'vw_hospital_lite_toggle_author',

true) == 1 || get_theme_mod( 'vw_hospital_lite_toggle_comments',

true) == 1 || get_theme_mod( 'vw_hospital_lite_toggle_time',

true) == 1) { ?>
        <div class="metabox">
          <?php if(get_theme_mod('vw_hospital_lite_toggle_postdate',true)==1){ ?>
            <span class="entry-date"><i class="fas fa-calendar-alt"></i><a href="<?php echo esc_url( get_day_link( $vw_hospital_lite_archive_year, $vw_hospital_lite_archive_month, $vw_hospital_lite_archive_day)); ?>"><?php echo esc_html( get_the_date() ); ?><span class="screen-reader-text"><?php echo esc_html( get_the_date() ); ?></span></a></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_toggle_author',true)==1){ ?>
            <span><?php echo esc_html(get_theme_mod('vw_hospital_lite_meta_field_separator'));?></span> <i class="far fa-user"></i><span class="entry-author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>"><?php the_author(); ?></a></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_toggle_comments',true)==1){ ?>
            <span><?php echo esc_html(get_theme_mod('vw_hospital_lite_meta_field_separator'));?></span> <i class="fas fa-comments"></i><span class="entry-comments"><?php comments_number( __('0 Comments','vw-hospital-lite'), __('0 Comments','vw-hospital-lite'), __('% Comments','vw-hospital-lite')); ?></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_toggle_time',true)==1){ ?>
            <span><?php echo esc_html(get_theme_mod('vw_hospital_lite_meta_field_separator'));?></span> <i class="fas fa-clock"></i><span class="entry-time"><?php echo esc_html( get_the_time() ); ?></span>
          <?php } ?>
        </div>
      <?php } ?>           
      <div class="box-content">
        <div class="entry-content">
          <p>
            <?php $vw_hospital_lite_theme_lay = get_theme_mod( 'vw_hospital_lite_excerpt_settings','Excerpt');
            if($vw_hospital_lite_theme_lay == 'Content'){ ?>
              <?php the_content(); ?>
            <?php }
            if($vw_hospital_lite_theme_lay == 'Excerpt'){ ?>
              <?php if(get_the_excerpt()) { ?>
                <?php $vw_hospital_lite_excerpt = get_the_excerpt(); echo esc_html( vw_hospital_lite_string_limit_words( $vw_hospital_lite_excerpt, esc_attr(get_theme_mod('vw_hospital_lite_excerpt_number','30')))); ?> <?php echo esc_html(get_theme_mod('vw_hospital_lite_excerpt_suffix',''));?>
              <?php }?>
            <?php }?>
          </p>
        </div>
      </div>
      <?php if( get_theme_mod('vw_hospital_lite_category_hide_show',

true) == 1){ ?>
        <div class="cat-box">
          <i class="fas fa-folder-open"></i>
          <?php foreach((get_the_category()) as $category) { echo esc_html($category->cat_name) . ' '; } ?>
        </div>
      <?php } ?>
    </div>
   </div>
  <div class="clearfix"></div>
</article>