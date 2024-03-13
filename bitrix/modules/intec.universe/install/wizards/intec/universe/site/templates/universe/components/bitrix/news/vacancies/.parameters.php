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

$arTemplateParameters = [];

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
}

if(!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['PROPERTY_CITY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('C_NEWS_VACANCIES_PROPERTY_CITY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_SKILL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('C_NEWS_VACANCIES_PROPERTY_SKILL'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_TYPE_EMPLOYMENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('C_NEWS_VACANCIES_PROPERTY_TYPE_EMPLOYMENT'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
    $arTemplateParameters['PROPERTY_SALARY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => GetMessage('C_NEWS_VACANCIES_PROPERTY_SALARY'),
        'TYPE' => 'LIST',
        'VALUES' => $arIblockPropertyText
    ];
}
//подключаем параметры компонента news.list
include(__DIR__.'/parameters/list.php');
//подключаем параметры компонента news.detail
include(__DIR__.'/parameters/detail.php');