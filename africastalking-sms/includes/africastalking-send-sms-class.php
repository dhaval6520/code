<?php
/**
 * Class AfricasTalkingSendSms
 */
class AfricasTalkingSendSms {
    function __construct() {
        $this->init();
    }

    public function init() {
        add_action('admin_menu', array(__CLASS__, 'manage_sms_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'africastalking_send_enqueue_scripts'));
    }

    public static function manage_sms_menu() {
        add_submenu_page(
            'manage_sms',
            __('Send SMS', 'BASMS_BATCH_TEXT_DOMAIN'),
            __('Send SMS', 'BASMS_BATCH_TEXT_DOMAIN'),
            'manage_options',
            'manage_send_sms',
            array(__CLASS__, 'manage_send_sms')
        );
    }

    public static function africastalking_send_enqueue_scripts($hook) {
        $screen = get_current_screen();
        // phpcs:ignore
        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
        if ($page === 'manage_send_sms') {
            // Enqueue minified CSS
            $css_file = BASMS_BATCH_DIR_PATH . 'assets/css/africastalking-sms.min.css';
            $css_version = file_exists($css_file) ? filemtime($css_file) : '1.0';

            wp_enqueue_style(
                'hide_terms_stylesheet',
                BASMS_BATCH_URL . 'assets/css/africastalking-sms.min.css',
                array(), // Dependencies (if any)
                $css_version // Version number for cache busting
            );

            // Enqueue minified JavaScript
            $js_file = BASMS_BATCH_DIR_PATH . 'assets/js/africastalking-sms-form.min.js';
            $js_version = file_exists($js_file) ? filemtime($js_file) : '1.0';

            wp_enqueue_script(
                'africastalking_send_js',
                BASMS_BATCH_URL . 'assets/js/africastalking-sms-form.min.js',
                array('jquery'), // Dependencies
                $js_version,
                true // Load in footer
            );
        }
    }


    public static function manage_send_sms() {
        if (is_file(BASMS_BATCH_DIR_PATH . 'includes/template/africastalking-send-sms-form.php')) {
            include_once BASMS_BATCH_DIR_PATH . 'includes/template/africastalking-send-sms-form.php';
        }
    }
}

new AfricasTalkingSendSms();