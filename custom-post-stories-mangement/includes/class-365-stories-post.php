<?php
/**
 * Class 365 Stories_CPT
 */
class Stories_CPT {

	const Stories_POST_TYPE = '365_stories';

	function __construct()
	{
		$this->init();
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'stories_select2_enqueue' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

	}
	/**
	 * Hook into the appropriate actions when the class is initiated.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'stories_post' ), 0 );
	}
	/**
	 * Add the 365-stories Content Type
	 */
	public static function stories_post() {
		load_plugin_textdomain( Stories_CPT_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		$labels = array(
			'name'                  => _x( 'Stories', 'Post type general name', Stories_CPT_TEXT_DOMAIN ),
			'singular_name'         => _x( 'Stories', 'Post type singular name', Stories_CPT_TEXT_DOMAIN ),
			'menu_name'             => _x( 'Stories', 'Admin Menu text', Stories_CPT_TEXT_DOMAIN ),
			'name_admin_bar'        => _x( 'Stories', 'Add New on Toolbar', Stories_CPT_TEXT_DOMAIN ),
			'add_new'               => __( 'Add New', Stories_CPT_TEXT_DOMAIN ),
			'add_new_item'          => __( 'Add New Stories', Stories_CPT_TEXT_DOMAIN ),
			'new_item'              => __( 'New Stories', Stories_CPT_TEXT_DOMAIN ),
			'edit_item'             => __( 'Edit Stories', Stories_CPT_TEXT_DOMAIN ),
			'view_item'             => __( 'View Stories', Stories_CPT_TEXT_DOMAIN ),
			'all_items'             => __( 'All Stories', Stories_CPT_TEXT_DOMAIN ),
			'search_items'          => __( 'Search Stories', Stories_CPT_TEXT_DOMAIN ),
			'parent_item_colon'     => __( 'Parent Stories:', Stories_CPT_TEXT_DOMAIN ),
			'not_found'             => __( 'No Stories found.', Stories_CPT_TEXT_DOMAIN ),
			'not_found_in_trash'    => __( 'No Stories found in Trash.', Stories_CPT_TEXT_DOMAIN )
		);
		$rewrite = array(
			'slug'                => '365-stories',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true
		);
		$args = array(
			'labels'             => $labels,
			'description'        => '365 Stories custom post type.',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 29,
			'menu_icon'           => 'dashicons-list-view',
			'supports'           => array( 'title', 'editor', 'author' )
		);
		register_post_type( self::Stories_POST_TYPE, $args );
	}
	public static function stories_select2_enqueue() {
		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
		// please create also an empty JS file in your theme directory and include it too
		wp_enqueue_script( 'wpc_js', plugin_dir_url( __FILE__ ) . "../assets/js/mycustom.js", array( 'jquery' ), Stories_CPT_VERSION, true );
	}
	public static function enqueue_scripts() {
	    wp_enqueue_style( 'stories_stylesheet',  plugin_dir_url( __FILE__ ) . '../assets/css/style.css' );
	    wp_enqueue_script( 'stories_js', plugin_dir_url( __FILE__ ) . "../assets/js/stories.js", array( 'jquery' ), Stories_CPT_VERSION, true );
	    $ajax_url = admin_url( 'admin-ajax.php' );
			$per_page= '1';
			wp_localize_script( 'stories_js', 'stories_ajax_object',
				array( 
					'ajaxurl' => $ajax_url,
					'per_page' => $per_page,
				)
			);
	}
}
new Stories_CPT();