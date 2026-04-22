<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Medicare Clinic
 * @subpackage medicare_clinic
 */

?>
<article class="col-lg-4 col-md-6">
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="page-box">
	        <div class="box-image ">
	            <?php the_post_thumbnail();  ?>
	        </div>
		    <div class="box-content text-center">
		    	<h4><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a></h4>
		        <div class="box-info">
				    <?php 
				    // Add 'time' to your default meta order array
				    $medicare_clinic_blog_archive_ordering = get_theme_mod('blog_meta_order', array('date', 'author', 'comment', 'category', 'time'));

				    foreach ($medicare_clinic_blog_archive_ordering as $medicare_clinic_blog_data_order) : 
				        if ('date' === $medicare_clinic_blog_data_order) : ?>
				            <i class="far fa-calendar-alt mb-1"></i>
				            <a href="<?php echo esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))); ?>" class="entry-date">
				                <?php echo esc_html(get_the_date('j F, Y')); ?>
				            </a>
				        <?php elseif ('author' === $medicare_clinic_blog_data_order) : ?>
				            <i class="fas fa-user mb-1"></i>
				            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="entry-author">
				                <?php the_author(); ?>
				            </a>
				        <?php elseif ('comment' === $medicare_clinic_blog_data_order) : ?>
				            <i class="fas fa-comments mb-1"></i>
				            <a href="<?php comments_link(); ?>" class="entry-comments">
				                <?php comments_number(__('0 Comments', 'medicare-clinic'), __('1 Comment', 'medicare-clinic'), __('% Comments', 'medicare-clinic')); ?>
				            </a>
				        <?php elseif ('category' === $medicare_clinic_blog_data_order) : ?>
				            <i class="fas fa-list mb-1"></i>
				            <?php
				            $categories = get_the_category();
				            if (!empty($categories)) :
				                foreach ($categories as $category) : ?>
				                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="entry-category">
				                        <?php echo esc_html($category->name); ?>
				                    </a>
				                <?php endforeach;
				            endif; 
				            ?>
				        <?php elseif ('time' === $medicare_clinic_blog_data_order) : ?>
				            <i class="fas fa-clock mb-1"></i>
				            <span class="entry-time">
				                <?php echo get_the_time(); ?>
				            </span>
				        <?php endif;
				    endforeach; ?>
				</div>
		        <p><?php echo esc_html( wp_trim_words( get_the_content(), get_theme_mod( 'medicare_clinic_excerpt_count', 35 ) ) ); ?></p>
	        	<?php if(get_theme_mod('medicare_clinic_remove_read_button',true) != ''){ ?>
		            <div class="readmore-btn">
		                <a href="<?php echo esc_url( get_permalink() );?>" class="blogbutton-small" title="<?php esc_attr_e( 'Read More', 'medicare-clinic' ); ?>"><?php echo esc_html(get_theme_mod('medicare_clinic_read_more_text',__('Read More','medicare-clinic')));?></a>
		            </div>
	        	<?php }?>
		    </div>
		    <div class="clearfix"></div>
		</div>
	</div>
</article>