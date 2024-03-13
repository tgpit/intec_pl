<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else
    $bBase = false;

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertyTextSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyFileSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'F' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] !== 'Y')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyFileSingle = $arProperties->asArray($hPropertyFileSingle);

    $arTemplateParameters['PROPERTY_URL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_PROPERTY_URL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ICON'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_PROPERTY_ICON'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFileSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BASKET_USE'] === 'Y' || $arCurrentValues['COMPARE_USE'] === 'Y') {
        $mode = $arCurrentValues['MODE'] === 'code' ? 'CODE' : 'ID';

        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ];

        if ($arCurrentValues['MODE'] === 'code') {
            $filter['CODE'] = $arCurrentValues['ELEMENTS'];
        } else {
            $filter['ID'] = $arCurrentValues['ELEMENTS'];
        }

        $elements = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], $filter))->indexBy($mode);
    }

    $arTemplateParameters['BASKET_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_BASKET_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BASKET_USE'] === 'Y') {
        $arTemplateParameters['BASKET_ELEMENT'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_BASKET_ELEMENT'),
            'TYPE' => 'LIST',
            'VALUES' => $elements->asArray(function ($key, $value) use (&$arCurrentValues) {
                if (
                    isset($arCurrentValues['DELAY_ELEMENT']) && $arCurrentValues['DELAY_USE'] === 'Y' && $value['ID'] === $arCurrentValues['DELAY_ELEMENT'] ||
                    isset($arCurrentValues['COMPARE_ELEMENT']) && $arCurrentValues['COMPARE_USE'] === 'Y' && $value['ID'] === $arCurrentValues['COMPARE_ELEMENT']
                )
                    return ['skip' => true];

                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if ($bBase) {
            $arTemplateParameters['DELAY_USE'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_DELAY_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['DELAY_USE'] === 'Y') {
                $arTemplateParameters['DELAY_ELEMENT'] = [
                    'PARENT' => 'DATA_SOURCE',
                    'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_DELAY_ELEMENT'),
                    'TYPE' => 'LIST',
                    'VALUES' => $elements->asArray(function ($key, $value) use (&$arCurrentValues) {
                        if (
                            isset($arCurrentValues['BASKET_ELEMENT']) && $arCurrentValues['BASKET_USE'] === 'Y' && $value['ID'] === $arCurrentValues['BASKET_ELEMENT'] ||
                            isset($arCurrentValues['COMPARE_ELEMENT']) && $arCurrentValues['COMPARE_USE'] === 'Y' && $value['ID'] === $arCurrentValues['COMPARE_ELEMENT']
                        )
                            return ['skip' => true];

                        return [
                            'key' => $key,
                            'value' => '['.$key.'] '.$value['NAME']
                        ];
                    }),
                    'ADDITIONAL_VALUES' => 'Y',
                    'REFRESH' => 'Y'
                ];
            }
        }
    }

    $arTemplateParameters['COMPARE_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_COMPARE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['COMPARE_USE'] === 'Y') {
        $arTemplateParameters['COMPARE_IBLOCK_TYPE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_COMPARE_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => CIBlockParameters::GetIBlockTypes(),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (empty($arCurrentValues['COMPARE_IBLOCK_TYPE'])) {
            $compareIBlocksFilter = [
                'ACTIVE' => 'Y',
                'SITE_ID' => $sSite
            ];
        } else {
            $compareIBlocksFilter = [
                'ACTIVE' => 'Y',
                'SITE_ID' => $sSite,
                'TYPE' => $arCurrentValues['COMPARE_IBLOCK_TYPE']
            ];
        }

        $compareIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], $compareIBlocksFilter))
            ->indexBy('ID')
            ->asArray(function ($key, $value) {
                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];
            });

        $arTemplateParameters['COMPARE_IBLOCK_ID'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_COMPARE_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $compareIBlocks,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['COMPARE_IBLOCK_ID'])) {
            $arTemplateParameters['COMPARE_NAME'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_COMPARE_NAME'),
                'TYPE' => 'STRING',
                'DEFAULT' => 'compare'
            ];
            $arTemplateParameters['COMPARE_ELEMENT'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_COMPARE_ELEMENT'),
                'TYPE' => 'LIST',
                'VALUES' => $elements->asArray(function ($key, $value) use (&$arCurrentValues) {
                    if (isset($arCurrentValues['BASKET_ELEMENT']) && $arCurrentValues['BASKET_USE'] === 'Y' && ($value['ID'] === $arCurrentValues['BASKET_ELEMENT'] || (isset($arCurrentValues['DELAY_ELEMENT']) && $arCurrentValues['DELAY_USE'] === 'Y' && $value['ID'] === $arCurrentValues['DELAY_ELEMENT'])))
                        return ['skip' => true];

                    return [
                        'key' => $key,
                        'value' => '['.$key.'] '.$value['NAME']
                    ];
                }),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }

    if ($arCurrentValues['BASKET_USE'] === 'Y' || $arCurrentValues['COMPARE_USE'] === 'Y') {
        unset($elements);
    }

    $arTemplateParameters['NAME_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_NAME_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['PANEL_FIXED'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_PANEL_FIXED'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if (!empty($arCurrentValues['PROPERTY_ICON'])) {
        $arTemplateParameters['SVG_COLOR_MODE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_SVG_COLOR_MODE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'fill' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_SVG_COLOR_MODE_FILL'),
                'stroke' => Loc::getMessage('C_MAIN_PANEL_TEMPLATE_1_SVG_COLOR_MODE_STROKE')
            ],
            'DEFAULT' => 'stroke'
        ];
    }
}