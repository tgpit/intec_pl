<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$iPropertiesCounter = 0;

?>
<div class="widget-fields">
    <div class="widget-fields-wrapper intec-grid intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-8">
        <div class="widget-fields-name intec-grid-item">
            <div class="widget-fields-name-wrapper">
                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_ITEM_NAME') ?>
            </div>
            <?php if(!empty($arProperties) && $arVisual['PROPERTIES']['AMOUNT'] >= 1 && $arVisual['JOIN_FIRST_PROPERTY']) { ?>
            <?php
                $arProperty1 = reset($arProperties);
                $arProperty2 = next($arProperties);
            ?>
                <div class="widget-fields-name-properties intec-grid intec-grid-wrap intec-grid-i-h-7 intec-grid-a-v-center">
                    <div class="widget-fields-name-property intec-grid-item-auto">
                        <?= $arProperty1['NAME'] ?>
                    </div>
                    <?php if(!empty($arProperty2) && $arVisual['PROPERTIES']['AMOUNT'] >= 2) { ?>
                        <div class="widget-fields-name-property intec-grid-item-auto">
                            <?= $arProperty2['NAME'] ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if (!$arVisual['JOIN_FIRST_PROPERTY'] || (count($arProperties) > 2 && $arVisual['PROPERTIES']['AMOUNT'] > 2)) { ?>
            <div class="widget-fields-properties-wrap intec-grid-item">
                <div class="widget-fields-properties intec-grid intec-grid-1200-wrap intec-grid-a-v-center intec-grid-i-h-16 intec-grid-i-v-10">
                    <?php foreach($arProperties as $arProperty) { ?>
                    <?php
                        $iPropertiesCounter ++;

                        if ($arVisual['JOIN_FIRST_PROPERTY']) {
                            if ($iPropertiesCounter <= 2)
                                continue;
                        }

                        if ($iPropertiesCounter > $arVisual['PROPERTIES']['AMOUNT'])
                            break;
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-fields-property' => true,
                                'intec-grid-item' => [
                                    '4' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                    '3' => $arVisual['PROPERTIES']['COLUMNS'] == 3,
                                    '2' => $arVisual['PROPERTIES']['COLUMNS'] == 2,
                                    '1' => $arVisual['PROPERTIES']['COLUMNS'] == 1,
                                    '1200-3' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                    '1000-2' => $arVisual['PROPERTIES']['COLUMNS'] > 2,
                                ]
                            ], true)
                        ]) ?>
                        <div class="widget-fields-property-name">
                            <?= $arProperty['NAME'] ?>
                        </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
            <div class="widget-fields-quantity-wrap intec-grid-item">
                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_ITEM_QUANTITY') ?>
            </div>
        <?php } ?>
        <div class="widget-fields-price-wrap intec-grid-item">
            <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_ITEM_UNIT_PRICE') ?>
        </div>
        <?php if ($bActionButtonsShow || $arResult['ACTION'] !== 'none') { ?>
            <div class="widget-fields-buttons-wrap intec-grid-item"></div>
        <?php } ?>
    </div>
