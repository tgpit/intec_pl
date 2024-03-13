<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$sPrefix = 'SECTIONS_';
$arSections = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, $sPrefix . 'TEMPLATE'),
    'PARAMETERS' => []
];

if (!empty($arSections['TEMPLATE'])) {
    $arSections['TEMPLATE'] = 'catalog.'.$arSections['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arSections['PARAMETERS'][$sKey] = $mValue;
    }

    $arSections['PARAMETERS'] = ArrayHelper::merge($arSections['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['PRODUCTS_IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['PRODUCTS_IBLOCK_ID'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'FILTER_NAME' => $arParams['SECTIONS_FILTER_NAME'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'COUNT_ELEMENTS' => $arParams['SECTIONS_COUNT_ELEMENTS'],
        'WIDE' => $arParams['WIDE'],
        'RECURSION' => 'N',
        'SECTION_USER_FIELDS' => ['UF_*']
    ]);
} else {
    $arResult['VISUAL']['SECTIONS']['SHOW'] = false;
}