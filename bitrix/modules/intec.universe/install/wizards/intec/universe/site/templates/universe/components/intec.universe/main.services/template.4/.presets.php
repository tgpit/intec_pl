<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_4_PRESET_WIDE_1'),
        'group' => 'services',
        'sort' => 301,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'N',
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'NUMBER_SHOW' => 'Y',
            'DESCRIPTION_SHOW' => 'Y',
            'DETAIL_SHOW' => 'Y',
            'DETAIL_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_4_DETAIL_TEXT'),
            'PARALLAX_USE' => 'N',
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