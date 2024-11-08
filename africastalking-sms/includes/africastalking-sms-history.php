<?php
/**
 * Class AfricasTalkingSmsHistory
 */
require_once('africastalking-sms-history-class.php');
class AfricasTalkingSmsHistory {
    function __construct()
    {
        $this->init();
    }
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function init() {
        add_action('admin_menu', array( __CLASS__, 'africastalking_sms_history_sub_menu' ) );
    }
    public static function africastalking_sms_history_sub_menu() {
        add_submenu_page(
            'manage_sms',
            __('SMS History', 'BASMS_BATCH_TEXT_DOMAIN'),
            __('SMS History', 'BASMS_BATCH_TEXT_DOMAIN'),
            'manage_options',
            'africastalking_sms_history_page',
            array( __CLASS__, 'africastalking_sms_history_page' )
        );
    }
   
  public static function africastalking_sms_history_page() {
    $table = new Supporthost_List_Table();
    $title = 'SMS History';
    echo '<div class="wrap">';
    echo '<h2>' . esc_html($title) . '</h2>';
    echo '</div>';

    // Check nonce for security
    if (isset($_POST['africastalking_sms_history_nonce'])) {
        $nonce = sanitize_key(wp_unslash($_POST['africastalking_sms_history_nonce']));
        if (wp_verify_nonce($nonce, 'africastalking_sms_history_action')) {
            // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $action = isset($_REQUEST['action']) ? wp_unslash($_REQUEST['action']) : '';
            $action = sanitize_text_field($action); // Ensure the action is sanitized

            if ($action === 'delete') {
                $message = '<div class="updated below-h2" id="message"><p>' . esc_html__('Items deleted successfully', 'emngt') . '</p></div>';
                echo wp_kses_post($message);
            }
        } else {
            echo '<div class="error below-h2" id="message"><p>' . esc_html__('Nonce verification failed. Please try again.', 'emngt') . '</p></div>';
        }
    }

    echo '<form method="post">';
    wp_nonce_field('africastalking_sms_history_action', 'africastalking_sms_history_nonce');
    $table->prepare_items();
    $table->search_box('search', 'search_id');
    $table->display();
    echo '</div></form>';
}

}
new AfricasTalkingSmsHistory();
