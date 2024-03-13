<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 */

$arSlider = [
    'nav' => $arVisual['SLIDER']['USE'],
    'loop' => $arVisual['SLIDER']['LOOP'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'navContainer' => '#'.$sTemplateId.' [data-role="slider.navigation"]',
    'navClass' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'responsive' => [
        '0' => [
            'items' => 1
        ],
        '400' => [
            'items' => 2
        ],
    ]
];

if ($arVisual['COLUMNS'] >= 3)
    $arSlider['responsive'][600] = ['items' => 3];

if ($arVisual['COLUMNS'] >= 4)
    $arSlider['responsive'][800] = ['items' => 4];

if ($arVisual['COLUMNS'] >= 5)
    $arSlider['responsive'][1000] = ['items' => 5];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var sliderSettings = <?= JavaScript::toObject($arSlider) ?>;
        var sliderNavigation = $('[data-role="slider.navigation"]', root);
        var firstImage = $('[data-role="image"]', root)[0];

        slider.owlCarousel({
            'nav': sliderSettings.nav,
            'dots': false,
            'loop': sliderSettings.loop,
            'autoplay': sliderSettings.autoplay,
            'autoplayTimeout': sliderSettings.autoplayTimeout,
            'autoplayHoverPause': sliderSettings.autoplayHoverPause,
            'navContainer': sliderSettings.navContainer,
            'navClass': sliderSettings.navClass,
            'navText': sliderSettings.navText,
            'responsive': sliderSettings.responsive
        });

        updateNavigationPosition();

        $(window).on('resize', function () {
            setTimeout (function () {
                updateNavigationPosition();
            }, 400);
        });

        function updateNavigationPosition () {
            if (_.isNil(sliderNavigation) || _.isNil(firstImage))
                return false;

            var navigationPosition = $(firstImage).outerHeight() / 2;
            sliderNavigation.css('top', navigationPosition + 'px')
        }
    }, {
        'name': '[Component] intec.universe:main.staff (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>