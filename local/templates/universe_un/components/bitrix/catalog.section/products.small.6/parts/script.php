<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => $arVisual['COLUMNS'],
    'nav' => $arVisual['SLIDER']['NAV']['SHOW'],
    'loop' => $arVisual['SLIDER']['LOOP'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
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
    'margin' => 4,
    'responsive' => [
        0 => ['items' => 1],
        501 => ['items' => 2],
        769 => ['items' => 3]
    ]
];

if ($arVisual['COLUMNS'] >= 4)
    $arSlider['responsive'][1025] = ['items' => $arVisual['COLUMNS']];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);
        var navigation = $('[data-role="navigation"]', root);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        navigation.view = navigation.data('view');

        var adapt = function () {
            if (navigation.view === 'top') {
                if ($(document).width() < 769)
                    navigation.attr('data-view', 'default');
                else
                    navigation.attr('data-view', 'top');
            }
        };

        slider.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'loop': settings.loop,
            'autoplay': settings.autoplay,
            'autoplayTimeout': settings.autoplayTimeout,
            'autoplayHoverPause': settings.autoplayHoverPause,
            'navContainer': $(settings.navContainer, root),
            'navClass': settings.navClass,
            'navText': settings.navText,
            'dots': settings.dots,
            'margin': settings.margin,
            'responsive': settings.responsive,
            'onInitialized': adapt,
            'onResize': adapt
        });
    }, {
        'name': '[Component] bitrix:catalog.section (products.small.6)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arSlider) ?>