<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

if (!$arVisual['SLIDER']['USE'])
    return;

$arData = [
    'columns' => $arVisual['COLUMNS'],
    'responsive' => [
        0 => ['items' => 1],
        651 => ['items' => 2]
    ],
    'slider' => [
        'use' => $arVisual['SLIDER']['USE'],
        'loop' => $arVisual['SLIDER']['LOOP'],
        'dots' => $arVisual['SLIDER']['DOTS'],
        'navigation' => $arVisual['SLIDER']['NAVIGATION'],
        'navText' => [
            FileHelper::getFileData(__DIR__ . '/../svg/navigation.left.svg'),
            FileHelper::getFileData(__DIR__ . '/../svg/navigation.right.svg')
        ],
        'navClass' => [
                'owl-prev intec-cl-background-hover intec-cl-border-hover',
                'owl-next intec-cl-background-hover intec-cl-border-hover'
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
    $arData['responsive'][951] = ['items' => 3];

if ($arVisual['COLUMNS'] >= 4)
    $arData['responsive'][1151] = ['items' => 4];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var sliders = $('[data-role="slider"]', data.nodes);
        var settings = <?= JavaScript::toObject($arData) ?>;

        if (settings.slider.use) {
            sliders.owlCarousel({
                'items': settings.columns,
                'autoplay': settings.slider.auto.use,
                'autoplaySpeed': settings.slider.auto.speed,
                'autoplayTimeout': settings.slider.auto.time,
                'autoplayHoverPause': settings.slider.auto.pause,
                'loop': settings.slider.loop,
                'nav': settings.slider.navigation,
                'navText': settings.slider.navText,
                'navClass': settings.slider.navClass,
                'dots': settings.slider.dots,
                'margin': 30,
                'responsive': settings.responsive
            });
        }
    }, {
        'name': '[Component] intec.universe:main.projects (template.1)',
        'nodes': <?= JavaScript::toObject('#' . $sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>