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

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'SITE_ID' => $sSite,
    'ACTIVE' => 'Y'
]));

$arTemplateParameters = [];

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if (!empty($arCurrentValues['IBLOCK_TYPE']) && $value['IBLOCK_TYPE_ID'] !== $arCurrentValues['IBLOCK_TYPE'])
            return ['skip' => true];

        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['ELEMENTS_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_ELEMENTS_COUNT'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyCheckboxSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'C' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyLink = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] == 'E' && $value['LIST_TYPE'] === 'L')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyTextSingle = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyCheckboxSingle = $arProperties->asArray($hPropertyCheckboxSingle);
    $arPropertiesLink = $arProperties->asArray($hPropertyLink);
    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);

    $arTemplateParameters['PROPERTY_FILTER'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PROPERTY_FILTER'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckboxSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PRODUCTS'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PROPERTY_PRODUCTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesLink,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PRODUCTS'])) {
        include(__DIR__.'/parameters/products.php');
    }

    $arTemplateParameters['PROPERTY_PREVIEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PROPERTY_PREVIEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['HEADER_BLOCK_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['HEADER_BLOCK_SHOW'] === 'Y') {
        $arTemplateParameters['HEADER_BLOCK_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_POSITION_LEFT'),
                'center' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_POSITION_CENTER'),
                'right' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_POSITION_RIGHT'),
            ],
            'DEFAULT' => 'center'
        ];
        $arTemplateParameters['HEADER_BLOCK_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_HEADER_BLOCK_TEXT_DEFAULT')
        ];
    }

    $arTemplateParameters['DESCRIPTION_BLOCK_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DESCRIPTION_BLOCK_SHOW'] === 'Y') {
        $arTemplateParameters['DESCRIPTION_BLOCK_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_POSITION_LEFT'),
                'center' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_POSITION_CENTER'),
                'right' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_POSITION_RIGHT'),
            ],
            'DEFAULT' => 'center'
        ];
        $arTemplateParameters['DESCRIPTION_BLOCK_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DESCRIPTION_BLOCK_TEXT'),
            'TYPE' => 'STRING'
        ];
    }

    $arTemplateParameters['DATE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DATE_SHOW'] === 'Y') {
        $arTemplateParameters['DATE_SOURCE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SOURCE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'DATE_CREATE' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SOURCE_CREATE'),
                'DATE_ACTIVE_FROM' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SOURCE_ACTIVE_FROM'),
                'DATE_ACTIVE_TO' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SOURCE_ACTIVE_TO'),
                'TIMESTAMP_X' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_SOURCE_TIMESTAMP')
            ],
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => 'DATE_ACTIVE_FROM'
        ];
        $arTemplateParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
            Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_DATE_FORMAT'),
            'VISUAL'
        );
    }

    $arTemplateParameters['RATING_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_RATING_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['LINK_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_LINK_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['LINK_USE'] === 'Y') {
        $arTemplateParameters['LINK_BLANK'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_LINK_BLANK'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID']) && Type::isArray($arCurrentValues['PRODUCTS_PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRODUCTS_PRICE_CODE']))) {
        $arTemplateParameters['PRICE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRICE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PRICE_SHOW'] === 'Y') {
            $arTemplateParameters['PRICE_DISCOUNT_SHOW'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRICE_DISCOUNT_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }
    }
}

$arTemplateParameters['SORT_BY'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT'
];
$arTemplateParameters['ORDER_BY'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];