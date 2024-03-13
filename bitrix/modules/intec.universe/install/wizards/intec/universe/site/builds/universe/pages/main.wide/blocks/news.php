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
                'code' => 'intec.universe:main.news',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_NEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_NEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '4',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Новости',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'DATE_SHOW' => 'Y',
                    'DATE_FORMAT' => 'd.m.Y',
                    'COLUMNS' => 3,
                    'LINK_USE' => 'Y',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
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
                'code' => 'intec.universe:main.news',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_NEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_NEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '6',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Новости',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'DATE_SHOW' => 'Y',
                    'DATE_FORMAT' => 'd.m.Y',
                    'DATE_TYPE' => 'DATE_ACTIVE_FROM',
                    'LINK_USE' => 'Y',
                    'LINK_BLANK' => 'N',
                    'COLUMNS' => 2,
                    'PREVIEW_SHOW' => 'N',
                    'SLIDER_LOOP' => 'N',
                    'SLIDER_AUTO_USE' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
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
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.news',
                'template' => 'template.3',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_NEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_NEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '4',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Новости',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'DATE_SHOW' => 'Y',
                    'DATE_FORMAT' => 'd.m.Y',
                    'COLUMNS' => 4,
                    'LINK_USE' => 'Y',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'slider.1' => [
            'name' => 'Слайдер 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.news',
                'template' => 'template.5',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_NEWS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_NEWS_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_BLOCK_SHOW' => 'Y',
                    'HEADER_BLOCK_POSITION' => 'center',
                    'HEADER_BLOCK_TEXT' => 'Новости',
                    'DESCRIPTION_BLOCK_SHOW' => 'N',
                    'DATE_SHOW' => 'Y',
                    'DATE_FORMAT' => 'd.m.Y',
                    'COLUMNS' => 4,
                    'LINK_USE' => 'Y',
                    'PREVIEW_SHOW' => 'Y',
                    'PREVIEW_LENGTH' => '150',
                    'SLIDER_NAV' => 'Y',
                    'SLIDER_LOOP' => 'N',
                    'SLIDER_AUTO_USE' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
