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

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'ELEMENT_HEADER_PROPERTY_TEXT' => null,
    'COLUMNS' => 2,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'ELEMENT_HEADER_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_WORDS' => 0,
    'LINK_ALL_SHOW' => 'N',
    'LINK_ALL_TEXT' => null,
    'TIMER_USE' => 'N',
    'TIMER_PROPERTY_UNTIL_DATE' => null,
    'TIMER_PROPERTY_DISCOUNT' => null,
    'NAVIGATION_TEMPLATE' => 'lazy.2'
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 1], $arParams['COLUMNS']),
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
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y' && !empty($arParams['TIMER_PROPERTY_UNTIL_DATE']),
        'SECONDS' => [
            'SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'] === 'Y'
        ],
        'SALE' => [
            'SHOW' => $arParams['TIMER_SALE_SHOW'] === 'Y'
        ]
    ]
]);

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

                $arItem['DATA']['HEADER']['SHOW'] = $arVisual['ELEMENT']['HEADER']['SHOW'];
                $arItem['DATA']['HEADER']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }
    }

    if ($arVisual['PREVIEW']['SHOW']) {
        $preview = null;

        if (!empty($arItem['PREVIEW_TEXT']))
            $preview = $arItem['PREVIEW_TEXT'];
        else if (!empty($arItem['DETAIL_TEXT']))
            $preview = $arItem['DETAIL_TEXT'];

        if (!empty($preview)) {
            if ($arVisual['PREVIEW']['TRUNCATE']['USE']) {
                $words = array_filter(
                    explode(' ', Html::stripTags($preview))
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

                    $preview = implode(' ', $words).'...';

                    unset($lastKey);
                }

                unset($words);
            }

            $arItem['DATA']['PREVIEW']['SHOW'] = $arVisual['PREVIEW']['SHOW'];
            $arItem['DATA']['PREVIEW']['VALUE'] = $preview;
        }

        unset($preview);
    }

    if (!empty($arParams['TIMER_PROPERTY_UNTIL_DATE'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['TIMER_PROPERTY_UNTIL_DATE']);

        if (!empty($arProperty)) {
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

                if ($arProperty['DISPLAY_VALUE'] >= Date('Y-m-d H:i:s'))
                    $arItem['DATA']['TIMER']['SHOW'] = $arVisual['TIMER']['SHOW'];

                $arItem['DATA']['TIMER']['VALUES']['UNTIL_DATE'] = $arProperty['DISPLAY_VALUE'];
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
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['TIMER']['VALUES']['SALE_VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }
}

unset($arItem);

if ($arVisual['TIMER']['SHOW']) {
    $arResult['TIMER'] = [
        'COMPONENT' => 'intec.universe:product.timer',
        'TEMPLATE' => 'template.2',
        'PARAMETERS' => [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'MODE' => 'set',
            'QUANTITY' => 1,
            'UNTIL_DATE' => null,
            'TIMER_QUANTITY_OVER' => 'N',
            'TIME_ZERO_HIDE' => 'Y',
            'TIMER_HEADER_SHOW' => 'N',
            'TIMER_HEADER' => null,
            'TIMER_TITLE_SHOW' => 'N',
            'TIMER_QUANTITY_SHOW' => 'N',
            'TIMER_SECONDS_SHOW' => $arParams['TIMER_TIMER_SECONDS_SHOW'],
            'SALE_SHOW' => $arParams['TIMER_SALE_SHOW'],
            'SALE_VALUE' => null,
            'COMPOSITE_FRAME_MODE' => 'A',
            'COMPOSITE_FRAME_TYPE' => 'AUTO',
            'RANDOMIZE_ID' => 'Y'
        ]
    ];
}

if ($arParams['LINK_ALL_SHOW'] === 'Y') {
    $arResult['LINK_ALL_BLOCK'] = [
        'SHOW' => false,
        'TEXT' => !empty($arParams['LINK_ALL_TEXT']) ? trim($arParams['LINK_ALL_TEXT']) : null,
        'LIST_PAGE' => $arParams['LIST_PAGE_URL']
    ];

    if (!empty($arResult['LINK_ALL_BLOCK']['LIST_PAGE']))
        $arResult['LINK_ALL_BLOCK']['LIST_PAGE'] = StringHelper::replaceMacros($arResult['LINK_ALL_BLOCK']['LIST_PAGE'], [
            'SITE_DIR' => SITE_DIR
        ]);

    if (empty($arResult['LINK_ALL_BLOCK']['LIST_PAGE'])) {
        $item = ArrayHelper::getFirstValue($arResult['ITEMS']);

        if (!empty($item['LIST_PAGE_URL']))
            $arResult['LINK_ALL_BLOCK']['LIST_PAGE'] = $item['LIST_PAGE_URL'];

        unset($item);
    }

    if (!empty($arResult['LINK_ALL_BLOCK']['LIST_PAGE']))
        $arResult['LINK_ALL_BLOCK']['SHOW'] = true;
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);