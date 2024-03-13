<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\Model\PropertyFeature;
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

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$sSite = null;

if (!empty($_REQUEST['site'])) {
    $sSite = $_REQUEST['site'];
} else if (!empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$bEnabledProperties = PropertyFeature::isEnabledFeatures();

$arIBlocks = [
    'type' => CIBlockParameters::GetIBlockTypes(),
    'items' => []
];

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arIBlocks['items'] = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite,
        'TYPE' => $arCurrentValues['IBLOCK_TYPE']
    ]));
} else {
    $arIBlocks['items'] = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC']));
}

$arTemplateParameters = [];

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks['type'],
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks['items']->asArray(function ($key, $value) {
        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['MODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'period' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_MODE_PERIOD'),
            'day' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_MODE_DAY')
        ],
        'DEFAULT' => 'period',
        'REFRESH' => 'Y'
    ];

    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertyDate = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['USER_TYPE'] === 'Date' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyText = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && empty($value['USER_TYPE']) && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyCheckbox = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'C' && empty($value['USER_TYPE']) && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyFiles = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'F' && $value['LIST_TYPE'] === 'L' && empty($value['USER_TYPE']) && $value['MULTIPLE'] === 'Y')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    if ($arCurrentValues['MODE'] === 'day') {
        $arTemplateParameters['PROPERTY_DAY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_DAY'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertyDate),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    } else {
        $arTemplateParameters['PROPERTY_PERIOD_START'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_PERIOD_START'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertyDate),
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_PERIOD_END'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_PERIOD_END'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertyDate),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['SORT_BY'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'SORT' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_SORT'),
            'ID' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_ID'),
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_NAME'),
            'SHOWS' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_SHOWS'),
            'TIMESTAMP_X' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_TIMESTAMP_X'),
            'ACTIVE_FROM' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_ACTIVE_FROM'),
            'ACTIVE_TO' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SORT_BY_ACTIVE_TO'),
        ],
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'SORT'
    ];
    $arTemplateParameters['ORDER_BY'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_BY'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'ASC' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_BY_ASC'),
            'DESC' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ORDER_BY_DESC')
        ],
        'DEFAULT' => 'ASC'
    ];
    $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_MARKS_HIT'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_MARKS_NEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_MARKS_RECOMMEND'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_SHARE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_MARKS_SHARE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyFiles),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ARTICLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyText),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertyCheckbox),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    include(__DIR__.'/parameters/prices.php');

    if ($bBase)
        include(__DIR__.'/parameters/base/forms.php');
    else if ($bLite)
        include(__DIR__.'/parameters/lite/forms.php');

    if (!empty($arCurrentValues['FORM_ID'])) {
        $arTemplateParameters['FORM_TITLE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_FORM_TITLE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_FORM_TITLE_DEFAULT')
        ];
    }

    include(__DIR__.'/parameters/order.fast.php');

    $arTemplateParameters['CONSENT_URL'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_CONSENT_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#company/consent/'
    ];
}

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['HEADER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_SHOW'] === 'Y') {
    $arTemplateParameters['HEADER_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arTemplateParameters['HEADER_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_HEADER_TEXT_DEFAULT')
    ];
}

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arTemplateParameters['DESCRIPTION_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DESCRIPTION_TEXT'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['GALLERY_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_GALLERY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUANTITY_SHOW'] === 'Y') {
    $arTemplateParameters['QUANTITY_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'number' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_MODE_NUMBER'),
            'logic' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_MODE_LOGIC'),
            'text' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_MODE_TEXT')
        ],
        'DEFAULT' => 'number',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['QUANTITY_MODE'] === 'text') {
        $arTemplateParameters['QUANTITY_BOUNDS_MANY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_BOUNDS_MANY'),
            'TYPE' => 'STRING',
            'DEFAULT' => '50'
        ];
        $arTemplateParameters['QUANTITY_BOUNDS_FEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_QUANTITY_BOUNDS_FEW'),
            'TYPE' => 'STRING',
            'DEFAULT' => '10'
        ];
    }
}

if (!empty($arCurrentValues['PROPERTY_MARKS_HIT']) || !empty($arCurrentValues['PROPERTY_MARKS_NEW']) || !empty($arCurrentValues['PROPERTY_MARKS_RECOMMEND']) || !empty($arCurrentValues['PROPERTY_MARKS_SHARE'])) {
    $arTemplateParameters['MARKS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_MARKS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['PROPERTY_ARTICLE'])) {
    $arTemplateParameters['ARTICLE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ARTICLE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['VOTE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_VOTE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VOTE_USE'] === 'Y') {
    $arTemplateParameters['VOTE_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_VOTE_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rating' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_VOTE_MODE_RATING'),
            'vote_avg' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_VOTE_MODE_AVG')
        ],
        'DEFAULT' => 'rating'
    ];
}

$arTemplateParameters['COMPARE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_COMPARE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['COMPARE_USE'] === 'Y') {
    $arTemplateParameters['COMPARE_CODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_COMPARE_CODE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'compare'
    ];
}

$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ACTION_NONE'),
        'buy' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ACTION_BUY'),
        'order' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_ACTION_ORDER'),
    ],
    'DEFAULT' => 'none',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ACTION'] !== 'none') {
    if ($arCurrentValues['ACTION'] === 'buy') {
        $arTemplateParameters['BASKET_URL'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_BASKET_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#SITE_DIR#personal/basket/'
        ];

        if ($bBase) {
            $arTemplateParameters['DELAY_USE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DELAY_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
            $arTemplateParameters['SUBSCRIBE_USE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SUBSCRIBE_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }

        $arTemplateParameters['COUNTER_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_COUNTER_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}

if ($bBase) {
    $arTemplateParameters['TIMER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TIMER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['TIMER_SHOW'] === 'Y' || $arCurrentValues['QUICK_VIEW_TIMER_SHOW'] === 'Y') {
        include(__DIR__ . '/parameters/base/timer.php');
    }
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    include(__DIR__ . '/parameters/quick.view.php');
}

$arTemplateParameters['LIST_PAGE_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_LIST_PAGE_URL'),
    'TYPE' => 'STRING',
    'DEFAULT' => ''
];
$arTemplateParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'SECTION_URL',
    Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);
$arTemplateParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'DETAIL_URL',
    Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);