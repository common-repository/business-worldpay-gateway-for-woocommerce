<?php
/**
 * Fired during gateway request
 *
 * @link       http://www.mdsdev.eu
 * @since      1.0.0
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/includes/partials
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates requests to send to Worldpay.
 *
 * This class defines all code necessary for gateway request.
 *
 * @since      1.0.0
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/includes/partials
 * @author     MDSDev <info@mdsdev.eu>
 */
class MDS_Worldpay_Gateway_Request
{
    /**
     * Pointer to gateway making the request.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MDS_Worldpay_Gateway $gateway Gateway instance
     */
    protected $gateway;

    /**
     * Endpoint for requests from Worldpay.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $notify_url Endpoint URL
     */
    protected $notify_url;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param    MDS_Worldpay_Gateway $gateway
     */
    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->notify_url = WC()->api_request_url('MDS_Worldpay_Gateway');
    }

    /**
     * Get the transaction URL
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @return   string
     */
    private function get_payment_url($sandbox = false)
    {
        if ($sandbox) {
            $payment_url = 'https://secure-test.worldpay.com/wcc/purchase';
        } else {
            $payment_url = 'https://secure.worldpay.com/wcc/purchase';
        }
        return $payment_url;
    }

    /**
     * Get required params to pass to WorldPay.
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @param   bool $sandbox
     * @return    array
     */
    private function get_payment_required_args($order, $sandbox)
    {
        return array(
            'instId' => $this->gateway->api_installation_id,
            'amount' => $order->get_total(),
            'currency' => get_woocommerce_currency(),
            'cartId' => $order->get_id(),
            'testMode' => $sandbox ? '100' : '',
        );
    }

    /**
     * Generate the md5 value for the order if the md5 secret is set
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @return    array
     */
    private function get_md5_arg($order)
    {
        if (empty($this->gateway->api_md5_secret)) {
            return array();
        }
        $ordered_fields[] = $this->gateway->api_md5_secret;
        $signature_fields_value = array(
            'instId' => $this->gateway->api_installation_id,
            'amount' => $order->get_total(),
            'currency' => get_woocommerce_currency(),
            'cartId' => $order->get_id()
        );
        foreach ($this->gateway->api_md5_fields as $field) {
            $ordered_fields[] = $signature_fields_value[$field];
        }

        return array(
            'signature' => md5(implode(':', $ordered_fields))
        );
    }

    /**
     * Get merchant codes params to pass to WorldPay.
     *
     * @since   1.0.0
     * @return array
     */
    private function get_merchant_codes_args()
    {
        $merchant_args = array();
        foreach ($this->gateway->api_merchant_codes as $key => $value) {
            $merchant_args['accId' . ($key + 1)] = $value;
        }

        return $merchant_args;
    }

    /**
     * Get payment pages params to pass to WorldPay.
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @return    array
     */
    private function get_payment_pages_args($order)
    {

        return array(
            'desc' => sprintf(__('Order %s', 'mds-worldpay-woocommerce'), $order->get_order_number()),
            'hideContact' => $this->gateway->api_hide_contact ? 'true' : 'false',
            'fixContact' => $this->gateway->api_fix_contact ? 'true' : 'false',
            'withDelivery' => $this->gateway->api_with_delivery ? 'true' : 'false',
            'noLanguageMenu' => $this->gateway->api_no_language_menu ? 'true' : 'false',
            'lang' => $this->gateway->api_lang,
            'hideCurrency' => $this->gateway->api_hide_currency ? 'true' : 'false',
        );
    }

    /**
     * Get biling params to pass to WorldPay.
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @return    array
     */
    private function get_billing_info_args($order)
    {
        return array(
            'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'address1' => $order->get_billing_address_1(),
            'address2' => $order->get_billing_address_2(),
            'town' => $order->get_billing_city(),
            'region' => $order->get_billing_state(),
            'postcode' => $order->get_billing_postcode(),
            'country' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'tel' => $order->get_billing_phone(),
        );
    }

    /**
     * Get shipping params to pass to WorldPay.
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @return    array
     */
    private function get_delivery_address_args($order)
    {
        if ($this->gateway->shipping_enabled && $this->gateway->api_with_delivery) {
            return array(
                'delvName' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'delvAddress1' => $order->get_shipping_address_1(),
                'delvAddress2' => $order->get_shipping_address_2(),
                'delvTown' => $order->get_shipping_city(),
                'delvPostcode' => $order->get_shipping_postcode(),
                'delvCountry' => $order->get_shipping_country(),
            );
        }

        return array();
    }

    /**
     * Get custom params to pass to WorldPay.
     *
     * @since   1.0.0
     * @param    WC_Order $order
     * @return    array
     */
    private function get_custom_args($order)
    {
        return array(
            'CM_success_payment_url' => $this->gateway->get_return_url($order),
            'CM_cancel_payment_url' => $order->get_cancel_order_url(),
            'CM_payment_response_url' => $this->notify_url,
            'CM_order_key' => $order->get_order_key()
        );
    }

    /**
     * Get Worldpay Args for passing to PP.
     *
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   array
     */
    private function get_worldpay_args($order, $sandbox = false)
    {
        MDS_Worldpay_Gateway::log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url);

        return apply_filters('woocommerce_worldpay_args',
            array_merge(
                $this->get_payment_required_args($order, $sandbox),
                $this->get_md5_arg($order),
                $this->get_merchant_codes_args(),
                $this->get_payment_pages_args($order),
                $this->get_billing_info_args($order),
                $this->get_delivery_address_args($order),
                $this->get_custom_args($order)
            )
            , $order);
    }

    /**
     * Generate Worldpay payment form
     *
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   string
     */
    public function generate_worldpay_form($order, $sandbox = false)
    {
        $worldpay_args = $this->get_worldpay_args($order, $sandbox);
        $worldpay_form[] = '<form action="' . esc_url($this->get_payment_url($sandbox)) . '" method="post" id="mds_worldpay_form">';

        foreach ($worldpay_args as $key => $value) {
            $worldpay_form[] = '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
        }
        $worldpay_form[] = '<input type="submit" class="button mds-worldpay-submit" name="" value="' . __('Pay via WorldPay', 'mds-worldpay-woocommerce') . '" />';
        $worldpay_form[] = '<a class="button mds-worldpay-cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order', 'mds-worldpay-woocommerce') . '</a>';
        $worldpay_form[] = '</form>';


        return implode('', $worldpay_form);
    }

}
