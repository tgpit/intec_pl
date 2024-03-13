<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();?>
<?php

use intec\core\helpers\JavaScript;

?>
<script>
    template.load(function (data) {

        var app = this;
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');

        var cityID = <?= (empty($arParams['CITY_ID']))? 'null' : "'".JavaScript::toObject($arParams['CITY_ID'])."'" ?>;
        var quantity = <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_VALUE']) ?>;
        var useBasket = <?=($arParams['USE_BASKET'] == 'Y')?'true':'false' ?>;

        locationUpdated = function(id) {
            var location = this.getNodeByLocationId(id);

            var cityName = location.DISPLAY;
            cityID = location.CODE;

            root.deliveries.update();
            root.cityBlock.currentName.html(cityName);
            root.cityBlock.attr('data-expanded', false);
        }

        //var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var root = data.nodes;

        root.cityBlock = $('[data-role="cityBlock"]', root);
        root.cityBlock.current = $('[data-role="currentCity"]', root);
        root.cityBlock.currentName = $('[data-role="currentCityName"]', root.cityBlock.current);
        root.cityBlock.current.on('click', function() {
            if (root.cityBlock.attr('data-expanded') == 'true') {
                root.cityBlock.attr('data-expanded', false);
            } else {
                root.cityBlock.attr('data-expanded', true);
            }
        });

        root.counter = $('[data-role="counter"]', root);
        root.quantity = app.ui.createControl('numeric', {
            'node': root.counter,
            'value': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_VALUE']) ?>,
            'bounds': {
                'minimum': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_MIN']) ?>,
                'maximum': <?=$arParams['PRODUCT_QUANTITY_MAX'] ?>
            },
            'step': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_STEP']) ?>
        });

        root.quantity.on('change', function () {
            quantity = root.quantity.get();
            root.deliveries.update();
        });

        root.useBasket = $('[data-role="useBasket"]', root);
        root.useBasket.on('change', function () {
            if ($(this).prop('checked')) {
                useBasket = true;
            } else
                useBasket = false;

            root.deliveries.update();
        });

        root.deliveries = $('[data-role="deliveries"]', root);
        root.deliveriesStub = $('[data-role="deliveries-stub"]', root);

        root.deliveries.update = function() {
            $.ajax({
                type: "POST",
                url: "<?= $component->getPath().'/ajax.php'?>",
                data: {
                    'delivery': {
                        'ajax': 'y'
                    },
                    'cityID': cityID,
                    'quantity': quantity,
                    'useBasket': useBasket ? 'y':'n',
                    'template': <?= JavaScript::toObject($templateName)?>,
                    'params': <?= JavaScript::toObject($arParams) ?>
                },
                beforeSend: function () {
                    console.log(4566);
                    root.deliveriesStub.show(0);
                },
                success: function (response) {
                    root.deliveries.html(response);
                    window.dispatchEvent(new Event('resize'));
                    root.deliveriesStub.hide(0);
                }
            });
        }

        $(root).on('click', '[data-role="buttonDetails"]', function(){
            var delivery = $(this).closest('[data-role="delivery"]');
            var blockDetails = delivery.find('[data-role="blockDetails"]');
            $(this).toggleClass('delivery-element-button-more-opened');

            if (blockDetails.attr('data-expanded') == 'true') {
                blockDetails.slideUp(400, function () {
                    window.dispatchEvent(new Event('resize'));
                });
                blockDetails.attr('data-expanded', false);
            } else {
                blockDetails.slideDown(400, function () {
                    window.dispatchEvent(new Event('resize'));
                });
                blockDetails.attr('data-expanded', true);
            }

        });
    }, {
        'name': '[Component] intec.universe:catalog.delivery (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>