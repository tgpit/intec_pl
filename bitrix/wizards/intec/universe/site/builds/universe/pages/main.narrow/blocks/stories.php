<?php

return [
    'type' => 'variable',
    'variants' => [
        'blocks.1' => [
            'name' => 'Блоки 1',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 60, 'measure' => 'px'],
                    'bottom' => ['value' => 60, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.stories',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_STORIES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_STORIES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '',
                    'ELEMENT_ITEMS_COUNT' => '',
                    'LIST_VIEW' => 'round',
                    'NAVIGATION_BUTTON_SHOW' => 'Y',
                    'POPUP_TIME' => '10',
                    'PROPERTY_LINK' => 'LINK',
                    'PROPERTY_BUTTON_TEXT' => 'BUTTON_TEXT',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Истории',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ],
        'blocks.2' => [
            'name' => 'Блоки 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 60, 'measure' => 'px'],
                    'bottom' => ['value' => 60, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.stories',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_STORIES_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_STORIES_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'SECTIONS' => [],
                    'ELEMENTS_COUNT' => '',
                    'ELEMENT_ITEMS_COUNT' => '',
                    'LIST_VIEW' => 'rectangle',
                    'NAVIGATION_BUTTON_SHOW' => 'Y',
                    'POPUP_TIME' => '10',
                    'PROPERTY_LINK' => 'LINK',
                    'PROPERTY_BUTTON_TEXT' => 'BUTTON_TEXT',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'center',
                    'HEADER_TEXT' => 'Истории',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'CACHE_NOTES' => '',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
