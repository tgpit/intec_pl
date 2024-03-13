<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_STAGES_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'stages',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '4',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_STAGES_TEMPLATE_1_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'COUNT_SHOW' => 'N',
            'ELEMENT_DESCRIPTION_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];