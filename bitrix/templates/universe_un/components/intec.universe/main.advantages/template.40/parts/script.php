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
        var slider = $('[data-role="container"]', data.nodes);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        slider.owlCarousel(settings);
    }, {
        'name': '[Component] intec.universe:main.advantages (template.40) > slider',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>