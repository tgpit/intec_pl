<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_1_PRESET_ICONS_1'),
        'group' => 'advantages',
        'sort' => 301,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'ELEMENTS_ROW_COUNT' => 4,
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_1_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];