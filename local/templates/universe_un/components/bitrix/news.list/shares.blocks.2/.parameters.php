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

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y',
    ]))->indexBy('CODE');

    $hPropertyDate = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['USER_TYPE'] === 'Date' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyString = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['MULTIPLE'] === 'N' && empty($value['USER_TYPE']))
            return [
                'key' => $key,
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyDate = $arProperties->asArray($hPropertyDate);
    $arPropertyString = $arProperties->asArray($hPropertyString);

    $arTemplateParameters['PROPERTY_DATE_END'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_PROPERTY_DATE_END'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyDate,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_DISCOUNT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_PROPERTY_DISCOUNT'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyString,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_DURATION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_PROPERTY_DURATION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyString,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['IBLOCK_DESCRIPTION_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_IBLOCK_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_DISCOUNT'])) {
        $arTemplateParameters['DISCOUNT_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_DISCOUNT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_DURATION'])) {
        $arTemplateParameters['DURATION_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_DURATION_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_DATE_END']))
        include(__DIR__.'/parameters/timer.php');
}