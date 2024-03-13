<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_2_PRESET_BLOCKS_1'),
        'group' => 'shares',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_2_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'ELEMENT_HEADER_PROPERTY_TEXT' => null,
            'COLUMNS' => 2,
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'N',
            'ELEMENT_HEADER_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'PREVIEW_TRUNCATE_USE' => 'N',
            'LINK_ALL_SHOW' => 'N',
            'TIMER_USE' => 'N',
            'NAVIGATION_TEMPLATE' => 'lazy.2',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];