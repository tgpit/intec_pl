<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 */

$arSlider = [
    'use' => $arVisual['SLIDER']['USE'],
    'items' => $arVisual['COLUMNS'],
    'nav' => $arVisual['SLIDER']['NAV'],
    'navContainer' => '[data-role="slider.navigation"]',
    'navClass' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'responsive' => [
        '0' => ['items' => 1]
    ]
];

if ($arSlider['items'] >= 3)
    $arSlider['responsive']['501'] = ['items' => 2];

if ($arSlider['items'] >= 4 || $arSlider['items'] == 3)
    $arSlider['responsive']['768'] = ['items' => 3];

if ($arSlider['items'] > 4)
    $arSlider['responsive']['1000'] = ['items' => 4];

$arSlider['responsive']['1201'] = ['items' => $arSlider['items']];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var items = $('[data-role="items"]', data.nodes);
        var sliderSettings = <?= JavaScript::toObject($arSlider) ?>;
        var navContainer = $(sliderSettings.navContainer, data.nodes);

        if (sliderSettings.use) {
            items.owlCarousel({
                'margin': 10,
                'nav': sliderSettings.nav,
                'navContainer': navContainer,
                'navClass': sliderSettings.navClass,
                'navText': sliderSettings.navText,
                'items': sliderSettings.items,
                'responsive': sliderSettings.responsive
            });
        }

        items.lightGallery({
            'selector': '[data-role="item"]'
        });
    }, {
        'name': '[Component] intec.universe:main.videos (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>