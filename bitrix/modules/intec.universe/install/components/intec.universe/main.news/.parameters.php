<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

$arIBlock = null;
$arFilter = [
    'ACTIVE' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_TYPE']))
    $arFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], $arFilter))->indexBy('ID');

if (!empty($arCurrentValues['IBLOCK_ID']))
    $arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arParameters = [];

$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) {
        return [
            'key' => $arIBlock['ID'],
            'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['ELEMENTS_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_ELEMENTS_COUNT'),
    'TYPE' => 'STRING'
];
$arParameters['HEADER_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_HEADER_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_BLOCK_SHOW'] === 'Y') {
    $arParameters['HEADER_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_HEADER_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_NEWS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_NEWS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_NEWS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['HEADER_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_HEADER_BLOCK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_NEWS_HEADER_BLOCK_TEXT_DEFAULT')
    ];
}

$arParameters['DESCRIPTION_BLOCK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_DESCRIPTION_BLOCK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_BLOCK_SHOW'] === 'Y') {
    $arParameters['DESCRIPTION_BLOCK_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_DESCRIPTION_BLOCK_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_NEWS_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_NEWS_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_NEWS_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arParameters['DESCRIPTION_BLOCK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_DESCRIPTION_BLOCK_TEXT'),
        'TYPE' => 'STRING'
    ];
}

$arParameters['DATE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_DATE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
    Loc::getMessage('C_MAIN_NEWS_DATE_FORMAT'),
    'VISUAL'
);
$arParameters['LIST_PAGE_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_LIST_PAGE_URL'),
    'TYPE' => 'STRING'
];
$arParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'SECTION_URL',
    Loc::getMessage('C_MAIN_NEWS_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'DETAIL_URL',
    Loc::getMessage('C_MAIN_NEWS_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);
$arParameters['NAVIGATION_USE'] = [
    'PARENT' => 'NAVIGATION',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NAVIGATION_USE'] === 'Y') {
    $arParameters['NAVIGATION_ID'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'news'
    ];
    $arParameters['NAVIGATION_MODE'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'standard' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_MODE_STANDARD'),
            'ajax' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_MODE_AJAX')
        ],
        'DEFAULT' => 'ajax',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['NAVIGATION_MODE'] === 'standard') {
        $arParameters['NAVIGATION_ALL'] = [
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('C_MAIN_NEWS_NAVIGATION_ALL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    include(__DIR__.'/parameters/navigation.template.php');
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT',
    'ADDITIONAL_VALUES' => 'Y'
];
$arParameters['ORDER_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_NEWS_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_NEWS_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_NEWS_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];
$arParameters['CACHE_TIME'] = [];

$arComponentParameters = [
    'GROUPS' => [
        'NAVIGATION' => [
            'NAME' => Loc::getMessage('C_MAIN_NEWS_GROUPS_NAVIGATION'),
            'SORT' => 410
        ],
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_NEWS_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];