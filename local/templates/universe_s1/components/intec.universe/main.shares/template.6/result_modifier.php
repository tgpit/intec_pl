<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'ELEMENT_HEADER_PROPERTY_TEXT' => null,
    'PROPERTY_PREVIEW' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'ELEMENT_HEADER_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_WORDS' => 0,
    'PICTURE_SHOW' => 'N',
    'LINK_ALL_SHOW' => 'N',
    'LINK_ALL_TEXT' => null,
    'TIMER_SHOW' => 'N',
    'TIMER_PROPERTY_UNTIL_DATE' => null,
    'TIMER_PROPERTY_DISCOUNT' => null
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'ELEMENT' => [
        'HEADER' => [
            'SHOW' => $arParams['ELEMENT_HEADER_SHOW'] === 'Y' && !empty($arParams['ELEMENT_HEADER_PROPERTY_TEXT'])
        ]
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y',
        'TRUNCATE' => [
            'USE' => $arParams['PREVIEW_TRUNCATE_USE'],
            'WORDS' => Type::toInteger($arParams['PREVIEW_TRUNCATE_WORDS'])
        ]
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y' && !empty($arParams['TIMER_PROPERTY_UNTIL_DATE']),
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

if ($arVisual['PREVIEW']['TRUNCATE']['WORDS'] < 1)
    $arVisual['PREVIEW']['TRUNCATE']['USE'] = false;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'HEADER' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'PICTURE' => [
            'VALUE' => null
        ],
        'TIMER' => [
            'SHOW' => false,
            'VALUES' => [
                'ELEMENT_ID' => $arItem['ID'],
                'ITEM_NAME' => $arItem['NAME'],
                'UNTIL_DATE' => null,
                'SALE_VALUE' => null
            ]
        ]
    ];

    if (!empty($arParams['ELEMENT_HEADER_PROPERTY_TEXT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['ELEMENT_HEADER_PROPERTY_TEXT']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['HEADER']['SHOW'] = $arVisual['HEADER']['SHOW'];
                $arItem['DATA']['HEADER']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if ($arVisual['PREVIEW']['SHOW']) {
        if (!empty($arParams['PROPERTY_PREVIEW'])) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PREVIEW']);

            if (!empty($arProperty)) {
                $arProperty = CIBlockFormatProperties::GetDisplayValue(
                    $arProperty,
                    $arItem,
                    false
                );

                if (!empty($arProperty['DISPLAY_VALUE'])) {
                    if (Type::isArray($arProperty['DISPLAY_VALUE']))
                        $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                    $arItem['DATA']['PREVIEW']['VALUE'] = $arProperty['DISPLAY_VALUE'];
                }
            }

            unset($arProperty);
        }

        if (empty($arItem['DATA']['PREVIEW']['VALUE'])) {
            if (!empty($arItem['PREVIEW_TEXT']))
                $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];
            else if (!empty($arItem['DETAIL_TEXT']))
                $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['DETAIL_TEXT'];
        }

        if (!empty($arItem['DATA']['PREVIEW']['VALUE'])) {
            if ($arVisual['PREVIEW']['TRUNCATE']['USE']) {
                $words = array_filter(
                    explode(' ', Html::stripTags($arItem['DATA']['PREVIEW']['VALUE']))
                );

                if (count($words) > $arVisual['PREVIEW']['TRUNCATE']['WORDS']) {
                    $words = ArrayHelper::slice($words, 0, $arVisual['PREVIEW']['TRUNCATE']['WORDS']);

                    $lastKey = $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1];

                    if (ArrayHelper::isIn(StringHelper::cut($lastKey, -1), ['.', ',', ':', ';', '!', '?']))
                        $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1] = StringHelper::cut(
                            $lastKey,
                            0,
                            StringHelper::length($lastKey) - 1
                        );

                    $arItem['DATA']['PREVIEW']['VALUE'] = implode(' ', $words).'...';

                    unset($lastKey);
                }

                unset($words);
            }

            $arItem['DATA']['PREVIEW']['SHOW'] = $arVisual['PREVIEW']['SHOW'];
        }
    }

    if ($arVisual['PICTURE']['SHOW']) {
        if (!empty($arItem['PREVIEW_PICTURE']))
            $arItem['DATA']['PICTURE']['VALUE'] = $arItem['PREVIEW_PICTURE'];
        else if (!empty($arItem['DETAIL_PICTURE']))
            $arItem['DATA']['PICTURE']['VALUE'] = $arItem['DETAIL_PICTURE'];
    }

    if (!empty($arParams['TIMER_PROPERTY_UNTIL_DATE'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['TIMER_PROPERTY_UNTIL_DATE'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arProperty = CIBlockFormatProperties::DateFormat(
                'Y-m-d H:i:s',
                MakeTimeStamp($arProperty, CSite::GetDateFormat())
            );

            if ($arProperty >= Date('Y-m-d H:i:s')) {
                $arItem['DATA']['TIMER']['SHOW'] = $arVisual['TIMER']['SHOW'];
                $arItem['DATA']['TIMER']['VALUES']['UNTIL_DATE'] = $arProperty;
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['TIMER_PROPERTY_DISCOUNT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['TIMER_PROPERTY_DISCOUNT']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['TIMER']['VALUES']['SALE_VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }
}

unset($arItem);

if ($arVisual['TIMER']['SHOW']) {
    $arResult['TIMER'] = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'QUANTITY' => 1,
        'TIMER_QUANTITY_OVER' => 'N',
        'TIME_ZERO_HIDE' => 'Y',
        'MODE' => 'set',
        'TIMER_SECONDS_SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'],
        'TIMER_QUANTITY_SHOW' => 'N',
        'TIMER_HEADER_SHOW' => $arParams['TIMER_TIMER_HEADER_SHOW'],
        'TIMER_HEADER' => $arParams['TIMER_TIMER_HEADER'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'TIMER_TITLE_SHOW' => 'N',
        'COMPOSITE_FRAME_MODE' => 'A',
        'COMPOSITE_FRAME_TYPE' => 'AUTO',
        'SALE_SHOW' => $arParams['TIMER_SALE_SHOW'],
        'SALE_VALUE' => $arParams['PROPERTY_DISCOUNT'],
        'RANDOMIZE_ID' => 'Y'
    ];
}

if ($arParams['LINK_ALL_SHOW'] === 'Y' && empty($arParams['LIST_PAGE_URL']))
    $arParams['LIST_PAGE_URL'] = ArrayHelper::getValue(
        ArrayHelper::getFirstValue($arResult['ITEMS']),
        'LIST_PAGE_URL'
    );

$arResult['LIST_BLOCK'] = [
    'SHOW' => $arParams['LINK_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
    'TEXT' => trim($arParams['LINK_ALL_TEXT']),
    'URL' => StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR
    ])
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);