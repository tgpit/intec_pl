<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_2_PRESET_LIST_1'),
        'group' => 'categories',
        'sort' => 201,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '6',
            'LINK_MODE' => 'property',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 3,
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'PICTURE_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC'
        ]
    ]
];