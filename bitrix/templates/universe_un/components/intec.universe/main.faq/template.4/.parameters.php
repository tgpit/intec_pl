<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');
    $hPropertyCheckbox = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['LIST_TYPE'] == 'C')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };
    $arPropertyCheckbox = $arProperties->asArray($hPropertyCheckbox);
    $arTemplateParameters['PROPERTY_EXPANDED'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_PROPERTY_EXPANDED'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckbox,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['LIMITED_ITEMS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_LIMITED_ITEMS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LIMITED_ITEMS_USE'] === 'Y') {
    $arTemplateParameters['LIMITED_ITEMS_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_LIMITED_ITEMS_COUNT'),
        'TYPE' => 'LIST',
        'VALUES' => [
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7'
        ],
        'DEFAULT' => 3,
    ];
}

$arTemplateParameters['SEE_ALL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_SEE_ALL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SEE_ALL_SHOW'] === 'Y') {
    $arTemplateParameters['SEE_ALL_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_SEE_ALL_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_POSITION_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
    $arTemplateParameters['SEE_ALL_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_SEE_ALL_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_SEE_ALL_TEXT_DEFAULT')
    ];
    $arTemplateParameters['SEE_ALL_URL'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_SEE_ALL_URL'),
        'TYPE' => 'STRING'
    ];
}