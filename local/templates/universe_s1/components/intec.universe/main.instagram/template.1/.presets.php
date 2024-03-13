<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_1_PRESET_TYPE_1'),
        'group' => 'social',
        'sort' => 101,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'Y',
            'ACCESS_TOKEN' => '',
            'COUNT_ITEMS' => '10',
            'CACHE_PATH' => 'upload/intec.universe/instagram/cache#SITE_DIR#',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_1_PRESET_TYPE_1_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'Y',
            'ITEM_WIDE' => 'N',
            'DESCRIPTION_POSITION' => 'center',
            'DESCRIPTION_TEXT' => Loc::getMessage('PRESETS_SOCIAL_TEMPLATE_1_PRESET_TYPE_1_DESCRIPTION_TEXT'),
            'ITEM_DESCRIPTION_SHOW' => 'Y',
            'ITEM_LINE_COUNT' => '5',
            'ITEM_PADDING_USE' => 'N',
            'FOOTER_SHOW' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ]
    ]
];