<?php
add_action('init', 'team_view_register_post_types');
function team_view_register_post_types() {
	$labels = array(
		'name' => __('Members', 'team-member'),
		'singular_name' => __('Member', 'team-member'),
		'menu_name' => __('Team', 'team-member'),
		'name_admin_bar' => __('Team', 'team-member'),
		'add_new' => __('Add New Member', 'team-member'),
		'add_new_item' => __('Add New Member', 'team-member'),
		'edit_item' => __('Edit Member', 'team-member'),
		'new_item' => __('New Member', 'team-member'),
		'view_item' => __('View Member', 'team-member'),
		'search_items' => __('Search Member', 'team-member'),
		'not_found' => __('No member found', 'team-member'),
		'not_found_in_trash' => __('No members found in trash', 'team-member'),
		'all_items' => __('All Members', 'team-member'),
	);

	$args = array(
		"label" => __('Teams', 'team-member'),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => true,
		"show_in_menu" => true,
		'menu_icon' => 'dashicons-groups',
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array("slug" => "team-member", "with_front" => true, 'pages' => true, 'feeds' => true),
		"query_var" => true,
		/* Only 3 caps are needed: 'manage_team', 'create_teams', and 'edit_teams'. */
		'capabilities' => array(

			// meta caps (don't assign these to roles)
			'edit_post' => 'edit_team_item',
			'read_post' => 'read_team_item',
			'delete_post' => 'delete_team_item',

			// primitive/meta caps
			'create_posts' => 'create_teams',

			// primitive caps used outside of map_meta_cap()
			'edit_posts' => 'edit_teams',
			'edit_others_posts' => 'manage_team',
			'publish_posts' => 'manage_team',
			'read_private_posts' => 'read',

			// primitive caps used inside of map_meta_cap()
			'read' => 'read',
			'delete_posts' => 'manage_team',
			'delete_private_posts' => 'manage_team',
			'delete_published_posts' => 'manage_team',
			'delete_others_posts' => 'manage_team',
			'edit_private_posts' => 'edit_teams',
			'edit_published_posts' => 'edit_teams',
		),

		"supports" => array("title", "editor", "thumbnail", "author"),
	);
	register_post_type("member_team", $args);

}

add_action('init', 'team_view_register_post_types_cat');
function team_view_register_post_types_cat() {
	$labels = array(
		"name" => __('Team Groups', 'team-member'),
		"singular_name" => __('Team Groups', 'team-member'),
		'search_items' => __('Search Groups', 'team-member'),
		'all_items' => __('All Groups', 'team-member'),
		'parent_item' => __('Parent Groups', 'team-member'),
		'parent_item_colon' => __('Parent Groups:', 'team-member'),
		'edit_item' => __('Edit Groups', 'team-member'),
		'update_item' => __('Update Groups', 'team-member'),
		'add_new_item' => __('Add New Groups', 'team-member'),
		'new_item_name' => __('New Groups Name', 'team-member'),
		'not_found' => __('No Groups found', 'team-member'),
		'not_found_in_trash' => __('No Groups found in trash', 'team-member'),
		'menu_name' => __('Groups', 'team-member'),
	);

	$args = array(
		"label" => __('Team Groups', 'team-member'),
		"labels" => $labels,
		"public" => true,
		"hierarchical" => true,
		"label" => "Team Groups",
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array('slug' => 'team_group', 'with_front' => false, 'pages' => true),
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"show_in_quick_edit" => false,
	);
	register_taxonomy("team_group", array("member_team"), $args);
}

add_action('admin_menu', 'team_view_admin_setup');

function team_view_admin_setup() {
	/* Add meta boxes an save metadata. */
	add_action('add_meta_boxes', 'team_view_add_meta_boxes', 1);
	add_action('save_post', 'save_team_post_options_box', 1, 2);
}

//For Add meta boxes
function team_view_add_meta_boxes() {

	add_meta_box('team-view-member-option', 'Member Options', 'team_view_meta', 'member_team', 'normal', 'high');

}

function team_view_meta($post) {

	global $post;

	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="team_view_meta_nonce" id="team_view_meta_nonce" value="' .

	wp_create_nonce(plugin_basename(__FILE__)) . '" />';

	$inxv_team_designation = get_post_meta($post->ID, 'team_view_designation', true);
	

	?>

		<div class="option-box">
			<p class="option-title"><?php _e('Member designation:', 'team-member');?></p>
			<p class="option-info"></p>
			<input type="text" size="30" placeholder="Team leader"   name="team_view_designation" value="<?=$inxv_team_designation?>" />
		</div>
			
	<?php
}

//For Save meta boxes
function save_team_post_options_box($post_id, $post) {

	if (!isset($_POST['team_view_meta_nonce']) || (!wp_verify_nonce(sanitize_text_field($_POST['team_view_meta_nonce']), plugin_basename(__FILE__)))) {

		return $post->ID;
	}
	if (!current_user_can('edit_post', $post->ID)) {
		return $post->ID;
	}

	$team_view_meta['team_view_designation'] = sanitize_text_field($_POST['team_view_designation']);
	
	foreach ($team_view_meta as $key => $value) {
		if ($post->post_type == 'revision') {
			return;
		}
		$value = implode(',', (array) $value);

		if (get_post_meta($post->ID, $key, FALSE)) {

			update_post_meta($post->ID, $key, $value);

		} else {

			add_post_meta($post->ID, $key, $value);
		}
		if (!$value) {
			delete_post_meta($post->ID, $key);
		}

	}

}

// view designation admin screen
function team_view_add_designation_column($columns) {
    $new = array();	
    $designation = 'Designation';
    $author = 'author';
    
	foreach($columns as $key=>$title) {
	    if($key==$author) {  // when we find the date column
	       $new[$designation] = $designation;  // put the tags column before it
	    }    
	    $new[$key]=$title;
	}  
	return $new;  

    $new_columns = array(
	'designation' => __( 'Designation', 'Designation' )
    );	  

    // Combine existing columns with new columns
    $filtered_columns = array_merge( $columns, $new_columns );

   // Return our filtered array of columns
   return $filtered_columns;
}


add_filter('manage_posts_columns' , 'team_view_add_designation_column');


function team_view_add_designation_column_data($columns){
	global $post;
    switch($columns){
    	case 'Designation' :
    	$inxv_team_designation = get_post_meta($post->ID,'team_view_designation',true);	 
        echo $inxv_team_designation;
        break;
    }
}
add_action('manage_posts_custom_column','team_view_add_designation_column_data',10,2);


?>