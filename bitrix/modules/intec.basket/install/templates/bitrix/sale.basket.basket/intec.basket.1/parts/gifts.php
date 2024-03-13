<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

if (empty($arParams['GIFTS_BLOCK_TITLE']))
    $arParams['GIFTS_BLOCK_TITLE'] = Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_GIFTS_TITLE');

CBitrixComponent::includeComponentClass('bitrix:sale.products.gift.basket');

?>
<div class="intec-basket-gifts intec-basket-part" data-entity="parent-container" data-position="<?= strtolower($arParams['GIFTS_PLACE']) ?>">
    <?php if ($arParams['GIFTS_HIDE_BLOCK_TITLE'] !== 'Y') { ?>
        <div class="intec-basket-gifts-title" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
            <?= $arParams['GIFTS_BLOCK_TITLE'] ?>
        </div>
    <?php } ?>
    <div class="intec-basket-gifts-list">
        <?php $APPLICATION->IncludeComponent(
            'bitrix:sale.products.gift.basket',
            'bootstrap_v4', [
                'SHOW_PRICE_COUNT' => 1,
                'PRODUCT_SUBSCRIPTION' => 'N',
                'PRODUCT_ID_VARIABLE' => 'id',
                'USE_PRODUCT_QUANTITY' => 'N',
                'ACTION_VARIABLE' => 'actionGift',
                'ADD_PROPERTIES_TO_BASKET' => 'Y',
                'PARTIAL_PRODUCT_PROPERTIES' => 'Y',
                'BASKET_URL' => $APPLICATION->GetCurPage(),
                'APPLIED_DISCOUNT_LIST' => $arResult['APPLIED_DISCOUNT_LIST'],
                'FULL_DISCOUNT_LIST' => $arResult['FULL_DISCOUNT_LIST'],
                'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_SHOW_VALUE'],
                'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
                'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
                'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
                'DETAIL_URL' => isset($arParams['GIFTS_DETAIL_URL']) ? $arParams['GIFTS_DETAIL_URL'] : null,
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
                'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
                'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
                'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
                'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
                'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
                'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],
                'PRODUCT_ROW_VARIANTS' => '',
                'PAGE_ELEMENT_COUNT' => 0,
                'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
                    SaleProductsGiftBasketComponent::predictRowVariants(
                        $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
                        $arParams['GIFTS_PAGE_ELEMENT_COUNT']
                    )
                ),
                'DEFERRED_PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
                'ADD_TO_BASKET_ACTION' => 'BUY',
                'PRODUCT_DISPLAY_MODE' => 'Y',
                'PRODUCT_BLOCKS_ORDER' => isset($arParams['GIFTS_PRODUCT_BLOCKS_ORDER']) ? $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'] : '',
                'SHOW_SLIDER' => isset($arParams['GIFTS_SHOW_SLIDER']) ? $arParams['GIFTS_SHOW_SLIDER'] : '',
                'SLIDER_INTERVAL' => isset($arParams['GIFTS_SLIDER_INTERVAL']) ? $arParams['GIFTS_SLIDER_INTERVAL'] : '',
                'SLIDER_PROGRESS' => isset($arParams['GIFTS_SLIDER_PROGRESS']) ? $arParams['GIFTS_SLIDER_PROGRESS'] : '',
                'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
                'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY']
            ],
            $component
        ) ?>
    </div>
</div>