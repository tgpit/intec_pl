<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;

/**
 * @var string $componentName
 * @var string $templateName
 * @var string $siteTemplate
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 * @var array $arParts
 * @var InnerTemplate $desktopTemplate
 * @var InnerTemplate $fixedTemplate
 * @var InnerTemplate $mobileTemplate
 */

$arParts['SOCIAL'] = null;

if (!empty($desktopTemplate)) {
    $arTemplateParameters['SOCIAL_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];
}

if (!empty($mobileTemplate)) {
    $arTemplateParameters['SOCIAL_SHOW_MOBILE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_SHOW_MOBILE'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['SOCIAL_SQUARE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_SQUARE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['SOCIAL_GREY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_GREY'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['SOCIAL_VK'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_VK'),
    'DEFAULT' => 'https://vk.com'
];

$arTemplateParameters['SOCIAL_INSTAGRAM'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_INSTAGRAM'),
    'DEFAULT' => 'https://instagram.com'
];

$arTemplateParameters['SOCIAL_FACEBOOK'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_FACEBOOK'),
    'DEFAULT' => 'https://facebook.com'
];

$arTemplateParameters['SOCIAL_TWITTER'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_TWITTER'),
    'DEFAULT' => 'https://twitter.com'
];

$arTemplateParameters['SOCIAL_YOUTUBE'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_YOUTUBE'),
    'DEFAULT' => 'https://youtube.com'
];

$arTemplateParameters['SOCIAL_ODNOKLASSNIKI'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_ODNOKLASSNIKI'),
    'DEFAULT' => 'https://ok.ru'
];

$arTemplateParameters['SOCIAL_VIBER'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_VIBER'),
    'DEFAULT' => 'https://viber.com'
];

$arTemplateParameters['SOCIAL_WHATSAPP'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_WHATSAPP'),
    'DEFAULT' => 'https://whatsapp.com'
];

$arTemplateParameters['SOCIAL_YANDEX_DZEN'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_YANDEX_DZEN'),
    'DEFAULT' => 'https://zen.yandex.ru/'
];

$arTemplateParameters['SOCIAL_MAIL_RU'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_MAIL_RU'),
    'DEFAULT' => 'https://mail.ru/'
];

$arTemplateParameters['SOCIAL_TELEGRAM'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_TELEGRAM'),
    'DEFAULT' => 'https://web.telegram.org/'
];

$arTemplateParameters['SOCIAL_PINTEREST'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_PINTEREST'),
    'DEFAULT' => 'https://pinterest.com/'
];

$arTemplateParameters['SOCIAL_TIKTOK'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_TIKTOK'),
    'DEFAULT' => 'https://tiktok.com/'
];

$arTemplateParameters['SOCIAL_SNAPCHAT'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_SNAPCHAT'),
    'DEFAULT' => 'https://snapchat.com/'
];

$arTemplateParameters['SOCIAL_LINKEDIN'] = [
    'PARENT' => 'DATA_SOURCE',
    'TYPE' => 'STRING',
    'NAME' => Loc::getMessage('C_HEADER_TEMP1_SOCIAL_LINKEDIN'),
    'DEFAULT' => 'https://linkedin.com/'
];