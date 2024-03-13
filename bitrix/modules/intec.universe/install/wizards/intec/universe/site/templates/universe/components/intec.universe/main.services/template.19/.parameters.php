<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

global $USER_FIELD_MANAGER;

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
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperty_UF = [];
    $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
        'IBLOCK_'.$arCurrentValues['IBLOCK_ID'].'_SECTION',
        0,
        LANGUAGE_ID
    );

    foreach ($arUserFields as $arUserField) {
        if ($arUserField['USER_TYPE']['USER_TYPE_ID'] !== 'file')
            continue;

        $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];

        $arProperty_UF[$arUserField['FIELD_NAME']] = $arUserField['LIST_COLUMN_LABEL'] ?
            '['.$arUserField['FIELD_NAME'].'] '.$arUserField['LIST_COLUMN_LABEL'] :
            $arUserField['FIELD_NAME'];
    }

    $arTemplateParameters['PROPERTY_SVG_FILE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_PROPERTY_SVG_FILE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'N',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => $arProperty_UF,
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
    $arTemplateParameters['PICTURE_SIZE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_PICTURE_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'default' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_PICTURE_SIZE_DEFAULT'),
            'small' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_PICTURE_SIZE_SMALL')
        ],
        'DEFAULT' => 'default'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID']) && !empty($arCurrentValues['PROPERTY_SVG_FILE'])) {
        $arTemplateParameters['SVG_FILE_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_SVG_FILE_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
    }
}

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['CHILDREN_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_CHILDREN_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];