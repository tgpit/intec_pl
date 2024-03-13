<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sLevel
 */

$arParams = ArrayHelper::merge([
    'SEARCH_SECTIONS_USE' => 'N',
    'SEARCH_SECTIONS_TEMPLATE' => null,
    'SEARCH_SECTIONS_QUANTITY_SHOW' => 'Y',
    'SEARCH_SECTIONS_SECTION_ID_VARIABLE' => 'section'
], $arParams);

$arSearchSections = [
    'USE' => $arParams['SEARCH_SECTIONS_USE'] === 'Y',
    'SHOW' => false,
    'TEMPLATE' => $arParams['SEARCH_SECTIONS_TEMPLATE'],
    'PARAMETERS' => []
];

$arSearchSections['SHOW'] = $arSearchSections['USE'];

if (empty($arSearchSections['TEMPLATE']))
    $arSearchSections['SHOW'] = false;

if ($arSearchSections['SHOW']) {
    $sPrefix = 'SEARCH_SECTIONS_';

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arSearchSections['PARAMETERS'][$sKey] = $mValue;
    }

    $arSearchSections['PARAMETERS'] = ArrayHelper::merge($arSearchSections['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        "ELEMENTS_ID" => $arElements['ID']
    ]);
}