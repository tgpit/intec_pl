<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 */

?>
<?php return function (&$arOffer) use (&$arResult, &$arSvg) { ?>
    <div class="catalog-element-offers-list-item-buttons" data-role="anchor" data-scroll-to="offers">
        <?php if ($arResult['COMPARE']['USE']) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-offers-list-item-button-action',
                    'catalog-element-offers-list-item-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARE_TITLE'),
                'data' => [
                    'compare-id' => $arOffer['ID'],
                    'compare-action' => 'add',
                    'compare-code' => $arResult['COMPARE']['CODE'],
                    'compare-state' => 'none',
                    'compare-iblock' => $arResult['IBLOCK_ID']
                ]
            ]) ?>
                <div class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['COMPARE'] ?>
                </div>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-offers-list-item-button-action',
                    'catalog-element-offers-list-item-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-border',
                    'intec-cl-background-light-hover',
                    'intec-cl-border-light-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_COMPARED_TITLE'),
                'data' => [
                    'compare-id' => $arOffer['ID'],
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
                        <i></i><i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php if ($arResult['DELAY']['USE'] && $arOffer['CAN_BUY']) { ?>
            <?php $arPrice = ArrayHelper::getValue($arOffer, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-offers-list-item-button-action',
                    'catalog-element-offers-list-item-button-action-add',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAY_TITLE'),
                'data' => [
                    'basket-id' => $arOffer['ID'],
                    'basket-action' => 'delay',
                    'basket-state' => 'none',
                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                ]
            ]) ?>
                <div class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['DELAY'] ?>
                </div>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-offers-list-item-button-action',
                    'catalog-element-offers-list-item-button-action-added',
                    'intec-ui',
                    'intec-ui-control-basket-button',
                    'intec-cl-background',
                    'intec-cl-border',
                    'intec-cl-background-light-hover',
                    'intec-cl-border-light-hover'
                ],
                'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELAYED_TITLE'),
                'data' => [
                    'basket-id' => $arOffer['ID'],
                    'basket-action' => 'remove',
                    'basket-state' => 'none'
                ]
            ]) ?>
                <div class="intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['DELAY'] ?>
                </div>
                <span class="intec-ui-part-effect intec-ui-part-effect-folding">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?php } ?>