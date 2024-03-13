<?php

return [
    'type' => 'variable',
    'variants' => [
        'wide.1' => [
            'name' => 'Широкий 1',
            'properties' => [
                'margin' => [
                    'bottom' => ['value' => 50, 'measure' => 'px']
                ]
            ],
            'component' => [
                'code' => 'intec.universe:main.video',
                'template' => 'template.1',
                'properties' => [
                    'IBLOCK_TYPE' => '#CONTENT_VIDEO_IBLOCK_TYPE#',
                    'IBLOCK_ID' => '#CONTENT_VIDEO_IBLOCK_ID#',
                    'SECTIONS_MODE' => 'id',
                    'ELEMENTS_MODE' => 'code',
                    'ELEMENT' => 'video_1',
                    'SETTINGS_USE' => 'Y',
                    'LAZYLOAD_USE' => 'N',
                    'PROPERTY_LINK' => 'LINK',
                    'HEADER_SHOW' => 'N',
                    'DESCRIPTION_SHOW' => 'N',
                    'WIDE' => 'Y',
                    'HEIGHT' => 400,
                    'FADE' => 'N',
                    'SHADOW_USE' => 'N',
                    'THEME' => 'light',
                    'PARALLAX_USE' => 'N',
                    'SORT_BY' => 'SORT',
                    'ORDER_BY' => 'ASC'
                ]
            ]
        ]
    ]
];
