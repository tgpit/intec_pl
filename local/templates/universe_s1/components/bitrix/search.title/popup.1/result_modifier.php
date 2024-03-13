<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arParams = ArrayHelper::merge([
    'INPUT_ID' => null,
    'TIPS_USE' => 'Y',
    'TIPS_VIEW' => 'list.2',
    'PRODUCTS_SHOW' => 'N',
    'HIDE_REQUESTED' => 'N',
    'PROPERTY_REQUEST' => null,
], $arParams);

if (empty($arParams['INPUT_ID']))
    $arParams['INPUT_ID'] = 'title-search-input';

$arVisual = [
    'INPUT' => [
        'ID' => $arParams['INPUT_ID']
    ],
    'TIPS' => [
        'USE' => $arParams['TIPS_USE'] === 'Y',
        'VIEW' => ArrayHelper::fromRange([
            'list.1',
            'list.2',
            'list.3'
        ], $arParams['TIPS_VIEW'])
    ],
    'RESULTS' => [
        'SHOW' => false
    ],
    'PRODUCTS' => [
        'SHOW' => $arParams['PRODUCTS_SHOW'] === 'Y'
    ],
    'SECTIONS' => [
        'SHOW' => false
    ],
    'REQUESTED' => [
        'HIDE' => $arParams['HIDE_REQUESTED'] === 'Y' && !empty($arParams['PROPERTY_REQUEST']),
        'PROPERTY' => $arParams['PROPERTY_REQUEST']
    ],
    'SHOW' => false,
    'CATALOG_ELEMENTS' => []
];

include(__DIR__.'/modifiers/elements.php');

if ($bBase) {
    include(__DIR__.'/modifiers/base/prices.php');
    include(__DIR__.'/modifiers/base/elements.php');
} else if ($bLite) {
    include(__DIR__.'/modifiers/lite/prices.php');
    include(__DIR__.'/modifiers/lite/elements.php');
}

$arVisual['SECTIONS']['SHOW'] = !empty($arResult['SECTIONS']);

foreach ($arResult['CATEGORIES'] as &$arCategory) {
    foreach ($arCategory['ITEMS'] as &$arItem) {
        if ($arItem['ELEMENT']['IS_PRODUCT'] === 'Y') {
            $arVisual['CATALOG_ELEMENTS'][] = $arItem['ITEM_ID'];
        }
        if (!empty($arItem['ITEM_ID'])) {
            $arVisual['RESULTS']['SHOW'] = true;
        }
    }
}

unset($arItem, $arCategory);

$arVisual['SHOW'] = $arVisual['SECTIONS']['SHOW'] || $arVisual['RESULTS']['SHOW'];
$arResult['VISUAL'] = $arVisual;

unset($arVisual);
