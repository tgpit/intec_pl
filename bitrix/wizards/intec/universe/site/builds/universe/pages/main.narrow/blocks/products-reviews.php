<?php

return [
    'type' => 'variable',
    'variants' => [
        'slider.1' => [
            'name' => 'Слайдер 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.widget',
                'template' => 'products.reviews.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_REVIEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CATALOGS_PRODUCTS_REVIEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '',
                    'PROPERTY_FILTER' => 'SHOW',
                    'PROPERTY_PRODUCTS' => 'ELEMENT_ID',
                    'PRODUCTS_IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'PRODUCTS_IBLOCK_ID' => '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'PRODUCTS_FILTER' => 'productsReviewsFilter',
                    'PRODUCTS_PRICE_CODE' => [
                        'BASE'
                    ],
                    'PRODUCTS_CONVERT_CURRENCY' => 'Y',
                    'PRODUCTS_CURRENCY_ID' => 'RUB',
                    'PRODUCTS_PRICE_VAT_INCLUDE' => 'N',
                    'PRODUCTS_SHOW_PRICE_COUNT' => '1',
                    'PRODUCTS_LIST_URL' => '',
                    'PRODUCTS_SECTION_URL' => '',
                    'PRODUCTS_DETAIL_URL' => '',
                    'SETTINGS_USE' => 'Y',
                    'PROPERTY_PREVIEW' => '',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Отзывы к товарам',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'DATE_SHOW' => 'Y',
                    'DATE_SOURCE' => 'DATE_ACTIVE_FROM',
                    'DATE_FORMAT' => 'd.m.Y',
                    'RATING_SHOW' => 'Y',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'Y',
                    'PRICE_SHOW' => 'N',
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
