<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_2_PRESET_TILES_1'),
        'group' => 'projects',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'WIDE' => 'Y',
            'COLUMNS' => 5,
            'TABS_USE' => 'Y',
            'TABS_POSITION' => 'center',
            'LINK_USE' => 'Y',
            'FOOTER_SHOW' => 'Y',
            'FOOTER_POSITION' => 'center',
            'FOOTER_BUTTON_SHOW' => 'Y',
            'FOOTER_BUTTON_TEXT' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_2_FOOTER_BUTTON_TEXT'),
            'LIST_PAGE_URL' => '',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600000',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'DESC'
        ]
    ]
];