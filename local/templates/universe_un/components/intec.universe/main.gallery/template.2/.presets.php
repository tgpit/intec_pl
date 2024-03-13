<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_GALLERY_TEMPLATE_2_PRESET_TILES_1'),
        'group' => 'gallery',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '8',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_GALLERY_TEMPLATE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 4,
            'ALIGNMENT' => 'center',
            'DELIMITERS' => 'N',
            'WIDE' => 'N',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ], [
        'name' => Loc::getMessage('PRESETS_GALLERY_TEMPLATE_2_PRESET_TILES_3'),
        'group' => 'gallery',
        'sort' => 103,
        'properties' => [
            'ELEMENTS_COUNT' => '12',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_GALLERY_TEMPLATE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 6,
            'ALIGNMENT' => 'center',
            'DELIMITERS' => 'N',
            'WIDE' => 'Y',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];