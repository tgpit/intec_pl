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
                'code' => 'intec.universe:main.collections',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_COLLECTIONS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_COLLECTIONS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '5',
                    'SETTINGS_USE' => 'Y',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Коллекции',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'COLUMNS' => '5',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'Y',
                    'FOOTER_BLOCK_SHOW' => 'Y',
                    'FOOTER_BUTTON_SHOW' => 'Y',
                    'FOOTER_BUTTON_TEXT' => 'Показать все',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'NAVIGATION_USE' => 'Y',
                    'NAVIGATION_ID' => 'collections',
                    'NAVIGATION_MODE' => 'ajax',
                    'NAVIGATION_ALL' => 'Y',
                    'NAVIGATION_TEMPLATE' => 'lazy.2',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC',
                    'LAZYLOAD_USE' => 'N'
                ]
            ]
        ],
        'blocks.2' => [
            'name' => 'Блоки 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.collections',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_COLLECTIONS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_COLLECTIONS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Коллекции',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'COLUMNS' => '2',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'Y',
                    'FOOTER_BLOCK_SHOW' => 'Y',
                    'FOOTER_BUTTON_SHOW' => 'Y',
                    'FOOTER_BUTTON_TEXT' => 'Показать все',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'NAVIGATION_USE' => 'Y',
                    'NAVIGATION_ID' => 'collections',
                    'NAVIGATION_MODE' => 'ajax',
                    'NAVIGATION_ALL' => 'Y',
                    'NAVIGATION_TEMPLATE' => 'lazy.2',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC',
                    'LAZYLOAD_USE' => 'N'
                ]
            ]
        ],
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.collections',
                'template' => 'template.3',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_COLLECTIONS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_COLLECTIONS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Коллекции',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'COLUMNS' => '4',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'Y',
                    'FOOTER_BLOCK_SHOW' => 'Y',
                    'FOOTER_BUTTON_SHOW' => 'Y',
                    'FOOTER_BUTTON_TEXT' => 'Показать все',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'NAVIGATION_USE' => 'Y',
                    'NAVIGATION_ID' => 'collections',
                    'NAVIGATION_MODE' => 'ajax',
                    'NAVIGATION_ALL' => 'Y',
                    'NAVIGATION_TEMPLATE' => 'lazy.2',
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
