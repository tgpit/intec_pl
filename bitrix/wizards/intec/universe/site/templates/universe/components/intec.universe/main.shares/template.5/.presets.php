<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_5_PRESET_TILES_2'),
        'group' => 'shares',
        'sort' => 202,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_5_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'COLUMNS' => 4,
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'N',
            'ELEMENT_HEADER_SHOW' => 'N',
            'LINK_ALL_SHOW' => 'N',
            'LINK_ALL_TEXT' => null,
            'TIMER_SHOW' => 'N',
            'TIMER_PROPERTY_UNTIL_DATE' => null,
            'TIMER_PROPERTY_DISCOUNT' => null,
            'LIST_PAGE_URL' => '',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'NAVIGATION_TEMPLATE' => 'lazy.2'
        ]
    ]
];