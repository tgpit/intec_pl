<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

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

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], [
    'ACTIVE' => 'Y'
]))->indexBy('ID');
$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['NAME_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_NAME_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
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
    $hPropertyElement = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'E' && $arValue['LIST_TYPE'] === 'L')
            return [
                'key' => $arValue['CODE'],
                'value' => '[' . $arValue['CODE'] . '] ' . $arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyTextAll = $arProperties->asArray($hPropertyTextAll);
    $arPropertyElement = $arProperties->asArray($hPropertyElement);

    $arTemplateParameters['PROPERTY_POSITION'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_POSITION'])) {
        $arTemplateParameters['POSITION_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_POSITION_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_PHONE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_PHONE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PHONE'])) {
        $arTemplateParameters['PHONE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PHONE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_EMAIL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_EMAIL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_EMAIL'])) {
        $arTemplateParameters['EMAIL_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_EMAIL_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_SOCIAL_VK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_SOCIAL_VK'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_FB'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_SOCIAL_FB'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_INST'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_SOCIAL_INST'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_TW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_SOCIAL_TW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SOCIAL_SKYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_SOCIAL_SKYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (
        $arCurrentValues['PROPERTY_SOCIAL_VK'] ||
        $arCurrentValues['PROPERTY_SOCIAL_FB'] ||
        $arCurrentValues['PROPERTY_SOCIAL_INST'] ||
        $arCurrentValues['PROPERTY_SOCIAL_TW'] ||
        $arCurrentValues['PROPERTY_SOCIAL_SKYPE']
    ) {
        $arTemplateParameters['SOCIAL_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_SOCIAL_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['SOCIAL_SHOW'] === 'Y' && !empty($arCurrentValues['PROPERTY_SOCIAL_SKYPE'])) {
            $arTemplateParameters['SOCIAL_SKYPE_ACTION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_SOCIAL_SKYPE_ACTION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'chat' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_SOCIAL_SKYPE_ACTION_CHAT'),
                    'call' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_SOCIAL_SKYPE_ACTION_CALL')
                ],
                'DEFAULT' => 'chat'
            ];
        }
    }

    $arTemplateParameters['PROPERTY_DESCRIPTION_HEADER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_DESCRIPTION_HEADER'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['DESCRIPTION_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y' && !empty($arCurrentValues['PROPERTY_DESCRIPTION_HEADER'])) {
        $arTemplateParameters['DESCRIPTION_HEADER_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_DESCRIPTION_HEADER_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PROPERTY_PROJECTS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_PROJECTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyElement,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PROJECTS'])) {
        $arTemplateParameters['PROJECTS_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROJECTS_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
        $arTemplateParameters['PROJECTS_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROJECTS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['PROJECTS_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['PROJECTS_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        include(__DIR__.'/parameters/projects.php');
    }

    $arTemplateParameters['PROPERTY_REVIEWS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_PROPERTY_REVIEWS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyElement,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_REVIEWS'])) {
        $arTemplateParameters['REVIEWS_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_REVIEWS_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
        $arTemplateParameters['REVIEWS_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_REVIEWS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['REVIEWS_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['REVIEWS_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        include(__DIR__.'/parameters/reviews.php');
    }
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base.php');
else if (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');

$arTemplateParameters['BUTTON_BACK_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_BUTTON_BACK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_BACK_SHOW'] === 'Y') {
    $arTemplateParameters['BUTTON_BACK_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_BUTTON_BACK_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_BUTTON_BACK_TEXT_DEFAULT')
    ];
}
