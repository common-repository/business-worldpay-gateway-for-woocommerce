<?php

/**
 * The main-specific functionality of the plugin.
 *
 * @link       http://www.mdsdev.eu
 * @sincesuccessful
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main-specific functionality of the plugin.
 *
 * Defines the plugin name, version,
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/admin
 * @author     MDSDev <info@mdsdev.eu>
 */
class MDS_Worldpay_Gateway extends WC_Payment_Gateway {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      bool $shipping_enabled Whether or not shop shipping is enabled
     */
    public $shipping_enabled;

    /**
     * Logger enabled
     *
     * @since    1.0.0
     * @access   public
     * @var     bool $log_enabled Whether or not logging is enabled
     */
    public static $log_enabled = false;

    /**
     * Instance of logger
     *
     * @since    1.0.0
     * @access   public
     * @var      WC_Logger $log Logger instance
     */
    public static $log = false;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'mds-worldpay-woocommerce';
        $this->version = '1.4.0';
        $this->shipping_enabled = 'yes' === get_option('woocommerce_calc_shipping', 'no');

        $this->id = 'business_worldpay';
        $this->method_title = __('WorldPay Business', 'mds-worldpay-woocommerce');
        $this->method_description = __('WorldPay Business gateway sends customers to WorldPay to enter their payment information and redirects back to shop when the payment was completed.', 'mds-worldpay-woocommerce');
        $this->icon = apply_filters('mds-worldpay-woocommerce_icon', plugins_url('assets/img/worldpay.png', __FILE__));
        $this->has_fields = false;
        $this->credit_fields = false;

        // Define user set variables.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->testmode = 'yes' === $this->get_option('testmode', 'no');
        $this->debug = 'yes' === $this->get_option('debug', 'no');
        $this->api_installation_id = $this->get_option('api_installation_id');
        $this->api_hide_contact = 'yes' === $this->get_option('api_hide_contact', 'no');
        $this->api_merchant_codes = $this->get_merchant_codes();
        $this->api_with_delivery = 'yes' === $this->get_option('api_with_delivery', 'no');
        $this->api_fix_contact = 'yes' === $this->get_option('api_fix_contact', 'no');
        $this->api_no_language_menu = 'yes' === $this->get_option('api_no_language_menu', 'no');
        $this->api_lang = $this->get_option('api_lang', 'en');
        $this->api_hide_currency = 'yes' === $this->get_option('api_hide_currency', 'no');
        $this->api_callback_pw = $this->get_option('api_payment_response_password', '');
        $this->api_md5_secret = $this->get_option('api_md5_secret');
        $this->api_md5_fields = $this->get_option('api_md5_fields');
        $this->api_md5_fields_str = $this->build_signature_fields($this->api_md5_fields);

        //Load Settings
        $this->init_form_fields();
        $this->init_settings();

        self::$log_enabled = $this->debug;

