<?php
/**
 * this defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       c-metric.com
 *
 * @package    give-dpo-pay-add-on 
 * @subpackage give-dpo-pay-add-on /includes
*/
defined( 'ABSPATH' ) || exit;
/**
 * Class for registering a new settings page under Settings.
*/
class give_dpo_pay_common_class {
    /**
     * Constructor.
     */
    public $id = 'dpo-pay';
    function __construct() {
        add_filter( 'give_payment_gateways', array( $this, 'give_dpo_pay_register_payment_method'));
        add_filter( 'give_get_sections_gateways', array( $this, 'give_dpo_pay_register_payment_gateway_sections'));
        add_filter( 'give_get_settings_gateways', array( $this, 'give_dpo_pay_register_payment_gateway_setting_fields'));
        add_action( 'give_gateway_' . $this->id, array( $this, 'give_dpo_pay_process_payment' ) );
        add_action( 'give_donation_form_before_cc_form',array($this,'give_dpo_pay_cc_form'), 10, 1 );
        add_action( 'give_donation_form_before_register_login',array($this,'give_dpo_logo'), 10, 1 );
        //Filter to call tha custom template When dpo thank you page call
        add_filter( 'page_template',array($this,'dpo_custom_template'), 10, 1 );
    }
    public function give_dpo_pay_register_payment_method( $gateways )
    {
        $gateways[ $this->id ] = array(
            'admin_label'    => esc_html__( 'DPO Pay', give_dpo_pay_addon_SLUG ),
            'checkout_label' => esc_html__( 'DPO Pay', give_dpo_pay_addon_SLUG )
        );
        return $gateways;
    }
    public function give_dpo_pay_register_payment_gateway_sections($sections)
    {
        $sections[$this->id.'-settings'] = __( 'DPO Pay', give_dpo_pay_addon_SLUG );
        return $sections;
    }
    public function give_dpo_pay_register_payment_gateway_setting_fields( $settings )
    {
        if(give_get_current_setting_section() == $this->id.'-settings')
        {
            $settings = array(
                array(
                    'id'   => 'give_title_'.$this->id,
                    'type' => 'title',
                ),
                array(
                    'name' => '<strong>' . esc_html__( 'DPO Pay', give_dpo_pay_addon_SLUG ) . '</strong>',
                    'desc' => '<p style="background: #FFF; padding: 15px;border-radius: 5px;">' . sprintf( __( 'DPO Pay payment gateway', give_dpo_pay_addon_SLUG ), '' ) . '</p>',
                    'id'   => 'give_title_paylater_payment',
                    'type' => 'give_title',
                ),
                array(
                     'id'   => 'dpo_pay_title',
                     'name' => esc_html__( 'DPO Pay Title', give_dpo_pay_addon_SLUG ),
                     'type' => 'text',
                     'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_description',
                    'name' => esc_html__( 'DPO Pay Description', give_dpo_pay_addon_SLUG ),
                    'type' => 'wysiwyg',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_company_token',
                    'name' => esc_html__( 'DPO Pay Company Token', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_service_type',
                    'name' => esc_html__( 'Default DPO Service Type', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_test_mode',
                    'name' => esc_html__( 'Enable Test Mode', give_dpo_pay_addon_SLUG ),
                    'type' => 'checkbox',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_api_url',
                    'name' => esc_html__( 'DPO Pay API URL', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_pay_url',
                    'name' => esc_html__( 'DPO Pay URL', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_ptl_type',
                    'name' => esc_html__( 'PTL Type', give_dpo_pay_addon_SLUG ),
                    'type' => 'select',
                    'options' => [
                                'Hours' => sprintf('Hours', give_dpo_pay_addon_SLUG ),
                                'Minutes' => sprintf('Minutes', give_dpo_pay_addon_SLUG ),
                            ],
                    'default' => 'Hours',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_ptl',
                    'name' => esc_html__( 'PTL', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_order_status',
                    'name' => esc_html__( 'Successful Order Status', give_dpo_pay_addon_SLUG ),
                    'type' => 'select',
                    'options' => [
                                'Processing' => sprintf('Processing', give_dpo_pay_addon_SLUG ),
                                'Completed' => sprintf('Completed', give_dpo_pay_addon_SLUG ),
                                'On Hold' => sprintf('On Hold', give_dpo_pay_addon_SLUG ),
                            ],
                    'default' => 'Processing',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_cancel_page',
                    'name' => esc_html__( 'Select Cancel Page', give_dpo_pay_addon_SLUG ),
                    'type' => 'select',
                    'options' =>$this -> dpo_get_pages('Select Page'),
                    'default' => 'Processing',
                    'size' => 'regular'
                ),
                array(
                        'id'   => 'dpo_pay_thankyou_page',
                        'name' => esc_html__( 'Select Thank You Page', give_dpo_pay_addon_SLUG ),
                        'type' => 'select',
                        'options' =>$this -> dpo_get_pages('Select Page'),
                        'default' => 'Processing',
                        'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_logo',
                    'name' => esc_html__( 'Enable DPO Pay Logo', give_dpo_pay_addon_SLUG ),
                    'type' => 'checkbox',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'dpo_pay_logo_url',
                    'name' => esc_html__( 'DPO Pay Logo URL', give_dpo_pay_addon_SLUG ),
                    'type' => 'text',
                     'desc' => 'Add Logo URL Here',
                    'size' => 'regular'
                ),
                array(
                    'id'   => 'give_title_'.$this->id,
                    'type' => 'sectionend'
                )
            );
        }
        return $settings;
    }
    public function dpo_get_pages($title = false, $indent = true) {
        $wp_pages = get_pages('sort_column=menu_order');
        $page_list = array();
            if ($title) $page_list[] = $title;
            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while($has_parent) {
                        $prefix .=  ' - ';
                        $next_page = get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $url = get_permalink($page->ID);
                $url_new = rtrim($url, '/');
                $page_list[$url_new] = $prefix . $page->post_title;
            }
            return $page_list;
    }
    public function give_dpo_pay_process_payment( $posted_data ){
        give_clear_errors();
        $errors = give_get_errors();
        if ( ! $errors ) {
            $form_id         = intval( $posted_data['post_data']['give-form-id'] );
            $price_id        = ! empty( $posted_data['post_data']['give-price-id'] ) ? $posted_data['post_data']['give-price-id'] : 0;
            $donation_amount = ! empty( $posted_data['price'] ) ? $posted_data['price'] : 0;
            $currency = give_get_currency( $form_id );

            // Setup the payment details.
            $donation_data = array(
                'price'           => $donation_amount,
                'give_form_title' => $posted_data['post_data']['give-form-title'],
                'give_form_id'    => $form_id,
                'give_price_id'   => $price_id,
                'date'            => $posted_data['date'],
                'user_email'      => $posted_data['user_email'],
                'purchase_key'    => $posted_data['purchase_key'],
                'currency'        => $currency,
                'user_info'       => $posted_data['user_info'],
                'status'          => 'processing',
                'gateway'         => $this->id,
            );

            $payment_id = give_insert_payment( $donation_data );

            $dpo_pay_company_token = give_get_option('dpo_pay_company_token');
            $dpo_pay_service_type = give_get_option('dpo_pay_service_type');
            $dpo_pay_test_mode = give_get_option('dpo_pay_test_mode');
            $dpo_pay_api_url = give_get_option('dpo_pay_api_url');
            $dpo_pay_pay_url = give_get_option('dpo_pay_pay_url');
            $dpo_pay_ptl_type = give_get_option('dpo_pay_ptl_type');
            $dpo_pay_ptl = give_get_option('dpo_pay_ptl');
            $dpo_pay_order_status = give_get_option('dpo_pay_order_status');
            $dpo_pay_description = give_get_option('dpo_pay_description');
            $dpo_pay_cancel_page = give_get_option('dpo_pay_cancel_page');
            $dpo_pay_thankyou_page = give_get_option('dpo_pay_thankyou_page');
            $RedirectURL = $dpo_pay_thankyou_page.'?payment_id='.$payment_id.'&';

            if ( $payment_id ) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $dpo_pay_api_url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
                    <API3G>
                      <CompanyToken>'.$dpo_pay_company_token.'</CompanyToken>
                      <Request>createToken</Request>
                      <Transaction>
                        <PaymentAmount>'.$donation_amount.'</PaymentAmount>
                        <PaymentCurrency>'.$currency.'</PaymentCurrency>
                        <CompanyRef>49FKEOA</CompanyRef>
                        <RedirectURL>'.$RedirectURL.'</RedirectURL>
                        <BackURL>'.$dpo_pay_cancel_page.' </BackURL>
                        <CompanyRefUnique>0</CompanyRefUnique>
                        <PTL>5</PTL>
                      </Transaction>
                      <Services>
                        <Service>
                          <ServiceType>'.$dpo_pay_service_type.'</ServiceType>
                          <ServiceDescription>'.$dpo_pay_description.'</ServiceDescription>
                          <ServiceDate>' . current_time('Y/m/d H:i') . '</ServiceDate>
                        </Service>
                      </Services>
                    </API3G>',
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: text/xml'
                      ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    give_update_meta( $payment_id, '_give_dpo_token_genrate_response_meta',$response );
                    give_insert_payment_note($payment_id,'Token genrate Response Data: '.$response);   
                    if ($response != '') {
                        if (simplexml_load_string($response)) {
                        $xmlresponse = simplexml_load_string($response);
                        $encode_xmlresponse = json_encode($xmlresponse);
                        $arrayresponse = json_decode($encode_xmlresponse, true);
                        $Result = $arrayresponse['Result'];
                            if ($Result != '000') {
                                $ResultExplanation = $arrayresponse['ResultExplanation'];
                                header("Location: ".get_site_url()."/give-dpo-error/?issue=".$ResultExplanation);
                                die();
                            }
                            else{
                                $transactionToken = $arrayresponse['TransToken'];
                                $this->verifydpotoken($transactionToken);
                                header("Location: ".$dpo_pay_pay_url."?ID=".$transactionToken);
                                die();
                            }
                        }
                    }
            }
            else{
                give_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['give-gateway'] );
            }

        }
        else{
            give_send_back_to_checkout( '?payment-mode='.$this->id );
        }
    }
     function verifydpotoken($transactionToken){
        $dpo_pay_api_url = give_get_option('dpo_pay_api_url');
        $dpo_pay_company_token = give_get_option('dpo_pay_company_token');


        $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $dpo_pay_api_url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'<API3G>
              <CompanyToken>'.$dpo_pay_company_token.'</CompanyToken>
              <Request>verifyToken</Request>
              <TransactionToken>'.$transactionToken.'</TransactionToken>
            </API3G>',
              CURLOPT_HTTPHEADER => array(
                'Content-Transfer-Encoding: text/xml',
                'Content-Type: text/plain'
              ),
        ));
        $response = curl_exec($curl);
        if (isset($payment_id)) {
            if($payment_id == ''){
                $payment_id = sanitize_text_field($_GET['payment_id']);
            }
            give_update_meta( $payment_id, '_give_dpo_token_verification_response_meta',$response);
            give_insert_payment_note($payment_id,'Token verification Response Data: '.$response);  
        }
        return $response;
    }
    public function give_dpo_pay_cc_form($form_id){
        $current_getway= give_get_chosen_gateway( $form_id );
        if($current_getway==$this->id)
        {
            remove_action('give_cc_form','give_get_cc_form' );
        }
    }
    public function give_dpo_logo($form_id){
       $current_getway= give_get_chosen_gateway( $form_id );
       $dpo_pay_logo = give_get_option('dpo_pay_logo');
       $dpo_pay_logo_url = give_get_option('dpo_pay_logo_url');
       if($current_getway==$this->id){
        if ($dpo_pay_logo == 'on') {
            if ($dpo_pay_logo_url != '') {?>
                <img src="<?php echo esc_url( $dpo_pay_logo_url ); ?>" style="width: 50%;" />
            <?php }
        }
       }
    }
    public function dpo_custom_template( $page_template ){
        if ( is_page( 'give-dpo-thank-you' ) ) {
            $page_template = plugin_dir_path( __FILE__ ) . '../template/dpo-thankyou-template.php';
        }
        if ( is_page( 'give-dpo-error' ) ) {
            $page_template = plugin_dir_path( __FILE__ ) . '../template/error-template.php';
        }
        return $page_template;
    }
    public function dpo_pay_paymentverification($transactionToken) {
       
        $transactionToken = sanitize_text_field($_GET['TransactionToken']);
        $payment_id = sanitize_text_field($_GET['payment_id']);
        $response = $this->verifydpotoken($transactionToken);
        if ($response) {
            if (simplexml_load_string($response)) {
                $xmlresponse = simplexml_load_string($response);
                $encode_xmlresponse = json_encode($xmlresponse);
                $arrayresponse = json_decode($encode_xmlresponse, true);
                $Result = $arrayresponse['Result'];
                if ($Result == '000') {
                        if (isset($payment_id)) {
                            give_update_payment_status( $payment_id, 'publish' ); 
                            give_update_meta( $payment_id, '_give_dpo_payment_response_meta',$response);
                            give_insert_payment_note($payment_id,'Payment successfully done'.$response);
                            $response_msg = 'success';
                            update_post_meta( $payment_id, 'dpo_pay_response', $response_msg );
                        }
                        else{
                            if (isset($payment_id)) {
                                give_update_meta( $payment_id, '_give_dpo_payment_response_meta',$response);
                                give_insert_payment_note($payment_id,'Payment failed '.$response);  
                                $response_msg = 'failed';
                                update_post_meta( $payment_id, 'dpo_pay_response', $response_msg );
                            }
                        }
                }
            }
        }
        else{
        $response_msg = 'failed';
        update_post_meta( $payment_id, 'dpo_pay_response', $response_msg );
        }
        $response_msg;
    }
}
return new give_dpo_pay_common_class;