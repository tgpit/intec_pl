<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_VIDEOS_TEMPLATE_1_PRESET_SLIDER_1'),
        'group' => 'videos',
        'sort' => 201,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'ELEMENTS_COUNT' => '',
            'PICTURE_SOURCES' => [
                'service',
                'preview',
                'detail'
            ],
            'PICTURE_SERVICE_QUALITY' => 'sddefault',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'PROPERTY_URL' => 'LINK',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER' => Loc::getMessage('PRESETS_VIDEOS_TEMPLATE_1_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 3,
            'NAME_SHOW' => 'Y',
            'FOOTER_SHOW' => 'N',
            'SLIDER_USE' => 'Y',
            'SLIDER_LOOP_USE' => 'N',
            'SLIDER_AUTO_PLAY_USE' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];