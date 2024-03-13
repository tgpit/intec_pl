<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_BACKGROUND' => null,
    'PROPERTY_TITLE' => null,
    'PROPERTY_LINK' => null,
    'PROPERTY_VIDEO' => null,
    'BACKGROUND_SHOW' => 'N',
    'TITLE_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'BUTTON_SHOW' => 'N',
    'BUTTON_BLANK' => 'N',
    'BUTTON_TEXT' => null,
    'PICTURE_SHOW' => 'N',
    'PICTURE_SIZE' => 'cover',
    'POSITION_HORIZONTAL' => 'center',
    'POSITION_VERTICAL' => 'center',
    'VIDEO_SHOW' => 'N',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'BACKGROUND' => [
        'SHOW' => !empty($arParams['PROPERTY_BACKGROUND']) && $arParams['BACKGROUND_SHOW'] === 'Y'
    ],
    'TITLE' => [
        'SHOW' => !empty($arParams['PROPERTY_TITLE']) && $arParams['TITLE_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => !empty($arResult['ITEM']['PREVIEW_TEXT']) && $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'BUTTON' => [
        'SHOW' => !empty($arParams['PROPERTY_LINK']) && $arParams['BUTTON_SHOW'] === 'Y',
        'BLANK' => $arParams['BUTTON_BLANK'] === 'Y',
        'TEXT' => $arParams['BUTTON_TEXT']
    ],
    'PICTURE' => [
        'SHOW' => $arResult['PICTURE'] && $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => ArrayHelper::fromRange(['auto', 'cover', 'contain'], $arParams['PICTURE_SIZE']),
        'POSITION' => [
            'HORIZONTAL' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['POSITION_HORIZONTAL']),
            'VERTICAL' => ArrayHelper::fromRange(['center', 'top', 'bottom'], $arParams['POSITION_VERTICAL'])
        ]
    ],
    'VIDEO' => [
        'SHOW' => !empty($arParams['PROPERTY_VIDEO']) && $arParams['VIDEO_SHOW'] === 'Y'
    ]
];

if ($arVisual['BACKGROUND']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_BACKGROUND'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['BACKGROUND'] = CFile::GetFileArray($property);
    } else {
        $arVisual['BACKGROUND']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['TITLE']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_TITLE'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['TITLE'] = $property;
    } else {
        $arVisual['TITLE']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['BUTTON']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_LINK'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['LINK'] = $property;
    } else {
        $arVisual['BUTTON']['SHOW'] = false;
    }

    unset($property);
}

if ($arVisual['VIDEO']['SHOW']) {
    $property = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_VIDEO'],
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        $arResult['VIDEO'] = $property;
    } else {
        $arVisual['VIDEO']['SHOW'] = false;
    }

    unset($property);
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);