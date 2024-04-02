<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_FOOTER_TEMPLATE_1_PRESET_DEFAULT_1'),
        'group' => 'footer',
        'sort' => 101,
        'properties' => [
            'COMPONENT_TEMPLATE' => 'footer',
            'USE_GLOBAL_SETTINGS' => 'Y',
            'FOOTER_DESIGN' => 'TYPE_1',
            'FOOTER_BLACK' => 'N',
            'FOOTER_SHOW_FEEDBACK' => 'Y',
            'FOOTER_SHOW_MENU' => 'Y',
            'FOOTER_SHOW_SEARCH' => 'Y',
            'FOOTER_SHOW_SOCIAL' => 'Y',
            'FOOTER_LOGO' => 'Y',
            'FOOTER_PAYSYSTEM' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '0',
            'FOOTER_SHOW_TEXT_BUTTON' => Loc::getMessage('PRESETS_FOOTER_TEMPLATE_1_FOOTER_SHOW_TEXT_BUTTON'),
            'FOOTER_MENU' => 'footer',
            'FOOTER_CHILD_MENU' => '',
            'FOOTER_SHOW_SEARCH_PATH' => '#SITE_DIR#search/',
            'FOOTER_VKONTACTE' => 'https://vk.com/',
            'FOOTER_FACEBOOK' => 'https://facebook.com/',
            'FOOTER_INSTAGRAM' => 'https://www.instagram.com/',
            'FOOTER_TWITTER' => 'http://vk.com',
            'FOOTER_PAYSYSTEM_TYPE' => 'color',
            'FOOTER_ALFABANK' => 'Y',
            'FOOTER_SBERBANK' => 'Y',
            'FOOTER_YANDEX_MONEY' => 'Y',
            'FOOTER_QIWI' => 'Y',
            'FOOTER_VISA' => 'Y',
            'FOOTER_MASTERCARD' => 'Y',
            'CONSENT_URL' => '#SITE_DIR#company/consent/'
        ]
    ]
];