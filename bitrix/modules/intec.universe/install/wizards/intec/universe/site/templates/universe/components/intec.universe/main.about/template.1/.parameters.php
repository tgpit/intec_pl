<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

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
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID']) && !empty($arCurrentValues['ELEMENT'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyText = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyFile = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'F' && $value['LIST_TYPE'] && $value['MULTIPLE'] === 'N')
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesText = $arProperties->asArray($hPropertyText);
    $arPropertiesFile = $arProperties->asArray($hPropertyFile);

    $arTemplateParameters['PROPERTY_BACKGROUND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PROPERTY_BACKGROUND'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesFile,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PROPERTY_TITLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PROPERTY_LINK'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' =>'Y'
    ];
    $arTemplateParameters['PROPERTY_VIDEO'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PROPERTY_VIDEO'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesText,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_BACKGROUND'])) {
        $arTemplateParameters['BACKGROUND_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BACKGROUND_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    if (!empty($arCurrentValues['PROPERTY_TITLE'])) {
        $arTemplateParameters['TITLE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_TITLE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    $arTemplateParameters['PREVIEW_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PREVIEW_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if (!empty($arCurrentValues['PROPERTY_LINK'])) {
        $arTemplateParameters['BUTTON_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BUTTON_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
            $arTemplateParameters['BUTTON_BLANK'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BUTTON_BLANK'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
            $arTemplateParameters['BUTTON_TEXT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BUTTON_TEXT'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BUTTON_TEXT_DEFAULT')
            ];
        }
    }

    if (Type::isArray($arCurrentValues['PICTURE_SOURCES']) && !empty(array_filter($arCurrentValues['PICTURE_SOURCES']))) {
        $arTemplateParameters['PICTURE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
            $arTemplateParameters['PICTURE_SIZE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'auto' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE_AUTO'),
                    'cover' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE_COVER'),
                    'contain' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE_CONTAIN')
                ],
                'DEFAULT' => 'auto'
            ];
            $arTemplateParameters['POSITION_HORIZONTAL'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_HORIZONTAL'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'left' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_LEFT'),
                    'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_CENTER'),
                    'right' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_RIGHT')
                ],
                'DEFAULT' => 'center'
            ];
            $arTemplateParameters['POSITION_VERTICAL'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_VERTICAL'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'top' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_TOP'),
                    'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_CENTER'),
                    'bottom' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_BOTTOM')
                ],
                'DEFAULT' => 'center'
            ];
        }
    }

    if (!empty($arCurrentValues['PROPERTY_VIDEO'])) {
        $arTemplateParameters['VIDEO_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_VIDEO_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}