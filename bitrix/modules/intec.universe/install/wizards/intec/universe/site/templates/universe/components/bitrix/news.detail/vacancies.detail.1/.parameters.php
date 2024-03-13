<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::IncludeModule('iblock'))
    return;
if (!Loader::includeModule('intec.core'))
    return;

Loc::loadMessages(__FILE__);

$arTemplateParameters = [];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    //получаем свойства выбранного инфоблока
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

    $arTemplateParameters['PROPERTY_CITY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_PROPERTY_CITY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_SKILL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_PROPERTY_SKILL'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_TYPE_EMPLOYMENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_PROPERTY_TYPE_EMPLOYMENT'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_SALARY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_PROPERTY_SALARY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
}

$arTemplateParameters['SUMMARY_FORM_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_SUMMARY_FORM_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    "REFRESH" => "Y"
];

if ($arCurrentValues['SUMMARY_FORM_SHOW'] === 'Y') {
    if (Loader::includeModule('form')) {
        include('parameters/base.php');
    } else if (Loader::includeModule('intec.startshop')) {
        include('parameters/lite.php');
    }

    $arTemplateParameters['SUMMARY_FORM_TITLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_SUMMARY_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_SUMMARY_FORM_TITLE_DEFAULT')
    ];

    $arTemplateParameters['CONSENT_URL'] = [
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_CONSENT_URL'),
        'TYPE' => 'STRING'
    ];
}