<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 */

$arSlider = [
    'margin' => 32,
    'stageClass' => Html::cssClassFromArray([
        'owl-stage',
        'intec-grid',
        'intec-grid-a-v-stretch'
    ]),
    'itemClass' => Html::cssClassFromArray([
        'owl-item',
        'intec-grid-item-auto'
    ]),
    'nav' => true,
    'navClass' => [
        Html::cssClassFromArray([
            'widget-navigation-button',
            'widget-navigation-button-left',
            'intec-cl-border-hover',
            'intec-cl-background-hover',
            'intec-ui-picture'
        ]),
        Html::cssClassFromArray([
            'widget-navigation-button',
            'widget-navigation-button-right',
            'intec-cl-border-hover',
            'intec-cl-background-hover',
            'intec-ui-picture'
        ])
    ],
    'navText' => [
        $arSvg['NAVIGATION']['LEFT'],
        $arSvg['NAVIGATION']['RIGHT'],
    ],
    'dots' => false,
    'dotClass' => 'widget-dots-item',
    'rewind' => $arVisual['SLIDER']['LOOP'],
    'lazyLoad' => true,
    'responsive' => [
        '1025' => [
            'items' => $arVisual['COLUMNS']
        ],
        '769' => [
            'items' => $arVisual['COLUMNS'],
            'margin' => 24
        ],
        '501' => [
            'items' => 2,
            'nav' => false,
            'dots' => $arVisual['SLIDER']['DOTS'],
            'margin' => 16
        ],
        '0' => [
            'items' => 1,
            'nav' => false,
            'dots' => $arVisual['SLIDER']['DOTS'],
            'margin' => 0
        ]
    ]
];

if ($arVisual['COLUMNS'] > 3)
    $arSlider['responsive']['769']['items'] = 3;

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var _ = app.getLibrary('_');
        var $ = app.getLibrary('$');
        var container = $('[data-role="container"]', data.nodes);
        var handlers = {};

        handlers.expand = function (node, content, activator) {
            var height = 0;

            content.css('display', 'block');
            activator.css('pointer-events', 'none');

            height = content.outerHeight();

            content.css('height', 0).animate({'height': height}, 350, function () {
                node.attr('data-expanded', true);
                content.css({
                    'height': '',
                    'display': ''
                });
                activator.css('pointer-events', '');
            });
        };
        handlers.collapse = function (node, content, activator) {
            activator.css('pointer-events', 'none');
            content.animate({'height': 0}, 350, function () {
                node.attr('data-expanded', false);
                content.css('height', '');
                activator.css('pointer-events', '');
            });
        };
        handlers.toggleExpand = function (node, content, activator) {
            if (node.attr('data-expanded') === 'true')
                handlers.collapse(node, content, activator);
            else
                handlers.expand(node, content, activator);
        };
        handlers.dotsRefresh = function (items) {
            items.removeClass('intec-cl-background intec-cl-border')
                .filter('.active')
                .addClass('intec-cl-background intec-cl-border');
        };

        container.each(function () {
            var self = $(this);
            var slider = $('[data-role="slider"]', self);
            var items = $('[data-role="item"]', slider);
            var containers = {
                'nav': {
                    'container': $('[data-role="container.navigation"]', self)
                },
                'dots': {
                    'container': $('[data-role="container.dots"]', self),
                    'items': null
                }
            };

            slider.owlCarousel(
                _.merge({
                    'navContainer': containers.nav.container,
                    'dotsContainer': containers.dots.container,
                    'responsive': {
                        '501': {
                            'onInitialized': function () {
                                setTimeout(function () {
                                    if (containers.dots.items === null)
                                        containers.dots.items = $('[role="button"]', containers.dots.container);

                                    handlers.dotsRefresh(containers.dots.items);
                                }, 1);
                            },
                            'onTranslate': function () {
                                if (containers.dots.items === null)
                                    containers.dots.items = $('[role="button"]', containers.dots.container);

                                handlers.dotsRefresh(containers.dots.items);
                            },
                            'onResized': function () {
                                containers.dots.items = $('[role="button"]', containers.dots.container);

                                handlers.dotsRefresh(containers.dots.items);
                            }
                        },
                        '0': {
                            'onInitialized': function () {
                                setTimeout(function () {
                                    if (containers.dots.items === null)
                                        containers.dots.items = $('[role="button"]', containers.dots.container);

                                    handlers.dotsRefresh(containers.dots.items);
                                }, 1);
                            },
                            'onTranslate': function () {
                                if (containers.dots.items === null)
                                    containers.dots.items = $('[role="button"]', containers.dots.container);

                                handlers.dotsRefresh(containers.dots.items);
                            },
                            'onResized': function () {
                                containers.dots.items = $('[role="button"]', containers.dots.container);

                                handlers.dotsRefresh(containers.dots.items);
                            }
                        }
                    }
                }, <?= JavaScript::toObject($arSlider) ?>)
            );

            items.each(function () {
                var item = $(this);

                item.advantages = $('[data-role="item.advantages"]', item);
                item.advantages.content = $('[data-role="item.advantages.content"]', item.advantages);
                item.advantages.activator = $('[data-role="item.advantages.activator"]', item.advantages);

                if (item.advantages.activator.length > 0)
                    item.advantages.activator.on('click', function () {
                        handlers.toggleExpand(
                            item.advantages,
                            item.advantages.content,
                            item.advantages.activator
                        )
                    });
            });
        });
    }, {
        'name': '[Component] intec.universe:main.rates (template.7)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arSlider) ?>
<?php if ($arResult['FORM']['ORDER']['USE']) {

    $formOrder = [
        'id' => $arResult['FORM']['ORDER']['ID'],
        'template' => $arResult['FORM']['ORDER']['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
            'CONSENT_URL' => $arResult['CONSENT']['USE'] ? $arResult['CONSENT']['VALUE'] : null
        ],
        'settings' => [
            'title' => $arResult['FORM']['ORDER']['TITLE']
        ],
        'fields' => []
    ];

    if (empty($formOrder['settings']['title']))
        $formOrder['settings']['title'] = Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TEMPLATE_ITEM_ORDER_TEXT_DEFAULT');

    if (!empty($arResult['FORM']['ORDER']['FIELDS']['INSERT']))
        $formOrder['fields'][$arResult['FORM']['ORDER']['FIELDS']['INSERT']] = null;

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var parameters = <?= JavaScript::toObject($formOrder) ?>;
            var forms = $('[data-role="order"]', data.nodes);

            forms.on('click', function () {
                var self = $(this);

                if (parameters.fields.hasOwnProperty(<?= JavaScript::toObject($arResult['FORM']['ORDER']['FIELDS']['INSERT']) ?>))
                    parameters.fields[<?= JavaScript::toObject($arResult['FORM']['ORDER']['FIELDS']['INSERT']) ?>] = self.data('name');

                app.api.forms.show(parameters);

                app.metrika.reachGoal('forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$formOrder['id'].'.open') ?>);

                if (parameters.fields.hasOwnProperty(<?= JavaScript::toObject($arResult['FORM']['ORDER']['FIELDS']['INSERT']) ?>))
                    parameters.fields[<?= JavaScript::toObject($arResult['FORM']['ORDER']['FIELDS']['INSERT']) ?>] = null;
            });
        }, {
            'name': '[Component] intec.universe:main.rates (template.7) > Forms',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
    <?php unset($formOrder) ?>
<?php } ?>