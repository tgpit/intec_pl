<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_STAGES_TEMPLATE_2_PRESET_TILES_2'),
        'group' => 'stages',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_STAGES_TEMPLATE_2_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'LINE_COUNT' => 4,
            'ELEMENT_SHOW_DESCRIPTION' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];