<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_PRICE' => null,
    'PROPERTY_PRICE_OLD' => null,
    'PROPERTY_CURRENCY' => null,
    'PROPERTY_PRICE_FORMAT' => null,
    'COLUMNS' => 3,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PICTURE_SHOW' => 'N',
    'PRICE_SHOW' => 'N',
    'PRICE_OLD_SHOW' => 'N',
    'PRICE_FORMAT' => null,
    'SLIDER_USE' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_NAV_SHOW' => 'N',
    'SLIDER_NAV_VIEW' => 'default',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => $arParams['COLUMNS'] > 3 ? 'small' : 'default'
    ],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE']),
        'FORMAT' => $arParams['PRICE_FORMAT'],
        'OLD' => [
            'SHOW' => $arParams['PRICE_OLD_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE_OLD']),
        ]
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y' && count($arResult['ITEMS']) > $arParams['COLUMNS'],
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'NAV' => [
            'SHOW' => $arParams['SLIDER_NAV_SHOW'] === 'Y',
            'VIEW' => ArrayHelper::fromRange(['default', 'top'], $arParams['SLIDER_NAV_VIEW'])
        ],
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arData = [
        'PICTURE' => [],
        'PRICE' => [
            'SHOW' => false,
            'VALUE' => null,
            'PRINT' => null,
            'OLD' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'CURRENCY' => null,
            'FORMAT' => null,
        ]
    ];

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arData['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arData['PICTURE'] = $arItem['DETAIL_PICTURE'];

    if (!empty($arParams['PROPERTY_PRICE'])) {
        $price = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE']
        ]);

        if (!empty($price)) {
            $price = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $price,
                false
            );

            if (!empty($price['DISPLAY_VALUE'])) {
                if (Type::isArray($price['DISPLAY_VALUE']))
                    $price['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($price['DISPLAY_VALUE']);

                $arData['PRICE']['SHOW'] = $arVisual['PRICE']['SHOW'];
                $arData['PRICE']['VALUE'] = $price['DISPLAY_VALUE'];
            }
        }

        unset($price);
    }

    if (!empty($arParams['PROPERTY_PRICE_OLD'])) {
        $oldPrice = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_OLD']
        ]);

        if (!empty($oldPrice)) {
            $oldPrice = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $oldPrice,
                false
            );

            if (!empty($oldPrice['DISPLAY_VALUE'])) {
                if (Type::isArray($oldPrice['DISPLAY_VALUE']))
                    $oldPrice['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($oldPrice['DISPLAY_VALUE']);

                $arData['PRICE']['OLD']['SHOW'] = $arVisual['PRICE']['OLD']['SHOW'];
                $arData['PRICE']['OLD']['VALUE'] = $oldPrice['DISPLAY_VALUE'];
            }
        }

        unset($oldPrice);
    }

    if (!empty($arParams['PROPERTY_CURRENCY'])) {
        $currency = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_CURRENCY']
        ]);

        if (!empty($currency)) {
            $currency = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $currency,
                false
            );

            if (!empty($currency['DISPLAY_VALUE'])) {
                if (Type::isArray($currency['DISPLAY_VALUE']))
                    $currency['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($currency['DISPLAY_VALUE']);

                $arData['PRICE']['CURRENCY'] = $currency['DISPLAY_VALUE'];
            }
        }

        unset($currency);
    }

    if (!empty($arParams['PROPERTY_PRICE_FORMAT'])) {
        $format = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_FORMAT']
        ]);

        if (!empty($format)) {
            $format = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $format,
                false
            );

            if (!empty($format['DISPLAY_VALUE'])) {
                if (Type::isArray($format['DISPLAY_VALUE']))
                    $format['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($format['DISPLAY_VALUE']);

                $arData['PRICE']['FORMAT'] = $format['DISPLAY_VALUE'];
            }
        }

        unset($format);
    }

    if (empty($arData['PRICE']['FORMAT']) && !empty($arVisual['PRICE']['FORMAT']))
        $arData['PRICE']['FORMAT'] = $arVisual['PRICE']['FORMAT'];

    if (!empty($arData['PRICE']['FORMAT'])) {
        if (!empty($arData['PRICE']['VALUE']))
            $arData['PRICE']['PRINT'] = trim(
                StringHelper::replaceMacros($arData['PRICE']['FORMAT'], [
                    'VALUE' => $arData['PRICE']['VALUE'],
                    'CURRENCY' => $arData['PRICE']['CURRENCY']
                ])
            );

        if (!empty($arData['PRICE']['OLD']['VALUE']))
            $arData['PRICE']['OLD']['PRINT'] = trim(
                StringHelper::replaceMacros($arData['PRICE']['FORMAT'], [
                    'VALUE' => $arData['PRICE']['OLD']['VALUE'],
                    'CURRENCY' => $arData['PRICE']['CURRENCY']
                ])
            );
    } else {
        if (!empty($arData['PRICE']['VALUE'])) {
            if (!empty($arData['PRICE']['CURRENCY']))
                $arData['PRICE']['PRINT'] = $arData['PRICE']['VALUE'].' '.$arData['PRICE']['CURRENCY'];
            else
                $arData['PRICE']['PRINT'] = $arData['PRICE']['VALUE'];
        }

        if (!empty($arData['PRICE']['OLD']['VALUE'])) {
            if (!empty($arData['PRICE']['CURRENCY']))
                $arData['PRICE']['OLD']['PRINT'] = $arData['PRICE']['OLD']['VALUE'].' '.$arData['PRICE']['CURRENCY'];
            else
                $arData['PRICE']['OLD']['PRINT'] = $arData['PRICE']['OLD']['VALUE'];
        }
    }

    $arItem['DATA'] = $arData;

    unset($arData);
}

unset($arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);