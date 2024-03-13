<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_1_PRESET_BLOCK_1'),
        'group' => 'about',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SECTION' => '',
            'ELEMENTS_MODE' => 'code',
            'PICTURE_SOURCES' => [
                0 => 'preview'
            ],
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_BACKGROUND' => 'BACKGROUND_IMAGE',
            'PROPERTY_TITLE' => 'HEADER',
            'PROPERTY_LINK' => 'LINK',
            'PROPERTY_VIDEO' => 'VIDEO_LINK',
            'BACKGROUND_SHOW' => 'Y',
            'TITLE_SHOW' => 'Y',
            'PREVIEW_SHOW' => 'Y',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_BLANK' => 'N',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_1_BUTTON_TEXT'),
            'PICTURE_SHOW' => 'Y',
            'PICTURE_SIZE' => 'contain',
            'POSITION_HORIZONTAL' => 'center',
            'POSITION_VERTICAL' => 'center',
            'VIDEO_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
        ]
    ]
];