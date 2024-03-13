<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

$arSlider = [
    'items' => 1,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'margin' => 1,
    'stagePadding' => 1,
    'nav' => $arVisual['SLIDER']['NAV']['SHOW'],
    'navClass' => [
        Html::cssClassFromArray([
            'widget-navigation-left',
            'intec-cl-background-hover',
            'intec-cl-border-hover'
        ]),
        Html::cssClassFromArray([
            'widget-navigation-right',
            'intec-cl-background-hover',
            'intec-cl-border-hover'
        ])
    ],
    'navText' => [
        FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
        FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
    ],
    'dots' => false,
    'autoHeight' => false,
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

        slider.owlCarousel(<?= JavaScript::toObject($arSlider) ?>);
    }, {
        'name': '[Component] intec.universe:main.services (template.25) > slider',
        'nodes': <?= JavaScript::toObject('#' . $sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>