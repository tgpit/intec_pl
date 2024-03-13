<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 * @var array $arBlocks
 * @var array $arVisual
 */

if (empty($arResult['SECTIONS']))
    return;

?>
<?= Html::beginTag('ul', [
    'class' => [
        'widget-tabs',
        'intec-ui' => [
            '',
            'control-tabs',
            'mod-block',
            'mod-position-'.$arVisual['TABS']['POSITION'],
            'scheme-current',
            'view-1'
        ]
    ],
    'data' => [
        'ui-control' => 'tabs'
    ]
]) ?>
    <?php $iCounter = 0 ?>
    <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
        <?= Html::beginTag('li', [
            'class' => 'intec-ui-part-tab',
            'data' => [
                'active' => $iCounter === 0 ? 'true' : 'false'
            ]
        ]) ?>
            <a href="<?= '#'.$sTemplateId.'-tab-'.$iCounter ?>" data-type="tab">
                <?= $arSection['NAME'] ?>
            </a>
        <?= Html::endTag('li') ?>
        <?php $iCounter++ ?>
    <?php } ?>
<?= Html::endTag('ul') ?>
<div class="widget-tabs-content intec-ui intec-ui-control-tabs-content">
    <?php $iCounter = 0 ?>
    <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
        <?= Html::beginTag('div', [
            'id' => $sTemplateId.'-tab-'.$iCounter,
            'class' => 'intec-ui-part-tab',
            'data' => [
                'active' => $iCounter === 0 ? 'true' : 'false'
            ]
        ]) ?>
            <?php $arItems = &$arSection['ITEMS'] ?>
            <?php include(__DIR__.'/items.php') ?>
        <?= Html::endTag('div') ?>
        <?php $iCounter++ ?>
    <?php } ?>
</div>