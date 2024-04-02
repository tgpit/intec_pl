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

$arTemplateParameters = [];

$arTemplateParameters['BACKGROUND_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_BACKGROUND_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BACKGROUND_SHOW'] === 'Y') {
    $arTemplateParameters['BACKGROUND_COLOR'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_BACKGROUND_COLOR'),
        'TYPE' => 'STRING'
    ];

    $arTemplateParameters['THEME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_THEME'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'light' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_THEME_LIGHT'),
            'dark' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_THEME_DARK')
        ],
        'DEFAULT' => 'light'
    ];
}

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4',
        5 => '5'
    ],
    'DEFAULT' => 4
];

$arTemplateParameters['COLUMNS_MOBILE'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_COLUMNS_MOBILE'),
    'TYPE' => 'LIST',
    'VALUES' => array(
        1 => '1',
        2 => '2'
    ),
    'DEFAULT' => '2'
);

$arTemplateParameters['NUMBER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_NUMBER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NUMBER_SHOW'] === 'Y') {
    $arTemplateParameters['NUMBER_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_NUMBER_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyNumber = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'N' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyNumber = $arProperties->asArray($hPropertyNumber);

    $arTemplateParameters['PROPERTY_NUMBER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_PROPERTY_NUMBER'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyNumber,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PREVIEW_SHOW'] === 'Y') {
    $arTemplateParameters['PREVIEW_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_PREVIEW_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_31_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}