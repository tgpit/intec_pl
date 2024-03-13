<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_3_PRESET_SLIDER_2'),
        'group' => 'brands',
        'sort' => 202,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'LINK_USE' => 'Y',
            'LIST_PAGE_URL' => '#SITE_DIR#help/brands/',
            'LINK_BLANK' => 'Y',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'left',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_3_HEADER_TEXT'),
            'SLIDER_USE' => 'Y',
            'SLIDER_NAVIGATION' => 'Y',
            'SLIDER_DOTS' => 'N',
            'SLIDER_LOOP' => 'Y',
            'SLIDER_AUTO_USE' => 'N',
            'LINE_COUNT' => 6,
            'FOOTER_SHOW' => 'N',
            'EFFECT_PRIMARY' => 'shadow',
            'EFFECT_SECONDARY' => 'grayscale',
            'TRANSPARENCY' => 0,
            'BORDER_SHOW' => 'Y',
            'SHOW_ALL_BUTTON_DISPLAY' => 'top',
            'SHOW_ALL_BUTTON_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_TEXT'),
            'SECTION_URL' => '',
            'DETAIL_URL' => ''
        ]
    ]
];