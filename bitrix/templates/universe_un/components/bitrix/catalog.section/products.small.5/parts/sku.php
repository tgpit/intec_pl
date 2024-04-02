<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$arProperties) use (&$arVisual) { ?>
    <div class="catalog-section-item-offers intec-grid intec-grid-wrap intec-grid-i-h-16 intec-grid-i-v-5">
        <?php foreach ($arProperties as &$arProperty) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-item-offers-property',
                    'intec-grid-item-auto',
                    'intec-grid-item-shrink-1'
                ],
                'data' => [
                    'property' => $arProperty['code'],
                    'type' => $arProperty['type'],
                    'role' => 'item.property'
                ]
            ]) ?>
                <div class="catalog-section-item-offers-property-wrapper intec-grid">
                    <div class="catalog-section-item-offers-property-name intec-grid-item-auto">
                        <?= $arProperty['name'] ?>
                    </div>
                    <div class="catalog-section-item-offers-property-values intec-grid-item">
                        <?php foreach ($arProperty['values'] as &$arValue) { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-section-item-offers-property-value',
                                    'intec-cl-border-hover'
                                ],
                                'data' => [
                                    'state' => 'hidden',
                                    'value' => $arValue['id'],
                                    'role' => 'item.property.value'
                                ]
                            ]) ?>
                                <?php if ($arProperty['type'] === 'picture' && !empty($arValue['picture'])) { ?>
                                    <?= Html::tag('div', $arValue['name'], [
                                        'title' => $arValue['name'],
                                        'data' => [
                                            'type' => 'picture',
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arValue['picture'] : null
                                        ],
                                        'style' => [
                                            'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$arValue['picture'].'\')'
                                        ]
                                    ]) ?>
                                <?php } else { ?>
                                    <div data-type="text">
                                        <?= $arValue['name'] ?>
                                    </div>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?php } ?>