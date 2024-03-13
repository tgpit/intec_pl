<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_33_PRESET_NUMBERS_3'),
        'group' => 'advantages',
        'sort' => 103,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ELEMENTS_COUNT' => 4,
            'PROPERTY_NUMBER' => 'NUMBER_VALUE',
            'PROPERTY_MAX_NUMBER' => 'NUMBER_MAXIMUM',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_33_HEADER'),
            'COLUMNS' => 4
        ]
    ]
];