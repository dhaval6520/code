<?php
/**
 * Class AfricasTalkingSmsGatewayPlugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AfricasTalkingSmsGatewayPlugin {
    private $username;
    private $apiKey;
    private $gateway;

    public function __construct() {
        $this->username = get_option('sms_username', 'default_username');
        $this->apiKey = get_option('sms_api', 'default_api_key');
        $this->load_dependencies();
        $this->initialize_gateway();
    }

    private function load_dependencies() {
        // Ensure the file exists before requiring it
        $gateway_file = plugin_dir_path(__FILE__) . 'AfricasTalkingGateway.php';
        if (file_exists($gateway_file)) {
            require_once $gateway_file;
        } else {
            error_log("AfricasTalkingGateway.php file not found.");
        }
    }

    private function initialize_gateway() {
        try {
            $this->gateway = new AfricasTalkingGateway($this->username, $this->apiKey);
        } catch (Exception $e) {
            error_log("Error initializing Africa's Talking SDK: " . $e->getMessage());
            throw new Exception("Failed to initialize SMS service. Please try again later.");
        }
    }

    public function send_single_sms($to, $message, $senderId = null) {
        try {
            // Ensure the recipient number and message are properly sanitized
            $to = sanitize_text_field($to);
            $message = sanitize_textarea_field($message);
            $senderId = $senderId ? sanitize_text_field($senderId) : null;

            $result = $this->gateway->sendMessage($to, $message, $senderId);
            $sms_type = 'single';
            $this->log_sms_history(get_current_user_id(), $to, $message, $senderId, $sms_type);
            return $result;
        } catch (Exception $e) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'sms_logs';
                    $error_message = $e->getMessage(); 
                    $serialized_exception = serialize($e);  
                    $data_to_insert = array(
                        'recipient'      => $to,
                        'sms_type'       => 'single',
                        'error_code'     => $error_message,           // Assuming you have an error code variable
                        'error_message'  => $serialized_exception,      // Retrieves the exception message
                        'occurred_at'    => current_time('mysql')  // Adds the current timestamp
                    );
                        // Define the format of the data
                    $data_format = array(
                        '%s', // Format for recipient (string)
                        '%s', // Format for sms_type (string)
                        '%s', // Format for error_code (string or integer)
                        '%s', // Format for error_message (string)
                        '%s'  // Format for occurred_at (datetime)
                    );

                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                   $insert_result = $wpdb->insert(
                        $table_name,
                        $data_to_insert,
                        $data_format
                    );

                    // Check for errors and log them
                    if ($insert_result === false) {
                        error_log("Error logging SMS history: " . $wpdb->last_error);
                    }
            throw new Exception("Failed to send SMS - ". $error_message);
        }
    }

    public function send_bulk_sms($recipients, $message, $senderId = null) {
        try {
            // Sanitize and prepare the message and recipients
            $message = sanitize_textarea_field($message);
            $senderId = $senderId ? sanitize_text_field($senderId) : null;
            $recipients = array_map('sanitize_text_field', $recipients); // Sanitize each recipient number
            $to = implode(',', $recipients);

            $result = $this->gateway->sendMessage($to, $message, $senderId);
            foreach ($result as $recipient) {
                if ($recipient->status === 'Success') {
                    $sms_type = 'Bulk';
                    $this->log_sms_history(get_current_user_id(), $recipient->number, $message, $senderId, $sms_type);
                }
            }
            return $result;
        } catch (Exception $e) {
            global $wpdb;
                    $table_name = $wpdb->prefix . 'sms_logs';
                    $error_message = $e->getMessage(); 
                    $serialized_exception = serialize($e);  
                    $data_to_insert = array(
                        'recipient'      => $to,
                        'sms_type'       => 'bulk',
                        'error_code'     => $error_message,           // Assuming you have an error code variable
                        'error_message'  => $serialized_exception,      // Retrieves the exception message
                        'occurred_at'    => current_time('mysql')  // Adds the current timestamp
                    );
                        // Define the format of the data
                    $data_format = array(
                        '%s', // Format for recipient (string)
                        '%s', // Format for sms_type (string)
                        '%s', // Format for error_code (string or integer)
                        '%s', // Format for error_message (string)
                        '%s'  // Format for occurred_at (datetime)
                    );

                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                   $insert_result = $wpdb->insert(
                        $table_name,
                        $data_to_insert,
                        $data_format
                    );

                    // Check for errors and log them
                    if ($insert_result === false) {
                        error_log("Error logging SMS history: " . $wpdb->last_error);
                    }
            throw new Exception("Failed to send bulk SMS - ". $error_message);
        }
    }

   private function log_sms_history($user_id, $recipient, $message, $senderId = null, $sms_type) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'sms_history'; 
        // Sanitize and prepare the data
        $user_id = intval($user_id);
        $recipient = sanitize_text_field($recipient);
        $sms_type = sanitize_text_field($sms_type);
        $message = sanitize_textarea_field($message);
        $senderId = $senderId ? sanitize_text_field($senderId) : null;

        // Prepare data for insertion
        $data_to_insert = array(
            'user_id'    => $user_id,
            'sms_type'    => $sms_type,
            'recipient'  => $recipient,
            'message'    => $message,
            'sender_id'  => $senderId
        );

        // Define the format of the data
        $data_format = array(
            '%d', // Format for user_id (integer)
            '%s', // Format for recipient (string)
            '%s', // Format for recipient (string)
            '%s', // Format for message (string)
            '%s'  // Format for sender_id (string or null)
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $insert_result = $wpdb->insert(
            $table_name,
            $data_to_insert,
            $data_format
        );

        // Check for errors and log them
        if ($insert_result === false) {
            error_log("Error logging SMS history: " . $wpdb->last_error);
        }
    }
}
// Initialize the plugin
$AfricasTalkingSmsgatewayplugin = new AfricasTalkingSmsGatewayPlugin();
