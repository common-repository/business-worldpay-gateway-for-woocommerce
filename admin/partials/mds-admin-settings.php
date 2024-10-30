<?php

/**
 * Provides settings inputs for admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.mdsdev.eu
 * @since      1.0.0
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/admin/partials
 */
if (!defined('ABSPATH')) {
    exit;
}

return array(
    'enabled' => array(
        'title' => __('Enable/Disable', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Enable Business Payment Gateway', 'mds-worldpay-woocommerce'),
        'description' => __('Enable or disable the gateway.', 'mds-worldpay-woocommerce'),
        'desc_tip' => false,
        'default' => 'yes'
    ),
    'title' => array(
        'title' => __('Title', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => __('This controls the title which the user sees during checkout.', 'mds-worldpay-woocommerce'),
        'desc_tip' => true,
        'default' => __('WorldPay', 'mds-worldpay-woocommerce')
    ),
    'description' => array(
        'title' => __('Description', 'mds-worldpay-woocommerce'),
        'type' => 'textarea',
        'description' => __('This controls the description which the user sees during checkout.', 'mds-worldpay-woocommerce'),
        'default' => __("Pay via WorldPay: Accepts Mastercad, Maestro, Visa, American Express, JCB ", 'mds-worldpay-woocommerce')
    ),
    'api_installation_id' => array(
        'title' => __('Installation ID', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => __('Add your Business WorldPay Installation ID from your Setup->Installation.', 'mds-worldpay-woocommerce'),
        'default' => '',
        'desc_tip' => true
    ),
    'testmode' => array(
        'title' => __('WorldPay Test Mode', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Enable Test Mode', 'mds-worldpay-woocommerce'),
        'description' => __('Enable or disable the test mode for the gateway to test the payment method.', 'mds-worldpay-woocommerce'),
        'desc_tip' => false,
        'default' => 'yes'
    ),
    'debug' => array(
        'title' => __('Debug Log', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Enable logging', 'mds-worldpay-woocommerce'),
        'default' => 'no',
        'description' => sprintf(__('Log Worldpay events, inside <strong><code>%s</code></strong>', 'mds-worldpay-woocommerce'), wc_get_log_file_path('worldpay'))
    ),
    'advanced' => array(
        'title' => __('Advanced options', 'mds-worldpay-woocommerce'),
        'type' => 'title',
        'description' => '',
    ),
    'api_payment_response_password' => array(
        'title' => __('Payment Response Password ', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => __('Add your Payment Response Password from your Setup->Installation->Payment Response Password. ', 'mds-worldpay-woocommerce'),
        'default' => '',
        'desc_tip' => true
    ),
    'api_md5_secret' => array(
        'title' => __('MD5 Secret', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => __('Add your Business WorldPay MD5 Secret from your Setup->Installation->MD5 secret for transactions.', 'mds-worldpay-woocommerce'),
        'default' => '',
        'desc_tip' => true
    ),
    'api_md5_fields' => array(
        'title' => __('Set Signature Fields', 'mds-worldpay-woocommerce'),
        'type' => 'multiselect',
        'css' => 'max-width: 550px;',
        'desc_tip' => __('Select signature fields for MD5 encryption.', 'mds-worldpay-woocommerce'),
        'options' => $this->build_signature_options($this->api_md5_fields),
        'default' => array('instId', 'amount', 'currency', 'cartId'),
    ),
    'api_md5_generated_fields' => array(
        'title' => __('Signature Fields', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => __('The generated signature fields. Copy this in your WorldPay account Setup->Installations->Integration Setup->"SignatureFields" field', 'mds-worldpay-woocommerce'),
        'desc_tip' => false,
        'class' => 'mds-mds-fields-str',
        'custom_attributes' => array(
            'readonly' => 'readonly',
            'data-md5-str' => $this->api_md5_fields_str
        ),
        'default' => $this->api_md5_fields_str,
    ),
    'merchant_acc' => array(
        'title' => __('Merchant Code', 'mds-worldpay-woocommerce'),
        'type' => 'title',
        'description' => __('This parameters will enable you to select which merchant code you would like payments to go through. You can select a total of three different ones. So for example if you would like payments to go through a specific merchant account you can input your preferred WorldPay merchant code in one of the fields. The first one to be used will be the one from the first field, if for some reason we the first one can\'t be used (incorrect currency, different capture delay settings, etc.), the second one will be used and then the third. If you do not know this information or you only have one merchant code you may leave these fields blank.', 'mds-worldpay-woocommerce')
    ),
    'api_merchant_code_1' => array(
        'title' => __('Merchant Code 1', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => ''
    ),
    'api_merchant_code_2' => array(
        'title' => __('Merchant Code 2', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => ''
    ),
    'api_merchant_code_3' => array(
        'title' => __('Merchant Code 3', 'mds-worldpay-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => ''
    ),
    'payment_page' => array(
        'title' => __('Worldpay Payment Page Options', 'mds-worldpay-woocommerce'),
        'type' => 'title',
        'description' => '',
    ),
    'api_hide_contact' => array(
        'title' => __('Payment Page Billing Info', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Hide Billing Information', 'mds-worldpay-woocommerce'),
        'default' => 'yes',
        'description' => __('Display the shopper billing contact information on payment page', 'mds-worldpay-woocommerce'),
    ),
    'api_with_delivery' => array(
        'title' => __('Payment Page Delivery Info', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('With Delivery', 'mds-worldpay-woocommerce'),
        'custom_attributes' => $this->shipping_enabled ? '' : array(
            'disabled' => 'disabled'
        ),
        'default' => 'no',
        'description' => __('Display the shopper delivery information on payment page if shop shipping is enabled', 'mds-worldpay-woocommerce'),
    ),
    'api_fix_contact' => array(
        'title' => __('Payment Page Fix Info', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Fix Billing and Delivery Info', 'mds-worldpay-woocommerce'),
        'default' => 'yes',
        'description' => __('Allow shopper to edit the billing and delivery information on payment page', 'mds-worldpay-woocommerce'),
    ),
    'api_no_language_menu' => array(
        'title' => __('Payment Page Hide Language Menu', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Hide Language Menu', 'mds-worldpay-woocommerce'),
        'default' => 'yes',
        'description' => __('Allow shopper to change the language on payment page', 'mds-worldpay-woocommerce'),
    ),
    'api_lang' => array(
        'title' => __('Payment Page language', 'mds-worldpay-woocommerce'),
        'type' => 'select',
        'class' => 'chosen_select',
        'desc_tip' => __('Choose the default language shopper will see on Payment Page', 'mds-worldpay-woocommerce'),
        
        'options' => array(
            "en" => __('English', 'mds-worldpay-woocommerce'),
            "da" => __('Dansk', 'mds-worldpay-woocommerce'),
            "de" => __('Deutsch', 'mds-worldpay-woocommerce'),
            "et" => __('Eesti', 'mds-worldpay-woocommerce'),
            "es" => __('Español', 'mds-worldpay-woocommerce'),
            "el" => __('Eλληνικά', 'mds-worldpay-woocommerce'),
            "fr" => __('Français', 'mds-worldpay-woocommerce'),
            "it" => __('Italiano', 'mds-worldpay-woocommerce'),
            "lv" => __('Latviešu', 'mds-worldpay-woocommerce'),
            "hu" => __('Magyar', 'mds-worldpay-woocommerce'),
            "nl" => __('Nederlands', 'mds-worldpay-woocommerce'),
            "no" => __('Norsk', 'mds-worldpay-woocommerce'),
            "pl" => __('Polski', 'mds-worldpay-woocommerce'),
            "pt" => __('Português', 'mds-worldpay-woocommerce'),
            "ru" => __('Pyccкий', 'mds-worldpay-woocommerce'),
            "ro" => __('Română', 'mds-worldpay-woocommerce'),
            "sk" => __('Slovenčina', 'mds-worldpay-woocommerce'),
            "fi" => __('Suomi', 'mds-worldpay-woocommerce'),
            "sv" => __('Svenska', 'mds-worldpay-woocommerce'),
            "tr" => __('Türkçe', 'mds-worldpay-woocommerce'),
            "cs" => __('Čeština', 'mds-worldpay-woocommerce'),
            "bg" => __('Български', 'mds-worldpay-woocommerce'),
            "ja" => __('日本語', 'mds-worldpay-woocommerce'),
            "ko" => __('한국어', 'mds-worldpay-woocommerce'),

        ),
        'default' => 'en'
    ),
    'api_hide_currency' => array(
        'title' => __('Payment Page Hide Currency', 'mds-worldpay-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Hide Currency', 'mds-worldpay-woocommerce'),
        'default' => 'yes',
        'description' => __('Allow shopper to change the payment currency.', 'mds-worldpay-woocommerce'),
    ),

);
