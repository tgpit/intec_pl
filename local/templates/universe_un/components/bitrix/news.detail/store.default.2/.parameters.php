<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::IncludeModule('iblock'))
    return;

$arTemplateParameters = [
    'MAP_SHOW' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_MAP_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ],
    'DESCRIPTION_SHOW' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ],
    'MAP_VENDOR' => [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_MAP_VENDOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'google' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_MAP_VENDOR_GOOGLE'),
            'yandex' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_MAP_VENDOR_YANDEX'),
        ],
        'ADDITIONAL_VALUES' => 'N',
        'DEFAULT' => 'yandex'
    ]
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyText = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertyFileAll = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'F' && $arValue['LIST_TYPE'] === 'L')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertyMap = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && ($arValue['USER_TYPE'] == 'map_google' || $arValue['USER_TYPE'] == 'map_yandex'))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyText = $arProperties->asArray($hPropertyText);
    $arPropertyFileAll = $arProperties->asArray($hPropertyFileAll);
    $arPropertyMap = $arProperties->asArray($hPropertyMap);

    if ($arCurrentValues['MAP_SHOW'] == 'Y') {
        $arTemplateParameters['PROPERTY_MAP'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_MAP'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyMap,
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['API_KEY_MAP'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_API_KEY_MAP'),
            'TYPE' => 'STRING'
        ];
    }

    $arTemplateParameters['PICTURE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PICTURE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
        $arTemplateParameters['PICTURE_SOURCE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PICTURE_SOURCE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'preview' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PICTURE_SOURCE_PREVIEW'),
                'detail' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PICTURE_SOURCE_DETAIL'),
                'property' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PICTURE_SOURCE_PROPERTY')
            ],
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => 'preview',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PICTURE_SOURCE'] === 'property') {
            $arTemplateParameters['PROPERTY_PICTURES'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_PICTURES'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyFileAll,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }

    $arTemplateParameters['ADDRESS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_ADDRESS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ADDRESS_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_ADDRESS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_ADDRESS'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['PHONE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PHONE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PHONE_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_PHONE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_PHONE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['EMAIL_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_EMAIL_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['EMAIL_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_EMAIL'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_EMAIL'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['SCHEDULE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SCHEDULE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SCHEDULE_SHOW'] === 'Y') {
        $arTemplateParameters['PROPERTY_SCHEDULE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PROPERTY_SCHEDULE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['SOCIAL_SERVICES_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SOCIAL_SERVICES_SHOW'] === 'Y') {
    $arTemplateParameters['SOCIAL_SERVICES_VK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_VK'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_FACEBOOK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_FACEBOOK'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_INSTAGRAM'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_INSTAGRAM'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_TWITTER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_TWITTER'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_SKYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_SKYPE'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_YOUTUBE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_YOUTUBE'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_OK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_OK'),
        'TYPE' => 'STRING'
    ];
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base.php');
elseif (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');
else
    return;

$arTemplates = [];

foreach ($rsTemplates as $arTemplate)
    $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];
    $arTemplateParameters['FORM_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_TITLE_DEFAULT')
    ];
    $arTemplateParameters['CONSENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_CONSENT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#company/consent/'
    ];
}

$arTemplateParameters['FORM_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_SHOW'),
    'TYPE' => 'CHECKBOX'
];