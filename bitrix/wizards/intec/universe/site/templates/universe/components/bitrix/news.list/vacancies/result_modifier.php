<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!CModule::IncludeModule('intec.core'))
    return;

$sIBlockType = ArrayHelper::getValue($arParams, 'IBLOCK_TYPE');
$iIBlockId = ArrayHelper::getValue($arParams, 'IBLOCK_ID');
$arSections = [];

$arProperties = [
    'PROPERTY_SALARY' => 'SALARY'
];

if (!empty($sIBlockType) && !empty($iIBlockId)) {
    $rsSections = CIBlockSection::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SECTION_ID' => false,
        'IBLOCK_TYPE' => $sIBlockType,
        'IBLOCK_ID' => $iIBlockId
    ]);

    while ($arSection = $rsSections->Fetch()) {
        $arSection['ITEMS'] = [];
        $arSections[$arSection['ID']] = $arSection;
    }
}

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['SYSTEM_PROPERTIES'] = [];

    foreach ($arProperties as $sPropertyKey => $sPropertyName) {
        $arItem['SYSTEM_PROPERTIES'][$sPropertyName] = null;

        $sPropertyParameter = ArrayHelper::getValue($arParams, $sPropertyKey);

        if (!empty($sPropertyParameter))
            if (ArrayHelper::keyExists($sPropertyParameter, $arItem['PROPERTIES']))
                $arItem['SYSTEM_PROPERTIES'][$sPropertyName] = ArrayHelper::getValue($arItem, ['PROPERTIES', $sPropertyParameter]);
    }

    if (ArrayHelper::keyExists($arItem['IBLOCK_SECTION_ID'], $arSections)) {
        $arSections[$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
    }
}

$arResult['SECTIONS'] = $arSections;