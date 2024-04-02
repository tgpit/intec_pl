<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => 3,
    'nav' => $arVisual['SLIDER']['NAV']['SHOW'],
    'navContainer' => '[data-role="container.nav"]',
    'navClass' => [
        'nav-prev',
        'nav-next'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'dots' => $arVisual['SLIDER']['DOTS']['SHOW'],
    'dotsContainer' => '[data-role="container.dots"]',
    'margin' => 24,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'responsive' => [
        '0' => ['items' => 1],
        '500' => ['items' => 2],
        '1025' => ['items' => 3]
    ]
];

if ($arVisual['SLIDER']['NAV']['VIEW'] == 1) {
    $arSlider['navClass'] = [
        'nav-prev intec-cl-background-hover intec-cl-border-hover',
        'nav-next intec-cl-background-hover intec-cl-border-hover'
    ];
} else {
    $arSlider['navClass'] = [
        'nav-prev',
        'nav-next'
    ];
}

if ($arSlider['autoplay']) {
    $arSlider['autoplayTimeout'] = $arVisual['SLIDER']['AUTO']['TIME'];
    $arSlider['autoplayHoverPause'] = $arVisual['SLIDER']['AUTO']['HOVER'];
}

?>

<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var slider = $('[data-role="container"]', data.nodes);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        slider.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'navContainer': $(settings.navContainer, data.nodes),
            'navClass': settings.navClass,
            'navText': settings.navText,
            'dots': settings.dots,
            'dotsContainer': $(settings.dotsContainer, data.nodes),
            'margin': settings.margin,
            'loop': settings.loop,
            'autoplay': settings.autoplay,
            'responsive': settings.responsive
        });
    }, {
        'name': '[Component] intec.universe:main.services (template.17)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>