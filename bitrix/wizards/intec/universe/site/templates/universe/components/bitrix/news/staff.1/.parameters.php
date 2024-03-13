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
    'NAME' => Loc::getMessage('C_NEWS_STAFF_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_STAFF_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    if (Loader::includeModule('form'))
        include(__DIR__.'/parameters/base.php');
    else if (Loader::includeModule('intec.startshop'))
        include(__DIR__.'/parameters/lite.php');

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyTextAll = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyTextAll = $arProperties->asArray($hPropertyTextAll);

    $arTemplateParameters['PROPERTY_POSITION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PHONE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_PHONE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_EMAIL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_EMAIL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_VK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_SOCIAL_VK'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_FB'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_SOCIAL_FB'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_INST'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_SOCIAL_INST'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_TW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_SOCIAL_TW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_SKYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_STAFF_1_PROPERTY_SOCIAL_SKYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

include(__DIR__.'/parameters/list.php');
include(__DIR__.'/parameters/detail.php');