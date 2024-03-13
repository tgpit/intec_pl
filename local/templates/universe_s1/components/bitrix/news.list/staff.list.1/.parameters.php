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

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SECTIONS_MODE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SECTIONS_MODE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SECTIONS_MODE'] === 'Y') {
    $arTemplateParameters['SECTIONS_ROOT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SECTIONS_ROOT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SECTIONS_ROOT'] === 'Y') {
        $arTemplateParameters['SECTIONS_ROOT_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SECTIONS_ROOT_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SECTIONS_ROOT_NAME_DEFAULT')
        ];
        $arTemplateParameters['SECTIONS_ROOT_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SECTIONS_ROOT_DESCRIPTION'),
            'TYPE' => 'STRING'
        ];
    }
}

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFUALT' => 'N'
    ];
}

$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFUALT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
    $arTemplateParameters['PICTURE_VIEW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PICTURE_VIEW'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'squared' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PICTURE_VIEW_SQUARED'),
            'rounded' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PICTURE_VIEW_ROUNDED')
        ],
        'DEFAULT' => 'squared'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '[' . $arValue['CODE'] . '] ' . $arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyTextAll = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '[' . $arValue['CODE'] . '] ' . $arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyTextAll = $arProperties->asArray($hPropertyTextAll);

    $arTemplateParameters['PROPERTY_POSITION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_POSITION'])) {
        $arTemplateParameters['POSITION_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_POSITION_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_PHONE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_PHONE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PHONE'])) {
        $arTemplateParameters['PHONE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PHONE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_EMAIL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_EMAIL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_EMAIL'])) {
        $arTemplateParameters['EMAIL_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_EMAIL_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_SOCIAL_VK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_SOCIAL_VK'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_FB'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_SOCIAL_FB'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_INST'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_SOCIAL_INST'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_TW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_SOCIAL_TW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_SKYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PROPERTY_SOCIAL_SKYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (
        !empty($arCurrentValues['PROPERTY_SOCIAL_VK']) ||
        !empty($arCurrentValues['PROPERTY_SOCIAL_FB']) ||
        !empty($arCurrentValues['PROPERTY_SOCIAL_INST']) ||
        !empty($arCurrentValues['PROPERTY_SOCIAL_TW']) ||
        !empty($arCurrentValues['PROPERTY_SOCIAL_SKYPE'])
    ) {
        $arTemplateParameters['SOCIAL_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SOCIAL_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['SOCIAL_SHOW'] === 'Y' && !empty($arCurrentValues['PROPERTY_SOCIAL_SKYPE'])) {
            $arTemplateParameters['SOCIAL_SKYPE_ACTION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SOCIAL_SKYPE_ACTION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'chat' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SOCIAL_SKYPE_ACTION_CHAT'),
                    'call' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_SOCIAL_SKYPE_ACTION_CALL')
                ],
                'DEFAULT' => 'chat'
            ];
        }
    }
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base.php');
else if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');

$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PREVIEW_SHOW'] === 'Y') {
    $arTemplateParameters['PREVIEW_TRUNCATE_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PREVIEW_TRUNCATE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PREVIEW_TRUNCATE_USE'] === 'Y') {
        $arTemplateParameters['PREVIEW_TRUNCATE_COUNT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_PREVIEW_TRUNCATE_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => 30
        ];
    }
}