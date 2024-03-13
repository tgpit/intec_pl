<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

$arMenu = $arResult['MENU']['MAIN'];
$arMenuParams = !empty($arMenuParams) ? $arMenuParams : [];
$arContact = ArrayHelper::shift($arResult['CONTACTS']['SELECTED']);

if ($arResult['CONTACTS']['ADVANCED']) {
    $arPhones = $arContact['PHONE']['DISPLAY'];
} else {
    $arPhones[] = $arContact['DISPLAY'];

    if (!empty($arResult['CONTACTS']['VALUES'])) {
        foreach ($arResult['CONTACTS']['VALUES'] as $arPhone) {
            $arPhones[] = $arPhone['DISPLAY'];
        }
    }
}

$sPrefix = 'SEARCH_';
$arParameters = [];

foreach ($arParams as $sKey => $sValue)
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $arParameters[$sKey] = $sValue;
    }

$arParameters['SEARCH_PAGE'] = $arResult['SEARCH']['MODE'] === 'site' ? $arResult['URL']['SEARCH'] : $arResult['URL']['CATALOG'];
$arParameters['SEARCH_TYPE'] = ArrayHelper::fromRange(['page', 'popup'], $arParams['MOBILE_TYPE_SEARCH']);
$arParameters['SEARCH_INPUT_ID'] = $arParameters['SEARCH_INPUT_ID'].'-menu-'.$arParameters['SEARCH_TYPE'].'-1';

$arMenuParams = ArrayHelper::merge($arParameters, $arMenuParams);

?>

<?php $APPLICATION->IncludeComponent(
    'bitrix:menu',
    'mobile.2',
    ArrayHelper::merge([
        'ROOT_MENU_TYPE' => $arMenu['ROOT'],
        'CHILD_MENU_TYPE' => $arMenu['CHILD'],
        'MAX_LEVEL' => $arMenu['LEVEL'],
        'MENU_CACHE_TYPE' => 'N',
        'USE_EXT' => 'Y',
        'DELAY' => 'N',
        'ALLOW_MULTI_SELECT' => 'N',
        'ADDRESS_SHOW' => $arResult['ADDRESS']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'ADDRESS' => $arResult['ADDRESS']['VALUE'],
        'CITY' => $arResult['CITY']['VALUE'],
        'PHONES_SHOW' => $arResult['CONTACTS']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'PHONES' => $arPhones,
        'EMAIL_SHOW' => $arResult['EMAIL']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'EMAIL' => $arResult['EMAIL']['VALUE'],
        'LOGOTYPE_SHOW' => $arResult['LOGOTYPE']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'LOGOTYPE' => $arResult['LOGOTYPE']['PATH'],
        'LOGOTYPE_LINK' => $arResult['LOGOTYPE']['LINK']['USE'] ? $arResult['LOGOTYPE']['LINK']['VALUE'] : null,
        'REGIONALITY_USE' => $arResult['REGIONALITY']['USE'] ? 'Y' : 'N',

        'AUTHORIZATION_SHOW' => $arResult['AUTHORIZATION']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'PROFILE_URL' => $arResult['URL']['PROFILE'],
        'BASKET_SHOW' => $arResult['BASKET']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'BASKET_URL' => $arResult['URL']['BASKET'],
        'DELAY_SHOW' => $arResult['DELAY']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'COMPARE_SHOW' => $arResult['COMPARE']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'COMPARE_URL' => $arResult['URL']['COMPARE'],
        'COMPARE_CODE' => $arResult['COMPARE']['CODE'],
        'COMPARE_IBLOCK_TYPE' => $arResult['COMPARE']['IBLOCK']['TYPE'],
        'COMPARE_IBLOCK_ID' => $arResult['COMPARE']['IBLOCK']['ID'],
        'ORDER_URL' => $arResult['URL']['ORDER'],

        'SOCIAL_SHOW' => $arResult['SOCIAL']['SHOW']['MOBILE'] ? 'Y' : 'N',
        'SOCIAL_VK' => $arResult['SOCIAL']['ITEMS']['VK']['LINK'],
        'SOCIAL_INSTAGRAM' => $arResult['SOCIAL']['ITEMS']['INSTAGRAM']['LINK'],
        'SOCIAL_FACEBOOK' => $arResult['SOCIAL']['ITEMS']['FACEBOOK']['LINK'],
        'SOCIAL_TWITTER' => $arResult['SOCIAL']['ITEMS']['TWITTER']['LINK'],
        'SOCIAL_YOUTUBE' => $arResult['SOCIAL']['ITEMS']['YOUTUBE']['LINK'],
        'SOCIAL_ODNOKLASSNIKI' => $arResult['SOCIAL']['ITEMS']['ODNOKLASSNIKI']['LINK'],
        'SOCIAL_WHATSAPP' => $arResult['SOCIAL']['ITEMS']['WHATSAPP']['LINK'],
        'SOCIAL_VIBER' => $arResult['SOCIAL']['ITEMS']['VIBER']['LINK'],
        'SOCIAL_YANDEX_DZEN' => $arResult['SOCIAL']['ITEMS']['YANDEX_DZEN']['LINK'],
        'SOCIAL_MAIL_RU' => $arResult['SOCIAL']['ITEMS']['MAIL_RU']['LINK'],
        'SOCIAL_TELEGRAM' => $arResult['SOCIAL']['ITEMS']['TELEGRAM']['LINK'],
        'SOCIAL_PINTEREST' => $arResult['SOCIAL']['ITEMS']['PINTEREST']['LINK'],
        'SOCIAL_TIKTOK' => $arResult['SOCIAL']['ITEMS']['TIKTOK']['LINK'],
        'SOCIAL_SNAPCHAT' => $arResult['SOCIAL']['ITEMS']['SNAPCHAT']['LINK'],
        'SOCIAL_LINKEDIN' => $arResult['SOCIAL']['ITEMS']['LINKEDIN']['LINK'],

        'BORDER_SHOW' => $arParams['MOBILE_MENU_BORDER_SHOW'],
        'INFORMATION_VIEW' => $arParams['MOBILE_MENU_INFORMATION_VIEW'],
        'CACHE_SELECTED_ITEMS' => 'N'
    ], $arMenuParams),
    $this->getComponent()
); ?>
<?php unset($arMenu) ?>
