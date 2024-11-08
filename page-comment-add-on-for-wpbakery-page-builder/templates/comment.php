<?php
/**
 * The template for displaying Comments.
 *
 */
if ( post_password_required() )
    return;
?>
<div id="comments" class="comments-area comments-section">
    <?php if ( have_comments() ) : ?>
        <ul class="comment-list sssssss">
            <?php
                wp_list_comments( array(
                    'style'       => 'ul',
                    'max_depth'   => 0,
                    'avatar_size' => 74,
                    'reverse_top_level' => get_option( 'default_comments_page' ) === 'oldest' ? false : true,
                ) );
            ?>
            <button class="loadMore" id="loadMore_new" style="display: none;"><?php esc_html_e( 'Load More' , PCAFWPB_TEXT_DOMAIN ); ?></button>
        </ul><!-- .comment-list -->
        <?php if ( ! comments_open() && get_comments_number() ) : ?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.' , PCAFWPB_TEXT_DOMAIN ); ?></p>
        <?php endif; ?>
    <?php endif; // have_comments() ?>
    <?php comment_form(); ?>
</div><!-- #comments -->
