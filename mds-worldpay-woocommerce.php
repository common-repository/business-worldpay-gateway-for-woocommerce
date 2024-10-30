<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://www.mdsdev.eu
 * @since             1.0.0
 * @package           MDS_Worldpay_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Business WorldPay Gateway for Woocommerce
 * Plugin URI:        http://www.mdsdev.eu/mds-worldpay-woocommerce
 * Description:       WooCommerce Plugin for accepting payment through WorldPay Business Gateway
 * Version:           1.4.0
 * Author:            MDSDev
 * Author URI:        http://www.mdsdev.eu
 * Text Domain:       mds-worldpay-woocommerce
 * Domain Path:       /languages
 * Requires at least: 4.1
 * Tested up to:      4.7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Abort if the file is called directly
 */
if (!defined('WPINC')) {
    exit;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mds-worldpay-woocommerce-activator.php
 */
function activate_mds_worldpay_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-mds-worldpay-woocommerce-activator.php';
    MDS_Worldpay_Woocommerce_Activator::activate();
}

register_activation_hook(__FILE__, 'activate_mds_worldpay_woocommerce');


/**
 * Run the plugin after all plugins are loaded
 */
add_action('plugins_loaded', 'init_mds_worldpay_gateway', 0);
function init_mds_worldpay_gateway()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    /**
     * The core plugin class that is used to define internationalization and
     * admin-specific hooks
     */
    require plugin_dir_path(__FILE__) . 'includes/class-mds-worldpay-woocommerce.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_mds_worldpay_woocommerce()
    {
        $plugin = new MDS_Worldpay_Woocommerce();
        $plugin->run();
    }

    run_mds_worldpay_woocommerce();
}