<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_CATEGORY' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PICTURE_SHOW' => 'N',
    'CATEGORY_SHOW' => 'N',
    'DATE_SHOW' => 'N',
    'DATE_TYPE' => 'DATE_ACTIVE_FROM',
    'PREVIEW_SHOW' => 'N',
    'FOOTER_SHOW' => 'N',
    'FOOTER_POSITION' => 'center',
    'FOOTER_BUTTON_SHOW' => 'N',
    'FOOTER_BUTTON_TEXT' => null
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'DATE' => [
        'TYPE' => ArrayHelper::fromRange([
            'DATE_ACTIVE_FROM',
            'DATE_CREATE',
            'TIMESTAMP_X',
            'DATE_ACTIVE_TO'
        ], $arParams['DATE_TYPE'])
    ],
    'CATEGORY' => [
        'SHOW' => $arParams['CATEGORY_SHOW'] === 'Y' && !empty($arParams['PROPERTY_CATEGORY'])
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'DATE' => null,
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => $arItem['PREVIEW_TEXT']
        ],
        'CATEGORY' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arParams['PROPERTY_CATEGORY'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_CATEGORY'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['CATEGORY']['VALUE'] = $arProperty;
        }

        if (!empty($arItem['DATA']['CATEGORY']['VALUE']))
            $arItem['DATA']['CATEGORY']['SHOW'] = $arVisual['CATEGORY']['SHOW'];

        unset($arProperty);
    }

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['SHOW'] = $arVisual['PREVIEW']['SHOW'];

    if (!empty($arParams['DATE_FORMAT'])) {
        if (!empty($arItem['TIMESTAMP_X'])) {
            $arItem['TIMESTAMP_X_FORMATTED'] = CIBlockFormatProperties::DateFormat(
                $arParams['DATE_FORMAT'],
                MakeTimeStamp(
                    $arItem['TIMESTAMP_X'],
                    CSite::GetDateFormat()
                )
            );
        }
    }

    if (!empty($arItem[$arParams['DATE_TYPE'].'_FORMATTED']))
        $arItem['DATA']['DATE'] = $arItem[$arParams['DATE_TYPE'].'_FORMATTED'];
    else
        $arItem['DATA']['DATE'] = $arItem['DATE_CREATE_FORMATTED'];
}

$arFooter = [
    'SHOW' => $arParams['FOOTER_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['FOOTER_POSITION']),
    'BUTTON' => [
        'SHOW' => $arParams['FOOTER_BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['FOOTER_BUTTON_TEXT'],
        'LINK' => null
    ]
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arFooter['BUTTON']['LINK'] = StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR,
        'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
        'TEMPLATE_PATH' => $this->GetFolder().'/'
    ]);

if (empty($arFooter['BUTTON']['TEXT']) || empty($arFooter['BUTTON']['LINK']))
    $arFooter['BUTTON']['SHOW'] = false;

if (!$arFooter['BUTTON']['SHOW'])
    $arFooter['SHOW'] = false;

$arResult['BLOCKS']['FOOTER'] = $arFooter;

unset($arFooter);

$arResult['VISUAL'] = ArrayHelper::merge(
    $arResult['VISUAL'],
    $arVisual
);

unset($arVisual);