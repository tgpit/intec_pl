<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\io\Path;

/**
 * @var Arrays $blocks
 * @var array $block
 * @var array $data
 * @var string $page
 * @var Path $path
 * @global CMain $APPLICATION
 */

?>
<?= Html::beginTag('div') ?>
<?php $APPLICATION->IncludeComponent(
    "intec.universe:main.widget",
    "contact.1",
    array(
        "SETTINGS_USE" => "Y",
        "MAP_VENDOR" => "yandex",
        "INIT_MAP_TYPE" => "MAP",
        "MAP_MAP_DATA" => "",
        "BLOCK_SHOW" => "Y",
        "BLOCK_TITLE" => "Наши контакты",
        "ADDRESS_SHOW" => "Y",
        "ADDRESS_CITY" => "г. Челябинск",
        "ADDRESS_STREET" => "",
        "PHONE_SHOW" => "Y",
        "PHONE_VALUES" => array(
            "+7 (000) 000 00 00",
        ),
        "FORM_SHOW" => "Y",
        "FORM_ID" => "12",
        "FORM_TEMPLATE" => ".default",
        "FORM_TITLE" => "Заказать звонок",
        "FORM_BUTTON_TEXT" => "Заказать звонок",
        "CONTACT_TYPE" => "IBLOCK",
        "EMAIL_SHOW" => "Y",
        "EMAIL_VALUES" => array(
            "shop@example.com",
        ),
        "IBLOCK_ID" => "86",
        "IBLOCK_TYPE" => "content",
        "PROPERTY_CITY" => "CITY",
        "PROPERTY_EMAIL" => "EMAIL",
        "PROPERTY_MAP" => "MAP",
        "PROPERTY_PHONE" => "PHONE",
        "PROPERTY_REGION" => "REGIONS",
        "PROPERTY_STREET" => "ADDRESS",
        "REGIONALITY_STRICT" => "Y",
        "REGIONALITY_USE" => "Y",
        "CONSENT_URL" => "/company/consent/",
        "MAP_OVERLAY" => "Y",
        "WIDE" => "Y",
        "BLOCK_VIEW" => "over",
        "MAP_CONTROLS" => array(
            "ZOOM",
            "SMALLZOOM",
            "MINIMAP",
            "TYPECONTROL",
            "SCALELINE"
        ),
        "MAP_OPTIONS" => array(
            "ENABLE_SCROLL_ZOOM",
            "ENABLE_DBLCLICK_ZOOM",
            "ENABLE_RIGHT_MAGNIFIER",
            "ENABLE_DRAGGING",
        ),
        "MAP_MAP_ID" => ""
    ),
    false
); ?>
<?= Html::endTag('div') ?>

