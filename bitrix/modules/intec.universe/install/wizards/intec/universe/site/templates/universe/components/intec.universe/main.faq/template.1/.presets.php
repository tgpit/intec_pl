<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_FAQ_TEMPLATE_1_PRESET_WIDE_1'),
        'group' => 'faq',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_FAQ_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'BY_SECTION' => 'N',
            'ELEMENT_TEXT_ALIGN' => 'center',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ], [
        'name' => Loc::getMessage('PRESETS_FAQ_TEMPLATE_1_PRESET_WIDE_2'),
        'group' => 'faq',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_FAQ_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'BY_SECTION' => 'Y',
            'TABS_POSITION' => 'center',
            'ELEMENT_TEXT_ALIGN' => 'center',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];