<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_18_PRESET_VIDEO_2'),
        'group' => 'reviews',
        'sort' => 402,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'ELEMENTS_COUNT' => 4,
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'left',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_18_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'RATING_SHOW' => 'Y',
            'PROPERTY_RATING' => 'RATING',
            'RATING_MAX' => '5',
            'LINK_USE' => 'Y',
            'LINK_BLANK' => 'Y',
            'PREVIEW_TRUNCATE_USE' => 'N',
            'BUTTON_ALL_SHOW' => 'Y',
            'BUTTON_ALL_TEXT' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_14_BUTTON_ALL_TEXT'),
            'LIST_PAGE_URL' => '#SITE_DIR#company/reviews/',
            'VIDEO_SHOW' => 'Y',
            'PROPERTY_VIDEO' => 'VIDEOS_ELEMENTS',
            'VIDEO_IBLOCK_PROPERTY_LINK' => 'LINK',
            'VIDEO_IMAGE_QUALITY' => 'hqdefault',
            'SLIDER_USE' => 'Y',
            'SLIDER_DOTS' => 'Y',
            'SLIDER_LOOP' => 'Y',
            'SLIDER_AUTO_USE' => 'N'
        ]
    ]
];