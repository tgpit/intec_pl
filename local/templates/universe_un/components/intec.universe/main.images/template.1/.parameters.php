<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use Bitrix\Main\Loader;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['SETTINGS_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_SETTINGS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['LAZYLOAD_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_LAZYLOAD_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyLink = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'E' && $value['LIST_TYPE'] === 'L')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertiesLink = $arProperties->asArray($hPropertyLink);

    $arTemplateParameters['PROPERTY_DISPLAY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PROPERTY_DISPLAY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'MULTIPLE' => 'Y',
        'SIZE' => 5,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PREVIEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PROPERTY_PREVIEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['TABS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TABS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TABS_USE'] === 'Y') {
    $arTemplateParameters['TABS_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TABS_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TABS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TABS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TABS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}

$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (Type::isArray($arCurrentValues['PROPERTY_DISPLAY']) && !empty(array_filter($arCurrentValues['PROPERTY_DISPLAY']))) {
    $arTemplateParameters['DISPLAY_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_DISPLAY_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['DETAIL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_DETAIL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DETAIL_SHOW'] === 'Y') {
    $arTemplateParameters['DETAIL_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_DETAIL_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_DETAIL_TEXT_DEFAULT')
    ];
    $arTemplateParameters['DETAIL_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_DETAIL_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['MORE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_MORE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['MORE_SHOW'] === 'Y') {
    $arTemplateParameters['MORE_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_MORE_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_MORE_TEXT_DEFAULT')
    ];
    $arTemplateParameters['MORE_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_MORE_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['PRODUCTS_SHOW'] = [
        'PARENT' => 'PRODUCTS',
        'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PRODUCTS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_PRODUCTS'] = [
            'PARENT' => 'PRODUCTS',
            'NAME' => Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_PROPERTY_PRODUCTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesLink,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_PRODUCTS'])) {
            include(__DIR__.'/parameters/products.php');
        }
    }
}