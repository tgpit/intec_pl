<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'));


$arTemplateParameters = [];

/*$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];*/

$arTemplateParameters['BACKGROUND_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BACKGROUND_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BACKGROUND_SHOW'] === 'Y') {
    $arTemplateParameters['BACKGROUND_COLOR'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BACKGROUND_COLOR'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#FBFCFD'
    ];

    $arTemplateParameters['THEME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_THEME'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'light' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_THEME_LIGHT'),
            'dark' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_THEME_DARK')
        ],
        'DEFAULT' => 'light'
    ];
}

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
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_PROPERTY_NUMBER'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyNumber,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['PROPERTY_MAX_NUMBER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_PROPERTY_MAX_NUMBER'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyNumber,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_SHOW'] === 'Y' || $arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arTemplateParameters['BUTTON_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BUTTON_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
        $arTemplateParameters['BUTTON_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BUTTON_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BUTTON_TEXT_DEFAULT'),
        ];

        $arTemplateParameters['BUTTON_LINK'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BUTTON_LINK'),
            'TYPE' => 'STRING'
        ];

        $arTemplateParameters['BUTTON_ALIGN'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_BUTTON_ALIGN'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_ALIGN_LEFT'),
                'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_ALIGN_CENTER'),
                'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_34_ALIGN_RIGHT')
            ],
            'DEFAULT' => 'left'
        ];
    }
}