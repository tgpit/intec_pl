<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
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

    $arVideoIBlocksFilter = [
        'SITE_ID' => $sSite,
        'ACTIVE' => 'Y'
    ];

    if (!empty($arCurrentValues['VIDEO_IBLOCK_TYPE']))
        $arVideoIBlocksFilter['TYPE'] = $arCurrentValues['VIDEO_IBLOCK_TYPE'];

    $arTemplateParameters['VIDEO_IBLOCK_TYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['VIDEO_IBLOCK_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], $arVideoIBlocksFilter))->asArray(function ($key, $value) {
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arStaffIBlocksFilter = [
        'SITE_ID' => $sSite,
        'ACTIVE' => 'Y'
    ];

    if (!empty($arCurrentValues['STAFF_IBLOCK_TYPE']))
        $arStaffIBlocksFilter['TYPE'] = $arCurrentValues['STAFF_IBLOCK_TYPE'];

    $arTemplateParameters['STAFF_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_STAFF_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['STAFF_IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_STAFF_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], $arStaffIBlocksFilter))->asArray(function ($key, $value) {
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_TEXT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_TEXT'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_INFORMATION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_INFORMATION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_RATING'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_RATING'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesListSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['VIDEO_IBLOCK_ID'])) {
        $arTemplateParameters['PROPERTY_VIDEO'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_VIDEO'),
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
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_PROPERTY_URL'),
                'TYPE' => 'LIST',
                'VALUES' => $arVideoIBlockProperties->asArray($hPropertiesTextSingle),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            if (!empty($arCurrentValues['VIDEO_PROPERTY_URL'])) {
                $arTemplateParameters['PROPERTY_VIDEO_VIEW'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_VIDEO_VIEW'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arPropertiesCheckboxSingle,
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }
        }
    }

    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_FILES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_FILES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ANSWER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_ANSWER'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['STAFF_IBLOCK_ID'])) {
        $arTemplateParameters['PROPERTY_STAFF'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PROPERTY_STAFF'),
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
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_STAFF_PROPERTY_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => $arStaffIBlockProperties->asArray($hPropertiesTextSingle),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }

    $arTemplateParameters['PICTURE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PICTURE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['DATE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DATE_SHOW'] === 'Y') {
        $arTemplateParameters['DATE_SOURCE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SOURCE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'DATE_ACTIVE_FROM' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SOURCE_DATE_ACTIVE_FROM'),
                'DATE_ACTIVE_TO' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SOURCE_DATE_ACTIVE_TO'),
                'DATE_CREATE' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SOURCE_DATE_CREATE'),
                'TIMESTAMP_X' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_DATE_SOURCE_TIMESTAMP_X')
            ],
            'DEFAULT' => 'DATE_ACTIVE_FROM'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_INFORMATION'])) {
        $arTemplateParameters['INFORMATION_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_INFORMATION_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_RATING'])) {
        $arTemplateParameters['RATING_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_RATING_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (
        !empty($arCurrentValues['PROPERTY_VIDEO']) &&
        !empty($arCurrentValues['VIDEO_IBLOCK_ID']) &&
        !empty($arCurrentValues['VIDEO_PROPERTY_URL'])
    ) {
        $arTemplateParameters['VIDEO_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['VIDEO_SHOW'] === 'Y') {
            $arTemplateParameters['VIDEO_PICTURE_SOURCES'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_PICTURE_SOURCES'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'service' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_PICTURE_SOURCES_SERVICE'),
                    'preview' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_PICTURE_SOURCES_PREVIEW'),
                    'detail' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_PICTURE_SOURCES_DETAIL'),
                ],
                'ADDITIONAL_VALUES' => 'Y',
                'MULTIPLE' => 'Y',
                'SIZE' => 5,
                'REFRESH' => 'Y'
            ];

            if (Type::isArray($arCurrentValues['VIDEO_PICTURE_SOURCES']) && ArrayHelper::isIn('service', $arCurrentValues['VIDEO_PICTURE_SOURCES'])) {
                $arTemplateParameters['VIDEO_PICTURE_QUALITY'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_VIDEO_PICTURE_QUALITY'),
                    'TYPE' => 'LIST',
                    'VALUES' => [
                        'mqdefault' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_VIDEO_PICTURE_QUALITY_MQ'),
                        'hqdefault' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_VIDEO_PICTURE_QUALITY_HQ'),
                        'sddefault' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_VIDEO_PICTURE_QUALITY_SD'),
                        'maxresdefault' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_VIDEO_VIDEO_PICTURE_QUALITY_MAX')
                    ],
                    'DEFAULT' => 'hqdefault'
                ];
            }
        }
    }

    if (!empty($arCurrentValues['PROPERTY_PICTURES'])) {
        $arTemplateParameters['PICTURES_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_PICTURES_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_FILES'])) {
        $arTemplateParameters['FILES_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_FILES_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_ANSWER'])) {
        $arTemplateParameters['ANSWER_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['ANSWER_SHOW'] === 'Y') {
            $arTemplateParameters['ANSWER_DEFAULT_NAME'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_DEFAULT_NAME'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_DEFAULT_NAME_DEFAULT')
            ];
            $arTemplateParameters['ANSWER_PICTURE_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_PICTURE_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['ANSWER_PICTURE_SHOW'] === 'Y') {
                $arTemplateParameters['ANSWER_DEFAULT_PICTURE'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_DEFAULT_PICTURE'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => null
                ];
            }

            $arTemplateParameters['ANSWER_POSITION_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_POSITION_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['ANSWER_POSITION_SHOW'] === 'Y') {
                $arTemplateParameters['ANSWER_DEFAULT_POSITION'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_DEFAULT_POSITION'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_ANSWER_DEFAULT_POSITION_DEFAULT')
                ];
            }
        }
    }
}