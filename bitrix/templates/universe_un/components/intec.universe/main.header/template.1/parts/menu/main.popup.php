<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

$arMenu = [
    'PREFIX' => 'MENU_POPUP_',
    'TEMPLATE' => null,
    'PARAMETERS' => []
];

if (!empty($arParams['MENU_POPUP_TEMPLATE']))
    $arMenu['TEMPLATE'] = 'popup.'.$arParams['MENU_POPUP_TEMPLATE'];

if (!empty($arMenu['TEMPLATE'])) {
    foreach ($arParams as $sKey => $mValue) {
        if (
            StringHelper::startsWith($sKey, 'SEARCH_') ||
            StringHelper::startsWith($sKey, 'LOGOTYPE')
        ) {
            $arMenu['PARAMETERS'][$sKey] = $mValue;
        } if (StringHelper::startsWith($sKey, $arMenu['PREFIX'])) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($arMenu['PREFIX'])
            );

            if ($sKey === 'TEMPLATE')
                continue;

            if (
                StringHelper::startsWith($sKey, 'SEARCH_') ||
                StringHelper::startsWith($sKey, 'LOGOTYPE')
            ) continue;

            $arMenu['PARAMETERS'][$sKey] = $mValue;
        }
    }

    if (!empty($arMenuParams))
        $arMenu['PARAMETERS'] = ArrayHelper::merge($arMenu['PARAMETERS'], $arMenuParams);

    if ($arMenu['TEMPLATE'] === 'popup.2' || $arMenu['TEMPLATE'] === 'popup.3')
        $arMenuContacts['CONTACTS']['SELECTED'] = ArrayHelper::getFirstValue($arResult['CONTACTS']['SELECTED']);

    if ($arMenu['TEMPLATE'] === 'popup.2') {
        $arMenu['PARAMETERS']['CONTACTS_ADVANCED'] = $arResult['CONTACTS']['ADVANCED'] ? 'Y' : 'N';

        if ($arResult['CONTACTS']['ADVANCED']) {
            $arMenu['PARAMETERS']['CONTACTS_ADDRESS'] = [];
            $arMenu['PARAMETERS']['CONTACTS_EMAIL'] = [];
            $arMenu['PARAMETERS']['CONTACTS_PHONE'] = [];
            $arMenu['PARAMETERS']['CONTACTS_SCHEDULE'] = [];

            $arContacts = [];

            if (!empty($arMenuContacts['CONTACTS']['SELECTED']))
                $arContact[] = $arMenuContacts['CONTACTS']['SELECTED'];

            if (!empty($arResult['CONTACTS']['VALUES']))
                $arContacts = ArrayHelper::merge($arContacts, $arResult['CONTACTS']['VALUES']);

            foreach ($arContacts as $arContact) {
                $arMenu['PARAMETERS']['CONTACTS_ADDRESS'][$arContact['ID']] = $arContact['ADDRESS'];
                $arMenu['PARAMETERS']['CONTACTS_EMAIL'][$arContact['ID']] = $arContact['EMAIL'];
                $arMenu['PARAMETERS']['CONTACTS_PHONE'][$arContact['ID']] = !empty($arContact['PHONE']) ? $arContact['PHONE']['DISPLAY'] : null;
                $arMenu['PARAMETERS']['CONTACTS_SCHEDULE'][$arContact['ID']] = !empty($arContact['SCHEDULE']) ? implode(', ', $arContact['SCHEDULE']) : null;
            }

            unset($arContacts, $arContact);
        } else {
            $arMenu['PARAMETERS']['CONTACTS_ADDRESS'] = $arResult['ADDRESS']['VALUE'];
            $arMenu['PARAMETERS']['CONTACTS_EMAIL'] = $arResult['EMAIL']['VALUE'];
            $arMenu['PARAMETERS']['CONTACTS_PHONE'] = null;
            $arMenu['PARAMETERS']['CONTACTS_SCHEDULE'] = null;

            if (!empty($arMenuContacts['CONTACTS']['SELECTED']))
                $arMenu['PARAMETERS']['CONTACTS_PHONE'] = $arMenuContacts['CONTACTS']['SELECTED']['DISPLAY'];
        }
    } else {
        $arMenu['PARAMETERS']['CONTACTS_ADDRESS'] = $arResult['ADDRESS']['VALUE'];
        $arMenu['PARAMETERS']['CONTACTS_EMAIL'] = $arResult['EMAIL']['VALUE'];

        if (!empty($arMenuContacts['CONTACTS']['SELECTED'])) {
            if ($arResult['CONTACTS']['ADVANCED']) {
                if (!empty($arMenuContacts['CONTACTS']['SELECTED']['PHONE']))
                    $arMenu['PARAMETERS']['CONTACTS_PHONE'] = $arMenuContacts['CONTACTS']['SELECTED']['PHONE']['DISPLAY'];

                if (!empty($arMenuContacts['CONTACTS']['SELECTED']['SCHEDULE']))
                    $arMenu['PARAMETERS']['CONTACTS_SCHEDULE'] = $arMenuContacts['CONTACTS']['SELECTED']['SCHEDULE'];
            } else {
                $arMenu['PARAMETERS']['CONTACTS_PHONE'] = $arMenuContacts['CONTACTS']['SELECTED']['DISPLAY'];
            }
        }
    }

    $arMenu['PARAMETERS'] = ArrayHelper::merge($arMenu['PARAMETERS'], [
        'ROOT_MENU_TYPE' => $arResult['MENU']['MAIN']['ROOT'],
        'CHILD_MENU_TYPE' => $arResult['MENU']['MAIN']['CHILD'],
        'MAX_LEVEL' => $arResult['MENU']['MAIN']['LEVEL'],
        'MENU_CACHE_TYPE' => 'N',
        'USE_EXT' => 'Y',
        'DELAY' => 'N',
        'ALLOW_MULTI_SELECT' => 'N',

        'CATALOG_LINKS' => $arParams['MENU_MAIN_CATALOG_LINKS'],

        'CONTACTS_CITY' => null,

        'FORMS_CALL_SHOW' => $arResult['FORMS']['CALL']['SHOW'] ? 'Y' : 'N',
        'FORMS_CALL_ID' => $arResult['FORMS']['CALL']['ID'],
        'FORMS_CALL_TEMPLATE' => $arResult['FORMS']['CALL']['TEMPLATE'],
        'FORMS_CALL_TITLE' => $arResult['FORMS']['CALL']['TITLE'],

        'LOGOTYPE_SHOW' => $arResult['LOGOTYPE']['SHOW']['DESKTOP'] ? 'Y' : 'N',
        'LOGOTYPE' => $arResult['LOGOTYPE']['PATH'],
        'LOGOTYPE_LINK' => $arResult['LOGOTYPE']['LINK']['USE'] ? $arResult['LOGOTYPE']['LINK']['VALUE'] : null,

        'COMPARE_IBLOCK_TYPE' => $arResult['COMPARE']['IBLOCK']['TYPE'],
        'COMPARE_IBLOCK_ID' => $arResult['COMPARE']['IBLOCK']['ID'],
        'COMPARE_CODE' => $arResult['COMPARE']['CODE'],
        'COMPARE_URL' => $arResult['URL']['COMPARE'],

        'SOCIAL_VK_LINK' => $arResult['SOCIAL']['ITEMS']['VK']['LINK'],
        'SOCIAL_INSTAGRAM_LINK' => $arResult['SOCIAL']['ITEMS']['INSTAGRAM']['LINK'],
        'SOCIAL_FACEBOOK_LINK' => $arResult['SOCIAL']['ITEMS']['FACEBOOK']['LINK'],
        'SOCIAL_TWITTER_LINK' => $arResult['SOCIAL']['ITEMS']['TWITTER']['LINK'],
        'SOCIAL_YOUTUBE_LINK' => $arResult['SOCIAL']['ITEMS']['YOUTUBE']['LINK'],
        'SOCIAL_ODNOKLASSNIKI_LINK' => $arResult['SOCIAL']['ITEMS']['ODNOKLASSNIKI']['LINK'],
        'SOCIAL_VIBER_LINK' => $arResult['SOCIAL']['ITEMS']['VIBER']['LINK'],
        'SOCIAL_WHATSAPP_LINK' => $arResult['SOCIAL']['ITEMS']['WHATSAPP']['LINK'],
        'SOCIAL_YANDEX_DZEN_LINK' => $arResult['SOCIAL']['ITEMS']['YANDEX_DZEN']['LINK'],
        'SOCIAL_MAIL_RU_LINK' => $arResult['SOCIAL']['ITEMS']['MAIL_RU']['LINK'],
        'SOCIAL_TELEGRAM_LINK' => $arResult['SOCIAL']['ITEMS']['TELEGRAM']['LINK'],
        'SOCIAL_PINTEREST_LINK' => $arResult['SOCIAL']['ITEMS']['PINTEREST']['LINK'],
        'SOCIAL_TIKTOK_LINK' => $arResult['SOCIAL']['ITEMS']['TIKTOK']['LINK'],
        'SOCIAL_SNAPCHAT_LINK' => $arResult['SOCIAL']['ITEMS']['SNAPCHAT']['LINK'],
        'SOCIAL_LINKEDIN_LINK' => $arResult['SOCIAL']['ITEMS']['LINKEDIN']['LINK'],

        'CONSENT_URL' => $arResult['URL']['CONSENT'],
        'BASKET_URL' => $arResult['URL']['BASKET'],
        'ORDER_URL' => $arResult['URL']['ORDER'],
        'LOGIN_URL' => $arResult['URL']['LOGIN'],
        'PROFILE_URL' => $arResult['URL']['PROFILE'],
        'PASSWORD_URL' => $arResult['URL']['PASSWORD'],
        'REGISTER_URL' => $arResult['URL']['REGISTER'],
        'CACHE_SELECTED_ITEMS' => 'N'
    ]);

?>
    <?php $APPLICATION->IncludeComponent(
        'bitrix:menu',
        $arMenu['TEMPLATE'],
        $arMenu['PARAMETERS'],
        $this->getComponent()
    ); ?>
<?php }

unset($arMenu);
