<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arForms = [];
$bBase = false;

if (Loader::includeModule('form')) {
    include('parameters/base.php');
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    include('parameters/lite.php');
} else
    return;

foreach ($rsTemplates as $arTemplate) {
    $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);
}

$arTemplateParameters = [];

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['CONSENT_SHOW'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_CONSENT_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];
}

if ($arCurrentValues['SETTINGS_USE'] === 'Y' || $arCurrentValues['CONSENT_SHOW'] === 'Y') {
    $arTemplateParameters['CONSENT_URL'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_CONSENT_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#company/consent/'
    ];
}

$arTemplateParameters['FORM_TITLE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_TITLE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($bBase) {
    $arTemplateParameters['FORM_DESCRIPTION_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['FORM_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_WIDGET_FORM_8_FORM_POSITION_LEFT'),
        'right' => Loc::getMessage('C_WIDGET_FORM_8_FORM_POSITION_RIGHT'),
        'center' => Loc::getMessage('C_WIDGET_FORM_8_FORM_POSITION_CENTER')
    ],
    'DEFAULT' => 'left',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_POSITION'] != 'center') {
    $arTemplateParameters['FORM_ADDITIONAL_PICTURE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['FORM_ADDITIONAL_PICTURE_SHOW'] == 'Y') {
        $arTemplateParameters['FORM_ADDITIONAL_PICTURE_PATH'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_PATH'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#TEMPLATE_PATH#/images/picture.png'
        ];
        $arTemplateParameters['FORM_ADDITIONAL_PICTURE_VERTICAL'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_VERTICAL'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'top' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_VERTICAL_TOP'),
                'center' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_VERTICAL_CENTER'),
                'bottom' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_VERTICAL_BOTTOM')
            ],
            'DEFAULT' => 'center'
        ];
        $arTemplateParameters['FORM_ADDITIONAL_PICTURE_SIZE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_SIZE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'cover' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_SIZE_COVER'),
                'contain' => Loc::getMessage('C_WIDGET_FORM_8_FORM_ADDITIONAL_PICTURE_SIZE_CONTAIN')
            ],
            'DEFAULT' => 'contain'
        ];
    }
}

$arTemplateParameters['FORM_BACKGROUND_PATH'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_BACKGROUND_PATH'),
    'TYPE' => 'STRING',
    'DEFAULT' => '#TEMPLATE_PATH#/images/bg.jpg'
];
$arTemplateParameters['FORM_BACKGROUND_PARALLAX_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_BACKGROUND_PARALLAX_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_BACKGROUND_PARALLAX_USE'] == 'Y') {
    $arTemplateParameters['FORM_BACKGROUND_PARALLAX_RATIO'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_FORM_8_FORM_BACKGROUND_PARALLAX_RATIO'),
        'TYPE' => 'STRING',
        'DEFAULT' => 10
    ];
}