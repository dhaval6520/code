<?php
add_action( 'init',__namespace__.'\pcafwpb_comments_code' );
    function pcafwpb_comments_code() {
        if( is_singular() && post_type_supports( get_post_type(), 'comments' ) )
            {
                ob_start();
                add_filter( "comments_template", __namespace__."\pcafwpb_add_comment_template" );
                comments_template();
                return ob_get_clean();
            }
    }

if ( ! function_exists( 'pcafwpb_add_comment_template' ) ) {
    function pcafwpb_add_comment_template( $comment_template ) {
        global $post;
         if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
            return;
        }
        $comment_template = plugin_dir_path( __FILE__ ) . '../templates/comment.php';
        return $comment_template;
    }
}