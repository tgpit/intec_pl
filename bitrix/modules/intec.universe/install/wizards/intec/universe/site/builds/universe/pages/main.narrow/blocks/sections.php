<?php

return [
    'type' => 'variable',
    'variants' => [
        'list.1' => [
            'name' => 'Список 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.sections',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'QUANTITY' => 'N',
                    'SECTIONS_MODE' => 'id',
                    'DEPTH' => 2,
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'left',
                    'HEADER_TEXT' => 'Популярные категории',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 2,
                    'SUB_SECTIONS_SHOW' => 'Y',
                    'SUB_SECTIONS_MAX' => 3,
                    'SECTION_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.sections',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'QUANTITY' => 'N',
                    'SECTIONS_MODE' => 'id',
                    'DEPTH' => 1,
                    'ELEMENTS_COUNT' => '6',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'left',
                    'HEADER_TEXT' => 'Популярные категории',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 3,
                    'SECTION_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.2' => [
            'name' => 'Плитки 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.sections',
                'template' => 'template.3',
                'properties' => [
                    'IBLOCK_TYPE' => '#CATALOGS_PRODUCTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CATALOGS_PRODUCTS_IBLOCK_ID#',
                    'QUANTITY' => 'N',
                    'SECTIONS_MODE' => 'id',
                    'DEPTH' => 1,
                    'ELEMENTS_COUNT' => '6',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Популярные категории',
                    'DESCRIPTION_SHOW' => 'N',
                    'BUTTON_ALL_SHOW' => 'Y',
                    'BUTTON_ALL_TEXT' => 'Весь каталог',
                    'LINE_COUNT' => 3,
                    'SECTION_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
