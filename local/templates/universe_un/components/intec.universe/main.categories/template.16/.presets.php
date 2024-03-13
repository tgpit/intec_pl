<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_16_PRESET_CHESS_3'),
        'group' => 'categories',
        'sort' => 303,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC',
            'ELEMENTS_COUNT' => 6,
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'LINK_MODE' => 'property',
            'PROPERTY_LINK' => 'LINK',
            'STICKER_SHOW' => 'Y',
            'PROPERTY_STICKER' => 'STICKER_TEXT',
            'HEADER_SHOW' => 'N',
            'DESCRIPTION_SHOW' => 'N',
            'WIDE_BLOCKS' => 'Y',
            'NAME_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y'
        ]
    ]
];