<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var string $siteTemplate
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hProperties = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        return [
            'key' => $arProperty['CODE'],
            'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
        ];
    };

    $hPropertiesString = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['MULTIPLE'] !== 'Y')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesElements = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['MULTIPLE'] === 'Y' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertyDate = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N' && ($value['USER_TYPE'] === 'Date' || $value['USER_TYPE'] === 'DateTime'))
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertyCheckbox = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['LIST_TYPE'] == 'C')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };
}
$arTemplateParameters = [];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

/** Banner */

$arTemplateParameters['BANNER_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BANNER_SHOW'] === 'Y') {
    $arTemplateParameters['BANNER_DARK_TEXT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_DARK_TEXT'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['BANNER_PROPERTY_DURATION_END'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_PROPERTY_DURATION_END'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyDate),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['BANNER_SALE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_SALE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hProperties),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['BANNER_PROPERTY_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_PROPERTY_TITLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['BANNER_PROPERTY_SUBTITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_BANNER_PROPERTY_SUBTITLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

/** Timer */

$arTemplateParameters['TIMER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_TIMER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_SHOW'] === 'Y') {
    $arTemplateParameters['TIMER_SECONDS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_TIMER_SECONDS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['TIMER_SALE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_TIMER_SALE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

/** Description */

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_PROPERTY_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_DESCRIPTION_PROPERTY_TITLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

/** Icons */

$arTemplateParameters['ICONS_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_ICONS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ICONS_SHOW'] === 'Y') {
    include(__DIR__.'/parameters/icons.php');
}

/** Conditions */

$arTemplateParameters['CONDITIONS_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_CONDITIONS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['CONDITIONS_SHOW'] === 'Y') {
    include(__DIR__.'/parameters/conditions.php');
}

/** Form */

$arTemplateParameters['FORM_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_FORM_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_SHOW'] === 'Y') {
    include(__DIR__.'/parameters/form.php');
}

/** Services */

$arTemplateParameters['SERVICES_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_SERVICES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SERVICES_SHOW'] === 'Y') {
    include(__DIR__.'/parameters/services.php');
}

/** Products */

$arTemplateParameters['PRODUCTS_SHOW'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_SHARES_DEFAULT_2_PRODUCTS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
    include(__DIR__.'/parameters/products.php');
}

unset ($arTemplates, $sTemplate, $sComponent, $sPrefix);