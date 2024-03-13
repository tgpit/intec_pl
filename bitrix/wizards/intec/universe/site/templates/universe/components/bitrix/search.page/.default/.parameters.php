<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlocksType = CIBlockParameters::GetIBlockTypes();
$rsIBlocks = CIBlock::GetList();
$test = [];

while ($arIBlock = $rsIBlocks->GetNext()) {
    $test[] = $arIBlock;
    $arIBlocks[$arIBlock['IBLOCK_TYPE_ID']][$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
    $arIBlocks['all'][$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
}

$arTemplateParameters = [
	'SETTINGS_USE' => [
		'PARENT' => 'BASE',
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SETTINGS_USE'),
		'TYPE' => 'CHECKBOX'
	],
	'LAZYLOAD_USE' => [
		'PARENT' => 'BASE',
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_LAZYLOAD_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	],
	'USE_SUGGEST' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_USE_SUGGEST'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N'
	],
	'SHOW_ITEM_TAGS' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_ITEM_TAGS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	],
	'TAGS_INHERIT' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_TAGS_INHERIT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	],
	'SHOW_ITEM_DATE_CHANGE' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_ITEM_DATE_CHANGE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	],
	'SHOW_ORDER_BY' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_ORDER_BY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	],
	'SHOW_TAGS_CLOUD' => [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_TAGS_CLOUD'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	]
];

if ($arCurrentValues['SHOW_ITEM_TAGS'] == 'N')
	unset($arTemplateParameters['TAGS_INHERIT']);

if ($arCurrentValues['SHOW_TAGS_CLOUD'] == 'Y') {
	$arTemplateParameters = array_merge($arTemplateParameters, [
		'SHOW_TAGS_CLOUD' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_TAGS_CLOUD'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		],
		'TAGS_SORT' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SORT'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'VALUES' => [
			    'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_NAME'),
                'CNT' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_CNT')
            ],
			'DEFAULT' => 'NAME'
		],
		'TAGS_PAGE_ELEMENTS' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_PAGE_ELEMENTS'),
			'TYPE' => 'STRING',
			'DEFAULT' => '150'
		],
		'TAGS_PERIOD' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_PERIOD'),
			'TYPE' => 'STRING',
			'DEFAULT' => ''
		],
		'TAGS_URL_SEARCH' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_URL_SEARCH'),
			'TYPE' => 'STRING',
			'DEFAULT' => ''
		],
		'TAGS_INHERIT' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_TAGS_INHERIT'),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y'
		],
		'FONT_MAX' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_FONT_MAX'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '50'
		],
		'FONT_MIN' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_FONT_MIN'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '10'
		],
		'COLOR_NEW' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_COLOR_NEW'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '000000'
		],
		'COLOR_OLD' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_COLOR_OLD'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'C8C8C8'
		],
		'PERIOD_NEW_TAGS' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_PERIOD_NEW_TAGS'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => ''
		],
		'SHOW_CHAIN' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_CHAIN'),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y'
		],
		'COLOR_TYPE' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_COLOR_TYPE'),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y'
		],
		'WIDTH' => [
			'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_WIDTH'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '100%'
		]
	]);
}

if (COption::GetOptionString('search', 'use_social_rating') == 'Y') {
	$arTemplateParameters['SHOW_RATING'] = [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_RATING'),
		'TYPE' => 'LIST',
		'VALUES' => [
			'' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_SHOW_RATING_CONFIG'),
			'Y' => Loc::getMessage('MAIN_YES'),
			'N' => Loc::getMessage('MAIN_NO')
		],
		'MULTIPLE' => 'N',
		'DEFAULT' => ''
	];
	$arTemplateParameters['RATING_TYPE'] = [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => [
			'' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE_CONFIG'),
			'like' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE_LIKE_TEXT'),
			'like_graphic' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE_LIKE_GRAPHIC'),
			'standart_text' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE_STANDART_TEXT'),
			'standart' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_RATING_TYPE_STANDART_GRAPHIC')
		],
		'MULTIPLE' => 'N',
		'DEFAULT' => ''
	];
	$arTemplateParameters['PATH_TO_USER_PROFILE'] = [
		'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_PATH_TO_USER_PROFILE'),
		'TYPE' => 'STRING',
		'DEFAULT' => ''
	];
}

$arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_USE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_USE'] === 'Y') {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocksType,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE'])) {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks[$arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_TYPE']],
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID'])) {
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_TITLE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_TITLE_DEFAULT')
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_COUNT_ELEMENTS'),
        'TYPE' => 'STRING',
        'DEFAULT' => '6'
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_IS_CATALOG'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BLOCK_ON_EMPTY_RESULTS_PROPERTY_USE'] === 'Y') {
        $arProperties = null;
        $rsProperties = CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $arCurrentValues['BLOCK_ON_EMPTY_RESULTS_IBLOCK_ID']]);

        while ($arProperty = $rsProperties->GetNext()) {
            if ($arProperty['PROPERTY_TYPE'] === 'L')
                $arProperties[$arProperty['CODE']] = '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME'];
        }

        $arTemplateParameters['BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'] = [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('C_SEARCH_PAGE_DEFAULT_BLOCK_ON_EMPTY_RESULTS_PROPERTY_FILTER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    include(__DIR__.'/parameters/elements.php');
}
