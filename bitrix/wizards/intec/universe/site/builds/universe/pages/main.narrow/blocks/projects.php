<?php

return [
    'type' => 'variable',
    'variants' => [
        'tiles.1' => [
            'name' => 'Плитки 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px'],
                    'top' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.projects',
                'template' => 'template.2',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_PROJECTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_PROJECTS_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_COUNT' => '',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_POSITION' => 'left',
                    'HEADER_TEXT' => 'Реализованные проекты',
                    'DESCRIPTION_SHOW' => 'N',
                    'WIDE' => 'Y',
                    'COLUMNS' => 3,
                    'TABS_USE' => 'Y',
                    'TABS_POSITION' => 'left',
                    'LINK_USE' => 'Y',
                    'FOOTER_SHOW' => 'Y',
                    'FOOTER_POSITION' => 'center',
                    'FOOTER_BUTTON_SHOW' => 'Y',
                    'FOOTER_BUTTON_TEXT' => 'Показать все',
                    'LIST_PAGE_URL' => '',
                    'SECTION_URL' => '',
                    'DETAIL_URL' => '',
                    'CACHE_TYPE' => 'A',
                    'CACHE_TIME' => '3600000',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'DESC'
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
                'code' => 'intec.universe:main.projects',
                'template' => 'template.5',
                'properties' => [
                    'ALIGNMENT' => 'center',
                    'BUTTON_ALL_SHOW' => 'Y',
                    'CACHE_TIME' => '0',
                    'CACHE_TYPE' => 'A',
                    'COLUMNS' => '3',
                    'DESCRIPTION_POSITION' => 'center',
                    'DESCRIPTION_SHOW' => 'Y',
                    'DESCRIPTION_TEXT' => '',
                    'DETAIL_URL' => '',
                    'ELEMENTS_COUNT' => '',
                    'HEADER_POSITION' => 'left',
                    'HEADER_SHOW' => 'Y',
                    'HEADER_TEXT' => 'Реализованные проекты',
                    'IBLOCK_TYPE' => '#CONTENT_PROJECTS_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_PROJECTS_IBLOCK_ID#',
                    'LAZYLOAD_USE' => 'N',
                    'LINK_USE' => 'Y',
                    'LIST_PAGE_URL' => '',
                    'ORDER_BY' => 'ASC',
                    'SECTIONS_MODE' => 'id',
                    'SECTION_URL' => '',
                    'SETTINGS_USE' => 'N',
                    'SLIDER_USE' => 'N',
                    'SORT_BY' => 'SORT',
                    'TABS_ELEMENTS' => '',
                    'TABS_POSITION' => 'left',
                    'TABS_USE' => 'Y',
                    'COMPONENT_TEMPLATE' => 'template.5',
                    'BUTTON_ALL_TEXT' => 'Показать все'
                ]
            ]
        ],
    ]
];
