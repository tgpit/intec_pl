<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Iblock;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
CModule::IncludeModule("iblock");

/**
 * @var array $arCurrentValues
 */

if (!CModule::IncludeModule('intec.core'))
    return;

if (!CModule::IncludeModule('intec.startshop'))
    return;

$arTemplateParameters['SHOW_ALERT_FORM'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::GetMessage('SB_DEFAULT_SHOW_ALERT_FORM'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

$arTemplateParameters['USE_ADAPTABILITY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_ADAPTABILITY'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_ITEMS_PICTURES'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_ITEMS_PICTURES'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['USE_SUM_FIELD'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_SUM_FIELD'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];


// Buttons
$arTemplateParameters['USE_BUTTON_CLEAR'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_BUTTON_CLEAR'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_BUTTON_BASKET'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_BUTTON_BASKET'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_BUTTON_FAST_ORDER'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_BUTTON_FAST_ORDER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['USE_BUTTON_CONTINUE_SHOPPING'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_USE_BUTTON_CONTINUE_SHOPPING'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
$arTemplateParameters['VERIFY_CONSENT_TO_PROCESSING_PERSONAL_DATA'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::GetMessage('SB_DEFAULT_VERIFY_CONSENT_TO_PROCESSING_PERSONAL_DATA'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['USE_BUTTON_CONTINUE_SHOPPING'] == 'Y') {
    $arTemplateParameters['URL_CATALOG'] = [
        'PARENT' => 'URL',
        'NAME' => Loc::GetMessage('SB_DEFAULT_URL_CATALOG'),
        'TYPE' => 'STRING'
    ];
}

if ($arCurrentValues['VERIFY_CONSENT_TO_PROCESSING_PERSONAL_DATA'] == 'Y') {
    $arTemplateParameters['URL_RULES_OF_PERSONAL_DATA_PROCESSING'] = [
        'PARENT' => 'URL',
        'NAME' => Loc::GetMessage('SB_DEFAULT_URL_RULES_OF_PERSONAL_DATA_PROCESSING'),
        'TYPE' => 'STRING'
    ];
}

include(__DIR__.'/parameters/order.fast.php');
include(__DIR__.'/parameters/quick.view.php');