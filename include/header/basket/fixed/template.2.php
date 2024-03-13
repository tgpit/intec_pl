<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
	"intec.universe:sale.basket.small", 
	"panel.1", 
	array(
		"SETTINGS_USE" => "Y",
		"PANEL_SHOW" => "Y",
		"COMPARE_SHOW" => "Y",
		"COMPARE_CODE" => "compare",
		"COMPARE_IBLOCK_TYPE" => "catalogs",
		"COMPARE_IBLOCK_ID" => "58",
		"AUTO" => "Y",
		"FORM_ID" => "12",
		"FORM_TITLE" => "Заказать звонок",
		"BASKET_SHOW" => "Y",
		"FORM_SHOW" => "Y",
		"PERSONAL_SHOW" => "Y",
		"SBERBANK_ICON_SHOW" => "Y",
		"QIWI_ICON_SHOW" => "Y",
		"YANDEX_MONEY_ICON_SHOW" => "Y",
		"VISA_ICON_SHOW" => "Y",
		"MASTERCARD_ICON_SHOW" => "Y",
		"DELAYED_SHOW" => "Y",
		"CATALOG_URL" => "/catalog/",
		"BASKET_URL" => "/personal/basket/",
		"ORDER_URL" => "/personal/basket/order.php",
		"COMPARE_URL" => "/catalog/compare.php",
		"PERSONAL_URL" => "/personal/profile/",
		"CONSENT_URL" => "/company/consent/",
		"COMPONENT_TEMPLATE" => "panel.1",
		"CONVERT_CURRENCY" => "N",
		"HIDDEN" => "N",
		"REGISTER_URL" => "/personal/profile/",
		"FORGOT_PASSWORD_URL" => "/personal/profile/?forgot_password=yes",
		"PROFILE_URL" => "/personal/profile/"
	),
	false
); ?>