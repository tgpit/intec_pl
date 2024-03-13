<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arIBlocksType = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksType,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arSharesIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) use (&$arCurrentValues) {
        if ($arIBlock['IBLOCK_TYPE_ID'] === $arCurrentValues['IBLOCK_TYPE'])
            return [
                'key' => $arIBlock['ID'],
                'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
            ];

        return ['skip' => true];
    });
else
    $arSharesIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) {
        return [
            'key' => $arIBlock['ID'],
            'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
        ];
    });

$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arSharesIBlocks,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);

    $arTemplateParameters['ELEMENT_ID_ENTER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_ELEMENT_ID_ENTER'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ELEMENT_ID_ENTER'] === 'Y') {
        $arAllElements = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]))->indexBy('ID');

        $arSharesElements = $arAllElements->asArray(function ($key, $arIBlock) use (&$arCurrentValues) {
            if ($arIBlock['IBLOCK_ID'] === $arCurrentValues['IBLOCK_ID'])
                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];

            return ['skip' => true];
        });

        $arTemplateParameters['ELEMENT_ID'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_ELEMENT_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arSharesElements,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['DISCOUNT_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DISCOUNT_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];
    if ($arCurrentValues['DISCOUNT_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_DISCOUNT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_PROPERTY_DISCOUNT'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y',
        ];
        $arTemplateParameters['DISCOUNT_MINUS_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DISCOUNT_MINUS_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ];
    }

    $arTemplateParameters['DATE_SHOW_FROM'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_SHOW_FROM'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'property' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_FROM_PROPERTY'),
            'date' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_FROM_DATES'),
        ],
        'DEFAULT' => 'property',
        'REFRESH' => 'Y'
    ];
    if ($arCurrentValues['DATE_SHOW_FROM'] === 'property') {
        $arTemplateParameters['PROPERTY_DATE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_PROPERTY_DATE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y',
        ];
    }

    if ($arCurrentValues['DATE_SHOW_FROM'] === 'date') {
        $arTemplateParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
            Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_FORMAT'),
            'VISUAL'
        );
        $arTemplateParameters['DATE_ONLY_ONE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_DATE_ONLY_ONE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ];
    }

    $arTemplateParameters['TIMER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];
    if ($arCurrentValues['TIMER_SHOW'] === 'Y') {
        $arTemplateParameters['TIMER_SECONDS_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_SECONDS_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ];

        $arTemplateParameters['TIMER_END_HIDE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TIMER_END_HIDE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ];
    }

    $arTemplateParameters['TEXT_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TEXT_USE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'preview' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TEXT_USE_PREVIEW'),
            'detail' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_TEXT_USE_DETAIL'),
        ],
        'DEFAULT' => 'preview'
    ];

    $arTemplateParameters['ALL_TEXT_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_ALL_TEXT_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arTemplateParameters['BUTTON_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_BUTTON_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
    ];

    if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
        $arTemplateParameters['BUTTON_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_BUTTON_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_MAIN_WIDGET_CATALOG_SHARES_1_BUTTON_TEXT_DEFAULT'),
        ];
    }
}