<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['MAILING_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_MAILING_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['MAILING_SHOW'] === 'Y') {
    $arTemplateParameters['MAILING_PATH'] = [
        'PARENT' => 'URL_TEMPLATES',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_MAILING_PATH'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['SEF_MODE'] = [
    'index' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_MAIN_PERSONAL'),
        'DEFAULT' => 'index.php',
        'VARIABLES' => []
    ],
    'orders' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_ORDER'),
        'DEFAULT' => 'orders/',
        'VARIABLES' => ['ID']
    ],
    'account' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_ACCOUNT'),
        'DEFAULT' => 'account/',
        'VARIABLES' => ['ID']
    ],
    'subscribe' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_SUBSCRIBE'),
        'DEFAULT' => 'subscribe/',
        'VARIABLES' => ['ID']
    ],
    'profile' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_PROFILE_LIST'),
        'DEFAULT' => 'profiles/',
        'VARIABLES' => ['ID']
    ],
    'profile_detail' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_PROFILE'),
        'DEFAULT' => 'profiles/#ID#',
        'VARIABLES' => ['ID']
    ],
    'private' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_GROUP_PRIVATE'),
        'DEFAULT' => 'private/',
        'VARIABLES' => ['ID']
    ],
    'order_detail' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_DETAIL_DESC'),
        'DEFAULT' => 'orders/#ID#',
        'VARIABLES' => ['ID']
    ],
    'order_cancel' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_CANCEL_ORDER_DESC'),
        'DEFAULT' => 'cancel/#ID#',
        'VARIABLES' => ['ID']
    ]
];

$arTemplateParameters['ORDERS_LINK'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_ORDERS_LINK'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['PROFILE_LINK'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROFILE_LINK'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['CHANGE_PASSWORD_LINK'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_CHANGE_PASSWORD_LINK'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['SHOW_ICON'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SHOW_ICON'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['ALL_FIELDS_SHOW'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROFILE_ALL_FIELDS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arProperties = Arrays::fromDBResult(CUserTypeEntity::GetList([], [
    'ENTITY_ID' => 'USER',
    'USER_TYPE_ID' => 'iblock_element'
]))->indexBy('ID');

$arTemplateParameters['PROPERTY_MANAGER'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER'),
    'TYPE' => 'LIST',
    'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
        return [
            'key' => $arProperty['FIELD_NAME'],
            'value' => $arProperty['FIELD_NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

unset($arProperties);

if (!empty($arCurrentValues['PROPERTY_MANAGER'])) {

    $arIBlockTypes = CIBlockParameters::GetIBlockTypes();

    $arTemplateParameters['MANAGER_IBLOCK_TYPE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockTypes,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
    $arIBlockType = $arIBlocks->asArray();

    $hGetIblockByType = function ($sType) use ($arIBlockType) {
        $arIblockList = [];

        foreach ($arIBlockType as $sKey => $arIblock) {
            if ($arIblock['IBLOCK_TYPE_ID'] !== $sType) continue;

            $arIblockList[$arIblock['ID']] = $arIblock['NAME'];
        }

        return $arIblockList;
    };

    if (!empty($arCurrentValues['MANAGER_IBLOCK_TYPE'])) {
        $arTemplateParameters['MANAGER_IBLOCK_ID'] = [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $hGetIblockByType($arCurrentValues['MANAGER_IBLOCK_TYPE']),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
        $arTemplateParameters['MANAGER_DEFAULT_USE'] = [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_DEFAULT_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        if (!empty($arCurrentValues['MANAGER_IBLOCK_ID'])) {
            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['MANAGER_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $hPropertyTextSingle = function ($key, $arValue) {
                if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
                    return [
                        'key' => $arValue['CODE'],
                        'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
                    ];

                return ['skip' => true];
            };
            $hPropertyTextAll = function ($key, $arValue) {
                if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && empty($arValue['USER_TYPE']))
                    return [
                        'key' => $arValue['CODE'],
                        'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
                    ];

                return ['skip' => true];
            };

            $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
            $arPropertyTextAll = $arProperties->asArray($hPropertyTextAll);

            $arTemplateParameters['MANAGER_PROPERTY_POSITION'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            $arTemplateParameters['MANAGER_PROPERTY_PHONE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_PHONE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextAll,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            $arTemplateParameters['MANAGER_PROPERTY_EMAIL'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_EMAIL'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextAll,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            $arTemplateParameters['MANAGER_PROPERTY_SOCIAL_VK'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_SOCIAL_VK'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['MANAGER_PROPERTY_SOCIAL_FB'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_SOCIAL_FB'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['MANAGER_PROPERTY_SOCIAL_INST'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_SOCIAL_INST'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['MANAGER_PROPERTY_SOCIAL_TW'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_SOCIAL_TW'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['MANAGER_PROPERTY_SOCIAL_SKYPE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROPERTY_MANAGER_PROPERTY_SOCIAL_SKYPE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }
}

if (Loader::includeModule('sale'))
    include(__DIR__.'/parameters/orders.php');

if (Loader::includeModule('support')) {
    $arTemplateParameters['CLAIMS_USE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_CLAIMS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['CLAIMS_USE'] === 'Y') {
        $arTemplateParameters['SEF_MODE']['claims'] = [
            'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_CLAIMS'),
            'DEFAULT' => 'claims/',
            'VARIABLES' => ['ID']
        ];

        include(__DIR__.'/parameters/claims.php');
    }
}

$arTemplateParameters['PROFILE_ADD_USE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PROFILE_ADD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PROFILE_ADD_USE'] === 'Y') {
    $arTemplateParameters['SEF_MODE']['profile_add'] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_PROFILE_ADD'),
        'DEFAULT' => 'profile_add/',
        'VARIABLES' => ['ID']
    ];

    include(__DIR__.'/parameters/profile_add.php');
}

$arTemplateParameters['CRM_SHOW_PAGE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_CRM_SHOW_PAGE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['CRM_SHOW_PAGE'] === 'Y') {
    $arTemplateParameters['SEF_MODE']['crm'] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_CRM'),
        'DEFAULT' => 'crm/',
        'VARIABLES' => ['ID']
    ];
    $arTemplateParameters['CRM_PATH'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_CRM_PATH'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['PRODUCT_VIEWED_SHOW_PAGE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_PRODUCT_VIEWED_SHOW_PAGE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCT_VIEWED_SHOW_PAGE'] === 'Y') {
    $arTemplateParameters['SEF_MODE']['viewed'] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_SEF_MODE_PRODUCT_VIEWED'),
        'DEFAULT' => 'viewed/',
        'VARIABLES' => ['ID']
    ];

    include(__DIR__.'/parameters/viewed.php');
}
