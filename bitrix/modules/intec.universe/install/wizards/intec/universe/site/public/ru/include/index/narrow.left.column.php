<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use intec\core\collections\Arrays;
use intec\core\io\Path;

/**
 * @var Arrays $blocks
 * @var string $page
 * @var Closure $render($block, $data = [])
 * @var Path $path
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    "bitrix:menu",
    "vertical.1",
    array(
        "COMPONENT_TEMPLATE" => "vertical",
        "ROOT_MENU_TYPE" => "catalog",
        "IBLOCK_TYPE" => "#CATALOGS_PRODUCTS_IBLOCK_TYPE#",
        "IBLOCK_ID" => "#CATALOGS_PRODUCTS_IBLOCK_ID#",
        "PROPERTY_IMAGE" => "UF_IMAGE",
        "MENU_CACHE_TYPE" => "N",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "N",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "4",
        "CHILD_MENU_TYPE" => "catalog",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N"
    ),
    false
); ?>
<?php if ($blocks->exists('news') && $blocks['news']['active']) { ?>
    <?= Html::beginTag('div', [
        'style' => [
            'margin-top' => '50px'
        ]
    ]) ?>
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:main.news",
        "template.4",
        array(
            "IBLOCK_TYPE" => "#CONTENT_NEWS_IBLOCK_TYPE#",
            "IBLOCK_ID" => "#CONTENT_NEWS_IBLOCK_ID#",
            "ELEMENTS_COUNT" => 4,
            "HEADER_BLOCK_SHOW" => "Y",
            "HEADER_BLOCK_POSITION" => "center",
            "HEADER_BLOCK_TEXT" => "Новости",
            "DESCRIPTION_BLOCK_SHOW" => "N",
            "LINK_USE" => "Y",
            "DATE_SHOW" => "Y",
            "DATE_FORMAT" => "d.m.Y",
            "SEE_ALL_SHOW" => "N",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => 3600000,
            "SORT_BY" => "DATE_ACTIVE",
            "ORDER_BY" => "DESC"
        ),
        false
    ); ?>
    <?= Html::endTag('div') ?>
<?php } ?>
