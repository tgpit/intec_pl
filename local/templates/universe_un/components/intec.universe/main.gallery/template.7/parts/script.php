<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

$arSlider = [
    'items' => $arVisual['COLUMNS'],
    'nav' => $arVisual['SLIDER']['NAV'],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'navClass' => [
        'owl-prev intec-cl-background-hover intec-cl-border-hover',
        'owl-next intec-cl-background-hover intec-cl-border-hover'
    ],
    'dots' => $arVisual['SLIDER']['DOTS'],
    'loop' => $arVisual['SLIDER']['LOOP'],
    'center' => $arVisual['SLIDER']['CENTER'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'responsive' => [
        0 => ['items' => 1],
        451 => ['items' => 2],
        601 => ['item' => 3]
    ]
];

if ($arVisual['COLUMNS'] >= 4)
    $arSlider['responsive'][768] = ['items' => 4];

if ($arVisual['COLUMNS'] >= 5)
    $arSlider['responsive'][1200] = ['items' => $arVisual['COLUMNS']]

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var gallery = $('[data-role="items"]', data.nodes);

        gallery.lightGallery({
            'selector': '[data-role="item"]',
            'exThumbImage': 'data-preview-src'
        });

        <?php if ($arVisual['SLIDER']['USE']) { ?>
            var settings = <?= JavaScript::toObject($arSlider) ?>;

            gallery.owlCarousel({
                'items': settings.items,
                'margin': 10,
                'nav': settings.nav,
                'navText': settings.navText,
                'navClass': settings.navClass,
                'dots': settings.dots,
                'loop': settings.loop,
                'center': settings.center,
                'autoplay': settings.autoplay,
                'autoplayTimeout': settings.autoplayTimeout,
                'autoplayHoverPause': settings.autoplayHoverPause,
                'responsive': settings.responsive
            });
        <?php } ?>
    }, {
        'name': '[Component] intec.universe:main.gallery (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
