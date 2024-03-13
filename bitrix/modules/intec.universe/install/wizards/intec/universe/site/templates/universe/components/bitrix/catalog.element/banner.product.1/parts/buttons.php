<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

?>
<?php $vButtons = function (&$arItem) use (&$arResult, &$arParams, &$arVisual, &$arSvg) { ?>
    <div class="catalog-element-buttons">
        <div class="intec-grid intec-grid-wrap intec-grid-i-8 intec-grid-a-h-768-center">
            <div class="intec-grid-item-auto intec-grid-item-768-1 intec-grid intec-grid-a-h-center">
                <?= Html::tag('a', Loc::GetMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_PURCHASE_DETAIL'), [
                    'class' => [
                        'catalog-element-button-detail',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-2'
                        ],
                        'intec-grid' => [
                            'item-auto'
                        ]
                    ],
                    'href' => $arResult['DETAIL_PAGE_URL']
                ]) ?>
            </div>
            <?php if (empty($arResult['OFFERS'])) { ?>
                <div class="intec-grid-item-auto intec-grid intec-grid-nowrap intec-grid-i-8 intec-grid-a-h-center">
                    <?php if ($arItem['ACTION'] === 'order') { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('div', $arSvg['BASKET'], [
                                'class' => [
                                    'catalog-element-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'control-basket-button',
                                        'mod-round-2',
                                        'picture'
                                    ],
                                    'intec-cl' => [
                                        'border-hover',
                                        'background-hover'
                                    ]
                                ],
                                'data-role' => 'order'
                            ]) ?>
                        </div>
                    <?php } else if ($arItem['ACTION'] === 'buy' && $arItem['CAN_BUY']) { ?>
                        <div class="intec-grid-item-auto">
                            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]); ?>
                            <?= Html::tag('div', $arSvg['BASKET'], [
                                'class' => [
                                    'catalog-element-button',
                                    'catalog-element-button-add',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'control-basket-button',
                                        'mod-round-2',
                                        'picture'
                                    ],
                                    'intec-cl' => [
                                        'border-hover',
                                        'background-hover'
                                    ]
                                ],
                                'data' => [
                                    'basket-id' => $arItem['ID'],
                                    'basket-action' => 'add',
                                    'basket-state' => 'none',
                                    'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                                ]
                            ]) ?>
                            <?= Html::tag('a', $arSvg['BASKET'], [
                                'href' => $arItem['URL']['BASKET'],
                                'class' => [
                                    'catalog-element-button',
                                    'catalog-element-button-added',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'mod-round-2',
                                        'picture'
                                    ],
                                    'intec-cl' => [
                                        'background'
                                    ]
                                ],
                                'data' => [
                                    'basket-id' => $arItem['ID'],
                                    'basket-state' => 'none'
                                ]
                            ]) ?>
                        </div>
                        <?php if ($arItem['DELAY']['USE']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', $arSvg['DELAY'], [
                                    'class' => [
                                        'catalog-element-button',
                                        'catalog-element-button-delay',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'mod-round-2',
                                            'picture'
                                        ],
                                        'intec-cl' => [
                                            'border-hover',
                                            'background-hover'
                                        ]
                                    ],
                                    'data' => [
                                        'basket-id' => $arItem['ID'],
                                        'basket-action' => 'delay',
                                        'basket-state' => 'none',
                                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                                    ]
                                ]) ?>
                                <?= Html::tag('div', $arSvg['DELAY'], [
                                    'class' => [
                                        'catalog-element-button',
                                        'catalog-element-button-delayed',
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'mod-round-2',
                                            'scheme-current',
                                            'picture'
                                        ],
                                        'intec-cl' => [
                                            'background'
                                        ]
                                    ],
                                    'data' => [
                                        'basket-id' => $arItem['ID'],
                                        'basket-action' => 'remove',
                                        'basket-state' => 'none'
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($arItem['COMPARE']['USE']) { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('div', $arSvg['COMPARE'], [
                                'class' => [
                                    'catalog-element-button',
                                    'catalog-element-button-compare',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-round-2',
                                        'picture'
                                    ],
                                    'intec-cl' => [
                                        'border-hover',
                                        'background-hover'
                                    ]
                                ],
                                'data' => [
                                    'compare-id' => $arItem['ID'],
                                    'compare-action' => 'add',
                                    'compare-code' => $arItem['COMPARE']['CODE'],
                                    'compare-state' => 'none',
                                    'compare-iblock' => $arItem['IBLOCK_ID']
                                ]
                            ]) ?>
                            <?= Html::tag('div', $arSvg['COMPARE'], [
                                'class' => [
                                    'catalog-element-button',
                                    'catalog-element-button-compared',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-round-2',
                                        'scheme-current',
                                        'picture'
                                    ],
                                    'intec-cl' => [
                                        'background'
                                    ]
                                ],
                                'data' => [
                                    'compare-id' => $arItem['ID'],
                                    'compare-action' => 'remove',
                                    'compare-code' => $arItem['COMPARE']['CODE'],
                                    'compare-state' => 'none',
                                    'compare-iblock' => $arItem['IBLOCK_ID']
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php $vButtons($arResult);

unset($vButtons);