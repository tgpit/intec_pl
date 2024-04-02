<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_1_PRESET_TILES_2'),
        'group' => 'categories',
        'sort' => 402,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'LINK_MODE' => 'property',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 4,
            'LINK_USE' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC'
        ]
    ]
];