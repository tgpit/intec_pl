<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var string $sTemplateId
 * @var bool $bSliderUse
 */

$arSettings = [
    'attribute' => $arParams['~ATTRIBUTE'],
    'selector' => $arParams['~SELECTOR'],
    'slider' => [
        'use' => $bSliderUse,
        'items' => 1,
        'nav' => $arVisual['SLIDER']['NAV']['SHOW'],
        'navContainer' => '[data-role="container.nav"]',
        'navClass' => [
            'nav-prev widget-slider-nav-item widget-slider-nav-item-left intec-ui-picture intec-cl-background-hover',
            'nav-next widget-slider-nav-item widget-slider-nav-item-right intec-ui-picture intec-cl-background-hover'
        ],
        'navText' => [
            FileHelper::getFileData(__DIR__.'/../svg/navigation.arrow.left.svg'),
            FileHelper::getFileData(__DIR__.'/../svg/navigation.arrow.right.svg')
        ],
        'dots' => $arVisual['SLIDER']['DOTS']['SHOW'],
        'dotsContainer' => '[data-role="container.dots"]',
        'loop' => $arVisual['SLIDER']['LOOP'],
        'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
        'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
        'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER']
    ]
];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var slider = $('[data-role="slider-container"]', data.nodes);
        var settings = <?= JavaScript::toObject($arSettings) ?>;
        var schemeChange;

        schemeChange = function () {
            var content = $('[data-role="content"]', data.nodes);
            var item;
            var scheme;

            if (settings.slider.use) {
                item = $('.owl-item.active .widget-item', slider);

                if (settings.slider.dots && content.data('dots-view') !== 3) {
                    var dots = $(settings.slider.dotsContainer + ' button', data.nodes);

                    dots.removeClass('intec-cl-background intec-cl-border')
                        .filter('.active')
                        .addClass('intec-cl-background intec-cl-border');
                }
            } else {
                item = $('.widget-item', slider).eq(0);
            }

            scheme = item.data('item-scheme');
            content.attr('data-scheme', scheme);
        };

        if (settings.slider.use) {
            slider.owlCarousel({
                'items': settings.slider.items,
                'nav': settings.slider.nav,
                'navContainer': $(settings.slider.navContainer, data.nodes),
                'navClass': settings.slider.navClass,
                'navText': settings.slider.navText,
                'dots': settings.slider.dots,
                'dotsContainer': $(settings.slider.dotsContainer, data.nodes),
                'loop': settings.slider.loop,
                'autoplay': settings.slider.autoplay,
                'autoplayTimeout': settings.slider.autoplayTimeout,
                'autoplayHoverPause': settings.slider.autoplayHoverPause,
                'onTranslated': schemeChange
            });
        }

        <?php if ($arVisual['BANNER_PRODUCTS']['SHOW']) { ?>
            var contentWrapper = $('[data-role="content.wrapper"]', data.nodes);
            var product = $('[data-role="banner.products"]', data.nodes);
            var banner = $('[data-role="content"]', data.nodes);

            var resizeHandler = function () {
                var width;

                if (document.documentElement.offsetWidth > 768) {
                    if (product.width() > 0) {
                        width = (contentWrapper.width() - product.width() - 27) + 'px';
                    } else {
                        width = (contentWrapper.width() - product.width()) + 'px';
                        contentWrapper.css('margin-right', 0);
                    }
                } else {
                    width = product.width();
                }

                banner.css('width', width);

                setTimeout(function () {
                    if (document.documentElement.offsetWidth > 768) {
                        banner.height(product.height());
                    }
                }, 0);

                if (document.documentElement.offsetWidth > 768) {
                    banner.height($('[data-products-small-slider-use="false"]', data.nodes).height());
                } else {
                    banner.css('height', '')
                }
            };

            if (document.documentElement.offsetWidth > 768) {
                resizeHandler();
            }

            $(window).on('resize', function () {
                resizeHandler();
            });
        <?php } ?>

        schemeChange();
    }, {
        'name': '[Component] intec.universe:main.slider (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php if ($arResult['FORM']['SHOW']) {

    $arFormParameters = [
        'id' => $arResult['FORM']['ID'],
        'template' => $arResult['FORM']['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ASK',
            'CONSENT_URL' => $arResult['FORM']['CONSENT']
        ],
        'settings' => [
            'title' => $arResult['FORM']['NAME']
        ],
        'fields' => [
            $arResult['FORM']['FIELD'] => null
        ]
    ];

?>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = app.getLibrary('$');
            var form = {
                'nodes': $('[data-role="form"]', data.nodes),
                'parameters': <?= JavaScript::toObject($arFormParameters) ?>
            };

            form.nodes.each(function () {
                var self = $(this);

                self.on('click', function () {
                    form.parameters.fields[<?= JavaScript::toObject($arResult['FORM']['FIELD']) ?>] = self.attr('data-name');

                    app.api.forms.show(form.parameters);

                    form.parameters.fields[<?= JavaScript::toObject($arResult['FORM']['FIELD']) ?>] = null;

                    if (window.yandex && window.yandex.metrika) {
                        window.yandex.metrika.reachGoal('forms.open');
                        window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
                    }
                });
            });
        }, {
            'name': '[Component] intec.universe:main.slider (template.4) > form',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>