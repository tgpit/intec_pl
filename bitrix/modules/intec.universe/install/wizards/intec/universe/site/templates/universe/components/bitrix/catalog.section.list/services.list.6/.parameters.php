<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_LINK_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_DESCRIPTION_LINK_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DESCRIPTION_LINK_USE'] === 'Y'){
        $arTemplateParameters['DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_DESCRIPTION_LINK'),
            'TYPE' => 'STRING',
        ];
    } else {
        $arTemplateParameters['DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_DESCRIPTION'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_DESCRIPTION_TEXT_DEFAULT')
        ];
    }
}

$arTemplateParameters['SECTION_DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_SECTION_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['ELEMENTS_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_SERVICES_LIST_6_ELEMENTS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

