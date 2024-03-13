<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

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
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID']) && !empty($arCurrentValues['ELEMENT'])) {
    if (!empty($_REQUEST['site']))
        $sSite = $_REQUEST['site'];
    else if (!empty($_REQUEST['src_site']))
        $sSite = $_REQUEST['src_site'];

    $arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'SITE_ID' => $sSite,
        'ACTIVE' => 'Y'
    ]))->indexBy('ID');

    if (!empty($arCurrentValues['ADVANTAGES_IBLOCK_TYPE'])) {
        $arIBlocks = $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
            if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['ADVANTAGES_IBLOCK_TYPE'])
                return [
                    'key' => $key,
                    'value' => '['.$value['ID'].'] '.$value['NAME']
                ];

            return ['skip' => true];
        });
    } else {
        $arIBlocks = $arIBlocks->asArray(function ($key, $value) {
            return [
                'key' => $key,
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];
        });
    }

    $arTemplateParameters['ADVANTAGES_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['ADVANTAGES_IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyText = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyFile = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'F' && $value['LIST_TYPE'] && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesText = $arProperties->asArray($hPropertyText);
    $arPropertiesFile = $arProperties->asArray($hPropertyFile);

    $arTemplateParameters['PROPERTY_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PROPERTY_TITLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PROPERTY_LINK'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' =>'Y'
    ];
    $arTemplateParameters['PROPERTY_VIDEO'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PROPERTY_VIDEO'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['ADVANTAGES_IBLOCK_ID'])) {
        $hPropertyLinkMultiple = function ($key, $value) {
            if ($value['PROPERTY_TYPE'] === 'E' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'Y')
                return [
                    'key' => $value['CODE'],
                    'value' => '['.$value['CODE'].'] '.$value['NAME']
                ];

            return ['skip' => true];
        };

        $arPropertiesLinkMultiple = $arProperties->asArray($hPropertyLinkMultiple);

        $arTemplateParameters['PROPERTY_ADVANTAGES'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PROPERTY_ADVANTAGES'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesLinkMultiple,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_ADVANTAGES'])) {
            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['ADVANTAGES_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $hPropertyAdvantagesFile = function ($key, $value) {
                if ($value['PROPERTY_TYPE'] === 'F' && $value['LIST_TYPE'] && $value['MULTIPLE'] === 'N')
                    return [
                        'key' => $value['CODE'],
                        'value' => '['.$value['CODE'].'] '.$value['NAME']
                    ];

                return ['skip' => true];
            };

            $arPropertiesAdvantagesFile = $arProperties->asArray($hPropertyAdvantagesFile);

            $arTemplateParameters['ADVANTAGES_PROPERTY_SVG_FILE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_PROPERTY_SVG_FILE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertiesAdvantagesFile,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }

    $arTemplateParameters['VIEW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_VIEW'),
        'TYPE' => 'LIST',
        'VALUES' => [
            '1' => '1',
            '2' => '2',
            '3' => '3'
        ],
        'DEFAULT' => 1,
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_TITLE'])) {
        $arTemplateParameters['TITLE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_TITLE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PREVIEW_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PREVIEW_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if (!empty($arCurrentValues['PROPERTY_LINK'])) {
        $arTemplateParameters['BUTTON_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
            $arTemplateParameters['BUTTON_VIEW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_VIEW'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    '1' => '1',
                    '2' => '2'
                ],
                'DEFAULT' => 1
            ];
            $arTemplateParameters['BUTTON_BLANK'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_BLANK'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
            $arTemplateParameters['BUTTON_TEXT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_TEXT'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_TEXT_DEFAULT')
            ];
        }
    }

    if ($arCurrentValues['VIEW'] === '1' || $arCurrentValues['VIEW'] === '3') {
        if (Type::isArray($arCurrentValues['PICTURE_SOURCES']) && !empty(array_filter($arCurrentValues['PICTURE_SOURCES']))) {
            $arTemplateParameters['PICTURE_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
                $arTemplateParameters['PICTURE_SIZE'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE'),
                    'TYPE' => 'LIST',
                    'VALUES' => [
                        'auto' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE_AUTO'),
                        'cover' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE_COVER'),
                        'contain' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE_CONTAIN')
                    ],
                    'DEFAULT' => 'auto'
                ];
                $arTemplateParameters['POSITION_HORIZONTAL'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_HORIZONTAL'),
                    'TYPE' => 'LIST',
                    'VALUES' => [
                        'left' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_LEFT'),
                        'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_CENTER'),
                        'right' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_RIGHT')
                    ],
                    'DEFAULT' => 'center'
                ];
                $arTemplateParameters['POSITION_VERTICAL'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_VERTICAL'),
                    'TYPE' => 'LIST',
                    'VALUES' => [
                        'top' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_TOP'),
                        'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_CENTER'),
                        'bottom' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_BOTTOM')
                    ],
                    'DEFAULT' => 'center'
                ];
            }
        }


        if (!empty($arCurrentValues['PROPERTY_VIDEO'])) {
            $arTemplateParameters['VIDEO_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_VIDEO_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }
    }

    if ($arCurrentValues['VIEW'] === '1' || $arCurrentValues['VIEW'] === '2') {
        if (!empty($arCurrentValues['ADVANTAGES_IBLOCK_ID']) && !empty($arCurrentValues['PROPERTY_ADVANTAGES'])) {
            $arTemplateParameters['ADVANTAGES_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['ADVANTAGES_SHOW'] === 'Y') {
                if (!empty($arCurrentValues['ADVANTAGES_PROPERTY_SVG_FILE'])) {
                    $arTemplateParameters['SVG_FILE_USE'] = [
                        'PARENT' => 'VISUAL',
                        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_SVG_FILE_USE'),
                        'TYPE' => 'CHECKBOX',
                        'DEFAULT' => 'N'
                    ];
                }

                $arTemplateParameters['ADVANTAGES_COLUMNS'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_COLUMNS'),
                    'TYPE' => 'LIST',
                    'VALUES' => [
                        '2' => '2',
                        '3' => '3'
                    ],
                    'DEFAULT' => '2'
                ];
            }
        }
    }
}