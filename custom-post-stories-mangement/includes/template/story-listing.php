<?php 
/* Templatesss Name: Story Listing */ 
get_header();
$borntogive_options = get_option('borntogive_options');
$post_format = get_post_format();
borntogive_sidebar_position_module();
$pageSidebarGet = get_post_meta(get_the_ID(), 'borntogive_select_sidebar_from_list', true);
$pageSidebarStrictNo = get_post_meta(get_the_ID(), 'borntogive_strict_no_sidebar', true);
$pageSidebarOpt = (isset($borntogive_options['blog_sidebar'])) ? $borntogive_options['blog_sidebar'] : '';
if ($pageSidebarGet != '') {
	$pageSidebar = $pageSidebarGet;
} elseif ($pageSidebarOpt != '') {
	$pageSidebar = $pageSidebarOpt;
} else {
	$pageSidebar = 'post-sidebar';
}
if ($pageSidebarStrictNo == 1) {
	$pageSidebar = '';
}
$sidebar_column = get_post_meta(get_the_ID(), 'borntogive_sidebar_columns_layout', true);
if (!empty($pageSidebar) && is_active_sidebar($pageSidebar)) {
	$sidebar_column = ($sidebar_column == '') ? 4 : $sidebar_column;
	$left_col = 12 - intval($sidebar_column);
	$class = $left_col;
} else {
	$class = 12;
}
$page_header = get_post_meta(get_the_ID(), 'borntogive_pages_Choose_slider_display', true);
if ($page_header == 3 || $page_header == 4) {
	get_template_part('pages', 'flex');
} elseif ($page_header == 5) {
	get_template_part('pages', 'revolution');
} else {
	get_template_part('pages', 'banner');
}
$language = do_shortcode('[language]');
?>
<div id="overlay">
    <div class="cv-spinner">
        <span class="spinner"></span>
    </div>
</div>
<div class="main" role="main">
	<div id="content" class="content full">
		<div class="container">
			<div class="row">
			    <div class="col-md-4">
                </div>
                <?php if($language == 'ar'){?>
                     <div class="col-md-4">
    			        <input type="search" name="search" id="story_search" placeholder="<?php _e( 'search stories by title...' , '365-stories' ); ?>" class='form-control text_only' onkeydown="return /[a-z]/i.test(event.key)">
                        <input type="search" name="search" id="story_search_number" style="none;" placeholder="<?php _e( 'search stories by number...' , '365-stories' ); ?>" class='form-control number_only' oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                    </div>
                    <div class="col-md-4">
                        <select id="filter_by" class="form-control">
                            <option value="title"><?php _e( 'Title' , '365-stories' ); ?></option>
                            <option value="number"><?php _e( 'Stories Number' , '365-stories' ); ?></option>
                            <?php 
                                $categories_product =  get_terms( array('taxonomy' => 'categorie','hide_empty' => false,) ); 
                                foreach($categories_product as  $category_product){?>
                            	   <option value="<?php _e( $category_product->slug , '365-stories' ); ?>"><?php _e( $category_product->name , '365-stories' ); ?></option>
                            	<?php }
                            ?>
                        </select>
                        <label class="story_lable"><?php _e( 'Search By' , '365-stories' ); ?></label>
                    </div>
                <?php } else {?>
                    <div class="col-md-4">
                        <label class="story_lable"><?php _e( 'Search By' , '365-stories' ); ?></label>
                        <select id="filter_by" class="form-control">
                            <option value="title"><?php _e( 'Title' , '365-stories' ); ?></option>
                            <option value="number"><?php _e( 'Stories Number' , '365-stories' ); ?></option>
                            <?php 
                                $categories_product =  get_terms( array('taxonomy' => 'categorie','hide_empty' => false,) ); 
                                foreach($categories_product as  $category_product){?>
                            	   <option value="<?php _e( $category_product->slug , '365-stories' ); ?>"><?php _e( $category_product->name , '365-stories' ); ?></option>
                            	<?php }
                            ?>
                         </select>
                    </div>
                    <div class="col-md-4">
    			        <input type="search" name="search" id="story_search" placeholder="<?php _e( 'search stories by title...' , '365-stories' ); ?>" class='form-control text_only' onkeydown="return /[a-z]/i.test(event.key)">
                        <input type="search" name="search" id="story_search_number" style="none;" placeholder="<?php _e( 'search stories by number...' , '365-stories' ); ?>" class='form-control number_only' oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                    </div>
                <?php } ?>
    			<div class="col-md-12" id="content-col">
                   <div class="post-content">
                      <section class="wpb-content-wrapper">
                         <div class="vc_row wpb_row vc_row-fluid stories365">
                            <div class="sfound"><h2 class="search_result_for"><?php _e( 'Search result by : ' , '365-stories' ); ?><span class="result"></span></h2></div>
                            <div class="nofound"><h2 class="story_not_found"><?php _e( 'Not found related stories' , '365-stories' ); ?></h2></div>
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                               <div class="vc_column-inner">
                                  <div class="wpb_wrapper">
                                     <div class="wpb_text_column wpb_content_element  stories365-blocks">
                                        <input type="hidden" name="page_languag" value="<?php esc_html_e( do_shortcode('[language]') , '365-stories' ); ?>" id="page_languag">
                                        <div class="wpb_wrapper masonry" id="current_story">
                                            <?php
                                                $language = do_shortcode('[language]');
                                                $args = array(
                                                            'post_type' => '365_stories',
                                                            'posts_per_page' => -1,
                                                            'post_status' => 'publish', 
                                                            'order' => 'ASC',
                                                        );
                                                $query = new WP_Query($args);
                                                $i=1;
                                                if($query->have_posts()):
                                                    while($query->have_posts()):
                                                        $query->the_post(); 
                                                        $meta = get_post_meta( get_the_id(), 'story_number', true );
                                                        $meta_ar = get_post_meta( get_the_id(), 'story_number_ar', true );
                                                        ?>
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
                                                    <?php $i++ ; endwhile;
                                                    wp_reset_postdata();    
                                                endif;
                                            ?>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </div>
                         </div>
                      </section>
                   </div>
                </div> 
			 </div>
		</div>
	</div>
</div>
<?php get_footer();
?>
