<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var string $sTemplateId
 * @var bool $bBase
 */

$hData = include(__DIR__.'/../handlers/data.php');
$hOrderFast = include(__DIR__.'/../handlers/order.fast.php');

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var app = this;
        var entity = <?= JavaScript::toObject($hData($arResult)) ?>;
        var nodes = {
            'update': null,
            'price': {
                'current': $('[data-role="product.price.current"]', data.nodes),
                'discount': $('[data-role="product.price.discount"]', data.nodes),
                'economy': $('[data-role="product.price.economy"]', data.nodes)
            },
            'counter': $('[data-role="product.counter"]', data.nodes),
            'quantity': {},
            'buy': $('[data-basket-action="add"]', data.nodes),
            'orderFast': $('[data-role="product.orderFast"]', data.nodes)
        };

        nodes.quantity = app.ui.createControl('numeric', {
            'node': nodes.counter,
            'bounds': {
                'minimum': entity.quantity.ratio,
                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
            },
            'step': entity.quantity.ratio
        });

        nodes.update = function () {
            var price = null;

            nodes.buy.data('basket-quantity', nodes.quantity.get());
            nodes.buy.attr('data-basket-quantity', nodes.quantity.get());

            _.each(entity.prices, function (object) {
                if (object.quantity.from === null || nodes.quantity.get() >= object.quantity.from)
                    price = object;
            });

            nodes.price.current.html(price.discount.display);

            if (price.discount.use) {
                nodes.price.discount.html(price.base.display);
                nodes.price.economy.html(price.discount.difference);
            }
        };

        nodes.update();

        (function () {
            var update = false;

            nodes.quantity.on('change', function () {
                if (!update) {
                    update = true;
                    nodes.update();
                    update = false;
                }
            });
        })();

        if (nodes.orderFast.length !== 0) {
            var orderFast = <?= JavaScript::toObject($hOrderFast($arParams, $arResult['ID'], $arResult['CATALOG_MEASURE_RATIO'])) ?>;

            nodes.orderFast.on('click', function () {
                orderFast.parameters.QUANTITY = nodes.quantity.get();

                app.api.components.show(orderFast);
            });
        }
    }, {
        'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if (count($arResult['GALLERY']['VALUES']) > 1) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var gallery = {
                'content': $('[data-role="product.gallery.content"]', data.nodes),
                'slider': null,
                'dots': null
            };

            if (gallery.content.length !== 0) {
                gallery.slider = $('[data-role="product.gallery.slider"]', gallery.content);

                if (gallery.slider.length !== 0) {
                    var sliderParameters = {
                        'items': 1,
                        'dots': false,
                        'dotsContainer': false,
                        'overlayNav': false
                    };

                    gallery.dots = $('[data-role="product.gallery.dots"]', gallery.content);

                    if (gallery.dots.length !== 0) {
                        gallery.dots.dot = null;

                        sliderParameters.dots = true;
                        sliderParameters.dotsContainer = gallery.dots;
                        sliderParameters.overlayNav = true;
                        sliderParameters.onInitialized = function () {
                            gallery.dots.addClass('intec-grid');
                            gallery.dots.dot = gallery.dots.find('[role="button"]');
                            gallery.dots.dot.addClass('intec-grid-item')
                                .filter('.active')
                                .addClass('intec-cl-background');
                        };
                        sliderParameters.onTranslate = function () {
                            gallery.dots.dot.removeClass('intec-cl-background')
                                .filter('.active')
                                .addClass('intec-cl-background');
                        }
                    }

                    gallery.slider.owlCarousel(sliderParameters);
                }
            }
        }, {
            'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default) gallery slider',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        })
    </script>
<?php } ?>
<?php if ($arVisual['GALLERY']['USE']) { ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var gallery = $('[data-gallery]', data.nodes);

            if (gallery.length !== 0) {
                gallery.lightGallery({
                    'selector': '[data-role="product.gallery.item"]'
                });
            }
        }, {
            'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default) gallery',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arResult['ACTIONS']['ACTION'] === 'order') {

    $hOrder = include(__DIR__.'/../handlers/order.php');

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var order = $('[data-role="product.order"]', data.nodes);

            if (order.length !== 0) {
                order.on('click', function () {
                    app.api.forms.show(<?= JavaScript::toObject($hOrder($arParams, $sTemplateId, $arResult['NAME'])) ?>);

                    if (window.yandex && window.yandex.metrika) {
                        window.yandex.metrika.reachGoal('forms.open');
                        window.yandex.metrika.reachGoal('forms.' + order.id +'.open');
                    }
                });
            }
        }, {
            'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default) order',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        })
    </script>
<?php } ?>
<?php if ($arVisual['QUICK_VIEW']['USE']) {

    $hQuickView = include(__DIR__.'/../handlers/quick.view.php');

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var quickView = $('[data-role="product.quickView"]', data.nodes);

            if (quickView.length !== 0) {
                quickView.on('click', function () {
                    app.api.components.show(<?= JavaScript::toObject($hQuickView($arParams)) ?>);
                });
            }
        }, {
            'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default) quickView',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>
<?php if ($arVisual['TIMER']['SHOW']) {

    $hTimer = include(__DIR__.'/../handlers/timer.php');

?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var timer = $('[data-role="product.timer"]', data.nodes);

            if (timer.length !== 0) {
                this.api.components
                    .get(<?= JavaScript::toObject($hTimer($arParams, $arResult)) ?>)
                    .then(function (content) {
                        timer.html(content);

                        var enable = $('[data-role="timer"]', timer).attr('data-status') === 'enable';

                        if (enable)
                            timer.css('display', '');
                        else
                            timer.remove();
                    });
            }
        }, {
            'name': '[Component] intec.universe:main.widget (product.1) > bitrix:catalog.element (.default) timer',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>