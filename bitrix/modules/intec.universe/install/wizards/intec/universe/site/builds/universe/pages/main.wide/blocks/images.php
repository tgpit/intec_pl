<?php

return [
    'type' => 'variable',
    'variants' => [
        'blocks.1' => [
            'name' => 'Блоки 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.images',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_IMAGERY_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_IMAGERY_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'SECTIONS_COUNT' => '',
                    'SECTION_ELEMENTS_COUNT' => '',
                    'ELEMENTS_COUNT' => '',
                    'SETTINGS_USE' => 'Y',
                    'PROPERTY_DISPLAY' => [
                        'PROPERTY_SEASON',
                        'PROPERTY_STYLE',
                        'PROPERTY_COLOR',
                        'PROPERTY_PRICE_CATEGORY'
                    ],
                    'PROPERTY_PREVIEW' => '',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Готовые образы',
                    'DESCRIPTION_SHOW' => 'N',
                    'TABS_USE' => 'Y',
                    'TABS_POSITION' => 'left',
                    'PICTURE_SHOW' => 'Y',
                    'DISPLAY_SHOW' => 'Y',
                    'PREVIEW_SHOW' => 'Y',
                    'DETAIL_SHOW' => 'Y',
                    'DETAIL_TEXT' => 'Заглянуть в образ',
                    'DETAIL_BLANK' => 'Y',
                    'MORE_SHOW' => 'Y',
                    'MORE_TEXT' => 'Все образы',
                    'MORE_BLANK' => 'Y',
                    'PRODUCTS_SHOW'=> 'Y',
                    'PROPERTY_PRODUCTS'=> 'ELEMENTS',
                    'PRODUCTS_IBLOCK_TYPE'=> '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'PRODUCTS_IBLOCK_ID'=> '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'PRODUCTS_ELEMENTS_COUNT'=> '',
                    'PRODUCTS_FILTER'=> 'collectionsFilter',
                    'PRODUCTS_PRICE_CODE'=> [
                        'BASE',
                    ],
                    'PRODUCTS_CONVERT_CURRENCY'=> 'N',
                    'PRODUCTS_PRICE_VAT_INCLUDE'=> 'N',
                    'PRODUCTS_SHOW_PRICE_COUNT'=> '1',
                    'PRODUCTS_SORT_BY'=> 'SORT',
                    'PRODUCTS_ORDER_BY'=> 'ASC',
                    'PRODUCTS_LIST_URL'=> '',
                    'PRODUCTS_SECTION_URL'=> '',
                    'PRODUCTS_DETAIL_URL'=> '',
                    'PRODUCTS_PICTURE_SHOW'=> 'Y',
                    'PRODUCTS_PRICE_SHOW'=> 'Y',
                    'PRODUCTS_DISCOUNT_SHOW'=> 'Y',
                    'PRODUCTS_MEASURE_SHOW'=> 'N',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC',
                    'LAZYLOAD_USE' => 'N'
                ]
            ]
        ]
    ]
];
