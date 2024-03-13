<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<div class="catalog-element-sections" data-role="section" data-print="false">
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-element-tabs',
            'owl-carousel'
        ],
        'data' => [
            'role' => 'scroll',
            'ui-control' => 'tabs',
            'navigation' => 'false'
        ]
    ]) ?>
        <?php foreach ($arResult['SECTIONS'] as $sCode => $arSection) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-tab'
                ],
                'data-active' => $arSection['ACTIVE'] ? 'true' : 'false'
            ]) ?>
                <?= Html::tag('a', $arSection['NAME'], [
                    'href' => !empty($arSection['URL']) ? ($arSection['ACTIVE'] ? null : $arSection['URL']) : '#'.$sTemplateId.'-'.$arSection['CODE'],
                    'data-type' => 'tab'
                ]) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
</div>
<div class="catalog-element-sections-tabs intec-ui intec-ui-control-tabs-content">
    <?php foreach ($arResult['SECTIONS'] as $sCode => $arSection) {

        if (!empty($arSection['URL']) && !$arSection['ACTIVE'])
            continue;

    ?>
        <?= Html::beginTag('div', [
            'id' => empty($arSection['URL']) ? $sTemplateId.'-'.$arSection['CODE'] : null,
            'class' => [
                'catalog-element-section',
                'intec-ui-part-tab'
            ],
            'data' => [
                'active' => $arSection['ACTIVE'] ? 'true' : 'false',
                'code' => $arSection['CODE']
            ]
        ]) ?>
            <div class="catalog-element-section-content" data-role="section.content">
                <?php include(__DIR__.'/sections/'.$arSection['CODE'].'.php') ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
<?php unset($sCode, $arSection) ?>