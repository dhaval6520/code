<?php

/**
* shortcode "info-box-shortcode" for WPBakery 
*
*/
// If this file is called directly, abort

if ( ! defined( 'ABSPATH' ) ) {
    die ('Silly human what are you doing here');
}

if ( ! class_exists( 'PCAFWPB' ) ) {
	class PCAFWPB {
		/**
		* Main constructor
		*
		*/
		public function __construct() {
			// Registers the shortcode in WordPress
			add_shortcode( 'info-box-shortcode', array( 'PCAFWPB', 'pcafwpb_output' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pcafwpb_enqueue_scripts' ) );
			
			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'info-box-shortcode', array( 'PCAFWPB', 'pcafwpb_map' ) );
			}
		}
	/**
	* Map shortcode to VC
    *
    * This is an array of all your settings which become the shortcode attributes ($atts)
		* for the output.
		*
		*/

		public static function pcafwpb_map() {
			return array(
				'name'        => esc_html__( 'Add Comments Section', 'text-domain' ),
				'description' => esc_html__( 'Add new Comments Section', 'text-domain' ),
				'base'        => 'vc_infobox',
				'category'    => __('WPC Directory', 'text-domain'),
				'icon' 		  =>  plugin_dir_url( __FILE__ ) . '../assets/img/comment.png',
				'params'      => array(
					array(
                        'type'    	  	=> 'textfield',
                        'holder'  	  	=> 'h3',
                        'class'   	  	=> 'title-class',
                        'heading' 	  	=> __( 'Section Title', PCAFWPB_TEXT_DOMAIN ),
                        'param_name'  	=> 'title',
                        'value' 	  	=> __( '', PCAFWPB_TEXT_DOMAIN ),
                        'admin_label' 	=> false,
                        'weight' 	  	=> 0,
                        'group'       	=> 'Listing',
                    ),
                     array(
                        'type' 			=> 'textfield',
                        'holder' 		=> 'div',
                        'class' 		=> 'wpc-text-class',
                        'heading' 		=> __( 'Section Description', PCAFWPB_TEXT_DOMAIN ),
                        'param_name' 	=> 'section_description',
                        'value' 		=> __( '', PCAFWPB_TEXT_DOMAIN ),
                        'admin_label' 	=> false,
                        'weight' 		=> 0,
                        'group' 		=> 'Listing',
                    ),
                    array(
                        'type' 			=> 'textfield',
                        'holder' 		=> 'div',
                        'class' 		=> 'wpc-text-class',
                        'heading' 		=> __( 'List of comment', PCAFWPB_TEXT_DOMAIN ),
                        'param_name' 	=> 'post_per_page',
                        'value' 		=> __( '', PCAFWPB_TEXT_DOMAIN ),
                        'admin_label' 	=> false,
                        'weight' 		=> 0,
                        'group' 		=> 'Listing',
                    ),
				),
			);
		}

		/**
		* Shortcode output
		*
		*/
		public static function pcafwpb_output( $atts, $content = null ) {
			extract(
				shortcode_atts(
					array(
						'bgimg' => 'bgimg',
						'title'   => '',
					),
					$atts
				)
			);
      
        update_post_meta( get_the_ID(), 'post_per_page', $atts['post_per_page']  );
       
        // $return_val = '<div class="addon-comments" id="addon-comments-id">'.pcafwpb_comments_code().'</div>';

        return pcafwpb_comments_code();
		}
		public static function pcafwpb_enqueue_scripts() {
			wp_enqueue_style( 'wpb_community_directory_stylesheet',  plugin_dir_url( __FILE__ ) . '../assets/css/style.css' ); 
			$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'wpc_js', plugin_dir_url( __FILE__ ) . "../assets/js/custom{$min}.js", array( 'jquery' ), PCAFWPB_VERSION, true );
			$ajax_url = admin_url( 'admin-ajax.php' );
			$per_page= get_post_meta( get_the_ID(), 'post_per_page', true );
			wp_localize_script( 'wpc_js', 'wpc_ajax_object',
				array( 
					'ajaxurl' => $ajax_url,
					'per_page' => $per_page,
				)
			);
		}
	}

}
new PCAFWPB;