<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_STICKER' => null,
    'COLUMNS' => 4,
    'STICKER_SHOW' => 'N',
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'NAME_SHOW' => 'Y',
    'PREVIEW_SHOW' => 'N',
    'PRICE_SHOW' => 'N',
    'PROPERTY_PRICE_BACKGROUND_COLOR' => null,
    'PROPERTY_THEME_LIGHT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'COLUMNS' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'STICKER' => [
        'SHOW' => !empty($arParams['PROPERTY_STICKER']) && $arParams['STICKER_SHOW'] === 'Y'
    ],
    'LIGHT_TEXT' => $arParams['LIGHT_TEXT'],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE'])
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'STICKER' => null,
        'PRICE' => [
            'VALUE' => null,
            'BACKGROUND' => [
                'COLOR' => null
            ]
        ],
        'THEME' => null
    ];

    if (!empty($arParams['PROPERTY_STICKER'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_STICKER']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arItem['DATA']['STICKER'] = $arProperty['DISPLAY_VALUE'];
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_PRICE'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE']
        ]);

        if (!empty($arProperty) && !empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arItem['DATA']['PRICE']['VALUE'] = $arProperty['DISPLAY_VALUE'];
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_PRICE_BACKGROUND_COLOR'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_BACKGROUND_COLOR']
        ]);

        if (!empty($arProperty) && !empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arItem['DATA']['PRICE']['BACKGROUND']['COLOR'] = str_replace(' ', '', $arProperty['DISPLAY_VALUE']);
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_THEME_DARK'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_THEME_DARK'],
            'VALUE'
        ]);

        $arItem['DATA']['THEME'] = !empty($arProperty) ? 'dark' : 'light';

        unset($arProperty);
    }
}

unset($arItem);
