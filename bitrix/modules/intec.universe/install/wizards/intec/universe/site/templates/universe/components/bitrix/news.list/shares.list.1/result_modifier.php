<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var $arResult
 * @var $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_DATE_END' => null,
    'PROPERTY_DISCOUNT' => null,
    'DATE_SHOW' => 'N',
    'DATE_TYPE' => 'DATE_ACTIVE_FROM',
    'DATE_FORMAT' => 'd.m.Y',
    'IBLOCK_DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_SHOW' => 'N',
    'TIMER_SHOW' => 'Y',
    'TIMER_TIMER_SECONDS_SHOW' => 'N',
    'TIMER_SALE_SHOW' => 'Y',
    'TIMER_SALE_HEADER_SHOW' => 'Y',
    'TIMER_SALE_HEADER_VALUE' => 'Y'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'DATE' => [
        'SHOW' => $arParams['DATE_SHOW'] === 'Y',
        'TYPE' => ArrayHelper::fromRange([
            'DATE_ACTIVE_FROM',
            'DATE_CREATE',
            'DATE_ACTIVE_TO',
            'TIMESTAMP_X'
        ], $arParams['DATE_TYPE']),
        'FORMAT' => $arParams['DATE_FORMAT']
    ],
    'LINK' => [
        'HIDE' => $arParams['HIDE_LINK_WHEN_NO_DETAIL']
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'IBLOCK' => [
        'DESCRIPTION' => [
            'SHOW' => $arParams['IBLOCK_DESCRIPTION_SHOW'] === 'Y'
        ]
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER'] && !empty($arResult['NAV_STRING'])
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER'] && !empty($arResult['NAV_STRING'])
        ]
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DATE_END']),
        'HEADER' => [
            'SHOW' => $arParams['TIMER_TIMER_HEADER_SHOW'] === 'Y' && !empty($arParams['TIMER_TIMER_HEADER'])
        ],
        'SECONDS' => [
            'SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'] === 'Y'
        ],
        'SALE' => [
            'SHOW' => $arParams['TIMER_SALE_SHOW'] === 'Y'
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arData = [
        'HIDE_LINK' => false,
        'DATE' => [
            'SHOW' => false,
            'VALUE' => !empty($arItem[$arVisual['DATE']['TYPE']]) ? $arItem[$arVisual['DATE']['TYPE']] : $arItem['DATE_CREATE']
        ],
        'TIMER' => [
            'USE' => false,
            'SALE' => [
                'SHOW' => false
            ],
            'VALUES' => [
                'UNTIL_DATE' => null,
                'SALE_VALUE' => null
            ]
        ]
    ];

    /** Hide link item */
    $arData['HIDE_LINK'] = $arVisual['LINK']['HIDE'] && empty($arItem['DETAIL_TEXT']);

    if (!empty($arVisual['DATE']['FORMAT'])) {
        $arData['DATE']['SHOW'] = $arVisual['DATE']['SHOW'];
        $arData['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['DATE']['FORMAT'],
            MakeTimeStamp($arData['DATE']['VALUE'], CSite::GetDateFormat())
        );
    }

    if (!empty($arParams['PROPERTY_DATE_END'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_DATE_END']);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = CIBlockFormatProperties::DateFormat(
                    'Y-m-d H:i:s',
                    MakeTimeStamp($arProperty['DISPLAY_VALUE'], CSite::GetDateFormat())
                );

                if ($arProperty['DISPLAY_VALUE'] >= Date('Y-m-d H:i:s')) {
                    $arData['TIMER']['SHOW'] = $arVisual['TIMER']['SHOW'];
                    $arData['TIMER']['VALUES']['UNTIL_DATE'] = $arProperty['DISPLAY_VALUE'];
                }
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_DISCOUNT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_DISCOUNT']);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arData['TIMER']['SALE']['SHOW'] = $arVisual['TIMER']['SALE']['SHOW'];
                $arData['TIMER']['VALUES']['SALE_VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    $arItem['DATA'] = $arData;
}

unset($arItem, $arData);

if ($arVisual['TIMER']['SHOW']) {
    $arResult['TIMER'] = [
        'TIME_ZERO_HIDE' => 'Y',
        'MODE' => 'set',
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arVisual['LAZYLOAD']['USE'],
        'TIMER_SECONDS_SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'],
        'TIMER_QUANTITY_SHOW' => 'N',
        'TIMER_HEADER_SHOW' => $arParams['TIMER_TIMER_HEADER_SHOW'],
        'TIMER_HEADER' => $arParams['TIMER_TIMER_HEADER'],
        'TIMER_QUANTITY_OVER' => 'N',
        'TIMER_TITLE_SHOW' => 'N',
        'SALE_SHOW' => $arParams['TIMER_SALE_SHOW'],
        'SALE_HEADER_SHOW' => $arParams['TIMER_SALE_HEADER_SHOW'],
        'SALE_HEADER_VALUE' => $arParams['TIMER_SALE_HEADER_VALUE'],
        'TIMER_TITLE_ENTER' => 'N',
        'TIMER_PRODUCT_UNITS_USE' => 'N',
        'TIMER_QUANTITY_HEADER_SHOW' => 'N',
        'TIMER_QUANTITY_HEADER' => null,
        'RANDOMIZE_ID' => 'Y'
    ];
}

$arResult['VISUAL'] = $arVisual;