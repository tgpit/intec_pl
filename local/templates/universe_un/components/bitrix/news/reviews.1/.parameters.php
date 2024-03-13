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

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y'
]));

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['VIDEO_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_VIDEO_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockTypes,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['VIDEO_IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_VIDEO_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
            if (!empty($arCurrentValues['VIDEO_IBLOCK_TYPE']) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['VIDEO_IBLOCK_TYPE'])
                return ['skip' => true];

            return [
                'key' => $value['ID'],
                'value' => '[' . $value['ID'] . '] ' . $value['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['STAFF_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_STAFF_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockTypes,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['STAFF_IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_STAFF_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
            if (!empty($arCurrentValues['STAFF_IBLOCK_TYPE']) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['STAFF_IBLOCK_TYPE'])
                return ['skip' => true];

            return [
                'key' => $value['ID'],
                'value' => '[' . $value['ID'] . '] ' . $value['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['SETTINGS_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_SETTINGS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['LAZYLOAD_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_LAZYLOAD_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertiesCheckboxSingle = function($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'L' && $value['LIST_TYPE'] == 'C' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesTextSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesListSingle = function($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'L' && $value['LIST_TYPE'] == 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesLink = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'E' && $value['LIST_TYPE'] === 'L')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesLinkSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'E' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesFile = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'F' && $value['LIST_TYPE'] == 'L')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesCheckboxSingle = $arProperties->asArray($hPropertiesCheckboxSingle);
    $arPropertiesTextSingle = $arProperties->asArray($hPropertiesTextSingle);
    $arPropertiesListSingle = $arProperties->asArray($hPropertiesListSingle);
    $arPropertiesLink = $arProperties->asArray($hPropertiesLink);
    $arPropertiesLinkSingle = $arProperties->asArray($hPropertiesLinkSingle);
    $arPropertiesFile = $arProperties->asArray($hPropertiesFile);

    $arTemplateParameters['PROPERTY_INFORMATION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_INFORMATION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_RATING'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_RATING'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesListSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['VIDEO_IBLOCK_ID'])) {
        $arTemplateParameters['PROPERTY_VIDEO'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_VIDEO'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesLink,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_VIDEO'])) {
            $arVideoIBlockProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['VIDEO_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $arTemplateParameters['VIDEO_PROPERTY_URL'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_VIDEO_PROPERTY_URL'),
                'TYPE' => 'LIST',
                'VALUES' => $arVideoIBlockProperties->asArray($hPropertiesTextSingle),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }

    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_FILES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_FILES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['STAFF_IBLOCK_ID'])) {
        $arTemplateParameters['PROPERTY_STAFF'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_PROPERTY_STAFF'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesLinkSingle,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_STAFF'])) {
            $arStaffIBlockProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['STAFF_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $arTemplateParameters['STAFF_PROPERTY_POSITION'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_STAFF_PROPERTY_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => $arStaffIBlockProperties->asArray($hPropertiesTextSingle),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }

    $arTemplateParameters['SEND_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_REVIEWS_1_SEND_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SEND_USE'] === 'Y') {
        include(__DIR__.'/parameters/send.php');
    }

    include(__DIR__.'/parameters/list.php');
}