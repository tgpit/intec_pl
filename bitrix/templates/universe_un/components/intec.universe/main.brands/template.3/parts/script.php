<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

if (!$arVisual['SLIDER']['USE'])
    return;

$arData = [
    'columns' => $arVisual['COLUMNS'],
    'responsive' => [
        320 => [
            'items' => 1,
            'dotsEach' => 4
        ],
        375 => [
            'items' => 2,
            'dotsEach' => 4
        ],
    ],
    'slider' => [
        'use' => $arVisual['SLIDER']['USE'],
        'loop' => $arVisual['SLIDER']['LOOP'],
        'dots' => $arVisual['SLIDER']['DOTS'],
        'navigation' => $arVisual['SLIDER']['NAVIGATION'],
        'navText' => [
            FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
            FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
        ],
        'auto' => [
            'use' => $arVisual['SLIDER']['AUTO']['USE'],
            'pause' => $arVisual['SLIDER']['AUTO']['PAUSE'],
            'speed' => $arVisual['SLIDER']['AUTO']['SPEED'],
            'time' => $arVisual['SLIDER']['AUTO']['TIME']
        ]
    ]
];

if ($arVisual['COLUMNS'] >= 3)
    $arData['responsive'][721] = ['items' => 3];

if ($arVisual['COLUMNS'] >= 4)
    $arData['responsive'][951] = ['items' => 4];

if ($arVisual['COLUMNS'] >= 5)
    $arData['responsive'][1101] = ['items' => 5];

if ($arVisual['COLUMNS'] >= 6)
    $arData['responsive'][1201] = ['items' => 6];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var settings = <?= JavaScript::toObject($arData) ?>;

        if (settings.slider.use) {
            handler = function () {
                var items = root.find('.owl-stage:first');

                items.children('.owl-item').css('visibility', 'collapse');
                items.children('.owl-item.active').css('visibility', '');
            };

            slider.owlCarousel({
                'items': settings.columns,
                'autoplay': settings.slider.auto.use,
                'autoplaySpeed': settings.slider.auto.speed,
                'autoplayTimeout': settings.slider.auto.time,
                'autoplayHoverPause': settings.slider.auto.pause,
                'loop': settings.slider.loop,
                'nav': settings.slider.navigation,
                'navText': settings.slider.navText,
                'dots': settings.slider.dots,
                'margin': 0,
                'stagePadding': 1,
                'responsive': settings.responsive,
                'navClass': [
                    'owl-prev intec-cl-background-hover intec-cl-border-hover',
                    'owl-next intec-cl-background-hover intec-cl-border-hover'
                ],
                'dotsClass': 'intec-ui intec-ui-control-dots',
                'dotClass': 'intec-ui-part-dot',
                'onResized': handler,
                'onRefreshed': handler,
                'onInitialized': handler,
                'onTranslated': handler
            });
        }
    }, {
        'name': '[Component] intec.universe:main.brands (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>