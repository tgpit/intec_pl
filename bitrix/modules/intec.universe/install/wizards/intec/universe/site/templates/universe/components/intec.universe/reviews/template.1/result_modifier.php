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
 */

$bAjax = Context::getCurrent()
    ->getRequest()
    ->isAjaxRequest();

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTIES_DISPLAY' => [],
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => '#SITE_DIR#company/consent/',
    'PICTURE_SHOW' => 'N',
    'DATE_SHOW' => 'N',
    'DATE_FORMAT' => 'd.m.Y',
    'PROPERTIES_SHOW' => 'N'
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $bAjax)
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] == 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (Type::isArray($arParams['PROPERTIES_DISPLAY']))
    $arParams['PROPERTIES_DISPLAY'] = array_filter($arParams['PROPERTIES_DISPLAY']);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && !$bAjax ? Properties::get('template-images-lazyload-stub') : null
    ],
    'FORM' => [
        'CONSENT' => [
            'SHOW' => $arParams['CONSENT_SHOW'] === 'Y',
            'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
                'SITE_DIR' => SITE_DIR
            ])
        ],
        'ADD_MODE' => ArrayHelper::fromRange(['disabled', 'active'], $arParams['FORM_ADD_MODE'])
    ],
    'ITEMS' => [
        'SHOW' => $arParams['ITEMS_HIDE'] !== 'Y',
        'PICTURE' => [
            'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
        ],
        'DATE' => [
            'SHOW' => $arParams['DATE_SHOW'] === 'Y',
            'FORMAT' => $arParams['DATE_FORMAT']
        ],
        'PROPERTIES' => [
            'SHOW' => !empty($arParams['PROPERTIES_DISPLAY']) && $arParams['PROPERTIES_SHOW'] === 'Y'
        ]
    ]
];

$hModifier = function (&$arItem) use (&$arParams, &$arVisual) {
    $arData = [
        'PICTURE' => [
            'VALUE' => null
        ],
        'DATE' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'DISPLAY' => [
            'SHOW' => false,
            'VALUES' => []
        ]
    ];

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arData['PICTURE']['VALUE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arData['PICTURE']['VALUE'] = $arItem['DETAIL_PICTURE'];

    if (!empty($arVisual['ITEMS']['DATE']['FORMAT']))
        $arData['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['ITEMS']['DATE']['FORMAT'],
            MakeTimeStamp(
                $arItem['DATE_CREATE'],
                CSite::GetDateFormat()
            )
        );
    else
        $arData['DATE']['VALUE'] = CIBlockFormatProperties::DateFormat(
            'd.m.Y',
            MakeTimeStamp(
                $arItem['DATE_CREATE'],
                CSite::GetDateFormat()
            )
        );

    if (!empty($arData['DATE']['VALUE']))
        $arData['DATE']['SHOW'] = $arVisual['ITEMS']['DATE']['SHOW'];

    if (!empty($arItem['PROPERTIES'])) {
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

                    $arData['DISPLAY']['VALUES'][$arValue['CODE']] = [
                        'NAME' => $arValue['NAME'],
                        'VALUE' => $arValue['DISPLAY_VALUE']
                    ];
                }
            }
        }

        unset($arProperty, $arValue);

        if (!empty($arData['DISPLAY']['VALUES']))
            $arData['DISPLAY']['SHOW'] = $arVisual['ITEMS']['PROPERTIES']['SHOW'];
    }

    $arItem['DATA'] = $arData;
};

if (!empty($arResult['USER_ITEM']))
    $hModifier($arResult['USER_ITEM']);

foreach ($arResult['ITEMS'] as &$arItem)
    $hModifier($arItem);

unset($arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);