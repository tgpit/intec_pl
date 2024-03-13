<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var deliveriesRadio = $('[type="radio"][name="DELIVERY"]', data.nodes);

        if (deliveriesRadio.length === 0)
            return;

        var object = <?= JavaScript::toObject($arResult['JS_OBJECT']) ?>;
        var total = $('[data-role="total"]', data.nodes);

        total.delivery = $('[data-role="total.delivery"]', total);
        total.summary = $('[data-role="total.summary"]', total);

        deliveriesRadio.radioCheck = function (init) {
            var current = deliveriesRadio.filter(':checked');

            deliveriesRadio.each(function () {
                var radio = $(this);

                deliveriesRadio.radioHandler(radio, current, init);
                update(radio, current);
            });
        };

        deliveriesRadio.radioHandler = function (radio, current, init) {
            var delivery = radio.closest('[data-role="delivery"]');
            var height;

            delivery.properties = $('[data-role="delivery.properties"]', delivery);

            if (delivery.properties.length === 0)
                return;

            delivery.inputs = $('input, select, textarea', delivery.properties);

            delivery.properties.css('display', 'block');

            height = delivery.properties.height();

            delivery.properties.css('display', '');

            if (radio.val() === current.val()) {
                if (delivery.properties.attr('data-state') === 'enabled' && !init)
                    return;

                delivery.properties.attr('data-state', 'enabled').css('height', 0);

                setTimeout(function () {
                    delivery.properties.css('height', height);

                    setTimeout(function () {
                        delivery.properties.css('height', '');
                        delivery.inputs.attr('disabled', false);
                    }, 400);
                }, 5);
            } else {
                if (delivery.properties.attr('data-state') === 'disabled' && !init)
                    return;

                delivery.properties.css('height', height);
                delivery.inputs.attr('disabled', true);

                setTimeout(function () {
                    delivery.properties.css('height', 0);

                    setTimeout(function () {
                        delivery.properties.attr('data-state', 'disabled').css('height', '');
                    }, 400);
                }, 5);
            }
        };

        var update = function (radio, current) {
            if (radio.val() !== current.val())
                return;

            var delivery = object.deliveries[current.val()];

            if (total.delivery.length > 0 && !_.isNil(delivery))
                total.delivery.html(delivery.price.print);

            if (total.summary.length > 0 && !_.isNil(object.summary) && !_.isNil(delivery)) {
                var summary = object.summary.value + delivery.price.value;
                var summaryDecimals = 2;

                if (summary === parseInt(summary))
                    summaryDecimals = 0;

                if (!_.isNil(object.currency)) {
                    total.summary.html(_.replace(object.currency.format, '#', Startshop.Functions.numberFormat(
                        object.summary.value + delivery.price.value,
                        summaryDecimals,
                        object.currency.decimals.delimiter,
                        object.currency.thousands.delimiter
                    )));
                } else {
                    total.summary.html(Startshop.Functions.numberFormat(
                        object.summary.value + delivery.price.value,
                        summaryDecimals,
                        ',',
                        ' '
                    ));
                }
            }
        };

        deliveriesRadio.radioCheck(true);

        deliveriesRadio.on('click', function () {
            deliveriesRadio.radioCheck(false);
        });
    }, {
        'name': '[Component] intec.startshop:startshop.order (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>