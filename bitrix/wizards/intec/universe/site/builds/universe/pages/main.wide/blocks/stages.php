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
                'code' => 'intec.universe:main.stages',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '4',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Этапы работ',
                    'DESCRIPTION_SHOW' => 'N',
                    'COUNT_SHOW' => 'N',
                    'ELEMENT_DESCRIPTION_SHOW' => 'Y',
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
                'code' => 'intec.universe:main.stages',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_ADVANTAGES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_ADVANTAGES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '4',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER' => 'Этапы работ',
                    'DESCRIPTION_SHOW' => 'N',
                    'LINE_COUNT' => 4,
                    'ELEMENT_SHOW_DESCRIPTION' => 'N',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => 3600000,
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
