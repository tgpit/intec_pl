<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_2_PRESET_TILES_5'),
        'group' => 'services',
        'sort' => 105,
        'properties' => [
            'ELEMENTS_COUNT' => '5',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_PRICE' => 'PRICE',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_2_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'TEMPLATE_VIEW' => 'mosaic',
            'PRICE_SHOW' => 'N',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_TYPE' => 'order',
            'FORM_TEMPLATE' => '.default',
            'FORM_TITLE' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_1_FORM_TITLE'),
            'CONSENT_URL' => '#SITE_DIR#company/consent/',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_SERVICES_TEMPLATE_1_BUTTON_TEXT'),
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