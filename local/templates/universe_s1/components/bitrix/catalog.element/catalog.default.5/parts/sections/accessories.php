<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$GLOBALS['arCatalogElementFilterAccessories'] = [
    'ID' => $arResult['FIELDS']['ACCESSORIES']['VALUES']
];

$sPrefix = 'PRODUCTS_ACCESSORIES_';
$sTemplate = 'products.small.7';

if ($arParams['PRODUCTS_ACCESSORIES_VIEW'] === 'list')
    $sTemplate = 'products.small.8';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $sKey, $sValue);

$arProperties = ArrayHelper::merge($arProperties, [
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'SECTION_USER_FIELDS' => [],
    'SHOW_ALL_WO_SECTION' => 'Y',
    'FILTER_NAME' => 'arCatalogElementFilterAccessories',
    'PRICE_CODE' => $arParams['PRICE_CODE'],
    'PAGE_ELEMENT_COUNT' => count($arResult['FIELDS']['ACCESSORIES']['VALUES']),
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'COMPATIBLE_MODE' => 'Y'
]);

?>
    <div class="catalog-element-accessories">
        <div class="catalog-element-accessories-content">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.section',
                $sTemplate,
                $arProperties,
                $component
            ) ?>
        </div>
    </div>
<?php unset($sTemplate, $arProperties) ?>