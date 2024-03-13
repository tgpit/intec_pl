<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_2_PRESET_TILES_1'),
        'group' => 'advantages',
        'sort' => 201,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'LINE_COUNT' => 4,
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_2_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'VIEW' => 'number',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];