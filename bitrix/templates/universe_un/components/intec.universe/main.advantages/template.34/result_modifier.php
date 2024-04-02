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
    'BUTTON_SHOW' => 'N',
    'BUTTON_LINK' => null,
    'BUTTON_ALIGN' => 'left',
    'BACKGROUND_SHOW' => 'N',
    'BACKGROUND_COLOR' => '#FBFCFD',
    'THEME' => 'light',
], $arParams);

$arVisual = [
    'THEME' => ArrayHelper::fromRange(['light', 'dark'], $arParams['THEME']),
    'BACKGROUND' => [
        'SHOW' => $arParams['BACKGROUND_SHOW'] === 'Y',
        'COLOR' => $arParams['BACKGROUND_COLOR']
    ],
    'BUTTON' => [
        'SHOW' => $arParams['BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_TEXT'],
        'LINK' => $arParams['BUTTON_LINK'],
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['BUTTON_ALIGN'])
    ]
];

if (empty($arVisual['BUTTON']['LINK']))
    $arVisual['BUTTON']['SHOW'] = false;

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