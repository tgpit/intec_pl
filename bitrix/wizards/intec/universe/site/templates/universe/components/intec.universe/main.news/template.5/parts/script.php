<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

$arSettings = [
    'items' => $arVisual['COLUMNS'],
    'nav' => $arVisual['SLIDER']['NAV'],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'loop' => $arVisual['SLIDER']['LOOP'],
    'center' => $arVisual['SLIDER']['CENTER'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'responsive' => [
        0 => ['items' => 1],
        601 => ['items' => 2]
    ]
];

if ($arVisual['COLUMNS'] >= 3)
    $arSettings['responsive'][1025] = ['items' => 3];

if ($arVisual['COLUMNS'] >= 4)
    $arSettings['responsive'][1201] = ['items' => 4];

if ($arVisual['COLUMNS'] >= 5)
    $arSettings['responsive'][1441] = ['items' => 5];

if ($arVisual['COLUMNS'] >= 6)
    $arSettings['responsive'][1601] = ['items' => 6];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var slider = $('[data-role="slider"]', data.nodes);
        var settings = <?= JavaScript::toObject($arSettings) ?>

        slider.owlCarousel({
            'items': settings.items,
            'dots': false,
            'nav': settings.nav,
            'navText': settings.navText,
            'navContainerClass': 'intec-ui intec-ui-control-navigation',
            'navClass': [
                'intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover intec-ui-picture',
                'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover intec-ui-picture'
            ],
            'loop': settings.loop,
            'center': settings.center,
            'autoplay': settings.autoplay,
            'autoplayTimeout': settings.autoplayTimeout,
            'autoplayHoverPause': settings.autoplayHoverPause,
            'responsive': settings.responsive
        });
    }, {
        'name': '[Component] intec.universe:main.news (template.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
