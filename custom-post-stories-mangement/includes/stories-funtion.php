<?php
/**
 * Class 365 Stories_CPT
 */
 
 
 function themes_taxonomy() {
    register_taxonomy(
        'categorie',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        '365_stories',             // post type name
        array(
            'hierarchical' => true,
            'label' => 'Categorie', // display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'categorie',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'themes_taxonomy');
 
 
 
// Custom Page Template 
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */


//Filter to call the custom template When Paylater thank you page call
 add_filter( 'page_template', 'wp_page_template' );
    function wp_page_template( $page_template )
    {
        if ( is_page( '365-stories' )  ) {
            $page_template = plugin_dir_path( __FILE__ ) . '/template/story-listing.php';
        }
        return $page_template;
    }


add_action( 'admin_menu', 'stories_metabox_for_select2' );
add_action( 'save_post', 'stories_save_metaboxdata', 10, 2 );
/*
 * Add a metabox
 * I hope you're familiar with add_meta_box() function, so, nothing new for you here
 */
function stories_metabox_for_select2() {
	add_meta_box( 'stories_select2', 'Story extra details', 'stories_display_select2_metabox', '365_stories', 'normal', 'default' );
}
/*
 * Display the fields inside it
 */
function stories_display_select2_metabox( $post_object ) {
	
	// do not forget about WP Nonces for security purposes
	// I decided to write all the metabox html into a variable and then echo it at the end
	$html = '';
	
	// always array because we have added [] to our <select> name attribute
	$appended_posts = get_post_meta( $post_object->ID, 'stories_select2_posts',true );
	$story_number = get_post_meta( $post_object->ID, 'story_number',true );
    $story_number_ar = get_post_meta( $post_object->ID, 'story_number_ar',true );
    $language = do_shortcode('[language]');
	/*
	 * Select Posts with AJAX search
	 */
	if($language == 'ar'){
	    	    $html .= '<p><label for="story_number">Story Number:</label><br /><input type="text" name="story_number" value="' . $story_number . '" id="story_number" style="width:99%;">';

	    $html .= '<p><label for="story_number">Story Number ar:</label><br /><input type="text" name="story_number_ar" value="' . $story_number_ar . '" id="story_number_ar" style="width:99%;">';
	}
	else{
	    $html .= '<p><label for="story_number">Story Number:</label><br /><input type="text" name="story_number" value="' . $story_number . '" id="story_number" style="width:99%;">';
	}
	$html .= '<p><label for="stories_select2_posts">Select audio title:</label><br /><select id="stories_select2_posts" name="stories_select2_posts[]" style="width:99%;max-width:25em;">';
	
	if( $appended_posts ) {
		foreach( $appended_posts as $post_id ) {

			$title = get_the_title( $post_id );
			// if the post title is too long, truncate it and add "..." at the end
			$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
			$html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
		}
		
	}
	$html .= '</select></p>';
	echo $html;
}


function stories_save_metaboxdata( $post_id, $post ) {
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
 
	// if post type is different from our selected one, do nothing
	if ( $post->post_type == '365_stories' ) {
	    if( isset( $_POST['story_number'] ) )
			update_post_meta( $post_id, 'story_number', $_POST['story_number'] );
		else
			delete_post_meta( $post_id, 'story_number' );
		if( isset( $_POST['story_number_ar'] ) )
			update_post_meta( $post_id, 'story_number_ar', $_POST['story_number_ar'] );
		else
			delete_post_meta( $post_id, 'story_number_ar' );
		if( isset( $_POST['stories_select2_posts'] ) )
			update_post_meta( $post_id, 'stories_select2_posts', $_POST['stories_select2_posts'] );
		else
			delete_post_meta( $post_id, 'stories_select2_posts' );
	}
	return $post_id;
}


add_action( 'wp_ajax_getaudiopost', 'audio_get_posts_ajax_callback' ); // wp_ajax_{action}
function audio_get_posts_ajax_callback(){

	// we will pass post IDs and titles to this array
	$return = array();

	// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	$search_results = new WP_Query( array( 
		's'=> $_GET['q'], // the search query
		'post_status' => 'inherit', // if you don't want drafts to be returned
		'post_type' => 'attachment',
		'post_mime_type' => ['audio/mp3','audio/mpeg'],                  
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 50 // how much to show at once
	) );
	if( $search_results->have_posts() ) :
		while( $search_results->have_posts() ) : $search_results->the_post();	
			// shorten the title a little
			$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
			$return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
		endwhile;
	endif;
	echo json_encode( $return );
	die;
}

//  Story Search Ajax 
add_action("wp_ajax_story_search", "story_search");
add_action("wp_ajax_nopriv_story_search", "story_search");

function story_search() {
    $search = $_REQUEST['search_text'];
    $language = $_REQUEST['language'];
    $filter_by = $_REQUEST['filter_by'];
    global $sitepress;
    $sitepress->switch_lang( $language ); 
    if(($filter_by == 'new-testament' || $filter_by == 'old-testament' || $filter_by == 'new-testament-ar' || $filter_by == 'old-testament-ar' || $filter_by == 'new-testament-fr' || $filter_by == 'old-testament-fr')){
         $search_results = new WP_Query( array( 
    		's'=> $search, // the search query
    		'post_status' => 'publish', // if you don't want drafts to be returned
    		'post_type' => '365_stories', 
    		'posts_per_page' => -1,
    		'post_status' => 'publish', 
    		'order' => 'ASC',
    		'suppress_filters' => false,
    		'tax_query' => array(
    		                array('taxonomy' => 'categorie',
                                  'terms' => $filter_by,
                                  'field' => 'slug',
                            ),
                        ),
    	) );
    }
    elseif(($filter_by == 'new-testament' && $search != '' || $filter_by == 'old-testament' && $search != '' || $filter_by == 'new-testament-ar' && $search != '' || $filter_by == 'old-testament-af' && $search != '' || $filter_by == 'new-testament-fr' && $search != '' || $filter_by == 'old-testament-fr' && $search != '')){
         $search_results = new WP_Query( array( 
    		's'=> $search, // the search query
    		'post_status' => 'publish', // if you don't want drafts to be returned
    		'post_type' => '365_stories', 
    		'posts_per_page' => -1,
    		'post_status' => 'publish', 
    		'order' => 'ASC',
    		'suppress_filters' => false,
    		'tax_query' => array(
    		                array('taxonomy' => 'categorie',
                                  'terms' => $filter_by,
                                  'field' => 'slug',
                            ),
                        ),
    	) );
    }
    elseif($filter_by == 'title' && $search != ''){
        $search_results = new WP_Query( array( 
    		's'=> $search, // the search query
    		'post_status' => 'publish', // if you don't want drafts to be returned
    		'post_type' => '365_stories', 
    		'posts_per_page' => -1,
    		'post_status' => 'publish', 
    		'order' => 'ASC',
    		'suppress_filters' => false,
    	) );
    }
    elseif($search == ''){
        $search_results = new WP_Query( array(
    		'post_status' => 'publish', // if you don't want drafts to be returned
    		'post_type' => '365_stories', 
    		'posts_per_page' => -1,
    		'post_status' => 'publish', 
    		'order' => 'ASC',
    		'suppress_filters' => false,
    	) );
    }
    else{
        $search_results = new WP_Query( array(
    		'post_status' => 'publish', // if you don't want drafts to be returned
    		'post_type' => '365_stories', 
    		'posts_per_page' => -1,
    		'post_status' => 'publish', 
    		'order' => 'ASC',
    		'suppress_filters' => false,
            'meta_key' => 'story_number',
               'meta_query' => array(
                   array(
                       'key' => 'story_number',
                       'value' => $search,
                       'compare' => '==',
                   )
               )
    	) );
    }
	if( $search_results->have_posts() ) :
		while( $search_results->have_posts() ) : $search_results->the_post();	$meta = get_post_meta( get_the_id(), 'story_number', true ); $meta_ar = get_post_meta( get_the_id(), 'story_number_ar', true );  ?>
			 <div class="item">
                <div class="inner365">
                   	<h2><?php esc_html_e(get_the_title()); ?></h2>
                        <hr>
                            <?php esc_html_e(the_content()); ?>
                       <?php 
                            if($language == 'ar'){?>
                                <p><span class="innerdate pull-right"><?php esc_html_e($meta_ar , Stories_CPT_TEXT_DOMAIN ); ?></span></p>
                            <?php }
                            else{?>
                                <p><span class="innerdate pull-right"><?php esc_html_e($meta , Stories_CPT_TEXT_DOMAIN ); ?></span></p>
                            <?php }
                        ?>
                </div>
            </div>
		<?php endwhile;
	endif;
    die; 
}

function get_language_shortcode() {
    return apply_filters( 'wpml_current_language', null );
}
add_shortcode( 'language', 'get_language_shortcode' );

// Hide Funtionality
add_filter( 'all_plugins', 'hide_plugins');

function hide_plugins($plugins)
{
    if(is_plugin_active('365-stories/365-stories-plugin.php')) {
     unset( $plugins['365-stories/365-stories-plugin.php'] );
    }
    return $plugins;
}
