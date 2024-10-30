(function ($) {
    'use strict';
    $(function () {
        $('body').addClass('mds-worldpay-woocommerce');
        var overlayContent = '<div class="mds-worldpay-woocommerce-overlay-content">' +
            'Thank you for shopping with us. Redirecting to WorldPay to make the payment.' +
            '<div class="mds-progress-bar">' +
            '<div class="mds-container">' +
            '<div class="mds-bar mds-bar1"></div>' +
            '<div class="mds-bar mds-bar2"></div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('<div/>', {
            id: 'mds-worldpay-woocommerce-overlay'
        }).append(overlayContent).appendTo('body');
        $('form#mds_worldpay_form').submit();
    });

})(jQuery);
