<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_VIDEOS_TEMPLATE_2_PRESET_LIST_1'),
        'group' => 'videos',
        'sort' => 101,
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
            'HEADER' => Loc::getMessage('PRESETS_VIDEOS_TEMPLATE_2_HEADER'),
            'DESCRIPTION_SHOW' => 'N',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];