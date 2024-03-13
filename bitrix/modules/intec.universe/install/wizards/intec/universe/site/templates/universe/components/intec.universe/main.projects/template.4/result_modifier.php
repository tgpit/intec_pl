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
    'COLUMNS' => 2,
    'TABS_USE' => 'N',
    'TABS_POSITION' => 'center',
    'LINK_USE' => 'N',
    'SITE_SHOW' => 'N',
    'PROPERTY_SITE_NAME' => null,
    'PROPERTY_SITE' => null,
    'ADDITIONAL_SHOW' => 'N',
    'PROPERTY_ADDITIONAL' => null,
    'PROPERTIES_LIST_SHOW' => 'N',
    'PROPERTIES_LIST' => null,
    'RESULT_SHOW' => 'N',
    'PROPERTIES_RESULT' => null,
    'SLIDER_USE' => 'N',
    'SLIDER_NAV' => 'N',
    'ORDER_USE' => 'N',
    'ORDER_FORM_ID' => null,
    'ORDER_FORM_TEMPLATE' => null,
    'ORDER_FORM_FIELD' => null,
    'ORDER_FORM_TITLE' => null,
    'ORDER_FORM_CONSENT' => null,
    'ORDER_BUTTON' => null,
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([2, 3], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'SITE' => [
        'SHOW' => $arParams['SITE_SHOW'] === 'Y'
    ],
    'ADDITIONAL' => [
        'SHOW' => $arParams['ADDITIONAL_SHOW'] === 'Y'
    ],
    'PROPERTIES_LIST' => [
        'SHOW' => $arParams['PROPERTIES_LIST_SHOW'] === 'Y'
    ],
    'RESULT' => [
        'SHOW' => $arParams['RESULT_SHOW'] === 'Y'
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'NAV' => $arParams['SLIDER_USE'] === 'Y' && $arParams['SLIDER_NAV'] === 'Y'
    ],
    'BUTTON_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(ArrayHelper::getValue($arParams, 'LIST_PAGE_URL'), [
            'SITE_DIR' => SITE_DIR
        ])
    ]
];

if (count($arResult['ITEMS']) <= $arVisual['COLUMNS']) {
    $arVisual['SLIDER']['USE'] = $arVisual['SLIDER']['NAV'] = false;
}

if ($arVisual['BUTTON_ALL']['SHOW'] && empty($arVisual['BUTTON_ALL']['LINK'])) {
    $arIblock = Arrays::fromDBResult(CIBlock::GetByID($arParams['IBLOCK_ID']))->getFirst();
    $arMacros = [
        'SITE_DIR' => SITE_DIR,
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'IBLOCK_TYPE_ID' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_CODE' => $arIblock['CODE'],
        'IBLOCK_EXTERNAL_ID' => !empty($arIblock['EXTERNAL_ID']) ? $arIblock['EXTERNAL_ID'] : $arIblock['XML_ID']
    ];
    $arItem = ArrayHelper::getFirstValue($arResult['ITEMS']);
    $arVisual['BUTTON_ALL']['LINK'] = StringHelper::replaceMacros($arItem['LIST_PAGE_URL'], $arMacros);

    unset($arIblock, $arMacros, $arItem);
}

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

$arForm = [
    'USE' => $arParams['ORDER_USE'] === 'Y',
    'ID' => $arParams['ORDER_FORM_ID'],
    'TEMPLATE' => $arParams['ORDER_FORM_TEMPLATE'],
    'FIELD' => $arParams['ORDER_FORM_FIELD'],
    'TITLE' => $arParams['ORDER_FORM_TITLE'],
    'CONSENT' => $arParams['ORDER_FORM_CONSENT'],
    'BUTTON' => $arParams['ORDER_BUTTON']
];

if ($arForm['USE'])
    if (empty($arForm['ID']) || empty($arForm['TEMPLATE']))
        $arForm['USE'] = false;

$arResult['FORM'] = $arForm;

unset($arForm);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'SITE' => [
            'NAME' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_SITE_NAME'], 'VALUE']),
            'LINK' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_SITE'], 'VALUE'])
        ],
        'ADDITIONAL' => [],
        'RESULT' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_RESULT'], 'VALUE']),
        'PROPERTIES' => []
    ];

    if (Type::isArray($arItem['DATA']['RESULT'])) {
        if (ArrayHelper::keyExists('TEXT', $arItem['DATA']['RESULT']))
            $arItem['DATA']['RESULT'] = Html::stripTags($arItem['DATA']['RESULT']['TEXT']);
        else
            $arItem['DATA']['RESULT'] = implode(', ', $arItem['DATA']['RESULT']);
    }

    $arAdditional = ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_ADDITIONAL']]);

    foreach ($arAdditional['VALUE'] as $key => $value) {
        $arItem['DATA']['ADDITIONAL'][] = [
            'VALUE' => $value,
            'DESCRIPTION' => $arAdditional['DESCRIPTION'][$key]
        ];
    }

    unset($arAdditional);

    foreach ($arParams['PROPERTIES_LIST'] as $sProperty) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $sProperty
        ]);
        $arProp = [
            'NAME' => $arProperty['NAME'],
            'VALUE' => $arProperty['VALUE']
        ];

        if (!empty($arProp['VALUE'])) {
            if (Type::isArray($arProp['VALUE'])) {
                if (ArrayHelper::keyExists('TEXT', $arProp['VALUE']))
                    $arProp['VALUE'] = Html::stripTags($arProp['VALUE']['TEXT']);
                else
                    $arProp['VALUE'] = implode(', ', $arProp['VALUE']);

                if (empty($arProp['VALUE']))
                    $arProp['VALUE'] = null;
            }

            $arItem['DATA']['PROPERTIES'][] =  $arProp;
        }

        unset($arProperty, $arProp);
    }
}
