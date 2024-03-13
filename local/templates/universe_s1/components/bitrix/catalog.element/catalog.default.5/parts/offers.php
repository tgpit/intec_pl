<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

if (($bBase && empty($arResult['OFFERS_PROP'])) || empty($arResult['SKU_PROPS']))
    return;

?>
<div class="catalog-element-offers">
    <?php foreach ($arResult['SKU_PROPS'] as $arProperty) { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-offers-property',
            'data' => [
                'role' => 'property',
                'type' => $arProperty['type'],
                'property' => $arProperty['code']
            ]
        ]) ?>
            <div class="catalog-element-offers-property-name">
                <span class="catalog-element-offers-property-name-value">
                    <?= $arProperty['name'] ?>
                </span>
                <span class="catalog-element-offers-property-name-selected" data-role="property.selected"></span>
            </div>
            <div class="catalog-element-offers-property-value-container">
                <?php foreach ($arProperty['values'] as $arValue) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'catalog-element-offers-property-value',
                        'data' => [
                            'role' => 'property.value',
                            'value' => $arValue['id'],
                            'state' => 'hidden'
                        ]
                    ]) ?>
                        <?php if ($arProperty['type'] === 'picture' && !empty($arValue['picture'])) { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-element-offers-property-value-picture',
                                    'intec-cl-border-hover'
                                ],
                                'title' => $arValue['name'],
                                'data' => [
                                    'role' => 'property.value.content',
                                    'content-type' => 'picture'
                                ]
                            ]) ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'catalog-element-offers-property-value-picture-content',
                                    'data' => [
                                        'role' => 'item.property.value.image',
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arValue['picture'] : null
                                    ],
                                    'style' => [
                                        'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$arValue['picture'].'\')'
                                    ]
                                ]) ?>
                            <?= Html::endTag('div') ?>
                        <?php } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-element-offers-property-value-text',
                                    'intec-cl-border-hover',
                                    'intec-cl-background-hover'
                                ],
                                'title' => $arValue['name'],
                                'data' => [
                                    'role' => 'property.value.content',
                                    'content-type' => 'text'
                                ]
                            ]) ?>
                                <div class="catalog-element-offers-property-value-text-content">
                                    <?= $arValue['name'] ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php unset($arValue) ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php unset($arProperty) ?>
</div>
