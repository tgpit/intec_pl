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
    'NAME' => Loc::getMessage('C_MAIN_STORIES_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_IBLOCK_ID'),
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
        'NAME' => Loc::getMessage('C_MAIN_STORIES_SECTIONS_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'id' => Loc::getMessage('C_MAIN_STORIES_SECTIONS_MODE_ID'),
            'code' => Loc::getMessage('C_MAIN_STORIES_SECTIONS_MODE_CODE')
        ],
        'DEFAULT' => 'id',
        'REFRESH' => 'Y'
    ];
    $arParameters['SECTIONS'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_SECTIONS'),
        'TYPE' => 'LIST',
        'VALUES' => $arSections->asArray(function ($key, $value) {
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];
        }),
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arParameters['ELEMENTS_COUNT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_ELEMENTS_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
    $arParameters['ELEMENT_ITEMS_COUNT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_ELEMENT_ITEMS_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
}

$arParameters['HEADER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_SHOW'] === 'Y') {
    $arParameters['HEADER_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_HEADER_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_STORIES_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_STORIES_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_STORIES_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];

    $arParameters['HEADER_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_HEADER_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_STORIES_HEADER_TEXT_DEFAULT')
    ];
}

$arParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arParameters['DESCRIPTION_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_DESCRIPTION_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_STORIES_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_STORIES_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_STORIES_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];

    $arParameters['DESCRIPTION_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_STORIES_DESCRIPTION_TEXT'),
        'TYPE' => 'STRING'
    ];
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT',
    'ADDITIONAL_VALUES' => 'Y'
];
$arParameters['ORDER_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_STORIES_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_STORIES_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_STORIES_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];
$arParameters['CACHE_TIME'] = [];

$arComponentParameters = [
    'GROUPS' => [
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_STORIES_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];