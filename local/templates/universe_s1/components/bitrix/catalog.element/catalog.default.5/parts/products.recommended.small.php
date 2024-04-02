<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$GLOBALS['arCatalogElementFilterRecommended'] = [
    'ID' => $arResult['FIELDS']['RECOMMENDED']['VALUES']
];

$sPrefix = 'PRODUCTS_RECOMMENDED_';
$sTemplate = 'products.small.3';

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
    'FILTER_NAME' => 'arCatalogElementFilterRecommended',
    'PRICE_CODE' => $arParams['PRICE_CODE'],
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
]);

if (empty($arVisual['RECOMMENDED']['NAME']))
    $arVisual['RECOMMENDED']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRODUCTS_RECOMMENDED_NAME_DEFAULT');

?>
<div class="catalog-element-products-recommended-container catalog-element-additional-block">
    <div class="catalog-element-additional-block-name-small">
        <?= $arVisual['RECOMMENDED']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <?php $APPLICATION->IncludeComponent(
            'bitrix:catalog.section',
            $sTemplate,
            $arProperties,
            $component
        ) ?>
    </div>
</div>
<?php unset($sTemplate, $arProperties) ?>