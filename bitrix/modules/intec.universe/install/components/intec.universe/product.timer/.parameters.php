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

if (!Loader::includeModule('catalog'))
    return;

$arIBlock = null;
$arFilter = [
    'ACTIVE' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], $arFilter))->indexBy('ID');

if (!empty($arCurrentValues['IBLOCK_ID']))
    $arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arPropertiesSelect = [
    'SORT' => [
        'SORT' => 'ASC'
    ],
    'FILTER' => [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]
];

$arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
    $arPropertiesSelect['SORT'],
    $arPropertiesSelect['FILTER']
));

$hPropertyNumber= function ($key, $arValue) {
    if ($arValue['PROPERTY_TYPE'] === 'N' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
        return [
            'key' => $arValue['CODE'],
            'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
        ];

    return ['skip' => true];
};

$hPropertyList= function ($key, $arValue) {
    if ($arValue['PROPERTY_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
        return [
            'key' => $arValue['CODE'],
            'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
        ];

    return ['skip' => true];
};

$arPropertyList = $arProperties->asArray($hPropertyList);
$arPropertyNumber = $arProperties->asArray($hPropertyNumber);

$arParameters = [];

$arParameters['TIME_ZERO_HIDE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIME_ZERO_HIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arParameters['MODE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_MODE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'set' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_MODE_SET_TIME'),
        'discount' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_MODE_DISCOUNT_TIME')
    ],
    'DEFAULT' => 'discount',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['MODE'] === 'set') {
    $arParameters['UNTIL_DATE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_UNTIL_DATE'),
        'TYPE' => 'STRING'
    ];
}

if ($arCurrentValues['MODE'] === 'discount') {
    $arParameters['ELEMENT_ID_INTRODUCE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_ELEMENT_ID_INTRODUCE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ELEMENT_ID_INTRODUCE'] === 'Y') {
        $arParameters['ELEMENT_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_ELEMENT_ID'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];
    }
}

$arParameters['TIMER_SECONDS_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_SECONDS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arParameters['TIMER_QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_QUANTITY_SHOW'] === 'Y') {

    $arParameters['TIMER_QUANTITY_ENTER_VALUE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_ENTER_VALUE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['TIMER_QUANTITY_ENTER_VALUE'] === 'Y') {
        $arParameters['QUANTITY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_VALUE'),
            'TYPE' => 'STRING',
        ];
    }

    $arParameters['TIMER_PRODUCT_UNITS_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_PRODUCT_UNITS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    $arParameters['TIMER_QUANTITY_HEADER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['TIMER_QUANTITY_HEADER_SHOW'] === 'Y') {
        $arParameters['TIMER_QUANTITY_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_QUANTITY_HEADER_DEFAULT')
        ];
    }
}

$arParameters['TIMER_HEADER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_HEADER_SHOW'] === 'Y') {
    $arParameters['TIMER_HEADER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_HEADER'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_PRODUCT_TIMER_COMPONENT_TIMER_HEADER_DEFAULT')
    ];
}

$arComponentParameters = [
    'PARAMETERS' => $arParameters
];