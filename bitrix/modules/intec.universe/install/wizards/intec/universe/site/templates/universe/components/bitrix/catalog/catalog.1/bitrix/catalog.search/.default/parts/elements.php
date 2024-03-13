<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$GLOBALS['arSearchElementFilter'] = [
    'ID' => $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['ITEMS']
];

$sComponentName = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IS_CATALOG'] ? 'bitrix:catalog.section' : 'bitrix:news.list';
$sPrefix = $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IS_CATALOG'] ? 'PRODUCTS_BLOCK_ON_EMPTY_RESULTS' : 'NEWS_BLOCK_ON_EMPTY_RESULTS';
$sTemplate = ($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IS_CATALOG'] ? 'products.small.' : 'news.tile.').$arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['TEMPLATE'];
$arParamsComponent = $arResult['THEME_COMPONENT']->arParams;
$arProperties = [];

if (!empty($arParamsComponent)) {
    foreach ($arParamsComponent as $sKey => $sValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));

        if ($sKey === 'TEMPLATE')
            continue;

        $arProperties[$sKey] = $sValue;
    }
}

unset($sKey, $sValue, $arParamsComponent);

$arProperties = ArrayHelper::merge($arProperties, [
    'IBLOCK_TYPE' => $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IBLOCK_ID'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'PRICE_CODE' => $arParams['PRICE_CODE'],
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'FILTER_NAME' => 'arSearchElementFilter'
]);

if ($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['IS_CATALOG'])
    $arProperties['SHOW_ALL_WO_SECTION'] = 'Y';

?>

<div class="catalog-search-empty-result-block">
    <?php if (!empty($arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['TITLE'])) { ?>
        <div class="catalog-search-empty-result-block-title">
            <?= $arParams['BLOCK_ON_EMPTY_SEARCH_RESULTS']['TITLE'] ?>
        </div>
    <?php } ?>
    <?php $APPLICATION->IncludeComponent(
        $sComponentName,
        $sTemplate,
        $arProperties,
        $component
    ) ?>
</div>

<?php unset($sComponentName, $sPrefix, $sTemplate, $arProperties) ?>