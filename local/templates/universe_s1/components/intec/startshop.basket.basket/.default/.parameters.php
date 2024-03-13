<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

/**
 * @var array $arCurrentValues
 */

if (!Loader::IncludeModule('intec.core'))
    return;

if (!Loader::IncludeModule('intec.startshop'))
    return;

$arTemplateParameters['USE_ITEMS_PICTURES'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_ITEMS_PICTURES'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['USE_SUM_FIELD'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_SUM_FIELD'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];


// Buttons
$arTemplateParameters['USE_BUTTON_CLEAR'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_BUTTON_CLEAR'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_BUTTON_ORDER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_BUTTON_ORDER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
$arTemplateParameters['USE_BUTTON_FAST_ORDER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_BUTTON_FAST_ORDER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_BUTTON_CONTINUE_SHOPPING'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SBB_DEFAULT_USE_BUTTON_CONTINUE_SHOPPING'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['USE_BUTTON_ORDER'] == 'Y') {
    $arTemplateParameters['URL_ORDER'] = [
        'PARENT' => 'URL',
        'NAME' => Loc::GetMessage('SBB_DEFAULT_URL_ORDER'),
        'TYPE' => 'STRING'
    ];
}

if ($arCurrentValues['USE_BUTTON_CONTINUE_SHOPPING'] == 'Y') {
    $arTemplateParameters['URL_CATALOG'] = [
        'PARENT' => 'URL',
        'NAME' => Loc::GetMessage('SBB_DEFAULT_URL_CATALOG'),
        'TYPE' => 'STRING'
    ];
}

include(__DIR__.'/parameters/order.fast.php');