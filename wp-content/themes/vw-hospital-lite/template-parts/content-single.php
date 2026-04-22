<?php
/**
 * The template part for displaying single post content 
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
  <div class="single-post">
    <h1><?php the_title();?></h1>
      <?php if( get_theme_mod( 'vw_hospital_lite_single_postdate',true) == 1 || get_theme_mod( 'vw_hospital_lite_single_author',true) == 1 || get_theme_mod( 'vw_hospital_lite_single_comments',true) == 1 || get_theme_mod( 'vw_hospital_lite_single_time',true) == 1) { ?>
        <div class="metabox">
          <?php if(get_theme_mod('vw_hospital_lite_single_postdate',true)==1){ ?>
            <span class="entry-date"><i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_single_postdate_icon','fas fa-calendar-alt')); ?>"></i><a href="<?php echo esc_url( get_day_link( $vw_hospital_lite_archive_year, $vw_hospital_lite_archive_month, $vw_hospital_lite_archive_day)); ?>"><?php echo esc_html( get_the_date() ); ?><span class="screen-reader-text"><?php echo esc_html( get_the_date() ); ?></span></a></span><span><?php echo esc_html(get_theme_mod('vw_hospital_lite_single_post_meta_field_separator', '|'));?></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_single_author',true)==1){ ?>
             <i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_single_author_icon','far fa-user')); ?>"></i><span class="entry-author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>"><?php the_author(); ?></a></span><span><?php echo esc_html(get_theme_mod('vw_hospital_lite_single_post_meta_field_separator', '|'));?></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_single_comments',true)==1){ ?>
            <i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_single_comments_icon','fas fa-comments')); ?>"></i><span class="entry-comments"><?php comments_number( __('0 Comments','vw-hospital-lite'), __('0 Comments','vw-hospital-lite'), __('% Comments','vw-hospital-lite')); ?></span><span><?php echo esc_html(get_theme_mod('vw_hospital_lite_single_post_meta_field_separator', '|'));?></span>
          <?php } ?>

          <?php if(get_theme_mod('vw_hospital_lite_single_time',true)==1){ ?>
             <i class="<?php echo esc_attr(get_theme_mod('vw_hospital_lite_single_time_icon','fas fa-clock')); ?>"></i><span class="entry-time"><?php echo esc_html( get_the_time() ); ?></span><span><?php echo esc_html(get_theme_mod('vw_hospital_lite_single_post_meta_field_separator'));?></span>
          <?php } ?>
          <?php vw_hospital_lite_edit_link(); ?>
        </div>
      <?php } ?>
      <?php if(has_post_thumbnail()) { ?>
        <div class="feature-box">   
          <?php the_post_thumbnail(); ?>
        </div>
        <hr>                    
      <?php } ?> 
      <?php if( get_theme_mod( 'vw_hospital_lite_single_post_category',

true) == 1) { ?>
        <div class="single-post-category mt-3">
            <span class="category"><?php esc_html_e('Categories:','vw-hospital-lite'); ?></span>
            <?php the_category(); ?>
        </div>
      <?php }?>
      <div class="entry-content">
        <?php the_content(); ?>
        <?php if(get_theme_mod('vw_hospital_lite_toggle_tags',true)==1){ ?>
          <div class="tags"><?php the_tags(); ?></div> 
        <?php } ?>   
      </div> 
      <?php
        // If comments are open or we have at least one comment, load up the comment template
        if ( comments_open() || '0' != get_comments_number() )
            comments_template();
        
        if ( is_singular( 'attachment' ) ) {
          // Parent post navigation.
          the_post_navigation( array(
            'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'vw-hospital-lite' ),
          ) );
        } elseif ( is_singular( 'post' ) ) {
          // Previous/next post navigation.
          the_post_navigation( array(
            'next_text' => '<span class="meta-nav" aria-hidden="true">' .esc_html(get_theme_mod('vw_hospital_lite_single_blog_next_navigation_text','NEXT PAGE')) . '</span> ' .
              '<span class="screen-reader-text">' . __( 'Next post:', 'vw-hospital-lite' ) . '</span> ' .
              '<span class="post-title">%title</span>',
            'prev_text' => '<span class="meta-nav" aria-hidden="true">' .esc_html(get_theme_mod('vw_hospital_lite_single_blog_prev_navigation_text','PREVIOUS PAGE')) . '</span> ' .
              '<span class="screen-reader-text">' . __( 'Previous post:', 'vw-hospital-lite' ) . '</span> ' .
              '<span class="post-title">%title</span>',
          ));
        }
      ?>
  </div>
  <?php get_template_part('template-parts/related-posts'); ?>
</article>