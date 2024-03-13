<?php

return [
    'type' => 'variable',
    'variants' => [
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.rates',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_RATES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_RATES_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '6',
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
                    'HEADER_TEXT' => 'Тарифы',
                    'DESCRIPTION_SHOW' => 'N',
                    'COLUMNS' => 3,
                    'VIEW' => 'tabs',
                    'TABS_POSITION' => 'center',
                    'SECTION_DESCRIPTION_SHOW' => 'Y',
                    'SECTION_DESCRIPTION_POSITION' => 'center',
                    'COUNTER_SHOW' => 'Y',
                    'COUNTER_TEXT' => 'ТАРИФ',
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
        ]
    ]
];
