<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ]))->indexBy('CODE');

        $hPropertyDate = function ($key, $value) {
            if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['USER_TYPE'] === 'Date' && $value['MULTIPLE'] === 'N')
                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];

            return ['skip' => true];
        };
        $hPropertyTextSingle = function ($key, $value) {
            if ($value['PROPERTY_TYPE'] === 'S' && $value['MULTIPLE'] === 'N')
                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];

            return ['skip' => true];
        };

        $arPropertyDate = $arProperties->asArray($hPropertyDate);
        $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);

        $arTemplateParameters['PROPERTY_DATE_END'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_PROPERTY_DATE_END'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyDate,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_DATE_END'])) {
            $arTemplateParameters['PROPERTY_DISCOUNT'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_PROPERTY_DISCOUNT'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }

    $arTemplateParameters['DATE_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if ($arCurrentValues['DATE_SHOW'] === 'Y') {
        $arTemplateParameters['DATE_TYPE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'DATE_CREATE' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_TYPE_CREATE'),
                'DATE_ACTIVE_FROM' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_TYPE_ACTIVE_FROM'),
                'DATE_ACTIVE_TO' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_TYPE_ACTIVE_TO'),
                'TIMESTAMP_X' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_TYPE_TIMESTAMP_X')
            ],
            'DEFAULT' => 'DATE_ACTIVE_FROM'
        ];
        $arTemplateParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
            Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DATE_FORMAT'),
            'VISUAL'
        );
    }

    $arTemplateParameters['IBLOCK_DESCRIPTION_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_IBLOCK_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];
    $arTemplateParameters['DESCRIPTION_SHOW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_SHARES_LIST_1_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if (!empty($arCurrentValues['PROPERTY_DATE_END']))
        include(__DIR__ . '/parameters/timer.php');
}