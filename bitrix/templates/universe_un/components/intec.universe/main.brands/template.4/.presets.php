<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_4_PRESET_TILES_3'),
        'group' => 'brands',
        'sort' => 103,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ELEMENTS_COUNT' => 8,
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'left',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_4_HEADER_TEXT'),
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'FOOTER_SHOW' => 'Y',
            'FOOTER_POSITION' => 'center',
            'FOOTER_BUTTON_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_POSITION' => 'left',
            'DESCRIPTION_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_4_DESCRIPTION_TEXT'),
            'LINE_COUNT' => 4,
            'ALIGNMENT' => 'center',
            'EFFECT_PRIMARY' => 'shadow',
            'EFFECT_SECONDARY' => 'grayscale',
            'TRANSPARENCY' => 0,
            'BORDER_SHOW' => 'Y',
            'SHOW_ALL_BUTTON_SHOW' => 'Y',
            'SHOW_ALL_BUTTON_TEXT' => Loc::getMessage('PRESETS_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_TEXT'),
            'SHOW_ALL_BUTTON_POSITION' => 'left',
            'SHOW_ALL_BUTTON_BORDER' => 'rectangular',
            'LIST_PAGE_URL' => '#SITE_DIR#help/brands/'
        ]
    ]
];