<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arVisual
 * @var boolean $bAdditionalColumn
 */

$iColumns = 1;

if ($arVisual['GIFTS']['VIEW'] === '1') {
    $iColumns = 1;
} elseif ($arVisual['GIFTS']['VIEW'] === '2') {
    $iColumns = 3;

    if (!$bAdditionalColumn)
        $iColumns = 4;

} elseif ($arVisual['GIFTS']['VIEW'] === '3') {
    $iColumns = 2;

    if (!$bAdditionalColumn)
        $iColumns = 3;
}

?>
<div class="catalog-element-additional-container">
<?php CBitrixComponent::includeComponentClass('bitrix:sale.products.gift');

$APPLICATION->IncludeComponent(
    'bitrix:sale.products.gift',
    'template.1', [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'VOTE_PREFIX_ID' => '-gift',
        'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],

        'PRODUCT_ROW_VARIANTS' => '',
        'PAGE_ELEMENT_COUNT' => 0,
        'DEFERRED_PRODUCT_ROW_VARIANTS' => \Bitrix\Main\Web\Json::encode(
            SaleProductsGiftComponent::predictRowVariants(
                (int)$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                (int)$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT']
            )
        ),

        'DEFERRED_PRODUCT_ROW_VARIANTS' => (int)$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        'DEFERRED_PAGE_ELEMENT_COUNT' => (int)$arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],

        'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
        'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
        'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
        'PRODUCT_DISPLAY_MODE' => 'Y',
        'PRODUCT_BLOCKS_ORDER' => $arParams['GIFTS_PRODUCT_BLOCKS_ORDER'],
        'SHOW_SLIDER' => $arParams['GIFTS_SHOW_SLIDER'],
        'SLIDER_INTERVAL' => isset($arParams['GIFTS_SLIDER_INTERVAL']) ? $arParams['GIFTS_SLIDER_INTERVAL'] : '',
        'SLIDER_PROGRESS' => isset($arParams['GIFTS_SLIDER_PROGRESS']) ? $arParams['GIFTS_SLIDER_PROGRESS'] : '',

        'TEXT_LABEL_GIFT' => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],

        'LABEL_PROP_'.$arParams['IBLOCK_ID'] => [],
        'LABEL_PROP_MOBILE_'.$arParams['IBLOCK_ID'] => [],
        'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],

        'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
        'MESS_BTN_BUY' => $arParams['~GIFTS_MESS_BTN_BUY'],
        'MESS_BTN_ADD_TO_BASKET' => $arParams['~GIFTS_MESS_BTN_BUY'],
        'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
        'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],

        'SHOW_PRODUCTS_'.$arParams['IBLOCK_ID'] => 'Y',
        'PROPERTY_CODE_'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE'],
        'PROPERTY_CODE_MOBILE'.$arParams['IBLOCK_ID'] => $arParams['LIST_PROPERTY_CODE_MOBILE'],
        'PROPERTY_CODE_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
        'OFFER_TREE_PROPS_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFER_TREE_PROPS'],
        'CART_PROPERTIES_'.$arResult['OFFERS_IBLOCK'] => $arParams['OFFERS_CART_PROPERTIES'],
        'ADDITIONAL_PICT_PROP_'.$arParams['IBLOCK_ID'] => (isset($arParams['ADD_PICT_PROP']) ? $arParams['ADD_PICT_PROP'] : ''),
        'ADDITIONAL_PICT_PROP_'.$arResult['OFFERS_IBLOCK'] => (isset($arParams['OFFER_ADD_PICT_PROP']) ? $arParams['OFFER_ADD_PICT_PROP'] : ''),

        'HIDE_NOT_AVAILABLE' => 'Y',
        'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
        'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
        'PRICE_CODE' => $arParams['PRICE_CODE'],
        'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'BASKET_URL' => $arParams['BASKET_URL'],
        'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
        'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
        'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
        'USE_PRODUCT_QUANTITY' => 'Y',
        'QUANTITY_SHOW' => $arParams['QUANTITY_SHOW'],
        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
        'QUANTITY_BOUNDS_FEW' => $arParams['QUANTITY_BOUNDS_FEW'],
        'QUANTITY_BOUNDS_MANY' => $arParams['QUANTITY_BOUNDS_MANY'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'POTENTIAL_PRODUCT_TO_BUY' => [
            'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
            'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
            'PRODUCT_PROVIDER_CLASS' => isset($arResult['~PRODUCT_PROVIDER_CLASS']) ? $arResult['~PRODUCT_PROVIDER_CLASS'] : '\Bitrix\Catalog\Product\CatalogProvider',
            'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
            'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

            'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'])
                ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']
                : null,
            'SECTION' => [
                'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
                'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
                'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
                'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
            ],
        ],

        'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
        'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
        'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY'],
        'PROPERTY_ARTICLE' => $arParams['PROPERTY_ARTICLE'],
        'ARTICLE_SHOW' => $arParams['ARTICLE_SHOW'],
        'PROPERTY_PICTURES' => $arParams['PROPERTY_PICTURES'],
        'VOTE_SHOW' => $arParams['VOTE_SHOW'],
        'VOTE_MODE' => $arParams['VOTE_MODE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'COLUMNS' => $iColumns,
        'VIEW' => $arVisual['GIFTS']['VIEW'],
        'HEADER_SHOW' => 'Y',
        'CACHE_TYPE' => 'N'
    ],
    $component,
    ['HIDE_ICONS' => 'N']
) ?>
</div>
<?php unset($iColumns) ?>