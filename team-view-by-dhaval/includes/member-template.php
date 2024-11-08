<?php
/**
* The template for displaying all single Member details
*
*
* @package WordPress
* @subpackage team-member
* @since 1.0
* @version 1.0
*/
get_header();
?>
<div class="member-page">
 <div class="row">
<?php
while ( have_posts()) {
the_post(); 
$post_id = get_the_ID();

$img = get_the_post_thumbnail('', 'medium', '');
		if ( $img != '' ):
			$img = get_the_post_thumbnail('', 'medium', '');
		else:
			$img = '<img src="'.site_url().'/wp-content/plugins/dm-team/images/user-icon.jpg" class="demo-image"/>';
		endif;
echo '<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="member-content">
			<div class="member-image">
				' .$img . '
			</div>
		</div>
		<div class="member-designation text-center">
			<h1>'.get_post_meta($post->ID, 'member_designation', true).'</h1>
		</div>';
		
		echo '</div>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<div class="text-left title-heading">
				<h1>'.get_the_title().'</h1>
			</div>
			<div class="member-text">
			'.get_the_content().'
			</div>
		</div>';
}
?>
</div>
</div>
<!-- Members Pagination -->
<div class="pagination-button">
   <div class="page-link-previous"><?php previous_post_link( '%link', '<i class="fa fa-angle-double-left"></i> Previous' )?></div>
   <div class="page-link-next"><?php next_post_link( '%link', 'Next <i class="fa fa-angle-double-right"></i>' )?></div>
</div>

<?php
//  Page sidebar section
get_sidebar();

// Default Footer section.
get_footer();
?>