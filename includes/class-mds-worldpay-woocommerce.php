<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/includes
 * @author     MDSDev <info@mdsdev.eu>
 */
class MDS_Worldpay_Woocommerce {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MDS_Worldpay_Woocommerce_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'mds-worldpay-woocommerce';
        $this->version = '1.4.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MDS_Worldpay_Woocommerce_Loader. Orchestrates the hooks of the plugin.
     * - MDS_Worldpay_Woocommerce_i18n. Defines internationalization functionality.
     * - MDS_Worldpay_Woocommerce_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mds-worldpay-woocommerce-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mds-worldpay-woocommerce-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-mds-worldpay-gateway.php';

        $this->loader = new MDS_Worldpay_Woocommerce_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MDS_Worldpay_Woocommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new MDS_Worldpay_Woocommerce_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new MDS_Worldpay_Gateway();

        $this->loader->add_filter('woocommerce_payment_gateways', $plugin_admin, 'add_new_gateway');
        $this->loader->add_action('wp', $plugin_admin, 'set_wc_notice');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_scripts');
        add_action('woocommerce_update_options_payment_gateways_' . $plugin_admin->id, array($plugin_admin, 'process_admin_options'));
        add_action('woocommerce_receipt_business_worldpay', array($plugin_admin, 'receipt_page'));
        add_action('woocommerce_api_mds_worldpay_gateway', array($plugin_admin, 'worldpay_response_handler'));
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    MDS_Worldpay_Woocommerce_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
