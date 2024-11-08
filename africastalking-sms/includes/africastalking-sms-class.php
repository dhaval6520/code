<?php
/**
 * Class AfricasTalkingSmsActivated
 */
class AfricasTalkingSmsActivated {

    function __construct() {
        $this->init();
    }

    public function init() {
        add_action('admin_menu', array(__CLASS__, 'manage_sms_menu'));
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'africastalking_sms_enqueue_scripts' ) );
        add_action( 'wp_ajax_africastalking_sms_submit', array( $this, 'africastalking_sms_submit' ) ); 
        add_action( 'wp_ajax_nopriv_africastalking_sms_submit', array( $this, 'africastalking_sms_submit' ) );
    }
    
    public static function manage_sms_menu() {
        add_menu_page(
            __('Bible SMS', 'BASMS_BATCH_TEXT_DOMAIN'),
            __('Bible SMS', 'BASMS_BATCH_TEXT_DOMAIN'),
            'manage_options',
            'manage_sms',
            array(__CLASS__, 'manage_sms'),
            'dashicons-email', 
            26 
        );
    }

    public static function manage_sms() {
        if (is_file(BASMS_BATCH_DIR_PATH . 'includes/template/africastalking-sms-settings-form.php')) {
            include_once BASMS_BATCH_DIR_PATH . 'includes/template/africastalking-sms-settings-form.php';
        }
    }
    public static function africastalking_sms_enqueue_scripts() {
    $screen = get_current_screen();
    // phpcs:ignore
    $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
    if ($page === 'manage_sms') {
        $version = filemtime( BASMS_BATCH_DIR_PATH . 'assets/css/africastalking-sms.min.css' ); // Get the file modification time
        wp_enqueue_style(
            'hide_terms_stylesheet',
            BASMS_BATCH_URL . 'assets/css/africastalking-sms.min.css',
            array(), // Dependencies (if any)
            $version // Version number to handle cache busting
        );
        
        $script_version = filemtime( BASMS_BATCH_DIR_PATH . 'assets/js/africastalking-sms-ajax.min.js' ); // Get the file modification time
        wp_enqueue_script(
            'africastalking_sms_js', // Handle
            BASMS_BATCH_URL . 'assets/js/africastalking-sms-ajax.min.js', // Script URL
            array('jquery'), // Dependencies
            $script_version, // Version number to handle cache busting
            true // Load in footer
        );
        
        $ajax_url = admin_url( 'admin-ajax.php' );
        $nonce = wp_create_nonce( 'africastalking_sms_nonce' ); // Create a nonce
        
        wp_localize_script( 'africastalking_sms_js', 'africastalking_sms_object',
            array( 
                'ajaxurl' => $ajax_url,
                'nonce' => $nonce // Pass nonce to JavaScript
            )
        );
    }
}

  public static function africastalking_sms_submit(){
    global $wpdb;

    // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $sms_username = isset($_REQUEST['sms_username']) ? wp_unslash($_REQUEST['sms_username']) : '';
    // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $sms_api = isset($_REQUEST['sms_api']) ? wp_unslash($_REQUEST['sms_api']) : '';

    // Sanitize the unslashed data
    $sms_username = sanitize_text_field($sms_username);
    $sms_api = sanitize_text_field($sms_api);

    if (empty($sms_username) && empty($sms_api)) {
        $msg = "error_1";
        echo esc_html($msg);
    } elseif (empty($sms_username) || empty($sms_api)) {
        $msg = "error_2";
        echo esc_html($msg);
    } else {
        update_option('sms_username', $sms_username);
        update_option('sms_api', $sms_api);
        $msg = "Record updated successfully";
        echo esc_html($msg);
    }
    wp_die(); // Use wp_die() instead of die() for better integration with WordPress
}



    public static function africastalking_sms_log() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'sms_history';  // Define the table name correctly
        $sms_errors_table = $wpdb->prefix . 'sms_logs';

        // phpcs:ignore
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
            // phpcs:ignore 
            $sms_history_sql = "CREATE TABLE $table_name (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                sms_type VARCHAR(10000) NOT NULL,
                recipient VARCHAR(20) NOT NULL,
                message TEXT NOT NULL,
                sender_id VARCHAR(50) DEFAULT NULL,
                sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

             // SQL statement for creating the sms_errors table
                $sms_errors_sql = "CREATE TABLE $sms_errors_table (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    recipient TEXT NOT NULL,
                    sms_type TEXT NOT NULL,
                    error_code INT(5) NOT NULL,
                    error_message TEXT NOT NULL,
                    occurred_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;";

            // Include the upgrade.php file for dbDelta function
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            // Create or update the table
            dbDelta($sms_history_sql);
            dbDelta($sms_errors_sql);

        }
    }
}
new AfricasTalkingSmsActivated();
