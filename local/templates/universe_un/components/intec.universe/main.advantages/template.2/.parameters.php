<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

CModule::IncludeModule("iblock");
CModule::includeModule('intec.core');

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [
    'LINE_COUNT' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_LINE_COUNT'),
        'TYPE' => 'LIST',
        'VALUES' => [
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5
        ],
        'DEFAULT' => 4
    ]
];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'number' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_VIEW_NUMBER'),
        'icon' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_VIEW_ICON')
    ],
    'DEFAULT' => 'number'
];

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['LINK_PROPERTY_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_LINK_PROPERTY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID']) && $arCurrentValues['LINK_PROPERTY_USE'] === 'Y') {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
        ['SORT' => 'ASC'],
        [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]
    ))->indexBy('ID');

    $hPropertyText = function ($sKey, $arProperty) {
        if (!empty($arProperty['CODE']))
            if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['MULTIPLE'] === 'N')
                return[
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                ];

        return ['skip' => true];
    };

    $arPropertyText = $arProperties->asArray($hPropertyText);

    $arTemplateParameters['LINK_PROPERTY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_2_LINK_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}