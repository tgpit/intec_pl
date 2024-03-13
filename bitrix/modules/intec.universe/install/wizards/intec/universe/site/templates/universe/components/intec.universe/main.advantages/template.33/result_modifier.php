<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'PROPERTY_NUMBER' => null,
    'PROPERTY_MAX_NUMBER' => null,
    'COLUMNS' => 2
], $arParams);

$arVisual = [
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS'])
];

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [];
    $iMaxNumber = 0;
    $iNumber = 0;

    if (!empty($arParams['PROPERTY_NUMBER'])) {
        $arPropertyNumber = null;

        $arPropertyNumber = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_NUMBER']
        ]);

        if (!empty($arPropertyNumber['VALUE']))
            $arItem['DATA']['NUMBER'] = $arPropertyNumber['VALUE'];
    }

    if (!empty($arParams['PROPERTY_MAX_NUMBER'])) {
        $arPropertyNumber = null;

        $arPropertyNumber = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_MAX_NUMBER']
        ]);

        if (!empty($arPropertyNumber['VALUE']))
            $arItem['DATA']['MAX_NUMBER'] = $arPropertyNumber['VALUE'];
    }

    $iMaxNumber = $arItem['DATA']['MAX_NUMBER'];
    $iNumber = $arItem['DATA']['NUMBER'];

    if ($arItem['DATA']['NUMBER'] > $arItem['DATA']['MAX_NUMBER']) {
        $arItem['DATA']['MAX_NUMBER'] = $iNumber;
        $arItem['DATA']['NUMBER'] = $iMaxNumber;
    }

}

unset($arItem);