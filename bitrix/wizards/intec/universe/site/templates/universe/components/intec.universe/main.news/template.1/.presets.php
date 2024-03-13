<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_1_PRESET_BLOCKS_1'),
        'group' => 'news',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_NEWS_TEMPLATE_1_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'DATE_SHOW' => 'Y',
            'DATE_FORMAT' => 'd.m.Y',
            'COLUMNS' => 3,
            'LINK_USE' => 'Y',
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