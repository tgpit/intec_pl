<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['BUTTON_ALL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_BUTTON_ALL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_ALL_SHOW'] === 'Y') {
    $arTemplateParameters['BUTTON_ALL_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_BUTTON_ALL_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
    $arTemplateParameters['BUTTON_ALL_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_BUTTON_ALL_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_BUTTON_ALL_TEXT_DEFAULT')
    ];
    $arTemplateParameters['LIST_PAGE_URL'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_VIDEOS_TEMPLATE_5_LIST_PAGE_URL'),
        'TYPE' => 'STRING'
    ];
}