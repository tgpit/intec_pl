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

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['TIMER_QUANTITY_OVER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_QUANTITY_OVER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['TIMER_TITLE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_TITLE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['TIMER_TITLE_SHOW'] === 'Y') {
    $arTemplateParameters['TIMER_TITLE_ENTER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_TITLE_ENTER'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['TIMER_TITLE_ENTER'] === 'Y') {
        $arTemplateParameters['TIMER_TITLE_VALUE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_TITLE_VALUE'),
            'TYPE' => 'STRING',
        ];
    }
}

$arTemplateParameters['UNITS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_UNITS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['UNITS_USE'] === 'Y') {
    $arTemplateParameters['UNITS_VALUE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_TIMER_UNITS_VALUE'),
        'TYPE' => 'STRING',
    ];
}
