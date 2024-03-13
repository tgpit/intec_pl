<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$arSlider = $arVisual['SLIDER'];
$arResponsive = [
    '0' => ['items' => 1]
];

if ($arVisual['WIDE']) {
    if ($arVisual['COLUMNS'] >= 2)
        $arResponsive['651'] = ['items' => 2];

    if ($arVisual['COLUMNS'] >= 3)
        $arResponsive['951'] = ['items' => 3];

    if ($arVisual['COLUMNS'] >= 4)
        $arResponsive['1151'] = ['items' => 4];
} else {
    if ($arVisual['COLUMNS'] >= 2)
        $arResponsive['751'] = ['items' => 2];

    if ($arVisual['COLUMNS'] >= 3)
        $arResponsive['1051'] = ['items' => 3];

    if ($arVisual['COLUMNS'] >= 4)
        $arResponsive['1201'] = ['items' => 4];
}

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var slider = $('[data-role="slider"]', root);

        <?php if ($arSlider['USE']) { ?>
        slider.owlCarousel(<?= JavaScript::toObject([
            'items' => $arVisual['COLUMNS'],
            'autoplay' => $arSlider['AUTO']['USE'],
            'autoplaySpeed' => $arSlider['AUTO']['SPEED'],
            'autoplayTimeout' => $arSlider['AUTO']['TIME'],
            'autoplayHoverPause' => $arSlider['AUTO']['PAUSE'],
            'loop' => $arSlider['LOOP'],
            'nav' => $arSlider['NAVIGATION'],
            'navText' => [
                FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
                FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
            ],
            'dots' => $arSlider['DOTS'],
            'responsive' => $arResponsive,
            'navContainerClass' => 'intec-ui intec-ui-control-navigation',
            'navClass' => [
                'intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover',
                'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'
            ],
            'dotsClass' => 'intec-ui intec-ui-control-dots',
            'dotClass' => 'intec-ui-part-dot'
        ]) ?>);
        <?php } ?>
    }, {
        'name': '[Component] bitrix:catalog.section (services.small.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php

unset($arResponsive);
unset($arSlider);