<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_5_PRESET_TILES_2'),
        'group' => 'projects',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'left',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_5_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 4,
            'TABS_USE' => 'Y',
            'TABS_POSITION' => 'left',
            'LINK_USE' => 'Y',
            'FOOTER_SHOW' => 'Y',
            'FOOTER_BUTTON_SHOW' => 'Y',
            'FOOTER_BUTTON_TEXT' => Loc::getMessage('PRESETS_PROJECTS_TEMPLATE_5_FOOTER_BUTTON_TEXT'),
            'FOOTER_BUTTON_BLANK' => 'Y',
            'LIST_PAGE_URL' => '',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600000',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];