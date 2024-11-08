<?php 
/*
AfricasTalking SMS API Key Setting Template 
*/
$sms_username = get_option('sms_username', '');
$sms_api_key = get_option('sms_api', '');
?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php 
        // Translators: %s is the page title.
        echo wp_kses( sprintf( __('%s Africas Talking SMS API Details', 'BASMS_BATCH_TEXT_DOMAIN'), '<span class="dashicons dashicons-email"></span>'), array('span' => array('class' => true))); ?>
    </h1>
    <form action="" method="post" id="bible-form"><input type="hidden" name="message" id="update_msg" value="" /></form>
    
    <div class="loader_for_hide_term" style="display: none;"></div>
    <hr class="wp-header-end">
    <form method="post" action="" enctype="multipart/form-data" id="post">
        <div id="poststuff">
            <div class="sms_error"><span></span></div>
            <div id="post-body" class="metabox-holder columns-2">
                <div class="post-body-content">
                    <div id="postbox-container-2" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable postbox" style="min-height: 500px;">
                            <table class="form-table" style="margin-left:20px"> 
                                <tr>
                                    <th>
                                        <label for="subscription_type">
                                            <?php esc_html_e('User Name', 'BASMS_BATCH_TEXT_DOMAIN' ); ?> : 
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="sms_username" class="sms_api_class regular-text" id="sms_username" placeholder="Enter User Name" value="<?php echo esc_attr($sms_username); ?>"> 
                                    </td>
                                </tr>
                                 <tr>
                                    <th>
                                        <label for="subscription_type">
                                            <?php esc_html_e('API Key', 'BASMS_BATCH_TEXT_DOMAIN' ); ?> : 
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="sms_api" class="sms_api_class regular-text" id="sms_api" placeholder="API Key" value="<?php echo esc_attr($sms_api_key); ?>"> 
                                    </td>
                                </tr>
                            </table>
                            
                            <div class="form-table" id="form-table-ajax"> 
                                <div class="custom-loader-wrapper" style="display: none;">
                                    <div class="custom-loader"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div id="submitdiv" class="postbox">
                                <div class="postbox-header">
                                    <h2 class="hndle ui-sortable-handle">
                                        <span><?php esc_html_e('Actions', 'BASMS_BATCH_TEXT_DOMAIN' ); ?></span>
                                    </h2>
                                </div>
                                <div class="inside">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <?php 
                                            // phpcs:ignore
                                            if (isset($_POST['message'])) {
                                                // phpcs:ignore 
                                                $status = sanitize_text_field($_POST['message']); // Sanitize input
                                                // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                                                if ($status === 'false') {
                                                    ?>
                                                    <div class="misc-pub-section curtime misc-pub-last-log" id="error">
                                                        <p><?php esc_html_e('Please Enter User Name and API Key', 'BASMS_BATCH_TEXT_DOMAIN'); ?></p>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="misc-pub-section curtime misc-pub-last-log" id="success">
                                                        <p><?php esc_html_e('Data Added Successfully', 'BASMS_BATCH_TEXT_DOMAIN'); ?></p>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <input name="list_publish" type="button" class="button button-primary button-large" id="bible_sms_save" value="Save">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
