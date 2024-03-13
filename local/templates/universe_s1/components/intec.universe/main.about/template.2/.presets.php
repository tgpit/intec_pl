<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_2_PRESET_BLOCK_2'),
        'group' => 'about',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SECTION' => [],
            'ELEMENTS_MODE' => 'code',
            'PICTURE_SOURCES' => [
                0 => 'preview'
            ],
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_TITLE' => 'HEADER',
            'PROPERTY_LINK' => 'LINK',
            'PROPERTY_VIDEO' => 'VIDEO_LINK',
            'PROPERTY_ADVANTAGES' => 'ADVANTAGES',
            'ADVANTAGES_PROPERTY_SVG_FILE' => 'ICON',
            'VIEW' => '1',
            'TITLE_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_VIEW' => '1',
            'BUTTON_BLANK' => 'N',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_2_BUTTON_TEXT'),
            'PICTURE_SHOW' => 'Y',
            'PICTURE_SIZE' => 'contain',
            'POSITION_HORIZONTAL' => 'center',
            'POSITION_VERTICAL' => 'center',
            'VIDEO_SHOW' => 'Y',
            'ADVANTAGES_SHOW' => 'Y',
            'SVG_FILE_USE' => 'Y',
            'ADVANTAGES_COLUMNS' => '2',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];