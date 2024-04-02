<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

if (!empty($arParams['PRODUCTS_IBLOCK_ID'])) {
    $sPrefix = 'PRODUCTS_';
    $sTemplate = 'products.small.9';

    if ($arVisual['TIPS']['VIEW'] === 'list.3')
        $sTemplate = 'products.small.10';

    $iLength = StringHelper::length($sPrefix);

    $arProperties = [];

    foreach ($arParams as $sKey => $sValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut($sKey, $iLength);

        $arProperties[$sKey] = $sValue;
    }

    unset($sPrefix, $iLength, $sKey, $sValue);

    $oQuery = (new ElementsQuery())
        ->setIBlockId($arProperties['IBLOCK_ID'])
        ->setFilter([
            'ID' => $arVisual['CATALOG_ELEMENTS']
        ])
        ->setLimit(1);

    $arProducts = $oQuery->execute();

    if (!$arProducts->isEmpty()) {
        $GLOBALS['arrFilterSearchProducts'] = $oQuery->getFilter();

        $arProperties = ArrayHelper::merge([
            'TITLE_SHOW' => 'Y',
            'TITLE_TEXT' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PRODUCTS_TITLE')
        ], $arProperties, [
            'COLUMNS' => 3,
            'FILTER_NAME' => 'arrFilterSearchProducts',
            'INCLUDE_SUBSECTIONS' => 'N',
            'SHOW_ALL_WO_SECTION' => 'Y',
            'PRODUCTS_HIDE_NOT_AVAILABLE' => 'N',
            'PRODUCTS_HIDE_NOT_AVAILABLE_OFFERS' => 'N',
            'SECTION_USER_FIELDS' => [],
            'SET_TITLE' => 'N',
            'SET_META_KEYWORDS' => 'N',
            'SET_META_DESCRIPTION' => 'N',
            'DISPLAY_TOP_PAGER' => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'N'
        ]);
?>
        <div class="search-title-additional-products">
            <div class="search-title-additional-products-wrapper">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    $sTemplate,
                    $arProperties,
                    $component
                ); ?>
            </div>
        </div>
<?php
    }
}

unset($sTemplate, $arProperties)

?>
