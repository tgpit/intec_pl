<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 * @var Closure $vItems
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$sections) use (&$arVisual, &$arSvg, &$sTemplateId, $vItems, &$APPLICATION, &$component) {

    $isTabFirst = true;

?>
    <?= Html::beginTag('ul', [
        'class' => [
            'intec-ui' => [
                '',
                'control-tabs',
                'view-1',
                'scheme-current',
                'mod-block',
                'mod-position-'.$arVisual['TABS']['POSITION'],

            ]
        ],
        'data-ui-control' => 'tabs'
    ]) ?>
        <?php foreach ($sections as $section) { ?>
            <?= Html::beginTag('li', [
                'class' => 'intec-ui-part-tab',
                'data-active' => $isTabFirst ? 'true' : 'false'
            ]) ?>
                <?= Html::tag('a', $section['NAME'], [
                    'href' => '#'.$sTemplateId.'-tab-'.$section['ID'],
                    'data-type' => 'tab'
                ]) ?>
            <?= Html::endTag('li') ?>
            <?php $isTabFirst = false ?>
        <?php } ?>
    <?= Html::endTag('ul') ?>
    <?= Html::beginTag('div', [
        'class' => [
            'intec-ui' => [
                '',
                'control-tabs-content'
            ]
        ]
    ]) ?>
        <?php $isTabFirst = true ?>
        <?php foreach ($sections as $section) { ?>
            <?= Html::beginTag('div', [
                'id' => $sTemplateId.'-tab-'.$section['ID'],
                'class' => 'intec-ui-part-tab',
                'data-active' => $isTabFirst ? 'true' : 'false'
            ]) ?>
                <?php $vItems($section['ITEMS']) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>