        if (!$this->is_valid_for_use()) {
            $this->enabled = false;
        }
    }

    /**
     * Logging method
     *
     * @since    1.0.0
     * @param    string $message
     */
    public static function log($message) {
        if (self::$log_enabled) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }
            self::$log->add('worldpay', $message);
        }
    }

    /**
     * Check if the currency of store is accepted by Worldpay
     *
     * @since    1.0.0
     */
    public function is_valid_for_use() {
        if (!in_array(get_woocommerce_currency(),
            array('LRD', 'UYU', 'GBP', 'EGP', 'FKP', 'LBP', 'SHP', 'JPY', 'AUD', 'AFN', 'AOA', 'ARS', 'AZN', 'BSD', 'BND','SLL', 'LTL', 'BGN','LSL', 'MDL', 'MXN',
                'PAB', 'BMD', 'BHD', 'BBD', 'THB', 'ETB', 'BTN', 'CVE', 'CAD', 'CDF', 'KMF', 'XPF', 'CLP', 'CHF', 'KYD', 'COP', 'KHR', 'CZK', 'VND', 'GMD', 'DZD',
                'STD', 'DJF', 'AED', 'MAD', 'DKK', 'XCD', 'EUR', 'FJD', 'BIF', 'HUF', 'HTG', 'GEL', 'GHS', 'HKD', 'HRK', 'JMD', 'JOD', 'KES', 'PGK', 'MMK', 'KWD',
                'LAK', 'KZT', 'LSL', 'MDL', 'MXN', 'MGA', 'MZN', 'ANG', 'NGN', 'ILS', 'NOK', 'TWD', 'NZD', 'MOP', 'BWP', 'PHP', 'PLN', 'TOP', 'QAR', 'BRL', 'RUB',
                'ZAR', 'RWF', 'MVR', 'MYR', 'OMR', 'RON', 'IDR', 'INR', 'PKR', 'RSD', 'SGD', 'PEN', 'SAR', 'SBD', 'SEK', 'LKR', 'SOS', 'SCR', 'SRD', 'TND', 'TJS',
                'BDT', 'TMT', 'TZS', 'TTD', 'MNT', 'UAH', 'MRO', 'USD', 'UGX', 'UZS', 'VUV', 'KRW', 'WST', 'CNY', 'TRY', 'ZMW'))
        ) {
            $this->msg = sprintf(__("WorldPay doesn't accept your store currency. Check available currencies  %s here", 'mds-worldpay-woocommerce') . "</a>", "<a href='http://support.worldpay.com/support/kb/bg/pdf/rhtml.pdf#page=50'>");
            return false;
        }

        return true;
    }

    /**
     * Get admin options template
     *
     * @since    1.0.0
     */
    public function admin_options() {
        include('partials/views/mds-admin-settings-template.php');
    }

    /**
     * Get Form fields array
     *
     * @since    1.0.0
     */
    public function init_form_fields() {
        $this->form_fields = include('partials/mds-admin-settings.php');
    }

    /**
     * Process the payment and return the result.
     *
     * @since    1.0.0
     * @param    int $order_id
     * @return   array
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        );
    }

    /**
     * Handle the response from WorldPay.
     *
     * @since    1.0.0
     */
    public function worldpay_response_handler() {

        if (isset($_POST['transStatus'])) {
            $transaction_status = $_POST['transStatus'];

            if (isset($_POST['cartId'])) {
                $order_id = $_POST['cartId'];
                $installation_id = $_POST['instId'];
                $order = wc_get_order((int)$order_id);
                if(!$order && isset($_POST['CM_order_key'])) {
                    $order_id = wc_get_order_id_by_order_key($_POST['CM_order_key']);
                    $order    = wc_get_order($order_id);
                }

                $order_total = $order->get_total();

                if (isset($_POST['callbackPW']) && !empty($_POST['callbackPW'])) {
                    if ($_POST['callbackPW'] != $this->api_callback_pw) {
                        MDS_Worldpay_Gateway::log('Payment Response Passwords do not match on order: ' . $order_id);

                        $message = __('Security check failed, the transaction wasn\'t successful, order has been canceled.', 'mds-worldpay-woocommerce');
                        $message_type = 'error';

                        //Add Customer Order Note
                        $order->add_order_note($message, 1);

                        //Add Admin Order Note
                        $order->add_order_note(__('Payment Response password do not match.', 'mds-worldpay-woocommerce'));

                        //Update the order status
                        $order->update_status('failed', '');

                        $worldpay_message = array(
                            'message' => $message,
                            'message_type' => $message_type
                        );

                        update_post_meta($order_id, '_mds_worldpay_message', $worldpay_message);

                        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                        exit;
                    }
                }

                if ($transaction_status === 'Y') {
                    $transaction_id = $_POST['transId'];
                    $amount_paid = $_POST['amount'];
                    $currency_symbol = get_woocommerce_currency_symbol($_POST['currency']);

                    if ($installation_id != $this->api_installation_id) {
                        //Update the order status
                        $order->update_status('on-hold', '');

                        //Error Note
                        $message = __('Thank you for shopping with us.<br />Your payment transaction was successful, but the amount was paid to the wrong merchant account. Illegal hack attempt.<br />Your order is currently on-hold.<br />Contact us for more information.', 'mds-worldpay-woocommerce');
                        $message_type = 'notice';

                        //Add Customer Order Note
                        $order->add_order_note($message . '<br />' . __('WorldPay Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id, 1);

                        //Add Admin Order Note
                        $order->add_order_note(sprintf(__('Look into this order. <br />This order is currently on hold.<br />Reason: Illegal hack attempt. The order was successful but the money was paid to the wrong WorldPay account.<br /> Your WorldPay Installation ID %s the Installation id the payment was sent to %s<br />WorldPay Transaction ID: %s', 'mds-worldpay-woocommerce'), $this->api_installation_id, $installation_id, $transaction_id));


                    } else if ($order_total != $amount_paid) {
                        //Update the order status
                        $order->update_status('on-hold', '');

                        //Error Note
                        $message = __('Thank you for shopping with us.<br />Your payment transaction was successful, but the amount paid is not the same as the total order amount.<br />Your order is currently on-hold.<br />Contact us for more information.', "mds-worldpay-woocommerce");
                        $message_type = 'notice';

                        //Add Customer Order Note
                        $order->add_order_note($message . '<br />' . __('WorldPay Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id, 1);

                        //Add Admin Order Note
                        $order->add_order_note(sprintf(__('Look into this order. <br />This order is currently on hold.<br />Reason: Amount paid is less than the total order amount.<br />Amount Paid was  %s while the total order amount is %s<br />WorldPay Transaction ID: %s', "mds-worldpay-woocommerce"), $currency_symbol . $amount_paid, $currency_symbol . $order_total, $transaction_id));

                    } else {

                        if ($order->status == 'processing') {
                            //Success Note
                            $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'mds-worldpay-woocommerce');
                            $message_type = 'success';

                            //Add customer order note
                            $order->add_order_note(sprintf(__('Payment Received.<br />Your order is currently being processed.<br />WorldPay Transaction ID: %s', 'mds-worldpay-woocommerce'), $transaction_id), 1);

                            //Add Admin Order Note
                            $order->add_order_note(__('Payment Via WorldPay<br />Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id);

                        } else {

                            if ($order->has_downloadable_item()) {
                                //Update order status
                                $order->update_status('completed', __('Payment received, your order is now complete.'));

                                //Success Note
                                $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is now complete.', 'mds-worldpay-woocommerce');
                                $message_type = 'success';

                                //Add customer order note
                                $order->add_order_note(__('Payment Received.<br />Your order is now complete.<br />WorldPay Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id, 1);

                                //Add admin order note
                                $order->add_order_note(__('Payment Via WorldPay <br />Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id);

                            } else {
                                //Update order status
                                $order->update_status('processing', __('Payment received, your order is currently being processed.'));

                                //Success Note
                                $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'mds-worldpay-woocommerce');
                                $message_type = 'success';

                                //Add customer order note
                                $order->add_order_note(__('Payment Received.<br />Your order is currently being processed.<br />We will be shipping your order to you soon.<br />WorldPay Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id, 1);

                                //Add admin order noote
                                $order->add_order_note(__('Payment Via WorldPay Payment Gateway<br />Transaction ID: ', 'mds-worldpay-woocommerce') . $transaction_id);

                            }
                        }

                    }
                    // Reduce stock levels
                    wc_reduce_stock_levels($order_id);

                    // Empty cart
                    wc_empty_cart();

                    MDS_Worldpay_Gateway::log('Payment Completed on order: ' . $order_id . ', Transaction id:' . $transaction_id);
                    $worldpay_message = array(
                        'message' => $message,
                        'message_type' => $message_type
                    );

                    if (version_compare(WOOCOMMERCE_VERSION, "2.2") >= 0) {
                        add_post_meta( $order_id, '_paid_date', current_time( 'mysql' ), true );
                        update_post_meta($order_id, '_transaction_id', $transaction_id);
                    }

                    update_post_meta($order_id, '_mds_worldpay_message', $worldpay_message);
                    do_action( 'woocommerce_payment_complete', $order_id);
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                    exit;

                } else if ($transaction_status === 'C') {

                    MDS_Worldpay_Gateway::log('Payment Canceled by user on order: ' . $order_id);

                    $message = __('Thank you for shopping with us. <br />However, the transaction wasn\'t successful, payment has been canceled.', 'mds-worldpay-woocommerce');
                    $message_type = 'error';

                    //Add Customer Order Note
                    $order->add_order_note($message, 1);

                    //Add Admin Order Note
                    $order->add_order_note(__('Payment canceled by user.', 'mds-worldpay-woocommerce'));

                    //Update the order status
                    $order->update_status('cancelled', '');

                    $worldpay_message = array(
                        'message' => $message,
                        'message_type' => $message_type
                    );

                    update_post_meta($order_id, '_mds_worldpay_message', $worldpay_message);

                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                    exit;
                } else {
                    MDS_Worldpay_Gateway::log('Payment Canceled by user on order: ' . $order_id);

                    $message = __('Thank you for shopping with us. <br /> However your recurring payment transaction has been declined.', 'mds-worldpay-woocommerce');
                    $message_type = 'error';

                    //Add Customer Order Note
                    $order->add_order_note($message, 1);

                    //Add Admin Order Note
                    $order->add_order_note(__('Recurring payment transaction has been declined.', 'mds-worldpay-woocommerce'));

                    //Update the order status
                    $order->update_status('failed', '');

                    $worldpay_message = array(
                        'message' => $message,
                        'message_type' => $message_type
                    );

                    update_post_meta($order_id, '_mds_worldpay_message', $worldpay_message);

                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                }

            } else {
                MDS_Worldpay_Gateway::log('Cart id and/or M_order parameter/s are/is missing from payment response.');

            }

        }
    }

    /**
     * Set a Woocommerce notice with the payment status on the order received page
     *
     * @since    1.0.0
     */
    public function set_wc_notice() {
        if (get_query_var('order-received')) {
            $order_id = absint(get_query_var('order-received'));
            $order = wc_get_order($order_id);
            $payment_method = $order->payment_method;

            if (is_order_received_page() && ('business_worldpay' == $payment_method)) {
                $worldpay_message = get_post_meta($order_id, '_mds_worldpay_message', true);

                if (!empty($worldpay_message)) {
                    $message = $worldpay_message['message'];
                    $message_type = $worldpay_message['message_type'];

                    delete_post_meta($order_id, '_mds_worldpay_message');

                    wc_add_notice($message, $message_type);
                }
            }
        }
    }

    /**
     * Redirect page to WorldPay PP.
     *
     * @since    1.0.0
     * @param    int $order_id
     */
    public function receipt_page($order_id) {
        include_once('partials/class-mds-worldpay-gateway-request.php');

        $this->enqueue_styles();
        $this->enqueue_scripts();

        $order = wc_get_order($order_id);
        $worldpay_request = new MDS_Worldpay_Gateway_Request($this);

        echo $worldpay_request->generate_worldpay_form($order, $this->testmode);
    }

    /**
     * Get Merchant Codes
     *
     * @since    1.0.0
     * @return   array
     */
    private function get_merchant_codes() {
        $merchant_codes = array();
        for ($i = 1; $i < 4; $i++) {
            $code = $this->get_option('api_merchant_code_' . $i);
            if (!empty($code)) {
                array_push($merchant_codes, $code);
            }
        }

        return $merchant_codes;
    }

    /**
     * Builds the  Signature fields string from the fields selected
     *
     * @since   1.0.0
     * @param   string $md5_fields
     *
     * @return  string
     */
    private function build_signature_fields($md5_fields = '') {
        $md5_fields_string = __('Select the fields for the signature and save to generate the it');
        if (!empty($md5_fields)) {
            $md5_fields_string = implode(':', $md5_fields);
        }
        return $md5_fields_string;
    }

    /**
     * Build the ordered options for the signature fields
     *
     * @since   1.0.0
     * @param   string $md5_fields
     *
     * @return  array
     */
    private function build_signature_options($md5_fields = '') {
        $ordered_fields = array();
        $available_fields = array(
            'instId' => 'Installation ID',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'cartId' => 'Cart ID',
        );
        foreach ((array)$md5_fields as $field) {
            if (isset($available_fields[$field])) {
                $ordered_fields[$field] = $available_fields[$field];
                unset($available_fields[$field]);
            }
        }

        return array_merge($ordered_fields, $available_fields);
    }

    /**
     * Register the stylesheets for frontend redirect loading box.
     *
     * @since    1.0.0
     */
    private function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/css/mds-worldpay-woocommerce.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for frontend redirect.
     *
     * @since    1.0.0
     */
    private function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/js/mds-worldpay-woocommerce.js', array('jquery'), $this->version, TRUE);
    }

    /**
     * Register the JavaScript for admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '/assets/css/mds-worldpay-woocommerce-admin.css', false, $this->version );
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/js/mds-worldpay-woocommerce-admin.js', array('jquery'), $this->version, TRUE);
    }

    /**
     * Add WorldPay as Woocommerce payment methods.
     *
     * @since    1.0.0
     */
    public function add_new_gateway($methods)
    {
        $methods[] = 'MDS_Worldpay_Gateway';

        return $methods;
    }

}
