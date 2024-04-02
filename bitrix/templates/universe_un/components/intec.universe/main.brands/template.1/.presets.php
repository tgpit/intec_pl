<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'brands',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '8',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'LINK_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 4,
            'ALIGNMENT' => 'center',
            'EFFECT' => 'none',
            'TRANSPARENCY' => '0',
            'SLIDER_USE' => 'N',
            'FOOTER_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ], [
        'name' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_1_PRESET_SLIDER_1'),
        'group' => 'brands',
        'sort' => 201,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'LINK_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 5,
            'EFFECT' => 'none',
            'TRANSPARENCY' => '0',
            'SLIDER_USE' => 'Y',
            'SLIDER_NAVIGATION' => 'Y',
            'SLIDER_DOTS' => 'Y',
            'SLIDER_LOOP' => 'N',
            'SLIDER_AUTO_USE' => 'N',
            'FOOTER_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];