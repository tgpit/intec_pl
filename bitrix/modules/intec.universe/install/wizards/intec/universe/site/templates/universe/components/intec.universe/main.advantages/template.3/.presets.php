<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_3_PRESET_CHESS_1'),
        'group' => 'advantages',
        'sort' => 401,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '3',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_3_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'BACKGROUND_SIZE' => 'cover',
            'ARROW_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];