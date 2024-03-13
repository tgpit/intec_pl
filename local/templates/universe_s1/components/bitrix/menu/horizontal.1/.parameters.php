<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues;
 */

if (!CModule::IncludeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$sIBlockType = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = array();
$iIBlockId = $arCurrentValues['IBLOCK_ID'];
$arIBlocksFilter = array();
$arIBlocksFilter['ACTIVE'] = 'Y';

if (!empty($sIBlockType))
    $arIBlocksFilter['TYPE'] = $sIBlockType;

$rsIBlocks = CIBlock::GetList(['SORT' => 'ASC'], $arIBlocksFilter);

while ($arIBlock = $rsIBlocks->Fetch())
    $arIBlocks[$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];

$arTemplateParameters = [];
$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'REFRESH' => 'Y',
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks,
    'REFRESH' => 'Y',
    'ADDITIONAL_VALUES' => 'Y'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['UPPERCASE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_UPPERCASE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['TRANSPARENT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_TRANSPARENT'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TRANSPARENT'] != 'Y') {
    $arTemplateParameters['DELIMITERS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_DELIMITERS'),
        'TYPE' => 'CHECKBOX'
    ];
}

if (!empty($iIBlockId)) {
    $arFields = array();
    $rsFields = CUserTypeEntity::GetList(['SORT' => 'ASC'], array(
        'ENTITY_ID' => 'IBLOCK_'.$iIBlockId.'_SECTION',
        'USER_TYPE_ID' => 'file'
    ));

    while ($arField = $rsFields->Fetch())
        $arFields[$arField['FIELD_NAME']] = $arField['FIELD_NAME'];

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $iIBlockId,
        'ACTIVE' => 'Y'
    ]));

    $hPropertyFileAll = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'F' && $arValue['LIST_TYPE'] === 'L')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyFileAll = $arProperties->asArray($hPropertyFileAll);

    $arTemplateParameters['PROPERTY_IMAGE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_PROPERTY_IMAGE'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields,
        'REFRESH' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_IMAGE_ELEMENTS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_PROPERTY_IMAGE_ELEMENTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFileAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['SECTION_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'default' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_VIEW_DEFAULT'),
        'images' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_VIEW_IMAGES'),
        'information' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_VIEW_INFORMATION'),
        'banner' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_VIEW_BANNER')
    ],
    'REFRESH' => 'Y'
];

$arTemplateParameters['SUBMENU_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SUBMENU_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'simple.1' => Loc::getMessage('C_MENU_HORIZONTAL_1_SUBMENU_VIEW_SIMPLE_1'),
        'simple.2' => Loc::getMessage('C_MENU_HORIZONTAL_1_SUBMENU_VIEW_SIMPLE_2')
    ],
    'REFRESH' => 'Y'
];

$arTemplateParameters['SECTION_COLUMNS_COUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_COLUMNS_COUNT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => 2,
        3 => 3,
        4 => 4
    ],
    'DEFAULT' => 3
];

if ($arCurrentValues['SECTION_VIEW'] == 'images' || $arCurrentValues['SECTION_VIEW'] == 'banner') {
    $arTemplateParameters['SECTION_ITEMS_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_ITEMS_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => 3
    ];
}

$arTemplateParameters['OVERLAY_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_OVERLAY_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['CATALOG_LINKS'] = [
    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_CATALOG_LINKS'),
    'PARENT' => 'VISUAL',
    'TYPE' => 'STRING',
    'MULTIPLE' => 'Y'
];

if ($arCurrentValues['SECTION_VIEW'] == 'banner') {

    $arSectionIBlocks = array();
    $arSectionIBlocksFilter = array();
    $arSectionIBlocksFilter['ACTIVE'] = 'Y';

    if (!empty($arCurrentValues['SECTION_BANNER_IBLOCK_TYPE']))
        $arSectionIBlocksFilter['TYPE'] = $arCurrentValues['SECTION_BANNER_IBLOCK_TYPE'];

    $rsSectionIBlocks = CIBlock::GetList(['SORT' => 'ASC'], $arSectionIBlocksFilter);

    while ($arSectionIBlock = $rsSectionIBlocks->Fetch())
        $arSectionIBlocks[$arSectionIBlock['ID']] = '['.$arSectionIBlock['ID'].'] '.$arSectionIBlock['NAME'];

    $arTemplateParameters['SECTION_BANNER_SHOW_ICONS_ROOT_ITEMS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_SHOW_ICONS_ROOT_ITEMS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SECTION_BANNER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['SECTION_BANNER_MENU_SYNCHRONIZE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_MENU_SYNCHRONIZE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    if ( $arCurrentValues['SECTION_BANNER_SHOW'] === 'Y') {
        $arTemplateParameters['SECTION_BANNER_IBLOCK_TYPE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'REFRESH' => 'Y',
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['SECTION_BANNER_IBLOCK_ID'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arSectionIBlocks,
            'REFRESH' => 'Y',
            'ADDITIONAL_VALUES' => 'Y'
        ];

        if (!empty($arCurrentValues['SECTION_BANNER_IBLOCK_ID'])) {

            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['SECTION_BANNER_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $hPropertyGroup = function ($sKey, $value) {
                if ($value['ACTIVE'] == 'Y' && $value['PROPERTY_TYPE'] == 'G')
                    return [
                        'key' => $value['CODE'],
                        'value' => '['.$value['CODE'].'] '.$value['NAME']
                    ];

                return ['skip' => true];
            };

            $hPropertyString = function ($sKey, $value) {

                if ($value['ACTIVE'] == 'Y' && $value['PROPERTY_TYPE'] == 'S')
                    return [
                        'key' => $value['CODE'],
                        'value' => '['.$value['CODE'].'] '.$value['NAME']
                    ];

                return ['skip' => true];
            };

            $arPropertyGroup = $arProperties->asArray($hPropertyGroup);
            $arPropertyString = $arProperties->asArray($hPropertyString);

            $arTemplateParameters['SECTION_BANNER_PROPERTY_SECTION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_PROPERTY_SECTION'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyGroup,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            $arTemplateParameters['SECTION_BANNER_PROPERTY_LINK'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_PROPERTY_LINK'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyString,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            $arTemplateParameters['SECTION_BANNER_HEADER_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_HEADER_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];

            $arTemplateParameters['SECTION_BANNER_DESCRIPTION_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_DESCRIPTION_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['SECTION_BANNER_DESCRIPTION_SHOW'] === 'Y') {
                $arTemplateParameters['SECTION_BANNER_DESCRIPTION_LIMIT'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_DESCRIPTION_LIMIT'),
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'Y'
                ];
            }

            $arTemplateParameters['SECTION_BANNER_DOTS_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_DOTS_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'Y'
            ];

            $arTemplateParameters['SECTION_BANNER_LOOP_USE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MENU_HORIZONTAL_1_SECTION_BANNER_LOOP_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];

        }
    }
}