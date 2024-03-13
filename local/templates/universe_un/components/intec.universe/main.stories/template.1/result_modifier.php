<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

/** Параметры отображения */

$arParams = ArrayHelper::merge([
    'LIST_VIEW' => 'round',
    'NAVIGATION_BUTTON_POSITION' => 'middle',
    'SHOW_MORE_BUTTON_SHOW' => 'N',
    'SHOW_MORE_BUTTON_TEXT' => null,
    'POPUP_ACTIVE' => 'N',
    'POPUP_SELECTED' => null,
    'LIST_COLUMNS' => '8',
    'PROPERTY_LINK' => null,
    'PROPERTY_BUTTON_TEXT' => null,
    'BUTTON_TEXT' => null,
    'POPUP_TIME' => 5
], $arParams);

$arVisual = [
    'LIST' => [
        'VIEW' => ArrayHelper::fromRange(['rectangle', 'round'], $arParams['LIST_VIEW']),
        'BUTTONS' => [
            'NAVIGATION' => [
                'SHOW' => $arParams['NAVIGATION_BUTTON_SHOW'] === 'Y'
            ],
            'MORE' => [
                'SHOW' => $arParams['SHOW_MORE_BUTTON_SHOW'] === 'Y' && !empty($arParams['SHOW_MORE_BUTTON_TEXT']),
                'TEXT' => $arParams['SHOW_MORE_BUTTON_TEXT']
            ]
        ]
    ],
    'POPUP' => [
        'SHOW' => $arParams['POPUP_ACTIVE'] === 'Y' && !empty($arParams['POPUP_SELECTED']),
        'SELECTED' => $arParams['POPUP_SELECTED'],
        'TIME' => Type::toInteger($arParams['POPUP_TIME'])
    ],
    'COLUMNS' => ArrayHelper::fromRange(['5', '6', '7', '8'], $arParams['LIST_COLUMNS'])
];

if ($arVisual['POPUP']['TIME'] <= 0)
    $arVisual['POPUP']['TIME'] = 1;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'TITLE' => [
            'POSITION' => 'bottom'
        ],
        'BUTTON' => [
            'SHOW' => false,
            'LINK' => null,
            'TEXT' => null
        ],
        'DESCRIPTION' => [
            'TEXT' => null
        ],
        'PICTURE' => [
            'SRC' => SITE_TEMPLATE_PATH.'/images/picture.missing.png',
        ]
    ];

    if (!empty($arParams['PROPERTY_LINK'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_LINK'],
            'VALUE'
        ]);

        if (!empty($arProperty) && !Type::isArray($arProperty)) {
            $arItem['DATA']['BUTTON']['LINK'] = StringHelper::replaceMacros($arProperty, [
                'SITE_DIR' => SITE_DIR
            ]);
            $arItem['DATA']['BUTTON']['SHOW'] = true;
        }
    }

    if (!empty($arParams['PROPERTY_BUTTON_TEXT'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_BUTTON_TEXT'],
            'VALUE'
        ]);

        if (!empty($arProperty) && !Type::isArray($arProperty))
            $arItem['DATA']['BUTTON']['TEXT'] = $arProperty;

        if (empty($arItem['DATA']['BUTTON']['TEXT']))
            $arItem['DATA']['BUTTON']['TEXT'] = $arParams['BUTTON_TEXT'];
    }

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['DATA']['PICTURE']['SRC'] = $arItem['PREVIEW_PICTURE']['SRC'];
    } else if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['DATA']['PICTURE']['SRC'] = $arItem['DETAIL_PICTURE']['SRC'];
    }
}

$arResult['VISUAL'] = $arVisual;