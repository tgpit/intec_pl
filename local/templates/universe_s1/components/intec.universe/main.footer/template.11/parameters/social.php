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
 * @var InnerTemplate $template
 */

$arTemplateParameters['SOCIAL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SOCIAL_SHOW'] === 'Y') {

    $arTemplateParameters['SOCIAL_SQUARE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_SQUARE'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SOCIAL_SQUARE'] !== 'Y') {
        $arTemplateParameters['SOCIAL_GREY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_GREY'),
            'TYPE' => 'CHECKBOX',
            'REFRESH' => 'Y'
        ];
    }

    $arTemplateParameters['SOCIAL_VK_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_VK_LINK'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'https://vk.com'
    ];

    $arTemplateParameters['SOCIAL_FACEBOOK_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_FACEBOOK_LINK'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'https://facebook.com'
    ];

    $arTemplateParameters['SOCIAL_INSTAGRAM_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_INSTAGRAM_LINK'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'https://instagram.com'
    ];

    $arTemplateParameters['SOCIAL_TWITTER_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_TWITTER_LINK'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'https://twitter.com'
    ];

    $arTemplateParameters['SOCIAL_YOUTUBE_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_YOUTUBE_LINK'),
        'DEFAULT' => 'https://youtube.com'
    ];

    $arTemplateParameters['SOCIAL_ODNOKLASSNIKI_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_ODNOKLASSNIKI_LINK'),
        'DEFAULT' => 'https://ok.ru'
    ];

    $arTemplateParameters['SOCIAL_VIBER_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_VIBER_LINK'),
        'DEFAULT' => 'https://viber.com'
    ];

    $arTemplateParameters['SOCIAL_WHATSAPP_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_WHATSAPP_LINK'),
        'DEFAULT' => 'https://whatsapp.com'
    ];

    $arTemplateParameters['SOCIAL_YANDEX_DZEN_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_YANDEX_DZEN_LINK'),
        'DEFAULT' => 'https://zen.yandex.ru/'
    ];

    $arTemplateParameters['SOCIAL_MAIL_RU_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_MAIL_RU_LINK'),
        'DEFAULT' => 'https://mail.ru/'
    ];

    $arTemplateParameters['SOCIAL_TELEGRAM_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_TELEGRAM_LINK'),
        'DEFAULT' => 'https://web.telegram.org/'
    ];

    $arTemplateParameters['SOCIAL_PINTEREST_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_PINTEREST_LINK'),
        'DEFAULT' => 'https://pinterest.com/'
    ];

    $arTemplateParameters['SOCIAL_TIKTOK_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_TIKTOK_LINK'),
        'DEFAULT' => 'https://tiktok.com/'
    ];

    $arTemplateParameters['SOCIAL_SNAPCHAT_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_SNAPCHAT_LINK'),
        'DEFAULT' => 'https://snapchat.com/'
    ];

    $arTemplateParameters['SOCIAL_LINKEDIN_LINK'] = [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('C_FOOTER_TEMPLATE_1_SOCIAL_LINKEDIN_LINK'),
        'DEFAULT' => 'https://linkedin.com/'
    ];
}