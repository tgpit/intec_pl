<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (empty($arParams['REVIEWS']))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

if (Type::isArray($arParams['PRICE_CODE']))
    $arParams['PRICE_CODE'] = array_filter($arParams['PRICE_CODE']);

$arResult['REVIEWS'] = $arParams['REVIEWS'];

unset($arParams['REVIEWS']);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'DATE' => [
        'SHOW' => $arParams['DATE_SHOW'] === 'Y',
        'SOURCE' => $arParams['DATE_SOURCE'],
        'FORMAT' => $arParams['DATE_FORMAT']
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y'
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PRICE_CODE']),
        'DISCOUNT' => [
            'SHOW' => $arParams['PRICE_DISCOUNT_SHOW'] === 'Y'
        ]
    ]
];

if (empty($arVisual['DATE']['FORMAT']))
    $arVisual['DATE']['FORMAT'] = 'd.m.Y';

$arResult['BLOCKS'] = [
    'HEADER' => [
        'SHOW' => $arParams['HEADER_BLOCK_SHOW'] === 'Y' && !empty($arParams['HEADER_BLOCK_TEXT']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['HEADER_BLOCK_POSITION']),
        'TEXT' => $arParams['~HEADER_BLOCK_TEXT']
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_BLOCK_SHOW'] === 'Y' && !empty($arParams['DESCRIPTION_BLOCK_TEXT']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['DESCRIPTION_BLOCK_POSITION']),
        'TEXT' => $arParams['~DESCRIPTION_BLOCK_TEXT']
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'RATING' => 3.125
    ];

    if ($arVisual['RATING']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], 'rating');

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = Type::toFloat($arProperty['DISPLAY_VALUE']);

                if ($arProperty['DISPLAY_VALUE'] > 0)
                    $arItem['DATA']['RATING'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }
}

unset($arItem);

foreach ($arResult['REVIEWS'] as &$arReview) {
    $isAttached = false;

    $arProperty = ArrayHelper::getValue($arReview['PROPERTIES'], [
        $arParams['PROPERTY_PRODUCTS'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (Type::isArray($arProperty))
            $arProperty = ArrayHelper::getFirstValue($arProperty);

        $arProperty = Type::toInteger($arProperty);

        if ($arProperty > 0) {
            foreach ($arResult['ITEMS'] as &$arItem) {
                if (empty($arItem['REVIEW']) && Type::toInteger($arItem['ID']) === $arProperty) {
                    $isAttached = true;

                    $arItem['REVIEW'] = &$arReview;

                    break;
                }
            }

            unset($arItem);
        }
    }

    unset($arProperty);

    if (!$isAttached)
        continue;

    $arReview['DATA'] = [
        'PREVIEW' => null,
        'DATE' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arParams['PROPERTY_PREVIEW'])) {
        $arProperty = ArrayHelper::getValue($arReview['PROPERTIES'], $arParams['PROPERTY_PREVIEW']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arReview,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arReview['DATA']['PREVIEW'] = $arProperty['DISPLAY_VALUE'];
            }
        }
    }

    if (empty($arReview['DATA']['PREVIEW']) && !empty($arReview['PREVIEW_TEXT']))
        $arReview['DATA']['PREVIEW'] = $arReview['PREVIEW_TEXT'];

    if ($arVisual['DATE']['SHOW']) {
        $sDate = null;

        if (!empty($arVisual['DATE']['SOURCE']))
            $sDate = $arReview[$arVisual['DATE']['SOURCE']];

        if (empty($sDate))
            $sDate = $arReview['DATE_CREATE'];

        $arReview['DATA']['DATE']['SHOW'] = true;
        $arReview['DATA']['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['DATE']['FORMAT'],
            MakeTimeStamp(
                $sDate,
                CSite::GetDateFormat()
            )
        );
    }
}

unset($arItem);

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);