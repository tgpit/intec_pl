<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
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
    'PROPERTY_RATING' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'LINK_TEXT' => null,
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_WORDS' => 0,
    'RATING_SHOW' => 'N',
    'ACTIVE_DATE_SHOW' => 'N',
    'ACTIVE_DATE_FORMAT' => 'd.m.Y',
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null,
    'BUTTON_ALL_POSITION' => 'left',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PREVIEW' => [
        'TRUNCATE' => [
            'USE' => $arParams['PREVIEW_TRUNCATE_USE'] === 'Y',
            'WORDS' => Type::toInteger($arParams['PREVIEW_TRUNCATE_WORDS'])
        ]
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y' && !empty($arParams['PROPERTY_RATING']),
        'MAX' => intval($arParams['RATING_MAX'])
    ],
    'ACTIVE_DATE' => [
        'SHOW' => $arParams['ACTIVE_DATE_SHOW'] === 'Y'
    ],
    'SLIDER' => [
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ],
    'BUTTON_SHOW_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(
            $arParams['LIST_PAGE_URL'],
            $arMacros
        )
    ]
];

if ($arVisual['PREVIEW']['TRUNCATE']['WORDS'] < 1)
    $arVisual['PREVIEW']['TRUNCATE']['USE'] = false;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null,
        ],
        'RATING' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arItem['PREVIEW_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];
    else if (!empty($arItem['DETAIL_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['DETAIL_TEXT'];

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['VALUE'] = trim($arItem['DATA']['PREVIEW']['VALUE']);

    if ($arVisual['PREVIEW']['TRUNCATE']['USE'] && !empty($arItem['DATA']['PREVIEW']['VALUE'])) {
        $words = array_filter(
            explode(' ', Html::stripTags($arItem['DATA']['PREVIEW']['VALUE']))
        );

        if (count($words) > $arVisual['PREVIEW']['TRUNCATE']['WORDS']) {
            $words = ArrayHelper::slice($words, 0, $arVisual['PREVIEW']['TRUNCATE']['WORDS']);

            $lastKey = $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1];

            if (ArrayHelper::isIn(StringHelper::cut($lastKey, -1), ['.', ',', ':', ';']))
                $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1] = StringHelper::cut(
                    $lastKey,
                    0,
                    StringHelper::length($lastKey) - 1
                );

            $arItem['DATA']['PREVIEW']['VALUE'] = implode(' ', $words) . '...';

            unset($lastKey);
        }

        unset($words);
    }

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['SHOW'] = true;

    if (!empty($arParams['PROPERTY_RATING'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_RATING'],
            'VALUE_XML_ID'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['RATING']['SHOW'] = $arVisual['RATING']['SHOW'];
            $arItem['DATA']['RATING']['VALUE'] = $arProperty;
        }

        unset($arProperty);
    }

    if (strlen($arItem['ACTIVE_FROM']) > 0)
        $arItem['DISPLAY_ACTIVE_FROM'] = CIBlockFormatProperties::DateFormat(
            $arParams['ACTIVE_DATE_FORMAT'],
            MakeTimeStamp(
                $arItem['ACTIVE_FROM'],
                CSite::GetDateFormat()
            )
        );
    else if (strlen($arItem['DATE_CREATE']) > 0)
        $arItem['DISPLAY_ACTIVE_FROM'] = CIBlockFormatProperties::DateFormat(
            $arParams['ACTIVE_DATE_FORMAT'],
            MakeTimeStamp(
                $arItem['DATE_CREATE'],
                CSite::GetDateFormat()
            )
        );
    else
        $arItem['DISPLAY_ACTIVE_FROM'] = '';
}

unset($arItem);

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['SLIDER']['AUTO']['TIME'] < 1)
    $arVisual['SLIDER']['AUTO']['TIME'] = 10000;

if ($arVisual['RATING']['SHOW']) {
    $arResult['RATING'] = Arrays::fromDBResult(CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CODE' => $arParams['PROPERTY_RATING']
    ]))->indexBy('XML_ID')->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => $value['VALUE']
        ];
    });
}

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);

unset($arVisual);

if (!empty($arParams['LIST_PAGE_URL']))
    $arButtons['SHOW_ALL']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

unset($arMacros);