<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_5_PRESET_TILES_5'),
        'group' => 'categories',
        'sort' => 405,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '6',
            'LINK_MODE' => 'property',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'PROPERTY_SIZE' => 'SIZE',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_5_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'POSITION_HORIZONTAL' => 'left',
            'POSITION_VERTICAL' => 'bottom',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC'
        ]
    ]
];