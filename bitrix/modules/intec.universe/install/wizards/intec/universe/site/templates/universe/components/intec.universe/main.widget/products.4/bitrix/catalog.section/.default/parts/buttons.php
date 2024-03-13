<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, &$arSvg) {

    $arParent = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID']
    ];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParent, &$arSvg) {

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && !$bOffer)
            return;

    ?>
        <?= Html::beginTag('div', [
            'class' => 'widget-item-action-buttons',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?php if ($arItem['VISUAL']['DELAY']['USE'] && $arItem['CAN_BUY']) {

                $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]);

            ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'widget-item-action-button' => [
                            '',
                            'delay'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_DELAY_ADD_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'widget-item-action-button' => [
                            '',
                            'delayed'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_DELAY_ADDED_TITLE'),
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
                        'widget-item-action-button' => [
                            '',
                            'compare'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_COMPARE_ADD_TITLE'),
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
                        'widget-item-action-button' => [
                            '',
                            'compared'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_COMPARE_ADDED_TITLE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParent['IBLOCK_ID']
                    ]
                ]) ?>
            <?php } ?>
            <?php if ($arItem['VISUAL']['ORDER_FAST']['USE'] && $arItem['CAN_BUY']) { ?>
                <?= Html::tag('div', $arSvg['ORDER_FAST'], [
                    'class' => [
                        'widget-item-action-button' => [
                            '',
                            'order-fast'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_ORDER_FAST_TITLE'),
                    'data-role' => 'orderFast'
                ]) ?>
            <?php } ?>
            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                <?= Html::tag('div', $arSvg['QUICK_VIEW'], [
                    'class' => [
                        'widget-item-action-button' => [
                            '',
                            'quick-view'
                        ],
                        'intec' => [
                            'ui-picture',
                            'cl-background-hover'
                        ]
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_QUICK_VIEW_TITLE'),
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