(function ($) {
    'use strict';

    var $select2 = $('#woocommerce_business_worldpay_api_md5_fields').select2();

    /**
     * defaults: Cache order of the initial values
     * @type {object}
     */
    var defaults = $select2.select2('data');
    defaults.forEach(function (obj) {
        var order = $select2.data('preserved-order') || [];
        order[order.length] = obj.id;
        $select2.data('preserved-order', order);
    });

    /**
     * select2_renderselections
     * @param  {jQuery Select2 object}
     * @return {null}
     */
    function select2_renderSelections($select2) {
        var order = $select2.data('preserved-order') || [];
        var $container = $select2.next('.select2-container');
        var $tags = $container.find('li.select2-selection__choice');
        var $input = $tags.last().next();

        // apply tag order
        order.forEach(function (val) {
            var $el = $tags.filter(function (i, tag) {
                return $(tag).data('data').id === val;
            });
            $input.before($el);
        });
    }

    /**
     * selectionHandler
     * @param  {Select2 Event Object}
     * @return {null}
     */
    function selectionHandler(e) {
        var $select2 = $(this);
        var val = e.params.data.id;
        var order = $select2.data('preserved-order') || [];

        switch (e.type) {
            case 'select2:select':
                order[order.length] = val;

                var option = $select2.find('option[value="' + val + '"]')[0];
                $select2.append(option);

                break;
            case 'select2:unselect':
                var found_index = order.indexOf(val);
                if (found_index >= 0) order.splice(found_index, 1);
                break;
        }
        $select2.data('preserved-order', order); // store it for later
        select2_renderSelections($select2);
    }

    $select2.on('select2:select select2:unselect', selectionHandler);

    var inputMd5Str = $('.mds-mds-fields-str');
    var mds5Str = inputMd5Str.data('md5-str');
    inputMd5Str.val(mds5Str);

}(jQuery));