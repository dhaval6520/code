<?php

// set grid view 
function team_view_front($atts) {
	ob_start();
	$args['post_type'] = 'member_team';

	if (isset($atts['limit'])):
		$args['posts_per_page'] = $atts['limit'];
	else:
		$args['posts_per_page'] = '5';
	endif;

	if (isset($atts['order'])):
		$args['order'] = $atts['order'];
	else:
		$args['order'] = 'ASC';
	endif;

	$args['post_status'] = 'publish';

    if (isset($atts['content_limit'])):
		$limit = $atts['content_limit'];
	else:
		$limit = '20';
	endif;
	
	if(isset($atts['view'])):
		switch ($atts['view']) {
			case 'square':
				$class = 'square';		
				break;
			case 'round':
				$class = 'round';		
				break;
			default:
				$class = 'rounded';
				break;
		}else:
		$class = 'rounded';
	endif;

	if (isset($atts['column'])):
		switch ($atts['column']) {
		case '1':
			$column = '12';
			$col_5 = 'col-md-5 col-sm-5 col-xs-12 member-col-one';
			$col_7 = 'col-md-7 col-sm-7 col-xs-12 member-col-seven';
			$limit = '15';
			break;
		case '2':
			$column = '6';
			$col_5 = '';
			$col_7 = '';
			break;
		case '3':
			$column = '4';
			$col_5 = '';
			$col_7 = '';
			break;
		case '4':
			$column = '3';
			$col_5 = '';
			$col_7 = '';
			break;
		case '5':
			$column = '2';
			$col_5 = '';
			$col_7 = '';
			break;
		case '6':
			$column = '2';
			$col_5 = '';
			$col_7 = '';
			break;
		default:
			$column = '4';
			break;
		} else :
		$column = '4';
	endif;

    
    if(isset($atts['group'])):
		if ($atts['group'] != ''):
    	    $args['tax_query'] = array(
    	    		array(
    	   				'taxonomy' => 'team_group',
    	   				'terms' => $atts['group'],
    	   				'field' => 'slug',
    	   				'include_children' => true,
    	   			)
	   			);
    	endif;
    endif;
	
	$team = new WP_Query($args);

	while ($team->have_posts()): $team->the_post();
		$post_id = get_the_ID();
		

		$img = get_the_post_thumbnail('', 'medium', '');

		if ( $img != '' ):
			$img = get_the_post_thumbnail('', 'medium', '');
		else:
			$img = '<img src="'.TEAM_VIEW_URI.'/images/user-icon.jpg" class="demo-image"/>';
		endif;

		echo '<div class="team-grid text-center col-md-' . $column . ' col-sm-' . $column . ' col-xs-12">
				  <div class="team-content '.$class.'">
				  <div class="member-image '.$col_5.'">
				  		<a href="' . get_permalink() . '" class="image-link" title="' . get_the_title() . '">' . $img . '</a>
				  </div>
				  <div class="team-section '.$col_7.'">
				  <div class="member-title">
				  	<a href="' . get_permalink() . '" title="' . get_the_title() . '"><h4>' . get_the_title() . '</h4>
				  	<div class="title-border"></div>
				  	</a>
				  </div>
				  <div class="member-designation">' . get_post_meta($post_id, 'team_view_designation', true) . '</div>
				  <div class="member-description"><p>"'.wp_trim_words( get_the_content(), $limit, '...' ).'"</p></div>
					<div class="social-media-links">
				<ul>';
		
		echo '</ul>
		      </div>
			</div>
			</div>
		 </div>';
	endwhile;
	?>
	  <div id="content1">
  
</div>
<div class="Loadmore">
  <a id="more_posts" class="lod-button">Load More</a>
</div>
<script type="text/javascript">
   var ajaxUrl = "<?php echo admin_url('admin-ajax.php', null); ?>";
   var page = 1; // What page we are on.
   var ppp = 5; // Post per page
   
   jQuery("#more_posts").on("click",function(){ // When btn is pressed.
   jQuery("#more_posts").attr("disabled",true); // Disable the button, temp.
   jQuery.post(ajaxUrl, {
       action:"more_post_ajax",
       offset: (page * ppp),
       ppp: ppp
   }).success(function(posts){
       page++;
       jQuery("#content1").append(posts);
       jQuery("#more_posts").attr("disabled",false);
   });
   });
</script>


	<?php
	return ob_get_clean();
}

add_shortcode('team-grid', 'team_view_front');

