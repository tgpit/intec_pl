<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

$arResult['DATA'] = [
    'MARKS' => [
        'HIT' => null,
        'NEW' => null,
        'RECOMMEND' => null,
        'SHARE' => null
    ],
    'ARTICLE' => [
        'SHOW' => false,
        'VALUE' => null
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'PARAMETERS' => [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ELEMENT_ID' => $arResult['ID'],
            'ELEMENT_CODE' => $arResult['CODE'],
            'MAX_VOTE' => 5,
            'VOTE_NAMES' => [
                0 => '1',
                1 => '2',
                2 => '3',
                3 => '4',
                4 => '5',
            ],
            'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'],
            'SHOW_RATING' => 'Y',
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME']
        ]
    ]
];

if (!empty($arParams['PROPERTY_MARKS_HIT'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_HIT']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['MARKS']['HIT'] = true;
        }
    }
}

if (!empty($arParams['PROPERTY_MARKS_NEW'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_NEW']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['MARKS']['NEW'] = true;
        }
    }
}

if (!empty($arParams['PROPERTY_MARKS_RECOMMEND'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_RECOMMEND']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['MARKS']['RECOMMEND'] = true;
        }
    }
}

if (!empty($arParams['PROPERTY_MARKS_SHARE'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_MARKS_SHARE']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['MARKS']['SHARE'] = true;
        }
    }
}

if (!empty($arParams['PROPERTY_ARTICLE'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_ARTICLE']
    ]);

    if (!empty($arProperty)) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['ARTICLE']['SHOW'] = $arVisual['ARTICLE']['SHOW'];
            $arResult['DATA']['ARTICLE']['VALUE'] = $arProperty['DISPLAY_VALUE'];
        }
    }
}