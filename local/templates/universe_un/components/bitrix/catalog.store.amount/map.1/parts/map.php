<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php if ($arVisual['MAP']['TYPE'] === 'google') {
    $APPLICATION->IncludeComponent(
        'bitrix:map.google.view',
        '.default', [
            'MAP_ID' => 'stores',
            'INIT_MAP_TYPE' => 'MAP',
            'MAP_WIDTH' => '100%',
            'MAP_HEIGHT' => '100%',
            'OVERLAY' => 'Y',
            'CONTROLS' => [
                'SMALL_ZOOM_CONTROL',
                'TYPECONTROL',
                'SCALELINE'
            ],
            'OPTIONS' => [
                'ENABLE_SCROLL_ZOOM',
                'ENABLE_DBLCLICK_ZOOM',
                'ENABLE_DRAGGING',
                'ENABLE_KEYBOARD'
            ],
            'DEV_MODE' => 'Y'
        ],
        $component
    );
} else if ($arVisual['MAP']['TYPE'] === 'yandex') {
    $APPLICATION->IncludeComponent(
        'bitrix:map.yandex.view',
        '.default', [
            'MAP_ID' => 'stores',
            'INIT_MAP_TYPE' => 'MAP',
            'MAP_WIDTH' => '100%',
            'MAP_HEIGHT' => '100%',
            'OVERLAY' => 'Y',
            'CONTROLS' => [
                'TOOLBAR',
                'ZOOM',
                'SMALLZOOM',
                'TYPECONTROL',
                'SCALELINE'
            ],
            'OPTIONS' => [
                'ENABLE_SCROLL_ZOOM',
                'ENABLE_DBLCLICK_ZOOM',
                'ENABLE_DRAGGING',
                'ENABLE_HOTKEYS'
            ],
            'DEV_MODE' => 'Y'
        ],
        $component
    );
} ?>