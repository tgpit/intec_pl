<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_4_PRESET_TILES_4'),
        'group' => 'categories',
        'sort' => 404,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'LINK_MODE' => 'property',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_4_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 2,
            'PREVIEW_SHOW' => 'Y',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC'
        ]
    ]
];