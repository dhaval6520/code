<?php
/**
 * Template Name: Dpopay Error
 *
 **/		 
ob_start();
get_header();  ?>

<?php while ( have_posts() ) : the_post(); ?>

<!-- Banners -->

<?php beevent_page_title(); ?>

<!-- /Banners --> 

<div class="inner_pages">

	<div class="container">

    	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        	<div class="content_pages">

    
				<div class="col-lg-12 ">
				<h3><?php echo apply_filters( 'give_dpo_pay_addon_management', __( 'Your Donation has some issue!', 'give' ) ); ?>
				</h3>
				<span><?php echo $_GET['issue']; ?></span>
				<span>
				</span>
			</div>

            </div>

        </article>
	
</div>
</div>
<?php endwhile; wp_reset_query(); ?>  
<?php get_footer();
