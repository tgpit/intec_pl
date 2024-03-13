<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arResult
 */

?>
<?php $vButtons = function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $arParentValues = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'DELAY' => $arItem['DELAY']['USE']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParentValues) { ?>
        <?php if (!empty($arItem['OFFERS']) && !$bOffer) return ?>
        <?= Html::beginTag('div', [
            'class' => 'widget-item-action-buttons',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?php if ($arParentValues['DELAY'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-delay',
                        'intec-cl-svg-path-stroke-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_DELAY_ADD_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.775 3C14.1525 3 15.75 5.235 15.75 7.32C15.75 11.5425 9.12 15 9 15C8.88 15 2.25 11.5425 2.25 7.32C2.25 5.235 3.8475 3 6.225 3C7.59 3 8.4825 3.6825 9 4.2825C9.5175 3.6825 10.41 3 11.775 3Z" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-delayed',
                        'intec-cl-svg-path-stroke',
                        'intec-cl-svg-path-fill'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_DELAY_ADDED_TITLE')
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.775 3C14.1525 3 15.75 5.235 15.75 7.32C15.75 11.5425 9.12 15 9 15C8.88 15 2.25 11.5425 2.25 7.32C2.25 5.235 3.8475 3 6.225 3C7.59 3 8.4825 3.6825 9 4.2825C9.5175 3.6825 10.41 3 11.775 3Z" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arResult['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-compare',
                        'intec-cl-svg-path-stroke-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COMPARE_ADD_TITLE')
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.75 9.75V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.75 6V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.25 2.25V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.25 6V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-compared',
                        'intec-cl-svg-path-stroke',
                        'intec-cl-svg-path-fill'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COMPARE_ADDED_TITLE')
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.75 9.75V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.75 6V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.25 2.25V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.25 6V15.75" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arItem['ORDER_FAST']['USE'] && $arItem['CAN_BUY']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-order-fast',
                        'intec-cl-svg-path-stroke-hover'
                    ],
                    'data' => [
                        'role' => 'orderFast'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ORDER_FAST_TITLE')
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.25 5.25H1.5" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 3H1.5" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.75 15H2.25" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.9566 3L12.2171 5.46525C12.1218 5.7825 11.8293 6 11.4986 6H9.16456C8.66206 6 8.30206 5.5155 8.44606 5.03475L9.05656 3" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 9.75H7.5975" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.0783 12H5.13729C3.93129 12 3.06654 10.8375 3.41304 9.6825L5.03304 4.2825C5.26179 3.52125 5.96229 3 6.75729 3H14.699C15.905 3 16.7698 4.1625 16.4233 5.3175L14.8033 10.7175C14.5745 11.4787 13.8733 12 13.0783 12Z" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-quick-view',
                        'intec-cl-svg-path-stroke-hover'
                    ],
                    'data' => [
                        'role' => 'quick.view'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUICK_VIEW_TITLE')
                ]) ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.33831 9.35025C2.22056 9.132 2.22056 8.86725 2.33831 8.649C3.75731 6.02475 6.37856 3.75 8.99981 3.75C11.6211 3.75 14.2423 6.02475 15.6613 8.64975C15.7791 8.868 15.7791 9.13275 15.6613 9.351C14.2423 11.9753 11.6211 14.25 8.99981 14.25C6.37856 14.25 3.75731 11.9753 2.33831 9.35025V9.35025Z" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.591 7.40901C11.4697 8.28769 11.4697 9.71231 10.591 10.591C9.71231 11.4697 8.28769 11.4697 7.40901 10.591C6.53033 9.71231 6.53033 8.28769 7.40901 7.40901C8.28769 6.53033 9.71231 6.53033 10.591 7.40901Z" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
            <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php $fRender($arItem) ?>
    <?php if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $fRender($arOffer, true)
    ?>
<?php } ?>