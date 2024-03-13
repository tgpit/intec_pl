<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
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
            'widget-slider-nav-item widget-slider-nav-item-left intec-ui-picture intec-cl-background-hover',
            'widget-slider-nav-item widget-slider-nav-item-right intec-ui-picture intec-cl-background-hover'
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

                if (settings.slider.dots) {
                    var dots = $(settings.slider.dotsContainer + ' button', data.nodes);

                    dots.removeClass('intec-cl-background intec-cl-border')
                        .filter('.active')
                        .addClass('intec-cl-background intec-cl-border');
                }
            } else {
                item = $('.widget-item', slider).eq(0);
            }

            scheme = item.attr('data-item-scheme');
            content.attr('data-scheme', scheme);

            if (settings.selector) {
                content = data.nodes.closest(settings.selector);

                if (settings.attribute)
                    content.attr(settings.attribute, scheme);
            }
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

        schemeChange();
    }, {
        'name': '[Component] intec.universe:main.slider (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>