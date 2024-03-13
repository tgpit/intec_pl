<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<div class="widget-map-container" id="<?= $sTemplateId ?>_map">
    <?php

        $arData = [];

        if (!empty($arResult['MAIN'])) {
            if ($arResult['MAIN']['DATA']['MAP']['SHOW']) {
                if ($arVisual['MAP']['VENDOR'] === 'google') {
                    $arData['google_lat'] = $arResult['MAIN']['DATA']['MAP']['VALUES']['LAT'];
                    $arData['google_lon'] = $arResult['MAIN']['DATA']['MAP']['VALUES']['LON'];
                    $arData['google_scale'] = 16;
                } else if ($arVisual['MAP']['VENDOR'] === 'yandex') {
                    $arData['yandex_lat'] = $arResult['MAIN']['DATA']['MAP']['VALUES']['LAT'];
                    $arData['yandex_lon'] = $arResult['MAIN']['DATA']['MAP']['VALUES']['LON'];
                    $arData['yandex_scale'] = 16;
                }
            }
        }

        $arData['PLACEMARKS'] = [];

        foreach ($arResult['ITEMS'] as &$arItem) {
            if (!$arItem['DATA']['MAP']['SHOW'])
                continue;

            $arData['PLACEMARKS'][] = [
                'LAT' => $arItem['DATA']['MAP']['VALUES']['LAT'],
                'LON' => $arItem['DATA']['MAP']['VALUES']['LON'],
                'TEXT' => $arItem['NAME']
            ];
        }

        if ($arVisual['MAP']['VENDOR'] == 'google') {
            $APPLICATION->IncludeComponent(
                'bitrix:map.google.view',
                '.default', [
                    'MAP_ID' => $arVisual['MAP']['ID'],
                    'API_KEY' => $arParams['API_KEY_MAP'],
                    'INIT_MAP_TYPE' => 'ROADMAP',
                    'MAP_DATA' => serialize($arData),
                    'MAP_WIDTH' => '100%',
                    'MAP_HEIGHT' => '100%',
                    'OVERLAY' => 'Y',
                    'CONTROLS' => [
                        0 => 'ZOOM',
                        1 => 'MINIMAP',
                        2 => 'TYPECONTROL',
                        3 => 'SCALELINE'
                    ],
                    'OPTIONS' => [
                        0 => 'ENABLE_SCROLL_ZOOM',
                        1 => 'ENABLE_DBLCLICK_ZOOM',
                        2 => 'ENABLE_DRAGGING'
                    ],
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            );
        } else if ($arVisual['MAP']['VENDOR'] == 'yandex') {
            $APPLICATION->IncludeComponent(
                'bitrix:map.yandex.view',
                '.default', [
                    'COMPONENT_TEMPLATE' => '.default',
                    'INIT_MAP_TYPE' => 'ROADMAP',
                    'MAP_ID' => $arVisual['MAP']['ID'],
                    'MAP_DATA' => serialize($arData),
                    'MAP_WIDTH' => '100%',
                    'MAP_HEIGHT' => '100%',
                    'CONTROLS' => [
                        0 => 'SMALLZOOM',
                        1 => 'MINIMAP',
                        2 => 'TYPECONTROL',
                        3 => 'SCALELINE'
                    ],
                    'OPTIONS' => [
                        0 => 'ENABLE_SCROLL_ZOOM',
                        1 => 'ENABLE_DBLCLICK_ZOOM',
                        2 => 'ENABLE_DRAGGING'
                    ],
                    'OVERLAY' => 'Y',
                    'DEV_MODE' => 'Y'
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            );
        }

    ?>
</div>