<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var CBitrixComponent $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams['MODE'] = ArrayHelper::fromRange(['period', 'day'], $arParams['MODE']);

$bReady = false;
$sDate = date('Y-m-d');
$arSort = [];
$arFilter = [
    'ACTIVE' => 'Y',
    'GLOBAL_ACTIVE' => 'Y',
    'IBLOCK_ID' => $arParams['IBLOCK_ID']
];

if (!empty($arParams['SORT_BY']) && !empty($arParams['ORDER_BY'])) {
    $arSort = [
        $arParams['SORT_BY'] => $arParams['ORDER_BY']
    ];
}

if ($arParams['MODE'] === 'period') {
    if (!empty($arParams['PROPERTY_PERIOD_START']) && !empty($arParams['PROPERTY_PERIOD_END'])) {
        $bReady = true;

        $arFilter['<=PROPERTY_'.$arParams['PROPERTY_PERIOD_START']] = $sDate;
        $arFilter['>=PROPERTY_'.$arParams['PROPERTY_PERIOD_END']] = $sDate;
    }
} else if ($arParams['MODE'] === 'day') {
    if (!empty($arParams['PROPERTY_DAY'])) {
        $bReady = true;

        $arFilter['=PROPERTY_'.$arParams['PROPERTY_DAY']] = $sDate;
    }
}

if ($bReady) {
    $element = Arrays::fromDBResult(CIBlockElement::GetList($arSort, $arFilter))->getFirst();

    if (!empty($element)) {
        $arParams['ELEMENT_ID'] = $element['ID'];
        $arParams['SECTION_ID'] = $element['IBLOCK_SECTION_ID'];
    }

    unset($element);
}

if (empty($arParams['ELEMENT_ID']) && empty($arParams['SECTION_ID'])) {
    $arParams['ELEMENT_ID'] = null;
    $arParams['SECTION_ID'] = null;
}

unset($bReady, $sDate, $arSort, $arFilter);