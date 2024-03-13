<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$bAjax = Context::getCurrent()->getRequest()->isAjaxRequest();

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTIES_DISPLAY' => [],
    'PROPERTIES_DISPLAY_RATING' => null,
    'PROPERTY_RATING' => null,
    'RATING_USE' => 'N',
    'FORM_SUBMIT_TEXT' => null,
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => '#SITE_DIR#company/consent/',
    'PICTURE_SHOW' => 'N',
    'PICTURE_VIEW' => 'rounded',
    'DATE_SHOW' => 'N',
    'DATE_FORMAT' => 'd.m.Y',
    'PROPERTIES_SHOW' => 'N',
    'PROPERTIES_RATING_USE' => 'N'
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && Context::getCurrent()->getRequest()->isAjaxRequest())
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (Type::isArray($arCurrentValues['PROPERTIES_DISPLAY']))
    $arParams['PROPERTIES_DISPLAY'] = array_filter($arParams['PROPERTIES_DISPLAY']);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && !$bAjax ? Properties::get('template-images-lazyload-stub') : null
    ],
    'FORM' => [
        'RATING' => [
            'CODE' => $arParams['PROPERTY_RATING'],
            'USE' => $arParams['RATING_USE'] === 'Y' && !empty($arParams['PROPERTY_RATING'])
        ],
        'SUBMIT' => [
            'TEXT' => $arParams['FORM_SUBMIT_TEXT']
        ],
        'AUTHORIZATION' => StringHelper::replaceMacros($arParams['FORM_ACCESS_AUTHORIZATION'], [
            'SITE_DIR' => SITE_DIR
        ]),
        'ADD_MODE' => ArrayHelper::fromRange(['disabled', 'active'], $arParams['FORM_ADD_MODE'])
    ],
    'CONSENT' => [
        'SHOW' => $arParams['CONSENT_SHOW'] === 'Y',
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ],
    'ITEMS' => [
        'HIDE' => $arParams['ITEMS_HIDE'] === 'Y',
        'PICTURE' => [
            'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
            'VIEW' => ArrayHelper::fromRange(['rounded', 'squared'], $arParams['PICTURE_VIEW'])
        ],
        'DATE' => [
            'SHOW' => $arParams['DATE_SHOW'] === 'Y',
            'FORMAT' => $arParams['DATE_FORMAT']
        ],
        'PROPERTIES' => [
            'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y' && !empty($arParams['PROPERTIES_DISPLAY']),
            'RATING' => [
                'USE' => $arParams['PROPERTIES_RATING_USE'] === 'Y' && !empty($arParams['PROPERTIES_DISPLAY_RATING'])
            ]
        ]
    ]
];

if ($arVisual['ITEMS']['PROPERTIES']['RATING']['USE'] && !$arVisual['ITEMS']['PROPERTIES']['SHOW'])
    $arVisual['ITEMS']['PROPERTIES']['RATING']['USE'] = false;

/**
 * Модификатор данных для шаблона
 * @param $arItem
 */
$hModifier = function (&$arItem) use (&$arParams, &$arVisual) {
    $arItem['DATA'] = [
        'DATE' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'RATING' => [
            'USE' => false,
            'VALUE' => null,
            'LIST' => []
        ],
        'DISPLAY' => [
            'SHOW' => false,
            'VALUES' => []
        ]
    ];

    if ($arVisual['ITEMS']['DATE']['SHOW']) {
        if (!empty($arVisual['ITEMS']['DATE']['FORMAT'])) {
            $arItem['DATA']['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
                $arVisual['ITEMS']['DATE']['FORMAT'],
                MakeTimeStamp(
                    $arItem['DATE_CREATE'],
                    CSite::GetDateFormat()
                )
            );
        } else {
            $arItem['DATA']['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
                'd.m.Y',
                MakeTimeStamp(
                    $arItem['DATE_CREATE'],
                    CSite::GetDateFormat()
                )
            );
        }

        if (!empty($arItem['DATA']['DATE']['VALUE']))
            $arItem['DATA']['DATE']['SHOW'] = true;
    }

    if ($arVisual['ITEMS']['PROPERTIES']['SHOW']) {
        foreach ($arItem['PROPERTIES'] as $arProperty) {
            if (ArrayHelper::isIn($arProperty['CODE'], $arParams['PROPERTIES_DISPLAY'])) {
                $arValue = CIBlockFormatProperties::GetDisplayValue(
                    $arItem,
                    $arProperty,
                    false
                );

                if (!empty($arValue['DISPLAY_VALUE'])) {
                    if (Type::isArray($arValue['DISPLAY_VALUE']))
                        $arValue['DISPLAY_VALUE'] = implode(', ', $arValue['DISPLAY_VALUE']);

                    $arItem['DATA']['DISPLAY']['VALUES'][$arValue['CODE']] = [
                        'NAME' => $arValue['NAME'],
                        'VALUE' => $arValue['DISPLAY_VALUE']
                    ];
                }
            }
        }

        unset($arProperty, $arValue);

        if (!empty($arItem['DATA']['DISPLAY']['VALUES']) && $arVisual['ITEMS']['PROPERTIES']['SHOW'])
            $arItem['DATA']['DISPLAY']['SHOW'] = true;

        if ($arVisual['ITEMS']['PROPERTIES']['RATING']['USE']) {
            $arDisplayRating = ArrayHelper::getValue($arItem, [
                'PROPERTIES',
                $arParams['PROPERTIES_DISPLAY_RATING']
            ]);

            if (!empty($arDisplayRating)) {
                if (!empty($arDisplayRating['VALUE']) && !empty($arDisplayRating['VALUES_LIST']))
                    $arItem['DATA']['RATING']['USE'] = true;

                $arItem['DATA']['RATING']['VALUE'] = $arDisplayRating['VALUE_ENUM_ID'];
                $arItem['DATA']['RATING']['LIST'] = $arDisplayRating['VALUES_LIST'];
            }

            if (ArrayHelper::keyExists($arDisplayRating['CODE'], $arItem['DATA']['DISPLAY']['VALUES']))
                unset($arItem['DATA']['DISPLAY']['VALUES'][$arDisplayRating['CODE']]);

            unset($arDisplayRating);
        }
    }
};

if (!empty($arResult['USER_ITEM']))
    $hModifier($arResult['USER_ITEM']);

foreach ($arResult['ITEMS'] as &$arItem) {
    $hModifier($arItem);
}

unset($arItem);

$arResult['VISUAL'] = $arVisual;