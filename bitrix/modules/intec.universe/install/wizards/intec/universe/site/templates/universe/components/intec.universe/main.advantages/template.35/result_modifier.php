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
    'SETTINGS_USE' => 'N',
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_ALIGN' => 'left',
    'BACKGROUND_SHOW' => 'N',
    'BACKGROUND_COLOR' => null,
    'THEME' => 'light',
    'NUMBER_SHOW' => 'N',
    'NUMBER_ALIGN' => 'left',
    'PROPERTY_NUMBER' => null,
    'BUTTON_SHOW' => 'N',
    'BUTTON_LINK' => null,
    'BUTTON_ALIGN' => 'left',
    'COLUMNS' => 3,
], $arParams);


$arVisual = [
    'NUMBER' => [
        'SHOW' => $arParams['NUMBER_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['NUMBER_ALIGN'])
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['COLUMNS']),
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['PREVIEW_ALIGN'])
    ],
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

    if ($arParams['NUMBER_SHOW'] === 'Y' && !empty($arParams['PROPERTY_NUMBER'])) {
        $arPropertyNumber = null;

        $arPropertyNumber = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_NUMBER']
        ]);

        if (!empty($arPropertyNumber['VALUE']))
            $arItem['DATA']['NUMBER'] = $arPropertyNumber['VALUE'];
    }
}

unset($arItem);