<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

?>

<?php $APPLICATION->IncludeComponent(
    'bitrix:menu',
    'vertical.4',
    [
        'ALLOW_MULTI_SELECT' => 'N',
        'CHILD_MENU_TYPE' => '',
        'DELAY' => 'N',
        'IBLOCK_ID' => '',
        'IBLOCK_TYPE' => '',
        'MAIN_LINK_SHOW' => 'N',
        'MAIN_VIEW' => 'simple',
        'MAX_LEVEL' => '1',
        'MENU_CACHE_GET_VARS' => [],
        'MENU_CACHE_TIME' => '3600',
        'MENU_CACHE_TYPE' => 'N',
        'MENU_CACHE_USE_GROUPS' => 'Y',
        'ROOT_MENU_TYPE' => $arParams['MENU_PERSONAL_SECTION'],
        'USE_EXT' => 'N',
        'VIEW' => 'simple.1',
        'COMPONENT_TEMPLATE' => 'vertical.4',
        'LAZYLOAD_USE' => 'N',
        'UPPERCASE' => 'N',
        'TRANSPARENT' => 'N',
        'DELIMITERS' => 'N',
        'SECTION_VIEW' => 'default',
        'SUBMENU_VIEW' => 'simple.1',
        'SECTION_COLUMNS_COUNT' => '3',
        'OVERLAY_USE' => 'N',
        'CATALOG_LINKS' => '',
        'LOGOTYPE_SHOW' => 'N',
        'BUTTONS_CLOSE_POSITION' => 'left',
        'LOGOUT_URL' => $arResult['LOGOUT_URL'],
        'CACHE_SELECTED_ITEMS' => 'N'
    ],
    $component
) ?>
