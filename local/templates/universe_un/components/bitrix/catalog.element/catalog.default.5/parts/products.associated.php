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

$GLOBALS['arCatalogElementFilterAssociated'] = [
    'ID' => $arResult['FIELDS']['ASSOCIATED']['VALUES']
];

$sPrefix = 'PRODUCTS_ASSOCIATED_';
$sTemplate = 'products.small.4';

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
    'FILTER_NAME' => 'arCatalogElementFilterAssociated',
    'PRICE_CODE' => $arParams['PRICE_CODE'],
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
    'COLUMNS' => $arVisual['MENU']['SHOW'] ? 2 : $arProperties['COLUMNS'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
]);

if (empty($arVisual['ASSOCIATED']['NAME']))
    $arVisual['ASSOCIATED']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRODUCTS_ASSOCIATED_NAME_DEFAULT');

?>
<div class="catalog-element-products-associated-container catalog-element-additional-block">
    <div class="catalog-element-additional-block-name">
        <?= $arVisual['ASSOCIATED']['NAME'] ?>
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