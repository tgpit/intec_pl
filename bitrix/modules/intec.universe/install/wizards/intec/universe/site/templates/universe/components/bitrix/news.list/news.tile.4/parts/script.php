<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => $arVisual['COLUMNS'],
    'nav' => true,
    'navContainer' => '[data-role="navigation"]',
    'navClass' => [
        'navigation-left intec-cl-background-hover intec-cl-border-hover',
        'navigation-right intec-cl-background-hover intec-cl-border-hover'
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'dots' => false,
    'responsive' => [
        0 => ['items' => 1],
        501 => ['items' => 2],
        769 => ['items' => 3]
    ],
    'loop' => true
];

if ($arVisual['COLUMNS'] >= 4)
    $arSlider['responsive'][1025] = ['items' => $arVisual['COLUMNS']];

?>

<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        slider.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'navContainer': $(settings.navContainer, root),
            'navClass': settings.navClass,
            'navText': settings.navText,
            'dots': settings.dots,
            'responsive': settings.responsive,
            'loop': settings.loop
        });
    }, {
        'name': '[Component] bitrix:news.list (news.tile.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>