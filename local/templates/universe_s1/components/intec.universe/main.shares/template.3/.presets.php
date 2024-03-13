<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_3_PRESET_TILES_2'),
        'group' => 'shares',
        'sort' => 202,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'ELEMENTS_COUNT' => 4,
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_3_HEADER_BLOCK_TEXT'),

            'COLUMNS' => 3,
            'LINK_USE' => 'N',
            'LINK_BLANK' => 'N',
            'PREVIEW_SHOW' => 'N',
            'LINK_ALL_SHOW' => 'Y',
            'LINK_ALL_TEXT' => null,

            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];