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
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_ALIGN' => 'left',
    'BACKGROUND_SHOW' => 'N',
    'BACKGROUND_COLOR' => null,
    'THEME' => 'light',
    'NUMBER_SHOW' => 'N',
    'NUMBER_ALIGN' => 'left',
    'PROPERTY_NUMBER' => null,
    'COLUMNS' => 4,
    'COLUMNS_MOBILE' => 2
], $arParams);

$arVisual = [
    'NUMBER' => [
        'SHOW' => $arParams['NUMBER_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['NUMBER_ALIGN'])
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['PREVIEW_ALIGN'])
    ],
    'THEME' => ArrayHelper::fromRange(['light', 'dark'], $arParams['THEME']),
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([2, 3, 4, 5], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([2, 1], $arParams['COLUMNS_MOBILE']),
    ],
    'BACKGROUND' => [
        'SHOW' => $arParams['BACKGROUND_SHOW'] === 'Y',
        'COLOR' => $arParams['BACKGROUND_COLOR']
    ]
];

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