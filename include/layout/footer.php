<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    "intec.universe:main.footer",
    "template.1",
    array(
        "SETTINGS_USE" => "Y",
        "PRODUCTS_VIEWED_SHOW" => "Y",
        "PRODUCTS_VIEWED_LAZYLOAD_USE" => "N",
        "REGIONALITY_USE" => "N",
        "CONTACTS_USE" => "Y",
        "CONTACTS_IBLOCK_TYPE" => "content",
        "CONTACTS_IBLOCK_ID" => "86",
        "CONTACTS_REGIONALITY_USE" => "Y",
        "CONTACTS_REGIONALITY_STRICT" => "N",
        "MENU_MAIN_ROOT" => "bottom",
        "MENU_MAIN_CHILD" => "left",
        "MENU_MAIN_LEVEL" => 4,
        "SEARCH_NUM_CATEGORIES" => 1,
        "SEARCH_TOP_COUNT" => 5,
        "SEARCH_ORDER" => "date",
        "SEARCH_USE_LANGUAGE_GUESS" => "Y",
        "SEARCH_CHECK_DATES" => "N",
        "SEARCH_SHOW_OTHERS" => "N",
        "SEARCH_INPUT_ID" => "footer-search",
        "SEARCH_TIPS_USE" => "N",
        "SEARCH_MODE" => "site",
        "LOGOTYPE_PATH" => "/include/logotype.intec.php",
        "LOGOTYPE_LINK" => "https://intecweb.ru/",
        "CONTACTS_PROPERTY_CITY" => "CITY",
        "CONTACTS_PROPERTY_ADDRESS" => "ADDRESS",
        "CONTACTS_PROPERTY_PHONE" => "PHONE",
        "CONTACTS_PROPERTY_EMAIL" => "EMAIL",
        "CONTACTS_PROPERTY_REGION" => "REGIONS",
        "PHONE_VALUE" => "+7 (000) 000 00 00",
        "PRODUCTS_VIEWED_IBLOCK_MODE" => "multi",
        "ADDRESS_VALUE" => "г. Челябинск",
        "EMAIL_VALUE" => "shop@example.com",
        "COPYRIGHT_VALUE" => "&copy; #YEAR# Universe, Все права защищены",
        "FORMS_CALL_ID" => "12",
        "FORMS_CALL_TEMPLATE" => ".default",
        "SOCIAL_VK_LINK" => "https://vk.com",
        "SOCIAL_FACEBOOK_LINK" => "https://facebook.com",
        "SOCIAL_INSTAGRAM_LINK" => "https://instagram.com",
        "SOCIAL_TWITTER_LINK" => "https://twitter.com",
        'SOCIAL_YOUTUBE_LINK' => 'https://youtube.com',
        'SOCIAL_YANDEX_DZEN_LINK' => 'https://zen.yandex.ru/',
        'SOCIAL_TELEGRAM_LINK' => 'https://web.telegram.org/',
        'SOCIAL_TIKTOK_LINK' => 'https://tiktok.com/',
        'SOCIAL_SNAPCHAT_LINK' => 'https://snapchat.com/',
        'SOCIAL_PINTEREST_LINK' => 'https://pinterest.com/',
        'SOCIAL_MAIL_RU_LINK' => 'https://mail.ru/',
        'SOCIAL_WHATSAPP_LINK' => 'https://whatsapp.com',
        "LOGOTYPE_SHOW" => "Y",
        "PHONE_SHOW" => "Y",
        "PRODUCTS_VIEWED_TITLE_SHOW" => "Y",
        "PRODUCTS_VIEWED_TITLE" => "Ранее вы смотрели",
        "PRODUCTS_VIEWED_PAGE_ELEMENT_COUNT" => "10",
        "PRODUCTS_VIEWED_COLUMNS" => 4,
        "PRODUCTS_VIEWED_SHOW_NAVIGATION" => "Y",
        "ADDRESS_SHOW" => "Y",
        "EMAIL_SHOW" => "Y",
        "COPYRIGHT_SHOW" => "Y",
        "FORMS_CALL_SHOW" => "Y",
        "FORMS_CALL_TITLE" => "Заказать звонок",
        "MENU_MAIN_SHOW" => "Y",
        "SEARCH_SHOW" => "Y",
        "SOCIAL_SHOW" => "Y",
        "THEME" => "dark",
        "TEMPLATE" => "template.1",
        "ICONS" => [
            "ALFABANK",
            "SBERBANK",
            "QIWI",
            "YANDEXMONEY",
            "VISA",
            "MASTERCARD"
        ],
        "CONSENT_URL" => "/company/consent/",
        "CATALOG_URL" => "/catalog/",
        "SEARCH_URL" => "/search/",
        "PRODUCTS_VIEWED_PRICE_CODE" => [
            "BASE"
        ],
        "SEARCH_CATEGORY_0" => [
            "no"
        ],
        "SEARCH_PRICE_CODE" => [
            "BASE"
        ],
        "SEARCH_PRICE_VAT_INCLUDE" => "Y",
        "SEARCH_CURRENCY_CONVERT" => "N"
    ),
    false
); ?>
