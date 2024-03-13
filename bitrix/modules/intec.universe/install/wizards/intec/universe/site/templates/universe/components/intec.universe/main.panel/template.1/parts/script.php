<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var _ = this.getLibrary('_');
        var $ = this.getLibrary('$');
        var app = this;
        var panel = {
            'compare': {
                'use': <?= JavaScript::toObject($arVisual['COMPARE']['USE']) ?>,
                'code': <?= JavaScript::toObject($arVisual['COMPARE']['NAME']) ?>,
                'key': <?= JavaScript::toObject($arVisual['COMPARE']['IBLOCK_ID']) ?>,
                'element': $('[data-role="panel.compare"]', data.nodes)
            },
            'basket': {
                'use': <?= JavaScript::toObject($arVisual['BASKET']['USE']) ?>,
                'element': $('[data-role="panel.basket"]', data.nodes)
            },
            'delay': {
                'use': <?= JavaScript::toObject($arVisual['DELAY']['USE']) ?>,
                'element': $('[data-role="panel.delayed"]', data.nodes)
            },
            'scroll': {
                'area': $(window),
                'content': $('[data-role="scrollbar"]', data.nodes),
                'value': 0
            },
            'getBasketCount': function (data) {
                var result = {
                    'basket': 0,
                    'delay': 0
                };

                _.each(data.items, function (item) {
                    if (item.isDelay)
                        ++result.delay;
                    else
                        ++result.basket;
                });

                return result;
            },
            'setCounter': function (element, count) {
                element.attr('data-state', count > 0 ? 'active' : 'disabled').html(count);
            }
        };

        var panelFixed = <?= JavaScript::toObject($arVisual['PANEL']['FIXED']) ?>;

        panel.scroll.content.scrollbar();

        panel.setCompareCount = function () {
            app.api.compare.getItems({
                'code': panel.compare.code
            }).run().then(function (response) {
                if (_.has(response, panel.compare.key))
                    panel.setCounter(panel.compare.element, response[panel.compare.key].length);
            });
        };

        panel.updateScroll = function () {
            if (panel.scroll.area.width() > 768) {
                data.nodes.attr('data-in', false);

                return;
            }

            var scrollCurrent = panel.scroll.area.scrollTop();

            if (scrollCurrent > 100 && scrollCurrent > panel.scroll.value) {
                if (!panelFixed)
                    data.nodes.attr('data-in', false);
            } else {
                data.nodes.attr('data-in', true);
            }

            panel.scroll.value = scrollCurrent;
        };

        panel.scroll.area.on('scroll', _.throttle(panel.updateScroll, 150, {
            'trailing': false,
            'leading': true
        }));

        panel.updateScroll();

        if (panel.compare.use) {
            app.api.compare.on('update', panel.setCompareCount);

            panel.setCompareCount();
        }

        if (panel.basket.use || panel.delay.use) {
            app.basket.on('update', function (data) {
                var count = panel.getBasketCount(data);

                if (panel.basket.use)
                    panel.setCounter(panel.basket.element, count.basket);

                if (panel.delay.use)
                    panel.setCounter(panel.delay.element, count.delay);
            });
        }
    }, {
        'name': '[Component] intec.universe:main.panel (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'options': {
                'await': [
                    'composite'
                ]
            }
        }
    });
</script>