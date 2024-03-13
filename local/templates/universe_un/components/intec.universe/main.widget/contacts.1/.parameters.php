<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var string $siteTemplate
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(
    ['SORT' => 'ASC'],
    ['ACTIVE' => 'Y']
))->indexBy('ID');

$arTemplateParameters = [];

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['IBLOCK_TYPE'])
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (empty($arCurrentValues['IBLOCK_ID']))
    return;

$arTemplateParameters['NEWS_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_NEWS_COUNT'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];
$arTemplateParameters['STAFF_IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_STAFF_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['STAFF_IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_STAFF_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks->asArray(function ($key, $value) use (&$arCurrentValues) {
        if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['STAFF_IBLOCK_TYPE'])
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['MODE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_MODE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ID' => Loc::getMessage('C_WIDGET_CONTACTS_1_MODE_ID'),
        'CODE' => Loc::getMessage('C_WIDGET_CONTACTS_1_MODE_CODE')
    ],
    'DEFAULT' => 'ID',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['MAP_VENDOR'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAP_VENDOR'),
        'TYPE' => 'LIST',
        'VALUES' => array(
            'google' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAP_VENDOR_GOOGLE'),
            'yandex' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAP_VENDOR_YANDEX'),
        ),
        'ADDITIONAL_VALUES' => 'N',
        'DEFAULT' => 'google'
    ];
}

$arTemplateParameters['MAP_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAP_ID'),
    'TYPE' => 'STRING',
    'DEFAULT' => null
];

if (Loader::IncludeModule('form'))
    include('parameters/base/forms.php');
else if (Loader::IncludeModule('intec.startshop'))
    include('parameters/lite/forms.php');

if (!empty($arCurrentValues['FORM_ID'])) {
    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['CONSENT_SHOW'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_CONSENT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
    }

    if ($arCurrentValues['CONSENT_SHOW'] === 'Y' || $arCurrentValues['SETTINGS_USE'] === 'Y') {
        $arTemplateParameters['CONSENT_URL'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_CONSENT_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#SITE_DIR#company/consent/'
        ];
    }
}

$arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
    'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
    'ACTIVE' => 'Y'
]))->indexBy('CODE');

$hPropertyMap = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] === 'S' &&
        $value['MULTIPLE'] === 'N' && (
            $value['USER_TYPE'] === 'map_yandex' ||
            $value['USER_TYPE'] === 'map_google'
        )
    )
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertyText = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] == 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N' && empty($value['USER_TYPE']))
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};
$hPropertyLink = function ($key, $value) {
    if ($value['PROPERTY_TYPE'] == 'E' && $value['MULTIPLE'] === 'N')
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];

    return ['skip' => true];
};

$arPropertyMap = $arProperties->asArray($hPropertyMap);
$arPropertyText = $arProperties->asArray($hPropertyText);
$arPropertyLink = $arProperties->asArray($hPropertyLink);

$arElements = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
    'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
    'ACTIVE' => 'Y'
]))->indexBy($arCurrentValues['MODE']);

$arTemplateParameters['MAIN'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAIN'),
    'TYPE' => 'LIST',
    'VALUES' => $arElements->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y'
];

unset($arElements);

$arTemplateParameters['PROPERTY_MAP'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_PROPERTY_MAP'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyMap,
    'ADDITIONAL_VALUES' => 'Y'
];
$arTemplateParameters['PROPERTY_PHONE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_PROPERTY_PHONE'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyText,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PROPERTY_ADDRESS'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_PROPERTY_ADDRESS'),
    'TYPE' => 'LIST',
    'VALUES' => $arPropertyText,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['STAFF_IBLOCK_ID'])) {
    $arStaffPersons = Arrays::fromDBResult(CIBlockElement::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['STAFF_IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]))->indexBy($arCurrentValues['MODE']);

    $arTemplateParameters['STAFF_PERSON'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_STAFF_PERSON'),
        'TYPE' => 'LIST',
        'VALUES' => $arStaffPersons->asArray(function ($key, $value) {
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    unset($arStaffPersons);
}

if (!empty($arCurrentValues['PROPERTY_PHONE'])) {
    $arTemplateParameters['PHONE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_PHONE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['PROPERTY_ADDRESS'])) {
    $arTemplateParameters['ADDRESS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_ADDRESS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['FEEDBACK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FEEDBACK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FEEDBACK_SHOW'] === 'Y') {
    $arTemplateParameters['FEEDBACK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FEEDBACK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_WIDGET_CONTACTS_1_FEEDBACK_TEXT_DEFAULT')
    ];

    if (!empty($arCurrentValues['FORM_ID'])) {
        $arTemplateParameters['FEEDBACK_BUTTON_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FEEDBACK_BUTTON_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_WIDGET_CONTACTS_1_FEEDBACK_BUTTON_TEXT_DEFAULT')
        ];
    }

    $arTemplateParameters['STAFF_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_STAFF_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['STAFF_SHOW'] === 'Y') {
        $arTemplateParameters['STAFF_DEFAULT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_STAFF_DEFAULT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#TEMPLATE_PATH#images/face.png'
        ];
    }
}

$arTemplateParameters['MAP_GRAY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_MAP_GRAY'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

include(__DIR__.'/parameters/regionality.php');