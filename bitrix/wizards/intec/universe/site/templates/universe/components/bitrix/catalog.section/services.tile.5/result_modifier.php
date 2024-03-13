<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bAjax = Context::getCurrent()->getRequest()->isAjaxRequest();

$arParams = ArrayHelper::merge([
    'PROPERTY_PRICE' => null,
    'PROPERTY_PRICE_OLD' => null,
    'PROPERTY_CURRENCY' => null,
    'PROPERTY_FORMAT' => null,
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'PICTURE_SHOW' => 'N',
    'PROPERTIES_SHOW' => 'N',
    'PROPERTIES_COUNT' => null,
    'PREVIEW_SHOW' => 'N',
    'PRICE_OLD_SHOW' => 'N',
    'FORMAT_USE' => 'N',
    'WIDE' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && !$bAjax ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && !$bAjax ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['COLUMNS']),
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE']),
        'OLD' => $arParams['PRICE_OLD_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE_OLD']),
        'FORMAT' => $arParams['FORMAT_USE'] === 'Y' && !empty($arParams['PROPERTY_FORMAT'])
    ],
    'PROPERTIES' => [
        'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y',
        'COUNT' => Type::toInteger($arParams['PROPERTIES_COUNT'])
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER']
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER']
        ],
        'LAZY' => [
            'BUTTON' => $arParams['LAZY_LOAD'] === 'Y',
            'SCROLL' => $arParams['LOAD_ON_SCROLL'] === 'Y'
        ]
    ]
];

if (empty($arResult['NAV_STRING'])) {
    $arVisual['NAVIGATION']['TOP']['SHOW'] = false;
    $arVisual['NAVIGATION']['BOTTOM']['SHOW'] = false;
}

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PRICE' => [
            'CURRENT' => [
                'SHOW' => false,
                'VALUE' => null
            ],
            'OLD' => [
                'SHOW' => false,
                'VALUE' => null
            ],
            'CURRENCY' => null,
            'FORMAT' => [
                'USE' => false,
                'VALUE' => null
            ]
        ]
    ];

    if (!empty($arParams['PROPERTY_PRICE'])) {
        if (ArrayHelper::keyExists($arParams['PROPERTY_PRICE'], $arItem['DISPLAY_PROPERTIES']))
            unset($arItem['DISPLAY_PROPERTIES'][$arParams['PROPERTY_PRICE']]);

        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $arProperty, false);

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['CURRENT']['VALUE'] = $arProperty['DISPLAY_VALUE'];
                $arItem['DATA']['PRICE']['CURRENT']['SHOW'] = $arVisual['PRICE']['SHOW'];
            }
        }
    }

    if (!empty($arParams['PROPERTY_PRICE_OLD'])) {
        if (ArrayHelper::keyExists($arParams['PROPERTY_PRICE_OLD'], $arItem['DISPLAY_PROPERTIES']))
            unset($arItem['DISPLAY_PROPERTIES'][$arParams['PROPERTY_PRICE_OLD']]);

        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_OLD']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $arProperty, false);

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['OLD']['VALUE'] = $arProperty['DISPLAY_VALUE'];
                $arItem['DATA']['PRICE']['OLD']['SHOW'] = $arVisual['PRICE']['OLD'];
            }
        }
    }

    if (!empty($arParams['PROPERTY_CURRENCY'])) {
        if (ArrayHelper::keyExists($arParams['PROPERTY_CURRENCY'], $arItem['DISPLAY_PROPERTIES']))
            unset($arItem['DISPLAY_PROPERTIES'][$arParams['PROPERTY_CURRENCY']]);

        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_CURRENCY']
        ]);

        if (!empty($arProperty['VALUE'])) {
            if (Type::isArray($arProperty['VALUE']))
                $arProperty['VALUE'] = ArrayHelper::getFirstValue($arProperty['VALUE']);

            $arItem['DATA']['PRICE']['CURRENCY'] = $arProperty['VALUE'];
        }
    }

    if (!empty($arParams['PROPERTY_FORMAT'])) {
        if (ArrayHelper::keyExists($arParams['PROPERTY_FORMAT'], $arItem['DISPLAY_PROPERTIES']))
            unset($arItem['DISPLAY_PROPERTIES'][$arParams['PROPERTY_FORMAT']]);

        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_FORMAT']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $arProperty, false);

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['FORMAT']['VALUE'] = $arProperty['DISPLAY_VALUE'];
                $arItem['DATA']['PRICE']['FORMAT']['USE'] = $arVisual['PRICE']['FORMAT'];
            }
        }
    }

    if ($arItem['DATA']['PRICE']['FORMAT']['USE']) {
        if (!empty($arItem['DATA']['PRICE']['CURRENT']['VALUE']))
            $arItem['DATA']['PRICE']['CURRENT']['VALUE'] = trim(StringHelper::replaceMacros(
                $arItem['DATA']['PRICE']['FORMAT']['VALUE'], [
                    'VALUE' => $arItem['DATA']['PRICE']['CURRENT']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ]
            ));

        if (!empty($arItem['DATA']['PRICE']['OLD']['VALUE']))
            $arItem['DATA']['PRICE']['OLD']['VALUE'] = trim(StringHelper::replaceMacros(
                $arItem['DATA']['PRICE']['FORMAT']['VALUE'], [
                    'VALUE' => $arItem['DATA']['PRICE']['OLD']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ]
            ));
    } else {
        if (!empty($arItem['DATA']['PRICE']['CURRENT']['VALUE']))
            $arItem['DATA']['PRICE']['CURRENT']['VALUE'] = trim(StringHelper::replaceMacros(
                '#VALUE# #CURRENCY#', [
                    'VALUE' => $arItem['DATA']['PRICE']['CURRENT']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ]
            ));

        if (!empty($arItem['DATA']['PRICE']['OLD']['VALUE']))
            $arItem['DATA']['PRICE']['OLD']['VALUE'] = trim(StringHelper::replaceMacros(
                '#VALUE# #CURRENCY#', [
                    'VALUE' => $arItem['DATA']['PRICE']['OLD']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ]
            ));
    }
}

unset($arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);