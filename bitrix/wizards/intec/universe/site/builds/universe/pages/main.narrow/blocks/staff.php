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
                'code' => 'intec.universe:main.staff',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_STAFF_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_STAFF_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '3',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'PROPERTY_POSITION' => 'POSITION',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Наша команда',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 3,
                    'POSITION_SHOW' => 'Y',
                    'SOCIALS_SHOW' => 'N',
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
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.staff',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_STAFF_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_STAFF_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '3',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Наша команда',
                    'DESCRIPTION_SHOW' => 'N',
                    'POSITION_PROPERTIES' => 'POSITION',
                    'LINE_COUNT' => 3,
                    'BUTTON_SHOW' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
