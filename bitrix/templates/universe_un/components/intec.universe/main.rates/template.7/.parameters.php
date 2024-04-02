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

if (empty($arCurrentValues['IBLOCK_ID']))
    return;

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base/forms.php');
else if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite/forms.php');

$arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
    'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
    'ACTIVE' => 'Y'
]))->indexBy('CODE');

$hPropertyTextSingle = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'S' && $value['MULTIPLE'] === 'N' && empty($value['USER_TYPE']))
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertyTextMultiple = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'S' && $value['MULTIPLE'] === 'Y' && empty($value['USER_TYPE']))
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertyCheckbox = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'C' && $value['MULTIPLE'] === 'N')
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertyList = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};

$arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
$arPropertyTextMultiple = $arProperties->asArray($hPropertyTextMultiple);
$arPropertyCheckbox = $arProperties->asArray($hPropertyCheckbox);
$arPropertyList = $arProperties->asArray($hPropertyList);

$arTemplateParameters['PROPERTY_ITEM_HEADER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_ITEM_HEADER'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyTextSingle,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_ADVANTAGES'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_ADVANTAGES'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyTextMultiple,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_MARK_NEW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_MARK_NEW'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyCheckbox,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_MARK_HIT'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_MARK_HIT'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyCheckbox,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_MARK_RECOMMEND'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_MARK_RECOMMEND'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyCheckbox,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_MARK_SHARE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_MARK_SHARE'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyCheckbox,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_PRICE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_PRICE'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyTextSingle,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
    $arTemplateParameters['PROPERTY_PRICE_DISCOUNT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_PRICE_DISCOUNT'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PRICE_CURRENCY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTY_PRICE_CURRENCY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyList,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['TABS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TABS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TABS_USE'] === 'Y') {
    $arTemplateParameters['TABS_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TABS_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TABS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TABS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TABS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['PROPERTY_ITEM_HEADER'])) {
    $arTemplateParameters['ITEM_HEADER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_ITEM_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (
    !empty($arCurrentValues['PROPERTY_MARK_NEW']) ||
    !empty($arCurrentValues['PROPERTY_MARK_HIT']) ||
    !empty($arCurrentValues['PROPERTY_MARK_RECOMMEND']) ||
    !empty($arCurrentValues['PROPERTY_MARK_SHARE'])
) {
    $arTemplateParameters['MARKS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_MARKS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['PROPERTIES_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PROPERTIES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['PROPERTY_ADVANTAGES'])) {
    $arTemplateParameters['ADVANTAGES_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_ADVANTAGES_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
    $arTemplateParameters['PRICE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PRICE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRICE_SHOW'] === 'Y' && !empty($arCurrentValues['PROPERTY_PRICE_DISCOUNT'])) {
        $arTemplateParameters['PRICE_BASE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PRICE_BASE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['PRICE_DISCOUNT_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PRICE_DISCOUNT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['PRICE_DIFFERENCE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_PRICE_DIFFERENCE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}

$arTemplateParameters['SLIDER_LOOP'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_SLIDER_LOOP'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];