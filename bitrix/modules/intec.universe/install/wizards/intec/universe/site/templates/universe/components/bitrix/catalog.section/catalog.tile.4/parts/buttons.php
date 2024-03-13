<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arResult
 */

?>
<?php $vButtons = function (&$arItem) use (&$arResult, &$arVisual, &$arSvg) { ?>
    <?php $arParent = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParent, &$arSvg) { ?>
        <?php if ($arItem['VISUAL']['OFFER'] && !$bOffer)
            return;
        ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-section-item-action-buttons',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?php if ($arItem['VISUAL']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'delay'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_DELAY_ADD_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'delayed'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_DELAY_ADDED_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ]
                ]) ?>
            <?php } ?>
            <?php if ($arItem['VISUAL']['COMPARE']['USE']) { ?>
                <?= Html::tag('div', $arSvg['COMPARE'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'compare'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_COMPARE_ADD_TITLE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParent['IBLOCK_ID']
                    ]
                ]) ?>
                <?= Html::tag('div', $arSvg['COMPARE'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'compared'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_COMPARE_ADDED_TITLE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParent['IBLOCK_ID']
                    ]
                ]) ?>
            <?php } ?>
            <?php if ($arItem['VISUAL']['ORDER_FAST']['USE']) { ?>
                <?= Html::tag('div', $arSvg['ORDER_FAST'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'order-fast'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_ORDER_FAST_TITLE'),
                    'data-role' => 'orderFast'
                ]) ?>
            <?php } ?>
            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                <?= Html::tag('div', $arSvg['QUICK_VIEW'], [
                    'class' => [
                        'catalog-section-item-action-button' => [
                            '',
                            'quick-view'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_QUICK_VIEW_TITLE'),
                    'data-role' => 'quick.view'
                ]) ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>