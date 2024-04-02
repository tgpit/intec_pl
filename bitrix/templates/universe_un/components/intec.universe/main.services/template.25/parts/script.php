<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSlider = [
    'items' => 1,
    'nav' => $arVisual['SLIDER']['NAV']['SHOW'],
    'navContainer' => '#' . $sTemplateId . ' [data-role="container.nav"]',
    'navClass' => [
        'nav-prev intec-cl-background-hover intec-cl-border-hover',
        'nav-next intec-cl-background-hover intec-cl-border-hover'
    ],
    'autoHeight' => false,
    'navText' => [
        '<span class="far fa-angle-left"></span>',
        '<span class="far fa-angle-right"></span>'
    ],
    'dots' => false,
    'margin' => 0,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE']
];

if ($arSlider['autoplay']) {
    $arSlider['autoplayTimeout'] = $arVisual['SLIDER']['AUTO']['TIME'];
    $arSlider['autoplayHoverPause'] = $arVisual['SLIDER']['AUTO']['HOVER'];
}

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="container"]', root);
        var sliderUse = <?= count($arResult['ITEMS']) > 1 ? 'true' : 'false' ?>;
        var itemPictures = $('[data-role="item.picture"]', root);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        var setItemPictureWidth = function () {
            var windowWidth = document.body.clientWidth;
            var containerWidth = $('[data-role="content"]', root)[0].clientWidth;

            itemPictures.each(function () {
                $(this)[0].style.width = Math.round(containerWidth + (windowWidth - containerWidth) / 2) + 'px';
            });
        };

        setItemPictureWidth();

        if (sliderUse) {
            slider.owlCarousel(settings);
        }

        $(window).on('resize', function () {
            setItemPictureWidth();
        });


    }, {
        'name': '[Component] intec.universe:main.services (template.25) > slider',
        'nodes': <?= JavaScript::toObject('#' . $sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>