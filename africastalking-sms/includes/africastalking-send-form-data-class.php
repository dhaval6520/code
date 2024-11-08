<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class AfricasTalkingSmsSender {

    protected $AfricasTalkingSmsGatewayPlugin;

    public function __construct() {
        require_once plugin_dir_path(__FILE__) . './africastalking-send-sms-class-main.php';
        $this->AfricasTalkingSmsGatewayPlugin = new AfricasTalkingSmsGatewayPlugin();
    }

    public function handle_sms_submission() {
        $message = ''; 
        $error = ''; 

        // phpcs:ignore
        $single_sms = isset($_POST['single_sms']) ? sanitize_text_field($_POST['single_sms']) : '';
        // phpcs:ignore
        $bulk_sms = isset($_POST['bulk_sms']) ? sanitize_textarea_field($_POST['bulk_sms']) : '';

        if (isset($single_sms) || isset($bulk_sms)) {
            try {
                // phpcs:ignore
                $senderId = !empty($_POST['senderId']) ? sanitize_text_field(wp_unslash($_POST['senderId'])) : null;

                if (isset($_POST['single_sms'])) {
                    // phpcs:ignore WordPress.Security.NonceVerification
                    $to = isset($_POST['to']) ? sanitize_text_field(wp_unslash($_POST['to'])) : '';
                    // phpcs:ignore WordPress.Security.NonceVerification
                    $sms_message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
                    // Send single SMS
                    $result = $this->AfricasTalkingSmsGatewayPlugin->send_single_sms($to, $sms_message, $senderId);
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'sms_logs';
                    $error_message = $result[0]->status; 
                    $serialized_exception = serialize($result);  
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
                    if ($result[0]->status == 'Success') {
                        $message = __('Single SMS sent successfully!', 'BASMS_BATCH_TEXT_DOMAIN');
                    }
                    else{
                        throw new Exception(sprintf(__('Failed to send bulk SMS due to an incorrect number: %s', 'BASMS_BATCH_TEXT_DOMAIN'), $result[0]->status.' '.$to ));
                    }
                }

                // Handle bulk SMS submission
                if (isset($_POST['bulk_sms']) && isset($_FILES['csv_file'])) {
                    // phpcs:ignore WordPress.Security.NonceVerification
                    if (isset($_FILES['csv_file']) && isset($_FILES['csv_file']['error']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                        // phpcs:ignore
                        $file_type = wp_check_filetype($_FILES['csv_file']['name']);
                        $recipients = [];

                        if ($file_type['ext'] === 'csv') {
                            // Process CSV file
                            $csv = array_map('str_getcsv', file($_FILES['csv_file']['tmp_name']));
                            foreach ($csv as $row) {
                                if (isset($row[0])) {
                                    $recipients[] = sanitize_text_field($row[0]);
                                }
                            }
                        } elseif ($file_type['ext'] === 'txt') {
                            // Process TXT file
                            $lines = file($_FILES['csv_file']['tmp_name']);
                            foreach ($lines as $line) {
                                $number = sanitize_text_field(trim($line));
                                if (!empty($number)) {
                                    $recipients[] = $number;
                                }
                            }
                        } else {
                            $dynamic_content = 'Please upload a CSV or TXT file.'; // Example dynamic content
                            wp_die(esc_html($dynamic_content));
                        }

                        $recipients = array_filter($recipients);
                        // phpcs:ignore
                        $sms_message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
                        // phpcs:ignore
                        $senderId = isset($_POST['senderId']) ? sanitize_text_field($_POST['senderId']) : '';
                        // Use the custom SMS gateway plugin to send the SMS
                        $result = $this->AfricasTalkingSmsGatewayPlugin->send_bulk_sms($recipients, $sms_message, $senderId);
                        $success = [];
                        $invalidPhoneNumber = [];
                        foreach ($result as $item) {
                            if ($item->status === 'Success') {
                                $success[] = $item;
                            } elseif ($item->status === 'InvalidPhoneNumber') {
                                $invalidPhoneNumber[] = $item;
                            }
                        }
                        $count_success = count($success); 
                        $count_PhoneNumber = count($invalidPhoneNumber); 
                        if ($count_PhoneNumber > 0) {
                            $message = __('A total of '.$count_success.' bulk SMS messages were sent successfully, <p class="error">but '.$count_PhoneNumber.' failed due to an incorrect number.</p>', 'BASMS_BATCH_TEXT_DOMAIN');
                        }
                        else{
                             $message = __('bulk SMS messages were sent successfully', 'BASMS_BATCH_TEXT_DOMAIN');
                        }
                        
                        global $wpdb;

                        $table_name = $wpdb->prefix . 'sms_logs';
                        $error_message = 'Success'.' '.$count_success.' failed'.$count_PhoneNumber; 
                        $serialized_exception = serialize($result);  
                        $data_to_insert = array(
                            'recipient'      => implode(',', $recipients),
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
                        
                    } else {
                        // phpcs:ignore
                        $error_message = isset($_FILES['csv_file']['error']) ? $_FILES['csv_file']['error'] : __('Unknown error.', 'BASMS_BATCH_TEXT_DOMAIN');
                        throw new Exception(sprintf(__('Error uploading file: %s', 'BASMS_BATCH_TEXT_DOMAIN'), $error_message));
                    }
                }
            } catch (Exception $e) {
                // Translators: %s is the error message from the uploaded file.
                $error = sprintf(__('Error: %s', 'BASMS_BATCH_TEXT_DOMAIN'), $e->getMessage());
            }
        }

        return compact('message', 'error');
    }
}
