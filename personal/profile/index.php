<?php define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\ModuleManager;

$APPLICATION->SetTitle("Личный кабинет пользователя");

?>
<?php if (ModuleManager::isModuleInstalled('sale')) { ?>
    <?php $APPLICATION->IncludeComponent(
	"bitrix:sale.personal.section",
	"template.1",
	array(
		"SHOW_ACCOUNT_PAGE" => "Y",
		"SHOW_ORDER_PAGE" => "Y",
		"SHOW_PRIVATE_PAGE" => "Y",
		"SHOW_PROFILE_PAGE" => "Y",
		"SHOW_SUBSCRIBE_PAGE" => "Y",
		"SHOW_CONTACT_PAGE" => "Y",
		"SHOW_BASKET_PAGE" => "Y",
		"CUSTOM_PAGES" => "",
		"SETTINGS_USE" => "N",
		"MAILING_SHOW" => "N",
		"PATH_TO_PAYMENT" => "/personal/basket/payment/",
		"PATH_TO_CONTACT" => "/contacts/",
		"PATH_TO_BASKET" => "/personal/basket/",
		"PATH_TO_CATALOG" => "/catalog/",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/personal/profile/",
		"SHOW_ACCOUNT_COMPONENT" => "Y",
		"SHOW_ACCOUNT_PAY_COMPONENT" => "Y",
		"ACCOUNT_PAYMENT_SELL_CURRENCY" => "RUB",
		"ACCOUNT_PAYMENT_PERSON_TYPE" => "1",
		"ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS" => array(
			"0"
		),
		"ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES" => "Y",
		"ACCOUNT_PAYMENT_SELL_TOTAL" => array(
			"100",
			"200",
			"500",
			"1000",
			"5000"
		),
		"ACCOUNT_PAYMENT_SELL_USER_INPUT" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"ORDER_HIDE_USER_INFO" => array(
			"0"
		),
		"PROP_1" => array(
		),
		"PROP_2" => array(
		),
		"ORDER_HISTORIC_STATUSES" => array(
			"F"
		),
		"ORDER_RESTRICT_CHANGE_PAYSYSTEM" => array(
			"0"
		),
		"ORDER_DEFAULT_SORT" => "STATUS",
		"ORDER_REFRESH_PRICES" => "N",
		"ORDER_DISALLOW_CANCEL" => "N",
		"ALLOW_INNER" => "N",
		"ONLY_INNER_FULL" => "N",
		"NAV_TEMPLATE" => "",
		"ORDERS_PER_PAGE" => "20",
		"USE_AJAX_LOCATIONS_PROFILE" => "N",
		"COMPATIBLE_LOCATION_MODE_PROFILE" => "N",
		"PROFILES_PER_PAGE" => "20",
		"SEND_INFO_PRIVATE" => "N",
		"CHECK_RIGHTS_PRIVATE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_GROUPS" => "Y",
		"MAIN_CHAIN_NAME" => "Мой кабинет",
		"SET_TITLE" => "Y",
		"ORDERS_LINK" => "",
		"PROFILE_LINK" => "",
		"CHANGE_PASSWORD_LINK" => "",
		"SHOW_ICON" => "Y",
		"PROPERTY_MANAGER" => "",
		"CLAIMS_USE" => "Y",
		"PROFILE_ADD_USE" => "Y",
		"CRM_SHOW_PAGE" => "N",
		"PRODUCT_VIEWED_SHOW_PAGE" => "Y",
		"PRODUCT_VIEWED_TEMPLATE" => "2",
		"PRODUCT_VIEWED_IBLOCK_MODE" => "single",
		"PRODUCT_VIEWED_IBLOCK_TYPE" => "catalogs",
		"PRODUCT_VIEWED_IBLOCK_ID" => "16",
		"PRODUCT_VIEWED_SHOW_FROM_SECTION" => "N",
		"PRODUCT_VIEWED_SECTION_ID" => $GLOBALS["CATALOG_CURRENT_SECTION_ID"],
		"PRODUCT_VIEWED_SECTION_CODE" => "",
		"PRODUCT_VIEWED_SECTION_ELEMENT_ID" => $GLOBALS["CATALOG_CURRENT_ELEMENT_ID"],
		"PRODUCT_VIEWED_SECTION_ELEMENT_CODE" => "",
		"PRODUCT_VIEWED_DEPTH" => "2",
		"PRODUCT_VIEWED_HIDE_NOT_AVAILABLE" => "N",
		"PRODUCT_VIEWED_HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"PRODUCT_VIEWED_PAGE_ELEMENT_COUNT" => "10",
		"PRODUCT_VIEWED_PRICE_CODE" => array(
			"BASE"
		),
		"PRODUCT_VIEWED_USE_PRICE_COUNT" => "Y",
		"PRODUCT_VIEWED_SHOW_PRICE_COUNT" => "1",
		"PRODUCT_VIEWED_PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_VIEWED_CONVERT_CURRENCY" => "Y",
		"PRODUCT_VIEWED_BASKET_URL" => "/personal/basket/",
		"PRODUCT_VIEWED_ACTION_VARIABLE" => "action_cpv",
		"PRODUCT_VIEWED_PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_VIEWED_USE_PRODUCT_QUANTITY" => "Y",
		"PRODUCT_VIEWED_PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_VIEWED_ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_VIEWED_PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_VIEWED_PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_VIEWED_CACHE_TYPE" => "A",
		"PRODUCT_VIEWED_CACHE_TIME" => "3600000",
		"PRODUCT_VIEWED_CACHE_GROUPS" => "Y",
		"PRODUCT_VIEWED_CURRENCY_ID" => "RUB",
		"PRODUCT_VIEWED_ADDITIONAL_PICT_PROP_13" => "PICTURES",
		"PRODUCT_VIEWED_LABEL_PROP_13" => array(
			"NEW",
			"HIT",
			"RECOMMEND"
		),
		"PRODUCT_VIEWED_ADDITIONAL_PICT_PROP_14" => "PICTURES",
		"PRODUCT_VIEWED_DISPLAY_COMPARE" => "Y",
		"PRODUCT_VIEWED_COMPARE_PATH" => "/catalog/compare.php",
		"PRODUCT_VIEWED_LAZYLOAD_USE" => "Y",
		"PRODUCT_VIEWED_TITLE_SHOW" => "Y",
		"PRODUCT_VIEWED_TITLE" => "Просмотренные товары",
		"PRODUCT_VIEWED_COLUMNS" => "5",
		"PRODUCT_VIEWED_COLUMNS_MOBILE" => "2",
		"PRODUCT_VIEWED_SHOW_NAVIGATION" => "Y",
		"PRODUCT_VIEWED_BORDERS" => "Y",
		"CLAIMS_TEMPLATE" => "1",
		"CLAIMS_AJAX_MODE" => "N",
		"CLAIMS_TICKETS_PER_PAGE" => "50",
		"CLAIMS_MESSAGES_PER_PAGE" => "20",
		"CLAIMS_MESSAGE_MAX_LENGTH" => "70",
		"CLAIMS_MESSAGE_SORT_ORDER" => "asc",
		"CLAIMS_SET_SHOW_USER_FIELD" => array(
		),
		"CLAIMS_FILTER_USER_FIELD" => "",
		"CLAIMS_VARIABLE_ALIASES_ID" => "ID",
		"CLAIMS_AJAX_OPTION_JUMP" => "N",
		"CLAIMS_AJAX_OPTION_STYLE" => "Y",
		"CLAIMS_AJAX_OPTION_HISTORY" => "N",
		"CLAIMS_AJAX_OPTION_ADDITIONAL" => "",
		"PROFILE_ADD_TEMPLATE" => "1",
		"PROFILE_ADD_PERSONS_ID" => array(
			"1",
			"2"
		),
		"PROFILE_ADD_USE_AJAX_LOCATIONS" => "N",
		"PROFILE_ADD_SET_TITLE" => "Y",
		"PROFILE_ADD_TITLE" => "Добавление профиля",
		"SEF_URL_TEMPLATES" => array(
			"index" => "index.php",
			"orders" => "orders/",
			"account" => "account/",
			"subscribe" => "subscribe/",
			"profile" => "profiles/",
			"profile_detail" => "profiles/#ID#",
			"private" => "private/",
			"order_detail" => "orders/#ID#",
			"order_cancel" => "cancel/#ID#",
			"claims" => "claims/",
			"profile_add" => "profile_add/",
			"viewed" => "viewed/",
		)
	),
	false
); ?>
<?php } else { ?>
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:sale.personal.section",
        "template.1",
        array(
            "PAGE_VARIABLE" => "page",
            "BASKET_URL" => "/personal/basket/",
            "CONTACTS_URL" => "/contacts/",
            "SEF_MODE" => "Y",
            "SET_STATUS_404" => "Y",
            "SHOW_404" => "Y",
            "MESSAGE_404" => "",
            "FILE_404" => "/404.php",
            "CHAIN_MAIN_NAME" => "Личный кабинет",
            "TITLE_SET" => "Y",
            "PRIVATE_PAGE_NAME" => "Персональные данные",
            "ORDERS_PAGE_NAME" => "Заказы",
            "BASKET_PAGE_NAME" => "Корзина",
            "CONTACTS_PAGE_NAME" => "Контакты",
            "SEF_FOLDER" => "/personal/profile/",
            "CURRENCY" => "RUB",
            "SEF_URL_TEMPLATES" => array(
                "private" => "private/",
                "order" => "orders/#ORDER_ID#/",
                "orders" => "orders/",
            )
        ),
        false
    ); ?>
<?php } ?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php") ?>