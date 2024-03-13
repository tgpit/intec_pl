<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

Loc::loadMessages(__FILE__);

if (!Loader::IncludeModule('iblock'))
    return;
if (!Loader::includeModule('intec.core'))
    return;

$arParametersCommon = [
    'PROPERTY_CITY',
    'PROPERTY_SKILL',
    'PROPERTY_TYPE_EMPLOYMENT',
    'PROPERTY_SALARY',
    'CONSENT_URL'
];

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyText = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arIblockPropertyText = $arProperties->asArray($hPropertyText);
}

if(!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['PROPERTY_CITY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_PROPERTY_CITY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText,
        'ADDITIONAL' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SKILL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_PROPERTY_SKILL'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText,
        'ADDITIONAL' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_TYPE_EMPLOYMENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_PROPERTY_TYPE_EMPLOYMENT'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText,
        'ADDITIONAL' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SALARY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_PROPERTY_SALARY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText,
        'ADDITIONAL' => 'Y'
    ];
}

include(__DIR__.'/parameters/menu.php');
include(__DIR__.'/parameters/list.php');
include(__DIR__.'/parameters/detail.php');

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_NEWS_VACANCIES_1_CONSENT_URL'),
    'TYPE' => 'STRING'
];