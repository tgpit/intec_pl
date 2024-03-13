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

$sPrefix = 'ACCESSORIES_SECTION_';
$sTemplate = 'catalog.';
$iLength = StringHelper::length($sPrefix);
$arProperties = [];

if (!empty($arParams['SECTION_TEMPLATE']))
    $sTemplate = $sTemplate . $arParams['SECTION_TEMPLATE'];
else
    $sTemplate = 'catalog.tile.4';

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $sKey, $sValue);

if (empty($GLOBALS['arAccessoriesFilterItems']) && empty($GLOBALS['arAccessoriesPreFilterItems']))
    return;

$arProperties = ArrayHelper::merge($arProperties, [
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'SECTION_USER_FIELDS' => [],
    'SHOW_ALL_WO_SECTION' => 'Y',
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'FILTER_NAME' => !empty($GLOBALS['arAccessoriesFilterItems']) ? 'arAccessoriesFilterItems' : 'arAccessoriesPreFilterItems',
    'SET_TITLE' => 'N',
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'USE_COMPARE' => $arParams['SECTION_COMPARE_USE'],
    'OFFER_TREE_PROPS' => $arProperties['OFFERS_PROPERTY_CODE'],
    'COMPARE_PATH' => $arParams['SECTION_COMPARE_PATH'],
    'COMPARE_NAME' => $arParams['SECTION_COMPARE_NAME'],
    'PRODUCT_DISPLAY_MODE' => 'Y'
]);
foreach ($arSort['PROPERTIES'] as $arSortProperty) {
    if ($arSortProperty['ACTIVE']) {
        $arProperties['ELEMENT_SORT_FIELD'] = $arSortProperty['FIELD'];
        $arProperties['ELEMENT_SORT_ORDER'] = $arSortProperty['ORDER'];

        break;
    }
}
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