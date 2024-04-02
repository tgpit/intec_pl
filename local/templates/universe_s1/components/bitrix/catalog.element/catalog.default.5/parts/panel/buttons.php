<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vPanelButtons = function (&$arItem, $bOffer = false) use (&$arResult, $arSvg) { ?>
    <?php if (!empty($arItem['OFFERS'])) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-panel-button-action-block',
        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
    ]) ?>
        <?php if ($arResult['COMPARE']['USE'] && ($bOffer || empty($arItem['OFFERS']))) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-button-action',
                    'catalog-element-panel-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARE_TITLE'),
                'data' => [
                    'compare-id' => $arItem['ID'],
                    'compare-action' => 'add',
                    'compare-code' => $arResult['COMPARE']['CODE'],
                    'compare-state' => 'none',
                    'compare-iblock' => $arResult['IBLOCK_ID']
                ]
            ]) ?>
                <span class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['COMPARE'] ?>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-button-action',
                    'catalog-element-panel-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover',
                    'intec-cl-border',
                    'intec-cl-border-light-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARED_TITLE'),
                'data' => [
                    'compare-id' => $arItem['ID'],
                    'compare-action' => 'remove',
                    'compare-code' => $arResult['COMPARE']['CODE'],
                    'compare-state' => 'none',
                    'compare-iblock' => $arResult['IBLOCK_ID']
                ]
            ]) ?>
                <span class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['COMPARE'] ?>
                </span>
                <span class="intec-ui-part-effect intec-ui-part-effect-folding">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if ($arResult['DELAY']['USE'] && $arItem['CAN_BUY'] && ($bOffer || empty($arItem['OFFERS']))) { ?>
        <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-button-action',
                    'catalog-element-panel-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAY_TITLE'),
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-action' => 'delay',
                    'basket-state' => 'none',
                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                ]
            ]) ?>
                <span class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['DELAY'] ?>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-button-action',
                    'catalog-element-panel-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover',
                    'intec-cl-border',
                    'intec-cl-border-light-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAYED_TITLE'),
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-action' => 'remove',
                    'basket-state' => 'none'
                ]
            ]) ?>
                <span class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['DELAY'] ?>
                </span>
                <span class="intec-ui-part-effect intec-ui-part-effect-folding">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vPanelButtons($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelButtons($arOffer, true);

    unset($arOffer);
}

unset($vPanelButtons) ?>