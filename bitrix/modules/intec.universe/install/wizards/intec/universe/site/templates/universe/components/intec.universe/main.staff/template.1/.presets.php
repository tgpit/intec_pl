<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_STAFF_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'staff',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_POSITION' => 'POSITION',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_STAFF_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 4,
            'POSITION_SHOW' => 'Y',
            'SOCIALS_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];