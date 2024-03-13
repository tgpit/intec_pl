<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_5_PRESET_SLIDER_1'),
        'group' => 'news',
        'sort' => 201,
        'properties' => [
            'ELEMENTS_COUNT' => '',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_5_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'DATE_SHOW' => 'Y',
            'DATE_FORMAT' => 'd.m.Y',
            'COLUMNS' => 4,
            'LINK_USE' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'PREVIEW_LENGTH' => '150',
            'SLIDER_NAV' => 'Y',
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