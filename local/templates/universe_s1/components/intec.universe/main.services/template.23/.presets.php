<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_23_PRESET_LIST_1'),
        'group' => 'services',
        'sort' => 501,
        'properties' => [
            'SECTIONS' => [],
            'ELEMENTS_COUNT' => '',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'Y',
            'PROPERTY_MEASURE' => '',
            'PROPERTY_PRICE' => 'PRICE',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_23_PRESET_LIST_1_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'LINK_USE' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'PRICE_SHOW' => 'Y',
            'ORDER_USE' => 'Y',
            'ORDER_FORM_TEMPLATE' => '.default',
            'ORDER_FORM_TITLE' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_23_PRESET_LIST_1_ORDER_FORM_TITLE'),
            'ORDER_FORM_CONSENT' => '#SITE_DIR#company/consent/',
            'LIST_PAGE_URL' => '',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];