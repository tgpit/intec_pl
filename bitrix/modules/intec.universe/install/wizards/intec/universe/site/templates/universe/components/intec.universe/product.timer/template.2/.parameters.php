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

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyString = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'S')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyStrings = $arProperties->asArray($hPropertyString);
}

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['TIMER_QUANTITY_OVER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_TIMER_QUANTITY_OVER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['TIMER_TITLE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_TIMER_TITLE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_TITLE_SHOW'] === 'Y') {
    $arTemplateParameters['TIMER_TITLE_ENTER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_TIMER_TITLE_ENTER'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['TIMER_TITLE_ENTER'] === 'Y') {
        $arTemplateParameters['TIMER_TITLE_VALUE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_TIMER_TITLE_VALUE'),
            'TYPE' => 'STRING',
        ];
    }
}

$arTemplateParameters['SALE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SALE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SALE_SHOW'] === 'Y') {
    $arTemplateParameters['SALE_VALUE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SALE_VALUE'),
        'TYPE' => 'STRING',
        'VALUES' => null
    ];

    $arTemplateParameters['SALE_HEADER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SALE_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SALE_HEADER_SHOW'] === 'Y') {
        $arTemplateParameters['SALE_HEADER_VALUE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SALE_HEADER_VALUE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_2_SALE_HEADER_VALUE_DEFAULT')
        ];
    }
}