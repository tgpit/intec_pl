<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 */

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

if (!Loader::IncludeModule('iblock'))
    return;
if (!Loader::includeModule('intec.core'))
    return;

Loc::loadMessages(__FILE__);

$arIBlockType = [];
$arIBlock = [];
$arIBlockElement = [];

if (!empty($_REQUEST['site'])) {
    $sSite = $_REQUEST['site'];
} else if (!empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlocksList = Arrays::fromDBResult(CIBlock::GetList([], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

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

    $arPropertyText = $arProperties->asArray($hPropertyText);
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['PROPERTY_CITY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_PROPERTY_CITY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText
    ];
    $arTemplateParameters['PROPERTY_SKILL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_PROPERTY_SKILL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText
    ];
    $arTemplateParameters['PROPERTY_TYPE_EMPLOYMENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_PROPERTY_TYPE_EMPLOYMENT'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText
    ];
}

$arTemplateParameters['SALARY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SALARY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    "REFRESH" => "Y"
];

if ($arCurrentValues['SALARY_SHOW'] === 'Y') {
    $arTemplateParameters['PROPERTY_SALARY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_PROPERTY_SALARY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText
    ];
}

unset($arPropertyText);

$arTemplateParameters['CONTACT_PERSON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    "REFRESH" => "Y"
];

if ($arCurrentValues['CONTACT_PERSON_SHOW'] === 'Y') {

    if (!empty($arCurrentValues['CONTACT_PERSON_IBLOCK_TYPE']))
        $arIBlock = $arIBlocksList->asArray(function ($sKey, $arProperty) use (&$arCurrentValues) {
            if ($arProperty['IBLOCK_TYPE_ID'] === $arCurrentValues['CONTACT_PERSON_IBLOCK_TYPE'])
                return [
                    'key' => $arProperty['ID'],
                    'value' => '[' . $arProperty['ID'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        });

    if (!empty($arCurrentValues['CONTACT_PERSON_IBLOCK_ID'])) {
        $arIBlocksListElement = Arrays::fromDBResult(CIBlockElement::GetList([], [
            'ACTIVE' => 'Y',
            'SITE_ID' => $sSite,
            "IBLOCK_ID" => $arCurrentValues['CONTACT_PERSON_IBLOCK_ID'],
        ]))->indexBy('ID');

        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['CONTACT_PERSON_IBLOCK_ID']
        ]))->indexBy('ID');

        $arElements = $arIBlocksListElement->asArray(function ($sKey, $arProperty) use (&$arCurrentValues) {
            if ($arProperty['IBLOCK_ID'] === $arCurrentValues['CONTACT_PERSON_IBLOCK_ID'])
                return [
                    'key' => $arProperty['ID'],
                    'value' => '[' . $arProperty['ID'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        });
    }

    if (!empty($arCurrentValues['CONTACT_PERSON_IBLOCK_ELEMENT'])) {
        $sPropertyText = function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] == 'S')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        };
        $arPropertyText = $arProperties->asArray($sPropertyText);
    }

    $arTemplateParameters["CONTACT_PERSON_IBLOCK_TYPE"] = [
        "PARENT" => "BASE",
        "NAME" => Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockType,
        "REFRESH" => "Y",
        "ADDITIONAL_VALUES" => "Y"
    ];
    if ($arCurrentValues['CONTACT_PERSON_IBLOCK_TYPE']) {
        $arTemplateParameters["CONTACT_PERSON_IBLOCK_ID"] = [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_IBLOCK"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ];
    }
    if ($arCurrentValues['CONTACT_PERSON_IBLOCK_TYPE'] && $arCurrentValues['CONTACT_PERSON_IBLOCK_ID']) {
        $arTemplateParameters['CONTACT_PERSON_IBLOCK_ELEMENT'] = [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_IBLOCK_ELEMENT"),
            "TYPE" => "LIST",
            "VALUES" => $arElements,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ];
    }
    if ($arCurrentValues['CONTACT_PERSON_IBLOCK_TYPE'] && $arCurrentValues['CONTACT_PERSON_IBLOCK_ID'] && $arCurrentValues['CONTACT_PERSON_IBLOCK_ELEMENT']) {
        $arTemplateParameters["PROPERTY_CONTACT_PERSON_EMAIL"] = [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_PROPERTY_EMAIL"),
            "TYPE" => "LIST",
            "VALUES" => $arPropertyText,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ];
        $arTemplateParameters["PROPERTY_CONTACT_PERSON_PHONE"] = [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_PROPERTY_PHONE"),
            "TYPE" => "LIST",
            "VALUES" => $arPropertyText,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ];
        $arTemplateParameters['CONTACT_PERSON_SHOW_FULL_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_SHOW_FULL_DESCRIPTION'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            "REFRESH" => "N"
        ];
    }


    $arTemplateParameters['CONTACT_PERSON_FORM_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_FORM_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        "REFRESH" => "Y"
    ];
}

$arTemplateParameters['SUMMARY_FORM_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    "REFRESH" => "Y"
];

if ($arCurrentValues['SUMMARY_FORM_SHOW'] === 'Y' || $arCurrentValues['CONTACT_PERSON_FORM_SHOW'] === 'Y') {
    if (Loader::includeModule('form')) {
        include('parameters/base.php');
    } else if (Loader::includeModule('intec.startshop')) {
        include('parameters/lite.php');
    }

    $arTemplateParameters['SUMMARY_FORM_TITLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_TITLE_DEFAULT')
    ];

    $arTemplateParameters['CONSENT_URL'] = [
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_CONSENT_URL'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['DETAIL_PAGE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_DETAIL_PAGE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DETAIL_PAGE_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];