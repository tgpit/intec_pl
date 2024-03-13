<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_8_PRESET_MOSAIC_1'),
        'group' => 'categories',
        'sort' => 501,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '5',
            'LINK_MODE' => 'property',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'PROPERTY_STICKER' => 'STICKER_TEXT',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_CATEGORIES_TEMPLATE_8_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 3,
            'FIRST_ITEM_BIG' => 'Y',
            'NAME_HORIZONTAL' => 'left',
            'NAME_VERTICAL' => 'bottom',
            'STICKER_SHOW' => 'Y',
            'STICKER_HORIZONTAL' => 'left',
            'STICKER_VERTICAL' => 'top',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'SORT_ORDER' => 'ASC'
        ]
    ]
];