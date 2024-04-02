<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <?php if ($arResult['ACTION'] === 'buy') { ?>
        <?php if (!$arItem['CAN_BUY']) { ?>
            <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                if (!empty($arItem['OFFERS']) && $bOffer == false)
                    return;

            ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-purchase-order-subscribe',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'size-3'
                                ]
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
            <?php } else {
                return;
            } ?>
        <?php } else { ?>
            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-element-purchase-order-buttons',
                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-ui',
                        'intec-ui-control-basket-button',
                        'catalog-element-purchase-order-button',
                        'catalog-element-purchase-order-button-add',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-block',
                        'intec-ui-size-3'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="button-icon glyph-icon-cart"></i>
                    </span>
                    <span class="intec-ui-part-content">
                            <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                    </span>
                    <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                        <span class="intec-ui-part-effect-wrapper">
                            <i></i><i></i><i></i>
                        </span>
                    </span>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('a', [
                    'href' => $arResult['URL']['BASKET'],
                    'class' => [
                        'catalog-element-purchase-order-button',
                        'catalog-element-purchase-order-button-added',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-block',
                        'intec-ui-size-3',
                        'intec-ui-state-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="button-icon glyph-icon-cart"></i>
                    </span>
                    <span class="intec-iu-part-content">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PURCHASE_ORDERED') ?>
                    </span>
                <?= Html::endTag('a') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?php } else if ($arResult['ACTION'] === 'order') { ?>
        <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
            <div class="catalog-element-purchase-order-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-purchase-order-button',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-block',
                        'intec-ui-size-3',
                    ],
                    'data-role' => 'order'
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="button-icon glyph-icon-cart"></i>
                    </span>
                    <span class="intec-ui-part-content">
                        <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        <?php } ?>
    <?php } else if ($arResult['ACTION'] === 'request') { ?>
        <?php if ($arResult['FORM']['REQUEST']['SHOW']) { ?>
            <div class="catalog-element-purchase-order-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-purchase-order-button',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-block',
                        'intec-ui-size-3',
                    ],
                    'data-role' => 'request'
                ]) ?>
                    <span class="intec-ui-part-content">
                        <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>


