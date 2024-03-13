<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\RegExp;
use intec\core\helpers\Type;
use Bitrix\Main\Loader;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */
if (!Loader::includeModule('intec.core'))
    return false;

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'CONTACT_PERSON_FORM_SHOW' => 'N',
    'SUMMARY_FORM_SHOW' => 'N',
    'SALARY_SHOW' => 'N',
    'CONTACT_PERSON_IBLOCK_ELEMENT' => null,
    'CONTACT_PERSON_IBLOCK_ID' => null,
    'PROPERTY_CITY' => null,
    'PROPERTY_SKILL' => null,
    'PROPERTY_TYPE_EMPLOYMENT' => null,
    'PROPERTY_SALARY' => null,
    'DETAIL_PAGE_USE' => 'N',
    'LINK_BLANK' => 'N',
    'SUMMARY_FORM_ID' => null,
    'SUMMARY_FORM_TEMPLATE' => null,
    'SUMMARY_FORM_TITLE' => null,
    'SUMMARY_FORM_VACANCY' => null,
    'CONSENT_URL' => null
], $arParams);

$arVisual = [
    'CONTACT_PERSON' => [
        'SHOW' => $arParams['CONTACT_PERSON_SHOW'] === 'Y',
        'FORM' => [
            'SHOW' => $arParams['CONTACT_PERSON_FORM_SHOW'] === 'Y'
        ]
    ],
    'FULL_DESCRIPTION' => [
        'SHOW' => $arParams['CONTACT_PERSON_SHOW_FULL_DESCRIPTION'] === 'Y',
    ],
    'SALARY' => [
        'SHOW' => $arParams['SALARY_SHOW'] === 'Y'
    ],
    'SUMMARY' => [
        'FORM' => [
            'SHOW' => $arParams['SUMMARY_FORM_SHOW'] === 'Y'
        ]
    ],
    'DETAIL_PAGE' => [
        'USE' => $arParams['DETAIL_PAGE_USE'] === 'Y',
        'LINK' => [
            'BLANK' => $arParams['LINK_BLANK'] === 'Y'
        ]
    ],
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ]
];

$arResult['FORMS'] = [
    'SUMMARY' => [
        'ID' => $arParams['SUMMARY_FORM_ID'],
        'TEMPLATE' => $arParams['SUMMARY_FORM_TEMPLATE'],
        'TITLE' => $arParams['SUMMARY_FORM_TITLE'],
        'PROPERTIES' => [
            'VACANCY' => $arParams['SUMMARY_FORM_VACANCY']
        ]
    ]
];

if (empty($arResult['FORMS']['SUMMARY']['ID'])) {
    $arVisual['CONTACT_PERSON']['FORM']['SHOW'] = false;
    $arVisual['SUMMARY']['FORM']['SHOW'] = false;
}


// start Contact PERSON

$arResult['CONTACT_PERSON'] = [];

if (!empty($arParams['CONTACT_PERSON_IBLOCK_ELEMENT'])) {
    $arContactPerson = [];
    $arFilter = [
        'ID' => $arParams['CONTACT_PERSON_IBLOCK_ELEMENT'],
        'ACTIVE' => 'Y'
    ];

    $arContactPerson = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter);
    $arContactPerson = $arContactPerson->GetNext();

    if (!empty($arContactPerson)) {
        $arProperties = Arrays::fromDBResult(CIBlockElement::GetProperty(
            $arParams['CONTACT_PERSON_IBLOCK_ID'],
            $arContactPerson['ID']
        ))->asArray(function ($sKey, $arProperty) {
            return [
                'key' => $arProperty['CODE'],
                'value' => $arProperty['VALUE']
            ];
        });

        $arResult['CONTACT_PERSON'] = [
            'NAME' => $arContactPerson['NAME'],
            'PREVIEW_TEXT' => $arContactPerson['PREVIEW_TEXT'],
            'PREVIEW_PICTURE' => $arContactPerson['PREVIEW_PICTURE'],
            'DETAIL_PICTURE' => $arContactPerson['DETAIL_PICTURE'],
            'EMAIL' => ArrayHelper::GetValue($arProperties, $arParams['PROPERTY_CONTACT_PERSON_EMAIL']),
            'PHONE' => ArrayHelper::GetValue($arProperties, $arParams['PROPERTY_CONTACT_PERSON_PHONE'])
        ];
    }

    unset($arContactPerson, $arProperties, $arFilter);
}

if (empty($arResult['CONTACT_PERSON']))
    $arVisual['CONTACT_PERSON']['SHOW'] = false;

// end Contact Person

$sIBlockType = ArrayHelper::getValue($arParams, 'IBLOCK_TYPE');
$iIBlockId = ArrayHelper::getValue($arParams, 'IBLOCK_ID');
$arSections = array();

if (!empty($sIBlockType) && !empty($iIBlockId)) {
    $rsSections = CIBlockSection::GetList(array(
        'SORT' => 'ASC'
    ), array(
        'ACTIVE' => 'Y',
        'SECTION_ID' => false,
        'IBLOCK_TYPE' => $sIBlockType,
        'IBLOCK_ID' => $iIBlockId
    ));

    while ($arSection = $rsSections->Fetch()) {
        $arSection['ITEMS'] = array();
        $arSections[$arSection['ID']] = $arSection;
    }
}

foreach ($arResult['ITEMS'] as &$arItem) {
    if (ArrayHelper::keyExists($arItem['IBLOCK_SECTION_ID'], $arSections))
        $arSections[$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
}

$arResult['SECTIONS'] = $arSections;

$arResult['VISUAL'] = $arVisual;

unset($arData, $arVisual);