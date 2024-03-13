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

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y'
]));

$arParameters = [];
$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if (!empty($arCurrentValues) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['IBLOCK_TYPE'])
            return ['skip' => true];

        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arSections = Arrays::fromDBResult(CIBlockSection::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'GLOBAL_ACTIVE' => 'Y',
        'ACTIVE' => 'Y'
    ]))->indexBy(!empty($arCurrentValues['SECTIONS_MODE']) ? strtoupper($arCurrentValues['SECTIONS_MODE']) : 'ID');

    $arParameters['SECTIONS_MODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_SECTIONS_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'id' => Loc::getMessage('C_MAIN_COLLECTIONS_SECTIONS_MODE_ID'),
            'code' => Loc::getMessage('C_MAIN_COLLECTIONS_SECTIONS_MODE_CODE')
        ],
        'DEFAULT' => 'id',
        'REFRESH' => 'Y'
    ];
    $arParameters['SECTIONS'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_SECTIONS'),
        'TYPE' => 'LIST',
        'VALUES' => $arSections->asArray(function ($key, $value) {
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];
        }),
        'MULTIPLE' => 'Y',
        'SIZE' => 6,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arParameters['ELEMENTS_COUNT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_ELEMENTS_COUNT'),
        'TYPE' => 'STRING'
    ];
}

$arParameters['HEADER_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_HEADER_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_BLOCK_SHOW'] === 'Y') {
    $arParameters['HEADER_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_HEADER_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['HEADER_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_HEADER_BLOCK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_COLLECTIONS_HEADER_BLOCK_TEXT_DEFAULT')
    ];
}

$arParameters['DESCRIPTION_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_DESCRIPTION_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_BLOCK_SHOW'] === 'Y') {
    $arParameters['DESCRIPTION_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_DESCRIPTION_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_COLLECTIONS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['DESCRIPTION_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_DESCRIPTION_BLOCK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
}

$arParameters['LIST_PAGE_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_LIST_PAGE_URL'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];
$arParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'SECTION_URL',
    Loc::getMessage('C_MAIN_COLLECTIONS_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'DETAIL_URL',
    Loc::getMessage('C_MAIN_COLLECTIONS_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['NAVIGATION_USE'] = [
    'PARENT' => 'NAVIGATION',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NAVIGATION_USE'] === 'Y') {
    $arParameters['NAVIGATION_ID'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'collections'
    ];
    $arParameters['NAVIGATION_MODE'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'standard' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_MODE_STANDARD'),
            'ajax' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_MODE_AJAX')
        ],
        'DEFAULT' => 'ajax',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['NAVIGATION_MODE'] === 'standard') {
        $arParameters['NAVIGATION_ALL'] = [
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_NAVIGATION_ALL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    include(__DIR__.'/parameters/navigation.template.php');
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ID' => Loc::getMessage('C_MAIN_COLLECTIONS_SORT_BY_ID'),
        'SORT' => Loc::getMessage('C_MAIN_COLLECTIONS_SORT_BY_SORT'),
        'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_SORT_BY_NAME'),
    ],
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => 'SORT'
];
$arParameters['ORDER_BY'] = [
    'PARENT' =>'SORT',
    'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_COLLECTIONS_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_COLLECTIONS_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];
$arParameters['CACHE_TIME'] = [];

$arComponentParameters = [
    'GROUPS' => [
        'NAVIGATION' => [
            'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_GROUPS_NAVIGATION'),
            'SORT' => 410
        ],
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_COLLECTIONS_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];