<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_11_PRESET_TILES_2'),
        'group' => 'advantages',
        'sort' => 202,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_ADVANTAGES_TEMPLATE_11_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'PREVIEW_SHOW' => 'Y',
            'NUMBER_SHOW' => 'Y',
            'COLUMNS' => 2,
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];