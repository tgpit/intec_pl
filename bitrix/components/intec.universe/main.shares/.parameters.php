<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\StringHelper;

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

$arFilter = [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y',
    'CHECK_PERMISSIONS' => 'Y',
    'MIN_PERMISSION' => 'R'
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC',
    'NAME' => 'ASC'
], $arFilter))->asArray(function ($key, $arValue) {
    return [
        'key' => $arValue['ID'],
        'value' => '['.$arValue['ID'].'] '.$arValue['NAME']
    ];
});

$arParameters = [];

$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arSections = Arrays::fromDBResult(CIBlockSection::GetList([
        'SORT' => 'ASC',
        'NAME' => 'ASC'
    ], [
        'IBLOCK_TYPE' => $arCurrentValues['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'IBLOCK_ACTIVE' => 'Y',
        'GLOBAL_ACTIVE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R'
    ]))->asArray(function ($key, $arValue) {
        return [
            'key' => $arValue['ID'],
            'value' => '['.$arValue['ID'].'] '.$arValue['NAME']
        ];
    });

    $arParameters['SECTIONS'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_SECTIONS'),
        'TYPE' => 'LIST',
        'VALUES' => $arSections,
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];

    unset($arSections);
}

$arParameters['ELEMENTS_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_ELEMENTS_COUNT'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];
$arParameters['HEADER_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_HEADER_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_BLOCK_SHOW'] === 'Y') {
    $arParameters['HEADER_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_HEADER_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_SHARES_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_SHARES_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_SHARES_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['HEADER_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_HEADER_BLOCK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_SHARES_HEADER_BLOCK_TEXT_DEFAULT')
    ];
}

$arParameters['DESCRIPTION_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_DESCRIPTION_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_BLOCK_SHOW'] === 'Y') {
    $arParameters['DESCRIPTION_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_DESCRIPTION_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_SHARES_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_SHARES_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_SHARES_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['DESCRIPTION_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_DESCRIPTION_BLOCK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
}

$arParameters['LIST_PAGE_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_LIST_PAGE_URL'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];
$arParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'SECTION_URL',
    Loc::getMessage('C_MAIN_SHARES_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'DETAIL_URL',
    Loc::getMessage('C_MAIN_SHARES_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['NAVIGATION_USE'] = [
    'PARENT' => 'NAVIGATION',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NAVIGATION_USE'] === 'Y') {
    $arParameters['NAVIGATION_ID'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'shares'
    ];
    $arParameters['NAVIGATION_MODE'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'standard' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_MODE_STANDARD'),
            'ajax' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_MODE_AJAX')
        ],
        'DEFAULT' => 'ajax',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['NAVIGATION_MODE'] === 'standard') {
        $arParameters['NAVIGATION_ALL'] = [
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('C_MAIN_SHARES_NAVIGATION_ALL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    include(__DIR__.'/parameters/navigation.template.php');
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ID' => Loc::getMessage('C_MAIN_SHARES_SORT_BY_ID'),
        'SORT' => Loc::getMessage('C_MAIN_SHARES_SORT_BY_SORT'),
        'NAME' => Loc::getMessage('C_MAIN_SHARES_SORT_BY_NAME'),
    ],
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => 'SORT'
];
$arParameters['ORDER_BY'] = [
    'PARENT' =>'SORT',
    'NAME' => Loc::getMessage('C_MAIN_SHARES_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_SHARES_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_SHARES_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];

$arComponentParameters = [
    'GROUPS' => [
        'TIMER' => [
            'NAME' => Loc::getMessage('C_MAIN_SHARES_GROUPS_TIMER'),
            'SORT' => 409
        ],
        'NAVIGATION' => [
            'NAME' => Loc::getMessage('C_MAIN_SHARES_GROUPS_NAVIGATION'),
            'SORT' => 410
        ],
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_SHARES_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];