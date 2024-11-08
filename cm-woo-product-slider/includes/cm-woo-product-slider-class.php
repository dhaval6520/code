<?php
/**
 * Class CMWPS_ProductSlider
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CMWPS_ProductSlider {
    function __construct() {
        $this->init();
    }
    public function init() {
        add_filter( 'init', array( $this, 'CMWPS_register_post_type' ) );
        add_filter( 'add_meta_boxes', array( $this, 'CMWPS_product_slider_metabox' ) );
        add_filter( 'save_post', array( $this, 'CMWPS_save_product_category_meta_box' ) );
        add_shortcode( 'cm_product_slider', array( $this, 'CMWPS_render_product_slider' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'CMWPS_enqueue_slider_assets' ) );
    }
    public static function CMWPS_register_post_type() {
        $labels = array(
            'name' => _x( 'CM Product Slider', 'Post Type General Name', 'CMWPS_TEXT_DOMAIN' ),
            'singular_name' => _x( 'CM Product Slider', 'Post Type Singular Name', 'CMWPS_TEXT_DOMAIN' ),
            'menu_name' => _x( 'CM Product Slider', 'Admin Menu text', 'CMWPS_TEXT_DOMAIN' ),
            'name_admin_bar' => _x( 'CM Product Slider', 'Add New on Toolbar', 'CMWPS_TEXT_DOMAIN' ),
            'archives' => __( 'CM Product Slider Archives', 'CMWPS_TEXT_DOMAIN' ),
            'attributes' => __( 'CM Product Slider Attributes', 'CMWPS_TEXT_DOMAIN' ),
            'parent_item_colon' => __( 'Parent CM Product Slider:', 'CMWPS_TEXT_DOMAIN' ),
            'all_items' => __( 'All CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'add_new_item' => __( 'Add New CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'add_new' => __( 'Add New', 'CMWPS_TEXT_DOMAIN' ),
            'new_item' => __( 'New CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'edit_item' => __( 'Edit CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'update_item' => __( 'Update CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'view_item' => __( 'View CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'view_items' => __( 'View CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'search_items' => __( 'Search CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'not_found' => __( 'Not found', 'CMWPS_TEXT_DOMAIN' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'CMWPS_TEXT_DOMAIN' ),
            'featured_image' => __( 'Featured Image', 'CMWPS_TEXT_DOMAIN' ),
            'set_featured_image' => __( 'Set featured image', 'CMWPS_TEXT_DOMAIN' ),
            'remove_featured_image' => __( 'Remove featured image', 'CMWPS_TEXT_DOMAIN' ),
            'use_featured_image' => __( 'Use as featured image', 'CMWPS_TEXT_DOMAIN' ),
            'insert_into_item' => __( 'Insert into CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'uploaded_to_this_item' => __( 'Uploaded to this CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'items_list' => __( 'CM Product Slider list', 'CMWPS_TEXT_DOMAIN' ),
            'items_list_navigation' => __( 'CM Product Slider list navigation', 'CMWPS_TEXT_DOMAIN' ),
            'filter_items_list' => __( 'Filter CM Product Slider list', 'CMWPS_TEXT_DOMAIN' ),
        );
        $args = array(
            'label' => __( 'CM Product Slider', 'CMWPS_TEXT_DOMAIN' ),
            'description' => esc_html__( 'C-Metric Product Slider & Carousel for WooCommerce', 'CMWPS_TEXT_DOMAIN' ),
            'labels' => $labels,
            'menu_icon' => 'dashicons-store',
            'supports' => array('title'),
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 10,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type( 'cmproductslider', $args );
    }
    public function CMWPS_product_slider_metabox() {
        add_meta_box(
            'prpduct_slider_id',         
            __('CM Product Slider Shortcode ', 'CMWPS_TEXT_DOMAIN'),  
            array( $this, 'CMWPS_product_slider_metabox_callback' ), 
            'cmproductslider',             
            'side',                        
            'high'
        );
        add_meta_box(
            'product_category_meta_box',
            __( 'Select Product Category', 'CMWPS_TEXT_DOMAIN' ), 
            array( $this, 'CMWPS_render_product_category_meta_box' ), 
            'cmproductslider', 
            'normal',
            'high'
        );
    }
    public function CMWPS_product_slider_metabox_callback( $post ) {
        $post_id = absint( $post->ID );
        echo '<p>' . esc_html__( 'Use the shortcode below to display the slider:', 'CMWPS_TEXT_DOMAIN' ) . '</p>';
        if ( $post_id ) {
            echo '<p>' . esc_html( '[cm_product_slider id="' . esc_attr( $post_id ) . '"]' ) . '</p>';
        }
    }
    public function CMWPS_render_product_category_meta_box( $post ) {
        wp_nonce_field( 'product_category_nonce_action', 'product_category_nonce' );

        $post_id = absint( $post->ID );
        $selected_category = get_post_meta( $post_id, '_cmwps_product_category', true );
        $categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ) );

        echo '<select name="product_category" id="product_category">';
        echo '<option value="">' . esc_html__( 'Select a category', 'CMWPS_TEXT_DOMAIN' ) . '</option>'; 
        foreach ( $categories as $category ) {
            $selected = ( absint( $selected_category ) === absint( $category->term_id ) ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $category->term_id ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $category->name ) . '</option>';
        }
        echo '</select>';
    }
    public function CMWPS_save_product_category_meta_box( $post_id ) {
        if ( ! isset( $_POST['product_category_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['product_category_nonce']) ), 'product_category_nonce_action' ) ) {
            return; 
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return; 
        }
        if ( ! isset( $_POST['product_category'] ) ) {
            return; 
        }
        $selected_category = isset($_POST['product_category']) ? absint( $_POST['product_category'] ) : 0;
        if ( $selected_category > 0 ) {
            update_post_meta( $post_id, '_cmwps_product_category', $selected_category );
        } else {
            delete_post_meta( $post_id, '_cmwps_product_category' );
        }
    }
    public function CMWPS_render_product_slider( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'cm_product_slider' );

        $id = absint( $atts['id'] ); 
        $meta_key = '_cmwps_product_category'; 
        $selected_category = absint( get_post_meta( $id, $meta_key, true )); 

        if ( $id > 0 ) {
            $cache_key = 'cmwps_product_slider_' . $selected_category; 
            $products_html = get_transient( $cache_key ); 

            if ( false === $products_html ) {
                $args = array(
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $selected_category, 
                        ),
                    ), 
                );

                $products = new WP_Query( $args );

                if ( $products->have_posts() ) {
                    ob_start(); 

                    echo '<div class="cmwps-product-slider">';

                    while ( $products->have_posts() ) {
                        $products->the_post();

                        echo '<div class="cmwps-product-item">';
                        echo '<a href="' . esc_url( get_permalink() ) . '">'; 
                        echo get_the_post_thumbnail( get_the_ID(), 'medium' ); 
                        echo '<h2>' . esc_html( get_the_title() ) . '</h2>'; 
                        echo '</a>';
                        echo '</div>';
                    }

                    echo '</div>';

                    wp_reset_postdata(); 

                    $products_html = ob_get_clean();

                    set_transient( $cache_key, $products_html, HOUR_IN_SECONDS );
                } else {
                    $products_html = esc_html__( 'No products found.', 'CMWPS_TEXT_DOMAIN' ); 
                }
            }
            return $products_html; 
        }
        return esc_html__( 'Invalid product category ID.', 'CMWPS_TEXT_DOMAIN' ); // Escape error message
    }
    public function CMWPS_enqueue_slider_assets() {
        if ( $this->CMWPS_is_slider_shortcode_present() ) {
            wp_enqueue_style( 'cmwps-slick-css', CMWPS_URL . 'assets/css/slick.css', array(), '1.0.0' );
            wp_enqueue_script( 'cmwps-slick-js', CMWPS_URL . 'assets/js/slick.min.js', array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'cmwps-slider-init', CMWPS_URL . 'assets/js/slider-init.js', array( 'jquery', 'cmwps-slick-js' ), '1.0.0', true );
        }
    }
    public function CMWPS_is_slider_shortcode_present() {
        global $post;
        if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'cm_product_slider' ) ) {
            return true;
        }
        return false;
    }
}
new CMWPS_ProductSlider();
