<?php
/**
 * The Template for displaying all single posts.
 *
 * @package VW Hospital Lite
 */

get_header(); ?>

<div class="container">
    <main id="maincontent" role="main" class="middle-align">
    	<?php
            $vw_hospital_lite_left_right = get_theme_mod( 'vw_hospital_lite_single_post_sidebar_layout','Right Sidebar');
            if($vw_hospital_lite_left_right == 'Left Sidebar'){ ?>
            <div class="row">
	            <div class="col-lg-4 col-md-4">
					<?php get_sidebar('page'); ?>
				</div>
				<div class="col-lg-8 col-md-8" id="content-vw">
					<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       	while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       	endwhile;

                       	else :
                            get_template_part( 'no-results' );
                       	endif; 
                    ?>
		       	</div>
	       </div>
	    <?php }elseif ($vw_hospital_lite_left_right == 'Right Sidebar'){ ?>
	    	<div class="row">
		       	<div class="col-lg-8 col-md-8" id="content-vw">
		       		<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       	while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       	endwhile;

                       	else :
                            get_template_part( 'no-results' );
                       	endif; 
                    ?>
		       	</div>
				<div class="col-lg-4 col-md-4">
					<?php get_sidebar('page'); ?>
				</div>
			</div>
		<?php }elseif ($vw_hospital_lite_left_right == 'One Column'){ ?>	
			<div id="content-vw">
					<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
				<?php if ( have_posts() ) :
                   	while ( have_posts() ) : the_post();
                        get_template_part( 'template-parts/content-single' ); 
                   	endwhile;

                   	else :
                        get_template_part( 'no-results' );
                   	endif; 
                ?>
	       	</div>
	    <?php }elseif ($vw_hospital_lite_left_right == 'Three Columns'){ ?>
	    	<div class="row">
		    	<div class="sidebar col-lg-3 col-md-3"><?php dynamic_sidebar( 'sidebar-1' ); ?></div>
		       	<div class="col-lg-6 col-md-6" id="content-vw">
		       		<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       	while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       	endwhile;

                       	else :
                            get_template_part( 'no-results' );
                       	endif; 
                    ?>
		       	</div>
				<div class="sidebar col-lg-3 col-md-3"><?php dynamic_sidebar( 'sidebar-2' ); ?></div>
			</div>
		<?php }elseif ($vw_hospital_lite_left_right == 'Three Columns'){ ?>
			<div class="row">
		    	<div class="sidebar col-lg-3 col-md-3"><?php dynamic_sidebar( 'sidebar-1' ); ?></div>
		       	<div class="col-lg-3 col-md-3" id="content-vw">
		       		<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       endwhile;

                       else :
                            get_template_part( 'no-results' );
                       endif; 
                    ?>
		       	</div>
				<div class="sidebar col-lg-3 col-md-3"><?php dynamic_sidebar( 'sidebar-2' ); ?></div>
				<div class="sidebar col-lg-3 col-md-3"><?php dynamic_sidebar( 'sidebar-3' ); ?></div>
			</div>
	    <?php }elseif ($vw_hospital_lite_left_right == 'Grid Layout'){ ?>
	    	<div class="row">
		       	<div class="col-lg-8 col-md-8" id="content-vw">
		       		<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       	while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       	endwhile;

                       	else :
                            get_template_part( 'no-results' );
                       	endif; 
                    ?>
		       	</div>
				<div class="col-lg-4 col-md-4">
					<?php get_sidebar('page'); ?>
				</div>
			</div>
		<?php }else {?>
			<div class="row">
		       	<div class="col-lg-8 col-md-8" id="content-vw">
		       		<?php if(get_theme_mod('vw_hospital_lite_single_post_breadcrumb',

true) == 1){ ?>
                        <div class="bradcrumbs">
                          <?php vw_hospital_lite_the_breadcrumb(); ?>
                        </div>
                    <?php }?>
					<?php if ( have_posts() ) :
                       	while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-single' ); 
                       	endwhile;

                       	else :
                            get_template_part( 'no-results' );
                      	endif; 
                    ?>
		       	</div>
				<div class="col-lg-4 col-md-4">
					<?php get_sidebar('page'); ?>
				</div>
			</div>
		<?php }?>
        <div class="clearfix"></div>
    </main>
</div>

<?php get_footer(); ?>