<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = app.getLibrary('$');
        var items = $('[data-role="item"]', data.nodes);

        app.api.basket.on('add', function (basket) {
            if (basket[data.nodes.data('trigger')] === true && basket.delay !== 'Y') {
                var actions = [];

                items.each(function () {
                    var item = $(this);
                    var action = {
                        'id': item.attr('data-basket-id'),
                        'price': item.attr('data-basket-price'),
                        'state': item.attr('data-basket-state'),
                        'checked': item.find('[data-role="item.input"]').prop('checked')
                    };

                    if (action.checked && action.state === 'none')
                        actions.push(app.api.basket.add({
                            'id': action.id,
                            'price': action.price
                        }));
                });

                if (actions.length > 0)
                    app.api.runActions(actions);
            }
        });
    }, {
        'name': '[Component] bitrix:catalog.section (products.additional.1) > Basket',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arResult['VISUAL']['RECALCULATION']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var checkbox = $('[data-role="item.input"]', data.nodes);
            var total = $('[data-role="products.additional.total"]', data.nodes);
            var priceFormat = function (value) {
                var base = <?= JavaScript::toObject($arResult['BASE']) ?>;
                var lite = <?= JavaScript::toObject($arResult['LITE']) ?>;
                var result = null;

                if (base) {
                    result = BX.Currency.currencyFormat(value, <?= JavaScript::toObject($arParams['CURRENCY_ID']) ?>, true);
                } else if (lite) {
                    var integer = _.toInteger(value);
                    var float = value - integer;
                    var format = {
                        'thousand': <?= JavaScript::toObject($arResult['CURRENCY']['THOUSAND']) ?>,
                        'decimal': <?= JavaScript::toObject($arResult['CURRENCY']['DECIMAL']) ?>,
                        'pattern': <?= JavaScript::toObject($arResult['CURRENCY']['PATTERN']) ?>
                    };

                    if (integer > 0) {
                        integer = _.replace(
                            new Intl.NumberFormat('ru-RU').format(integer),
                            String.fromCharCode(160),
                            format.thousand
                        );

                        result = integer;

                        if (float > 0) {
                            float = _.replace(
                                new Intl.NumberFormat('ru-RU', {
                                    'minimumFractionDigits': 2,
                                    'maximumFractionDigits': 2
                                }).format(float),
                                '0,',
                                ''
                            );

                            result = result + format.decimal + float;
                        }
                    }

                    if (!_.isNull(result))
                        result = _.replace(format.pattern, '#', result);
                }

                return result;
            };
            var update = function () {
                var result = 0;

                checkbox.each(function () {
                    var self = $(this);

                    if (self.prop('checked'))
                        result = result + _.toNumber(self.prop('value'));
                });

                if (result > 0) {
                    total.attr('data-active', true)
                        .find('[data-role="products.additional.total.value"]')
                        .html(priceFormat(result));
                } else {
                    total.attr('data-active', false)
                        .find('[data-role="products.additional.total.value"]')
                        .html(null);
                }
            };

            checkbox.on('change', update);

            update();
        }, {
            'name': '[Component] bitrix:catalog.section (products.additional.1) > Price',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>