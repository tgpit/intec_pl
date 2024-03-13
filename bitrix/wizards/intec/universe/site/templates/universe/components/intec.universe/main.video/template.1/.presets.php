<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_VIDEO_TEMPLATE_1_PRESET_WIDE_1'),
        'group' => 'video',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_MODE' => 'code',
            'ELEMENT' => 'video_1',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_LINK' => 'LINK',
            'HEADER_SHOW' => 'N',
            'DESCRIPTION_SHOW' => 'N',
            'WIDE' => 'Y',
            'HEIGHT' => 500,
            'FADE' => 'N',
            'SHADOW_USE' => 'N',
            'THEME' => 'light',
            'PARALLAX_USE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];