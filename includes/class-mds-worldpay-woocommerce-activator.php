<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.mdsdev.eu
 * @since      1.0.0
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/includes
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/includes
 * @author     MDSDev <info@mdsdev.eu>
 */
class MDS_Worldpay_Woocommerce_Activator
{

    /**
     * Check for Woocommerce version on activation
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        if (!class_exists('woocommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));

            wp_die(__('WorldPay Business Gateway for Woocommerce requires Woocommerce version 2.1 or higher', 'mds-worldpay-woocommerce'), __('Plugin Activation Error', 'mds-worldpay-woocommerce'), array('response' => 200, 'back_link' => TRUE));

        }
        if (version_compare(WC()->version, "2.2", '<')) {
            deactivate_plugins(plugin_basename(__FILE__));

            wp_die(__('WorldPay Business Gateway for Woocommerce requires Woocommerce version 2.1 or higher', 'mds-worldpay-woocommerce'), __('Plugin Activation Error', 'mds-worldpay-woocommerce'), array('response' => 200, 'back_link' => TRUE));
        }
    }

}
