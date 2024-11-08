<?php
/*
AfricasTalkingS SMS Send Template 
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$sms_sender = new AfricasTalkingSmsSender();
$response = $sms_sender->handle_sms_submission();
$message = $response['message'];
$error = $response['error'];
?>

<div class="wrap" id="send_sms_wp">
	<div class="sms_main">
	    <h1><?php esc_html_e( 'Send SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?></h1>
	    <?php if ( isset($error) && $error ) : ?>
	        <p class="error"><?php echo esc_html($error); ?></p>
	    <?php endif; ?>
        <?php if ( !empty($message) ) : ?>
            <p class="success"><?php echo wp_kses_post($message); ?></p>
        <?php endif; ?>
	    <div class="toggle-buttons">
	        <button type="button" class="toggle-btn active" onclick="showSection('single-sms-section', this)">
	            <?php esc_html_e( 'Single SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>
	        </button>
	        <button type="button" class="toggle-btn" onclick="showSection('bulk-sms-section', this)">
	            <?php esc_html_e( 'Bulk SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>
	        </button>
	    </div>

	    <div id="single-sms-section" class="form-section active">
	        <h2><?php esc_html_e( 'Single SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?></h2>
	        <form method="post">
	            <input type="text" name="to" placeholder="<?php esc_attr_e( 'Recipient Phone Number', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>" required><br>
	            <input type="text" name="senderId" placeholder="<?php esc_attr_e( 'Sender ID - BSZ', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>"><br>
	            <textarea name="message" placeholder="<?php esc_attr_e( 'Message', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>" required></textarea><br>
	            <input type="submit" name="single_sms" value="<?php esc_attr_e( 'Send Single SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>">
	        </form>
	    </div>

	    <div id="bulk-sms-section" class="form-section">
	        <h2><?php esc_html_e( 'Bulk SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?></h2>
	        <form method="post" enctype="multipart/form-data">
	            <input type="file" name="csv_file" accept=".csv,.txt" required><br>
	            <input type="text" name="senderId" placeholder="<?php esc_attr_e( 'Sender ID - BSZ', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>" ><br>
	            <textarea name="message" placeholder="<?php esc_attr_e( 'Message', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>" required></textarea><br>
	            <input type="submit" name="bulk_sms" value="<?php esc_attr_e( 'Send Bulk SMS', 'BASMS_BATCH_TEXT_DOMAIN' ); ?>">
	        </form>
	    </div>
    </div>
</div>

