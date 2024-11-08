<?php
/**
 * Template Name: Dpopay Thank You
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

             
    
    <?php if (isset($_GET['TransactionToken'])) {
	global $foo_class;
	$foo_class = new give_dpo_pay_common_class ();
	$transactionToken = sanitize_text_field($_GET['TransactionToken']);
	$payment_id = sanitize_text_field($_GET['payment_id']);
	$foo_class->dpo_pay_paymentverification($transactionToken);
	$current_status = get_post_status ( $payment_id );
	if ( false !== $current_status ) {
	    if ($current_status == 'publish' ) {?>
	        <div class="site-content" id="content">
				<main id="main" class="site-main" role="main">
					<div class="row padding-">
						<div class="col-lg-12 ">
							<h3><?php echo apply_filters( 'give_dpo_pay_addon_management', __( 'Thank you for Registering.', 'give' ) ); ?></h3>
							<p>You will receive an email with your receipt.</p>
							<p>We look forward to seeing you at the event.</p>
                            <p>For any inquiries, write to abli@biblesociety-zambia.org </p>
							<?php 
								$current_getway = give_get_meta( $payment_id, '_give_payment_gateway', true );
								if($current_getway=='dpo-pay'){
									$options = give_get_settings();
									$offline_instruction = $options['dpo_pay_description'];
									?>
									<span class="no-fields" id="give_offline_payment_info">
										<?php //echo stripslashes( $offline_instruction ); ?>
									</span>
									<?php
								}
							?>
						</div>
					</div>
				</main>
			</div>
	    <?php }
	    else{?>
	    	<div class="site-content" id="content">
				<main id="main" class="site-main" role="main">
					<div class="row padding-">
						<div class="col-lg-12 ">
							<h3>
								<?php echo apply_filters( 'give_dpo_pay_addon_management', __( 'There is some problem in payment please contact our team', 'give' ) ); ?>
							</h3>
							<span>biblesociety@c-metric.com</span>
						</div>
					</div>
				</main>
			</div>
	    <?php }
	}
}
?>

            </div>

        </article>
	
</div>
</div>
<?php endwhile; wp_reset_query(); ?>  
<?php get_footer();