<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_RATES_TEMPLATE_1_PRESET_TILES_1'),
        'group' => 'projects',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '8',
            'PROPERTY_LIST' => [
                'PROPERTY_PRODUCT_COUNT',
                'PROPERTY_PHOTO_COUNT',
                'PROPERTY_DOCUMENTS_COUNT',
                'PROPERTY_DISK_SPACE'
            ],
            'PROPERTY_PRICE' => 'PRICE',
            'PROPERTY_CURRENCY' => 'CURRENCY',
            'PROPERTY_DISCOUNT' => 'DISCOUNT',
            'PROPERTY_DISCOUNT_TYPE' => '',
            'PROPERTY_DETAIL_URL' => '',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_RATES_TEMPLATE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 4,
            'VIEW' => 'tabs',
            'TABS_POSITION' => 'center',
            'SECTION_DESCRIPTION_SHOW' => 'Y',
            'SECTION_DESCRIPTION_POSITION' => 'center',
            'COUNTER_SHOW' => 'Y',
            'COUNTER_TEXT' => Loc::getMessage('PRESETS_RATES_TEMPLATE_1_COUNTER_TEXT'),
            'PRICE_SHOW' => 'Y',
            'DISCOUNT_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'PROPERTIES_SHOW' => 'Y',
            'BUTTON_SHOW' => 'N',
            'SLIDER_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];