</div>
<?= Html::beginTag('div', [
    'class' => 'widget-items'
]) ?>
    <?php foreach($arItems as $arItem) {

        $sId = $sTemplateId.'_'.$arItem['ID'];
        $sAreaId = $this->GetEditAreaId($sId);
        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

        $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
        $sName = $arItem['NAME'];
        $sDescription = $arItem['PREVIEW_TEXT'];
        $sLink = $arItem['DETAIL_PAGE_URL'];
        $bOffers = $arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'];
        $iPropertiesCounter = 0;
        $bRecalculation = false;

        $arSkuProps = [];

        if (!empty($arResult['SKU_PROPS']))
            $arSkuProps = $arResult['SKU_PROPS'];
        else if (!empty($arItem['SKU_PROPS']))
            $arSkuProps = $arItem['SKU_PROPS'];

        if ($bBase && $arVisual['PRICE']['RECALCULATION']) {
            if ($arItem['VISUAL']['COUNTER']['SHOW'] && $arItem['VISUAL']['ACTION'] === 'buy')
                $bRecalculation = true;
        }

    ?>
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => 'widget-item',
            'data' => [
                'id' => $arItem['ID'],
                'role' => 'item',
                'data' => $sData,
                'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                'entity' => 'items-row'
            ]
        ]) ?>
            <div class="widget-item-background">
                <div class="widget-item-wrapper">
                    <div class="widget-item-content">
                        <div class="intec-grid intec-grid-900-wrap intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-8">
                            <div class="widget-item-name intec-grid-item">
                                <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $sName, [
                                    'class' => [
                                        'widget-item-name-wrapper',
                                        'intec-cl-text-hover'
                                    ],
                                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                                ]) ?>
                                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                    <div class="widget-item-vote">
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:iblock.vote',
                                            'template.1', [
                                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                'ELEMENT_ID' => $arItem['ID'],
                                                'ELEMENT_CODE' => $arItem['CODE'],
                                                'MAX_VOTE' => '5',
                                                'VOTE_NAMES' => [
                                                    0 => '1',
                                                    1 => '2',
                                                    2 => '3',
                                                    3 => '4',
                                                    4 => '5',
                                                ],
                                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                                'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                                'SHOW_RATING' => 'N'
                                            ],
                                            $component,
                                            ['HIDE_ICONS' => 'Y']
                                        ) ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($arItem['DISPLAY_PROPERTIES']) && $arVisual['PROPERTIES']['AMOUNT'] >= 1 && $arVisual['JOIN_FIRST_PROPERTY']) { ?>
                                    <div class="widget-item-name-properties intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2 intec-grid-a-v-center">
                                        <?php foreach($arProperties as $sKey => $arProperty) {

                                            if ($iPropertiesCounter >= 2 || $iPropertiesCounter >= $arVisual['PROPERTIES']['AMOUNT'])
                                                break;

                                            $iPropertiesCounter++;
                                            $arItemProperty = ArrayHelper::getValue($arItem['DISPLAY_PROPERTIES'], $sKey);

                                            if (
                                                empty($arItemProperty) ||
                                                empty($arItemProperty['DISPLAY_VALUE']) &&
                                                !Type::isNumeric($arItemProperty['DISPLAY_VALUE'])
                                            )
                                                continue;

                                        ?>
                                            <div class="widget-item-name-property intec-grid-item-1">
                                                <span class="widget-item-name-property-name">
                                                    <?= $arProperty['NAME'] ?>
                                                </span>
                                                <span class="widget-item-name-property-value">
                                                    <?= !Type::isArray($arItemProperty['DISPLAY_VALUE']) ?
                                                        $arItemProperty['DISPLAY_VALUE'] :
                                                        implode(', ', $arItemProperty['DISPLAY_VALUE'])
                                                    ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                        <?php $iPropertiesCounter = 0 ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (!$arVisual['JOIN_FIRST_PROPERTY'] || (count($arProperties) > 2 && $arVisual['PROPERTIES']['AMOUNT'] > 2)) { ?>
                                <div class="widget-item-properties-wrap intec-grid-item">
                                    <div class="widget-item-properties intec-grid intec-grid-1200-wrap intec-grid-a-v-center intec-grid-i-h-16 intec-grid-i-v-10">
                                        <?php foreach($arProperties as $sKey => $arProperty) {

                                            $iPropertiesCounter ++;

                                            if ($arVisual['JOIN_FIRST_PROPERTY']) {
                                                if ($iPropertiesCounter <= 2)
                                                    continue;
                                            }

                                            if ($iPropertiesCounter > $arVisual['PROPERTIES']['AMOUNT'])
                                                break;

                                            $arItemProperty = ArrayHelper::getValue($arItem['DISPLAY_PROPERTIES'], $sKey);

                                        ?>
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-property' => true,
                                                    'intec-grid-item' => [
                                                        '4' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                                        '3' => $arVisual['PROPERTIES']['COLUMNS'] == 3,
                                                        '2' => $arVisual['PROPERTIES']['COLUMNS'] == 2,
                                                        '1' => $arVisual['PROPERTIES']['COLUMNS'] == 1,
                                                        '1200-3' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                                        '1000-2' => $arVisual['PROPERTIES']['COLUMNS'] > 2,
                                                    ]
                                                ], true),
                                                'data-empty' => empty($arItemProperty) || empty($arItemProperty['DISPLAY_VALUE']) && !Type::isNumeric($arItemProperty['DISPLAY_VALUE']) ? 'true' : 'false'
                                            ]) ?>
                                            <div class="widget-item-property-name">
                                                <?= $arProperty['NAME'] ?>
                                            </div>
                                            <div class="widget-item-property-value">
                                                <?php if (!empty($arItemProperty)) { ?>
                                                    <?= !Type::isArray($arItemProperty['DISPLAY_VALUE']) ?
                                                        $arItemProperty['DISPLAY_VALUE'] :
                                                        implode(', ', $arItemProperty['DISPLAY_VALUE'])
                                                    ?>
                                                <?php } ?>
                                            </div>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                <div class="widget-item-quantity-wrap intec-grid-item">
                                    <?php if ($arItem['VISUAL']['QUANTITY']['SHOW'])
                                        $vQuantity($arItem);
                                    ?>
                                </div>
                            <?php } ?>
                            <div class="widget-item-price-wrap intec-grid-item">
                                <?php if ($arItem['VISUAL']['PRICE']['SHOW'])
                                    $vPrice($arItem);
                                ?>
                            </div>
                            <?php if ($bActionButtonsShow || $arResult['ACTION'] !== 'none') { ?>
                                <div class="widget-item-buttons-wrap intec-grid-item">
                                    <div class="widget-item-buttons intec-grid intec-grid-wrap intec-grid-a-h-end intec-grid-i-5">
                                        <?php if ($bActionButtonsShow) { ?>
                                            <div class="widget-item-action-buttons-wrap intec-grid-item-auto">
                                                <!--noindex-->
                                                <?php $vButtons($arItem) ?>
                                                <!--/noindex-->
                                            </div>
                                        <?php } ?>
                                        <?php if ($arResult['ACTION'] !== 'none') { ?>
                                            <div class="widget-item-order-buttons-wrap intec-grid-item-auto">
                                                <!--noindex-->
                                                <?php $vOrder($arItem) ?>
                                                <!--/noindex-->
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($bOffers) { ?>
                        <div class="widget-item-separator"></div>
                        <div class="widget-item-offers-wrap">
                            <!--noindex-->
                            <?php $vSku($arSkuProps) ?>
                            <!--/noindex-->
                        </div>
                    <?php } ?>
                </div>
                <?php if ($arItem['VISUAL']['ACTION'] === 'buy') { ?>
                    <div class="widget-item-additional-wrap" data-role="item.toggle">
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item-additional' => true,
                                'intec-grid' => [
                                    '' => true,
                                    'wrap' => true,
                                    'a-v-center' => true,
                                    'a-h-end' => true,
                                    'a-h-1000-between' => true,
                                    'i-5' => true
                                ]
                            ], true)
                        ]) ?>
                            <?php if ($bRecalculation)
                                $vPriceTotal($arItem);
                            ?>
                            <?php if ($arItem['VISUAL']['COUNTER']['SHOW']) { ?>
                                <!--noindex-->
                                <div class="widget-item-counter intec-grid-item-auto intec-grid-item-768-1 intec-grid intec-grid-a-v-center intec-grid-a-h-400-between">
                                    <?php if ($arItem['VISUAL']['MEASURE']['SHOW']) { ?>
                                        <div class="widget-item-ratio">
                                            <?php $vMeasure($arItem) ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-counter-wrapper">
                                        <div class="intec-ui intec-ui-control-numeric intec-ui-view-5 intec-ui-size-5 intec-ui-scheme-current" data-role="item.counter">
                                            <?= Html::tag('a', '-', [
                                                'class' => 'intec-ui-part-decrement',
                                                'href' => 'javascript:void(0)',
                                                'data-type' => 'button',
                                                'data-action' => 'decrement'
                                            ]) ?>
                                            <?= Html::input('text', null, 0, [
                                                'data-type' => 'input',
                                                'class' => 'intec-ui-part-input'
                                            ]) ?>
                                            <?= Html::tag('a', '+', [
                                                'class' => 'intec-ui-part-increment',
                                                'href' => 'javascript:void(0)',
                                                'data-type' => 'button',
                                                'data-action' => 'increment'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <!--/noindex-->
                            <?php } ?>
                            <div class="widget-item-purchase-wrap intec-grid-item-auto">
                                <!--noindex-->
                                <?php $vPurchase($arItem) ?>
                                <!--/noindex-->
                            </div>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?= Html::endTag('div') ?>