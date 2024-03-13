<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_1_PRESET_TILES_4'),
        'group' => 'services',
        'sort' => 104,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_1_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'LINE_COUNT' => 4,
            'ALIGNMENT' => 'center',
            'DESCRIPTION_SHOW' => 'Y',
            'DETAIL_SHOW' => 'Y',
            'DETAIL_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_1_DETAIL_TEXT'),
            'FOOTER_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];