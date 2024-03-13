<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

?>
<script type="text/javascript">
    template.load(function () {
        var app = this;
        var _ = app.getLibrary('_');
        var $ = app.getLibrary('$');

        var data;
        var refresh;
        var update;

        data = {};
        data.basket = [];
        data.compare = [];

        refresh = function () {
            $('[data-basket-id]').attr('data-basket-state', 'none');
            $('[data-compare-id]').attr('data-compare-state', 'none');

            _.each(data.basket, function (item) {
                $('[data-basket-id=' + item.id + ']').attr('data-basket-state', item.delay ? 'delayed' : 'added');
            });

            _.each(data.compare, function (item) {
                $('[data-compare-id=' + item.id + ']').attr('data-compare-state', 'added');
            });
        };

        update = function () {
            $.ajax(<?= JavaScript::toObject($arResult['ACTION']) ?>, {
                'type': 'POST',
                'cache': false,
                'dataType': 'json',
                'data': <?= JavaScript::toObject($arParams) ?>,
                'success': function (response) {
                    data = response;
                    refresh();
                }
            });
        };

        $(document).on('click', '[data-basket-id][data-basket-action]', function () {
            var node = $(this);
            var id = node.data('basketId');
            var action = node.data('basketAction');
            var quantity = node.data('basketQuantity');
            var price = node.data('basketPrice');
            var data = node.data('basketData');

            if (id == null)
                return;

            if (action === 'add') {
               $(':not([data-basket-action="remove"]):not([data-basket-action="delay"])[data-basket-id=' + id + ']').attr('data-basket-state', 'processing');

                app.api.basket.add(_.merge({
                    'quantity': quantity,
                    'price': price
                }, data, {
                    'id': id
                })).run();
            } else if (action === 'remove') {
                $('[data-basket-id=' + id + '][data-basket-action="remove"],[data-basket-id=' + id + '][data-basket-action="delay"]').attr('data-basket-state', 'processing');

                app.api.basket.remove(_.merge({}, data, {
                    'id': id
                })).run();
            } else if (action === 'delay') {
                $('[data-basket-id=' + id + '][data-basket-action="remove"],[data-basket-id=' + id + '][data-basket-action="delay"]').attr('data-basket-state', 'processing');

                app.api.basket.add(_.merge({
                    'quantity': quantity,
                    'price': price
                }, data, {
                    'id': id,
                    'delay': 'Y'
                })).run();
            } else if (action === 'setQuantity') {
                $('[data-basket-id=' + id + ']').attr('data-basket-state', 'processing');

                app.api.basket.setQuantity(_.merge({
                    'quantity': quantity,
                    'price': price
                }, data, {
                    'id': id,
                    'delay': 'Y'
                })).run();
            }
        }).on('click', '[data-compare-id][data-compare-action]', function () {
            var node = $(this);
            var id = node.data('compareId');
            var action = node.data('compareAction');
            var code = node.data('compareCode');
            var iblock = node.data('compareIblock');
            var data = node.attr('compareData');

            if (id == null)
                return;

            if (action === 'add') {
                $('[data-compare-id=' + id + ']').attr('data-compare-state', 'processing');

                app.api.compare.add(_.merge({}, data, {
                    'id': id,
                    'code': code,
                    'iblock': iblock
                })).run();
            } else if (action === 'remove') {
                $('[data-compare-id=' + id + ']').attr('data-compare-state', 'processing');

                app.api.compare.remove(_.merge({}, data, {
                    'id': id,
                    'code': code,
                    'iblock': iblock
                })).run();
            }
        });

        app.api.basket.on('update', update);
        app.api.compare.on('update', update);

        update();
    }, {
        'name': '[Component] intec.universe:system (basket.manager)',
        'loader': {
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    });
</script>