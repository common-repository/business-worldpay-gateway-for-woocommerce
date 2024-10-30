<?php

/**
 * Provide a admin area view for the plugin
 *
 *
 * @link       http://www.mdsdev.eu
 * @since      1.0.0
 *
 * @package    MDS_Worldpay_Woocommerce
 * @subpackage MDS_Worldpay_Woocommerce/admin/partials
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit;
}

if ($this->is_valid_for_use()): ?>
    <div class="mds-support-request">
        <h1>
            If you find this plugin useful consider supporting with a small amount. This will help me focus more on improving it and fixing the bugs faster.
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NC53TBZW66P4E" target="_blank">
                <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal - The safer, easier way to pay online!">
            </a>
        </h1>
    </div>
  

    <h3><?php echo $this->method_title; ?></h3>

    <?php echo (!empty($this->method_description)) ? wpautop($this->method_description) : ''; ?>

    <table class="form-table">
        <?php $this->generate_settings_html(); ?>
    </table>

    <?php
else: ?>
    <div class="inline error">
        <p>
            <strong><?php _e('Payment gateway is disabled', 'mds-worldpay-woocommerce'); ?></strong>:
            <?php _e('Business WorldPay does not support your store currency.', 'mds-worldpay-woocommerce'); ?>
        </p>
    </div>
<?php endif;
