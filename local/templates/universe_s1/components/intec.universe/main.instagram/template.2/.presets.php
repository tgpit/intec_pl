<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_2_PRESET_TYPE_2'),
        'group' => 'social',
        'sort' => 102,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'Y',
            'ACCESS_TOKEN' => '',
            'COUNT_ITEMS' => '10',
            'CACHE_PATH' => 'upload/intec.universe/instagram/cache#SITE_DIR#',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_2_PRESET_TYPE_2_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'Y',
            'DESCRIPTION_POSITION' => 'center',
            'DESCRIPTION_TEXT' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_2_PRESET_TYPE_2_DESCRIPTION_TEXT'),
            'ITEM_DATE_SHOW' => 'Y',
            'ITEM_DATE_FORMAT' => 'd.m.Y',
            'ITEM_DESCRIPTION_SHOW' => 'Y',
            'ITEM_FIRST_BIG' => 'Y',
            'ITEM_SHOW_MORE' => 'N',
            'ITEM_FILL_BLOCKS' => 'N',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ]
    ]
];