<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_2_PRESET_BLOCKS_2'),
        'group' => 'news',
        'sort' => 102,
        'properties' => [
            'ELEMENTS_COUNT' => '6',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_2_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'DATE_SHOW' => 'Y',
            'DATE_FORMAT' => 'd.m.Y',
            'DATE_TYPE' => 'DATE_ACTIVE_FROM',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'N',
            'COLUMNS' => 2,
            'PREVIEW_SHOW' => 'N',
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