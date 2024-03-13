<?php

return [
    'type' => 'variable',
    'variants' => [
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.gallery',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_PHOTO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_PHOTO_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '8',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Фотогалерея',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 4,
                    'ALIGNMENT' => 'center',
                    'DELIMITERS' => 'N',
                    'WIDE' => 'N',
                    'FOOTER_SHOW' => 'N',
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
                'code' => 'intec.universe:main.gallery',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_PHOTO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_PHOTO_IBLOCK_ID#',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Фотогалерея',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 4,
                    'ALIGNMENT' => 'center',
                    'TABS_POSITION' => 'center',
                    'DELIMITERS' => 'N',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'tiles.3' => [
            'name' => 'Плитки 3 (Широкие)',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.gallery',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_PHOTO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_PHOTO_IBLOCK_ID#',
                    'ELEMENTS_COUNT' => '12',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Фотогалерея',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 6,
                    'ALIGNMENT' => 'center',
                    'DELIMITERS' => 'N',
                    'WIDE' => 'Y',
                    'FOOTER_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
