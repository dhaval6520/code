<?php
/**
 * Plugin Name: Team View by Dhaval Kapdane
 * Description: Team Members plugin to provide Team view in different layouts.
 * Author: Dhaval Kapadane
 */

class Emp_Team_Member {

	/**
	 * PHP5 constructor method.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Set the constants needed by the plugin. */
		add_action('plugins_loaded', array(&$this, 'constants'), 1);

		/* Load the functions files. */
		add_action('plugins_loaded', array(&$this, 'includes'), 3);

		/* Load the admin files. */
		add_action('plugins_loaded', array(&$this, 'admin'), 4);

		/* Register activation hook. */
		register_activation_hook(__FILE__, array(&$this, 'activation'));

	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function constants() {

		/* Set constant path to the plugin directory. */
		define('TEAM_VIEW_DIR', trailingslashit(plugin_dir_path(__FILE__)));

		/* Set the constant path to the plugin directory URI. */
		define('TEAM_VIEW_URI', trailingslashit(plugin_dir_url(__FILE__)));

		/* Set the constant path to the includes directory. */
		define('TEAM_VIEW_INCLUDES', TEAM_VIEW_DIR . trailingslashit('includes'));

		/* Set the constant path to the admin directory. */
		define('TEAM_VIEW_ADMIN', TEAM_VIEW_DIR . trailingslashit('admin'));
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function includes() {
		require_once TEAM_VIEW_INCLUDES . 'functions.php';
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	public function admin() {
		require_once TEAM_VIEW_ADMIN . 'admin.php';
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  1.0
	 * @access public
	 * @return void
	 */
	function activation() {

		/* Get the administrator role. */
		$role = get_role('administrator');

		/* If the administrator role exists, add required capabilities for the plugin. */
		if (!empty($role)) {

			$role->add_cap('manage_team');
			$role->add_cap('create_teams');
			$role->add_cap('edit_teams');
		}
	}
}

new Emp_Team_Member();

/* Add stylesheet and javascript files with wp_enqueue_scripts hook */
add_action('wp_enqueue_scripts', 'team_view_style');
function team_view_style(){
	$date = time();
	wp_enqueue_style('team-style', TEAM_VIEW_URI . 'assets/css/style.css?'.$date.'',array(),'1.0','all');
}


/* Adding custom template for member view from includes folder */

add_filter('single_template', 'team_view_page_templates');

function team_view_page_templates($single) {
	global $wp_query, $post;

	if ($post->post_type == "member_team") {
		if (file_exists(TEAM_VIEW_INCLUDES . '/member-template.php')) {
			return TEAM_VIEW_INCLUDES . 'member-template.php';
		}
	}
	return $single;
}


?>
<!-- Ajax -->
<?php 
function more_post_ajax(){

$offset = $_POST["offset"];
$ppp = $_POST["ppp"];
//echo $ppp;
header("Content-Type: text/html");

$args2 = array(
	 'post_type' => 'member_team',
    'posts_per_page' => $ppp,
    'offset' => $offset,    
    'order'         => 'ASC',
);

$custom2 = new WP_Query($args2);

while ($custom2->have_posts()) : $custom2->the_post(); 
	
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

exit;
}

add_action('wp_ajax_nopriv_more_post_ajax', 'more_post_ajax'); 
add_action('wp_ajax_more_post_ajax', 'more_post_ajax');
?>
<!--  -->