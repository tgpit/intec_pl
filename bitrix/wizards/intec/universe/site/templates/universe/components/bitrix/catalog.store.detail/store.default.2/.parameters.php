<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::IncludeModule('iblock'))
    return;

$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['ADDRESS_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_ADDRESS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['PHONE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_PHONE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['EMAIL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_EMAIL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['SCHEDULE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SCHEDULE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['SOCIAL_SERVICES_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SOCIAL_SERVICES_SHOW'] === 'Y') {
    $arTemplateParameters['SOCIAL_SERVICES_VK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_VK'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_FACEBOOK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_FACEBOOK'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_INSTAGRAM'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_INSTAGRAM'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_TWITTER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_TWITTER'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_SKYPE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_SKYPE'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_YOUTUBE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_YOUTUBE'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SOCIAL_SERVICES_OK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SOCIAL_SERVICES_OK'),
        'TYPE' => 'STRING'
    ];
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base.php');
elseif (Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');
else
    return;

$arTemplates = [];

foreach ($rsTemplates as $arTemplate)
    $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];
    $arTemplateParameters['FORM_TITLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_TITLE_DEFAULT')
    ];
    $arTemplateParameters['CONSENT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_CONSENT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#company/consent/'
    ];
}

$arTemplateParameters['FORM_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_SHOW'),
    'TYPE' => 'CHECKBOX'
];