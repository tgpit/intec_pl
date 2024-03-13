<?php

return [
    'type' => 'variable',
    'variants' => [
        'block.1' => [
            'name' => 'Блок 1',
            'properties' => [],
            'component' => [
                'code' => 'intec.universe:main.about',
                'template' => 'template.1',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ABOUT_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ABOUT_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SECTION" => [],
                    "ELEMENTS_MODE" => "code",
                    "ELEMENT" => "about_1",
                    "PICTURE_SOURCES" => [
                        "preview"
                    ],
                    "SETTINGS_USE" => "Y",
                    "PROPERTY_BACKGROUND" => "BACKGROUND_IMAGE",
                    "PROPERTY_TITLE" => "HEADER",
                    "PROPERTY_LINK" => "LINK",
                    "PROPERTY_VIDEO" => "VIDEO_LINK",
                    "BACKGROUND_SHOW" => "Y",
                    "TITLE_SHOW" => "Y",
                    "PREVIEW_SHOW" => "Y",
                    "BUTTON_SHOW" => "Y",
                    "BUTTON_BLANK" => "N",
                    "BUTTON_TEXT" => "Узнать подробнее",
                    "PICTURE_SHOW" => "Y",
                    "PICTURE_SIZE" => "contain",
                    "POSITION_HORIZONTAL" => "center",
                    "POSITION_VERTICAL" => "bottom",
                    "VIDEO_SHOW" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600000,
                    "CACHE_NOTES" => "",
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC"
                ]
            ]
        ],
        'block.2' => [
            'name' => 'Блок 2',
            'properties' => [
                'margin' => [
                    'top' => ['value' => 50, 'measure' => 'px'],
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.about',
                'template' => 'template.2',
                'properties' => [
                    "IBLOCK_TYPE" => "#CONTENT_ABOUT_IBLOCK_TYPE#",
                    "IBLOCK_ID" => "#CONTENT_ABOUT_IBLOCK_ID#",
                    "SECTIONS_MODE" => "id",
                    "SECTION" => [],
                    "ELEMENTS_MODE" => "code",
                    "ELEMENT" => "about_2",
                    "PICTURE_SOURCES" => [
                        "preview"
                    ],
                    "SETTINGS_USE" => "Y",
                    "ADVANTAGES_IBLOCK_TYPE" => "#CONTENT_ADVANTAGES_3_IBLOCK_TYPE#",
                    "ADVANTAGES_IBLOCK_ID" => "#CONTENT_ADVANTAGES_3_IBLOCK_ID#",
                    "PROPERTY_TITLE" => "HEADER",
                    "PROPERTY_LINK" => "LINK",
                    "PROPERTY_VIDEO" => "VIDEO_LINK",
                    "PROPERTY_ADVANTAGES" => "ADVANTAGES",
                    "ADVANTAGES_PROPERTY_SVG_FILE" => "ICON",
                    "VIEW" => "1",
                    "TITLE_SHOW" => "Y",
                    "PREVIEW_SHOW" => "Y",
                    "BUTTON_SHOW" => "Y",
                    "BUTTON_VIEW" => "1",
                    "BUTTON_BLANK" => "N",
                    "BUTTON_TEXT" => "Узнать подробнее",
                    "PICTURE_SHOW" => "Y",
                    "PICTURE_SIZE" => "contain",
                    "POSITION_HORIZONTAL" => "center",
                    "POSITION_VERTICAL" => "center",
                    "VIDEO_SHOW" => "Y",
                    "ADVANTAGES_SHOW" => "Y",
                    "SVG_FILE_USE" => "Y",
                    "ADVANTAGES_COLUMNS" => "2",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600000",
                    "CACHE_NOTES" => "",
                    "SORT_BY" => "SORT",
                    "ORDER_BY" => "ASC",
                    "LAZYLOAD_USE" => "N"
                ]
            ]
        ]
    ]
];
