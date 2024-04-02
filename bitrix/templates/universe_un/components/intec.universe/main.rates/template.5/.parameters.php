<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyText = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyList = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'L' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyText = $arProperties->asArray($hPropertyText);
    $arPropertyList = $arProperties->asArray($hPropertyList);

    $arTemplateParameters['PROPERTY_PRICE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTY_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
        $arTemplateParameters['PROPERTY_CURRENCY'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTY_CURRENCY'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyList,
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_DISCOUNT'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTY_DISCOUNT'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_DISCOUNT'])) {
            $arTemplateParameters['PROPERTY_DISCOUNT_TYPE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTY_DISCOUNT_TYPE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyList,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }

    $arTemplateParameters['PROPERTY_DETAIL_URL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTY_DETAIL_URL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'simple' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_VIEW_SIMPLE'),
        'tabs' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_VIEW_TABS')
    ],
    'DEFAULT' => 'tabs',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VIEW'] === 'tabs') {
    $arTemplateParameters['TABS_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_TABS_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arTemplateParameters['SECTION_DESCRIPTION_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SECTION_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SECTION_DESCRIPTION_SHOW'] === 'Y') {
        $arTemplateParameters['SECTION_DESCRIPTION_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SECTION_DESCRIPTION_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_LEFT'),
                'center' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_CENTER'),
                'right' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_POSITION_RIGHT')
            ],
            'DEFAULT' => 'center'
        ];
    }
}

if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
    $arTemplateParameters['PRICE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PRICE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if (!empty($arCurrentValues['PROPERTY_DISCOUNT'])) {
        $arTemplateParameters['DISCOUNT_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_DISCOUNT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}

$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['PROPERTY_LIST'])) {
    $arTemplateParameters['PROPERTIES_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_PROPERTIES_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
    $arTemplateParameters['BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_TEXT_DEFAULT')
    ];
    $arTemplateParameters['BUTTON_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'detail' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_MODE_DETAIL'),
            'order' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_BUTTON_MODE_ORDER')
        ],
        'DEFAULT' => 'order',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BUTTON_MODE'] === 'detail') {
        $arTemplateParameters['DETAIL_BLANK'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_DETAIL_BLANK'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    } else if ($arCurrentValues['BUTTON_MODE'] === 'order') {
        /**
         * @var array $arForms - список форм
         * @var array $arFormFields список полей выбранной формы
         */

        if (Loader::includeModule('form'))
            include(__DIR__.'/parameters/base.php');
        elseif (Loader::includeModule('intec.startshop'))
            include(__DIR__.'/parameters/lite.php');
        else
            return;

        $arTemplates = [];

        foreach ($rsTemplates as $arTemplate)
            $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);

        $arTemplateParameters['ORDER_FORM_ID'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_FORM_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arForms,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['ORDER_FORM_ID'])) {
            $arTemplateParameters['ORDER_FORM_TEMPLATE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_FORM_TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => $arTemplates,
                'ADDITIONAL_VALUES' => 'Y',
                'DEFAULT' => '.default'
            ];
            $arTemplateParameters['ORDER_FORM_FIELD'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_FORM_FIELD'),
                'TYPE' => 'LIST',
                'VALUES' => $arFormFields,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['ORDER_FORM_TITLE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_FORM_TITLE'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_FORM_TITLE_DEFAULT')
            ];
            $arTemplateParameters['ORDER_CONSENT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_ORDER_CONSENT'),
                'TYPE' => 'STRING',
                'DEFAULT' => '#SITE_DIR#company/consent/'
            ];
        }
    }
}

$arTemplateParameters['SLIDER_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SLIDER_USE'] === 'Y') {
    $arTemplateParameters['SLIDER_NAV'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_NAV'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['SLIDER_DOTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_DOTS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['SLIDER_LOOP'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_LOOP'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['SLIDER_AUTO_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_AUTO_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SLIDER_AUTO_USE'] === 'Y') {
        $arTemplateParameters['SLIDER_AUTO_TIME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_AUTO_TIME'),
            'TYPE' => 'STRING',
            'DEFAULT' => '10000'
        ];
        $arTemplateParameters['SLIDER_AUTO_HOVER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_5_SLIDER_AUTO_HOVER'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}