<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */


?>
<div class="catalog-element-sections" data-role="section">
    <?= Html::beginTag('ul', [
        'class' => [
            'catalog-element-tabs',
            'owl-carousel',
            'intec-ui' => [
                '',
                'control-tabs',
                'scheme-current',
                'view-1',
                'mod-block',
                'mod-position-left'
            ],
            'intec-ui-clearfix'
        ],
        'data' => [
            'ui-control' => 'tabs',
            'animation' => $arVisual['TABS']['ANIMATION'] ? 'true' : 'false',
            'print' => 'false',
            'role' => 'scroll'
        ]
    ]) ?>
        <?php foreach ($arResult['SECTIONS'] as $sCode => $arSection) { ?>

            <?php
            if ($sCode == "STORES" && !$arVisual['STORES']['SHOW']) {
                continue;
            }?>

            <?= Html::beginTag('li', [
                'class' => [
                    'catalog-element-tab',
                    'intec-ui-part-tab'
                ],
                'data' => [
                    'active' =>$arSection['ACTIVE'] ? 'true' : 'false',
                    'code' => $arSection['CODE'],
                ]
            ]) ?>
                <?= Html::tag('a', $arSection['NAME'], [
                    'href' => !empty($arSection['URL']) ? ($arSection['ACTIVE'] ? null : $arSection['URL']) : '#'.$sTemplateId.'-'.$arSection['CODE'],
                    'data-type' => 'tab'
                ]) ?>
            <?= Html::endTag('li') ?>
        <?php } ?>
    <?= Html::endTag('ul') ?>
</div>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-sections',
        'catalog-element-sections-tabs',
        'intec-ui' => [
            '',
            'control-tabs-content',
            'clearfix'
        ],
    ]
]) ?>
    <?php foreach ($arResult['SECTIONS'] as $sCode => $arSection) {

        if (!empty($arSection['URL']) && !$arSection['ACTIVE'])
            continue;

        if ($sCode == "STORES" && !$arVisual['STORES']['SHOW']) {
            continue;
        }

    ?>
        <?= Html::beginTag('div', [
            'id' => empty($arSection['URL']) ? $sTemplateId.'-'.$arSection['CODE'] : null,
            'class' => [
                'catalog-element-section',
                'intec-ui-part-tab'
            ],
            'data' => [
                'active' => $arSection['ACTIVE'] ? 'true' : 'false',
                'print' => !ArrayHelper::getValue($arSection, 'PRINT') ? 'false' : '',
                'code' => $arSection['CODE']
            ]
        ]) ?>
            <div class="catalog-element-section-content" data-role="section.content">
                <?php include(__DIR__.'/sections/'.$arSection['CODE'].'.php') ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?= Html::endTag('div') ?>
<?php unset($sCode, $arSection) ?>