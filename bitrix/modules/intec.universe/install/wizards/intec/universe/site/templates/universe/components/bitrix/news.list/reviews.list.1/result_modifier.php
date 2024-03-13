<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

//Loc::loadMessages(__FILE__);

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'VIDEO_IBLOCK_TYPE' => null,
    'VIDEO_IBLOCK_ID' => null,
    'STAFF_IBLOCK_TYPE' => null,
    'STAFF_IBLOCK_ID' => null,
    'PROPERTY_TEXT' => null,
    'PROPERTY_INFORMATION' => null,
    'PROPERTY_RATING' => null,
    'PROPERTY_VIDEO' => null,
    'VIDEO_PROPERTY_URL' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_FILES' => null,
    'PROPERTY_ANSWER' => null,
    'PROPERTY_STAFF' => null,
    'STAFF_PROPERTY_POSITION' => null,
    'DATE_SHOW' => 'N',
    'DATE_SOURCE' => 'DATE_ACTIVE_FROM',
    'DATE_FORMAT' => 'd.m.Y',
    'INFORMATION_SHOW' => 'N',
    'RATING_SHOW' => 'N',
    'VIDEO_SHOW' => 'N',
    'VIDEO_PICTURE_SOURCES' => [],
    'VIDEO_PICTURE_QUALITY' => 'hqdefault',
    'PICTURES_SHOW' => 'N',
    'FILES_SHOW' => 'N',
    'ANSWER_SHOW' => 'N',
    'ANSWER_DEFAULT_NAME' => null,
    'ANSWER_POSITION_SHOW' => 'N',
    'ANSWER_DEFAULT_POSITION' => null,
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (Type::isArray($arParams['VIDEO_PICTURE_SOURCES']))
    $arParams['VIDEO_PICTURE_SOURCES'] = array_filter($arParams['VIDEO_PICTURE_SOURCES']);

if (empty($arParams['VIDEO_PICTURE_SOURCES']) || !Type::isArray($arParams['VIDEO_PICTURE_SOURCES']))
    $arParams['VIDEO_PICTURE_SOURCES'] = ['service'];

$arVisual = [
    'SETTINGS' => [
        'USE' => $arParams['SETTINGS_USE'] === 'Y'
    ],
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'DATE' => [
        'SHOW' => $arParams['DATE_SHOW'] === 'Y',
        'SOURCE' => ArrayHelper::fromRange([
            'DATE_CREATE',
            'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO',
            'TIMESTAMP_X'
        ], $arParams['DATE_SOURCE']),
        'FORMAT' => !empty($arParams['DATE_FORMAT']) ? $arParams['DATE_FORMAT'] : 'd.m.Y'
    ],
    'INFORMATION' => [
        'SHOW' => $arParams['INFORMATION_SHOW'] === 'Y' && !empty($arParams['PROPERTY_INFORMATION'])
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y' && !empty($arParams['PROPERTY_RATING'])
    ],
    'VIDEO' => [
        'SHOW' => $arParams['VIDEO_SHOW'] === 'Y' && !empty($arParams['PROPERTY_VIDEO']) && !empty($arParams['VIDEO_IBLOCK_ID']) && !empty($arParams['VIDEO_PROPERTY_URL']),
        'SOURCES' => $arParams['VIDEO_PICTURE_SOURCES'],
        'QUALITY' => ArrayHelper::fromRange([
            'hqdefault',
            'mqdefault',
            'sddefault',
            'maxresdefault',
        ], $arParams['VIDEO_PICTURE_QUALITY'])
    ],
    'PICTURES' => [
        'SHOW' => $arParams['PICTURES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PICTURES'])
    ],
    'FILES' => [
        'SHOW' => $arParams['FILES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_FILES'])
    ],
    'ANSWER' => [
        'SHOW' => $arParams['ANSWER_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ANSWER']),
        'DEFAULT' => $arParams['ANSWER_DEFAULT_NAME'],
        'PICTURE' => [
            'SHOW' => $arParams['ANSWER_PICTURE_SHOW'] === 'Y',
            'DEFAULT' => StringHelper::replaceMacros($arParams['ANSWER_DEFAULT_PICTURE'], [
                'SITE_DIR' => SITE_DIR
            ])
        ],
        'POSITION' => [
            'SHOW' => $arParams['ANSWER_POSITION_SHOW'] === 'Y',
            'DEFAULT' => $arParams['ANSWER_DEFAULT_POSITION']
        ]
    ],
    'NAVIGATION' => [
        'SHOW' => [
            'TOP' => $arParams['DISPLAY_TOP_PAGER'] && !empty($arResult['NAV_STRING']),
            'BOTTOM' => $arParams['DISPLAY_BOTTOM_PAGER'] && !empty($arResult['NAV_STRING'])
        ]
    ]
];

if ($arVisual['RATING']['SHOW']) {
    $arResult['RATING_VALUES'] = Arrays::fromDBResult(CIBlockPropertyEnum::GetList([
        'SORT' => 'ASC',
        'VALUE' => 'ASC'
    ], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CODE' => $arParams['PROPERTY_RATING']
    ]))->indexBy('XML_ID')->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => $value['VALUE']
        ];
    });

    if (empty($arResult['RATING_VALUES']))
        $arVisual['RATING']['SHOW'] = false;
} else {
    $arResult['RATING_VALUES'] = [];
}

$arUsers = [];
$arCollection = [
    'FILES' => [],
    'STAFF' => [],
    'VIDEO' => []
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PICTURE' => [],
        'DATE' => null,
        'INFORMATION' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'RATING' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'TEXT' => $arItem['PREVIEW_TEXT'],
        'VIDEO' => [
            'SHOW' => false,
            'VIEW' => false,
            'VALUES' => []
        ],
        'PICTURES' => [
            'SHOW' => false,
            'VALUES' => []
        ],
        'FILES' => [
            'SHOW' => false,
            'VALUES' => []
        ],
        'ANSWER' => [
            'SHOW' => false,
            'TEXT' => null,
            'NAME' => null,
            'PICTURE' => [
                'SHOW' => false,
                'VALUE' => null
            ],
            'POSITION' => [
                'SHOW' => false,
                'VALUE' => null
            ]
        ]
    ];

    if ($arVisual['PICTURE']['SHOW']) {
        if (!empty($arItem['PREVIEW_PICTURE']))
            $arItem['DATA']['PICTURE'] = $arItem['PREVIEW_PICTURE'];
        else if (!empty($arItem['DETAIL_PICTURE']))
            $arItem['DATA']['PICTURE'] = $arItem['DETAIL_PICTURE'];
    }

    if (empty($arItem[$arVisual['DATE']['SOURCE']]))
        $arItem['DATA']['DATE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['DATE']['FORMAT'],
            MakeTimeStamp($arItem['DATE_CREATE'], CSite::GetDateFormat())
        );
    else
        $arItem['DATA']['DATE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['DATE']['FORMAT'],
            MakeTimeStamp($arItem[$arVisual['DATE']['SOURCE']], CSite::GetDateFormat())
        );

    if ($arVisual['INFORMATION']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_INFORMATION']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            if (!empty($arProperty['DISPLAY_VALUE']))
                $arItem['DATA']['INFORMATION']['VALUE'] = $arProperty['DISPLAY_VALUE'];
        }

        unset($arProperty);

        if (!empty($arItem['DATA']['INFORMATION']['VALUE']))
            $arItem['DATA']['INFORMATION']['SHOW'] = true;
    }

    if ($arVisual['RATING']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_RATING'],
            'VALUE_XML_ID'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (ArrayHelper::keyExists($arProperty, $arResult['RATING_VALUES'])) {
                $arItem['DATA']['RATING']['SHOW'] = true;
                $arItem['DATA']['RATING']['VALUE'] = $arProperty;
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_TEXT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_TEXT']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            if (!empty($arProperty['DISPLAY_VALUE']))
                $arItem['DATA']['TEXT'] = $arProperty['DISPLAY_VALUE'];
        }

        unset($arProperty);
    }

    if ($arVisual['VIDEO']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_VIDEO'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty)) {
                foreach ($arProperty as $value) {
                    if (!ArrayHelper::isIn($value, $arCollection['VIDEO']))
                        $arCollection['VIDEO'][] = $value;
                }

                unset($value);
            } else {
                if (!ArrayHelper::isIn($arProperty, $arCollection['VIDEO']))
                    $arCollection['VIDEO'][] = $arProperty;
            }
        }

        unset($arProperty);
    }

    if ($arVisual['PICTURES']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_PICTURES'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty)) {
                foreach ($arProperty as $value) {
                    if (!ArrayHelper::isIn($value, $arCollection['VIDEO']))
                        $arCollection['FILES'][] = $value;
                }

                unset($value);
            } else {
                if (!ArrayHelper::isIn($arProperty, $arCollection['VIDEO']))
                    $arCollection['FILES'][] = $arProperty;
            }
        }

        unset($arProperty);
    }

    if ($arVisual['FILES']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_FILES'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty)) {
                foreach ($arProperty as $value) {
                    if (!ArrayHelper::isIn($value, $arCollection['FILES']))
                        $arCollection['FILES'][] = $value;
                }

                unset($value);
            } else {
                if (!ArrayHelper::isIn($arProperty, $arCollection['FILES']))
                    $arCollection['FILES'][] = $arProperty;
            }
        }

        unset($arProperty);
    }

    if ($arVisual['ANSWER']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_ANSWER']);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['ANSWER']['SHOW'] = true;
                $arItem['DATA']['ANSWER']['TEXT'] = $arProperty['DISPLAY_VALUE'];

                if (!empty($arVisual['ANSWER']['DEFAULT']))
                    $arItem['DATA']['ANSWER']['NAME'] = $arVisual['ANSWER']['DEFAULT'];

                if (!empty($arVisual['ANSWER']['PICTURE']['DEFAULT'])) {
                    $arItem['DATA']['ANSWER']['PICTURE']['SHOW'] = $arVisual['ANSWER']['PICTURE']['SHOW'];
                    $arItem['DATA']['ANSWER']['PICTURE']['VALUE'] = $arVisual['ANSWER']['PICTURE']['DEFAULT'];
                }

                if (!empty($arVisual['ANSWER']['POSITION']['DEFAULT'])) {
                    $arItem['DATA']['ANSWER']['POSITION']['SHOW'] = $arVisual['ANSWER']['POSITION']['SHOW'];
                    $arItem['DATA']['ANSWER']['POSITION']['VALUE'] = $arVisual['ANSWER']['POSITION']['DEFAULT'];
                }
            }

            if (!empty($arParams['PROPERTY_STAFF']) && !empty($arParams['STAFF_IBLOCK_ID'])) {
                $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_STAFF'],
                    'VALUE'
                ]);

                if (!empty($arProperty)) {
                    if (Type::isArray($arProperty))
                        $arProperty = ArrayHelper::getFirstValue($arProperty);

                    if (!empty($arProperty) && Type::isNumeric($arProperty))
                        $arCollection['STAFF'][] = $arProperty;
                }
            }
        }

        unset($arProperty);
    }

    if (StringHelper::startsWith($arItem['NAME'], Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_MODIFIER_UNREGISTERED_NAME', [
        '{{ID}}' => null
    ])))
        $arItem['NAME'] = Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_MODIFIER_UNREGISTERED_NAME', [
            '{{ID}}' => $arItem['ID']
        ]);
    else
        $arUsers[] = $arItem['NAME'];
}

unset($arItem);

if (!empty($arUsers)) {
    $arUsers = Arrays::from(
        UserTable::getList([
            'select' => [
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME'
            ],
            'filter' => [
                'LOGIN' => $arUsers
            ]
        ])->fetchAll()
    )->indexBy('LOGIN');

    if (!$arUsers->isEmpty()) {
        $arUsers = $arUsers->each(function ($key, &$value) {
            $name = [];

            if (!empty($value['NAME']))
                $name[] = $value['NAME'];

            if (!empty($value['LAST_NAME']))
                $name[] = $value['LAST_NAME'];

            if (!empty($name))
                $value['FULL_NAME'] = implode(' ', $name);
            else
                $value['FULL_NAME'] = Loc::getMessage('C_NEWS_LIST_REVIEWS_LIST_1_MODIFIER_REGISTERED_UNNAMED');
        })->asArray(function ($key, $value) {
            return [
                'key' => $key,
                'value' => $value['FULL_NAME']
            ];
        });

        foreach ($arResult['ITEMS'] as &$arItem) {
            if (ArrayHelper::keyExists($arItem['NAME'], $arUsers))
                $arItem['NAME'] = $arUsers[$arItem['NAME']];
        }

        unset($arItem);
    }
}

if (!empty($arCollection['VIDEO']))
    include(__DIR__.'/modifiers/video.php');

if (!empty($arCollection['STAFF']))
    include(__DIR__.'/modifiers/staff.php');

if (!empty($arCollection['FILES']))
    include(__DIR__.'/modifiers/files.php');

$bIsVideo = !empty($arCollection['VIDEO']);
$bIsStaff = !empty($arCollection['STAFF']);
$bIsFiles = !empty($arCollection['FILES']);

if ($bIsFiles || $bIsVideo) {
    foreach ($arResult['ITEMS'] as &$arItem) {
        if ($arVisual['VIDEO']['SHOW'] && $bIsVideo) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_VIDEO'],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty)) {
                    foreach ($arProperty as $value) {
                        if (ArrayHelper::keyExists($value, $arCollection['VIDEO'])) {
                            $arVideo = $arCollection['VIDEO'][$value];

                            if (!empty($arVideo['PICTURE'])) {
                                if (ArrayHelper::keyExists($arVideo['PICTURE'], $arCollection['FILES']))
                                    $arVideo['PICTURE'] = $arCollection['FILES'][$arVideo['PICTURE']];
                                else
                                    $arVideo['PICTURE'] = null;
                            }

                            if (!$arItem['DATA']['VIDEO']['SHOW'])
                                $arItem['DATA']['VIDEO']['SHOW'] = true;

                            $arItem['DATA']['VIDEO']['VALUES'][] = $arVideo;

                            unset($arVideo);
                        }
                    }

                    unset($value);
                } else {
                    if (ArrayHelper::keyExists($arProperty, $arCollection['VIDEO'])) {
                        $arVideo = $arCollection['VIDEO'][$arProperty];

                        if (!empty($arVideo['PICTURE'])) {
                            if (ArrayHelper::keyExists($arVideo['PICTURE'], $arCollection['FILES']))
                                $arVideo['PICTURE'] = $arCollection['FILES'][$arVideo['PICTURE']];
                            else
                                $arVideo['PICTURE'] = null;
                        }

                        $arItem['DATA']['VIDEO']['SHOW'] = true;
                        $arItem['DATA']['VIDEO']['VALUES'][] = $arVideo;

                        unset($arVideo);
                    }
                }
            }

            if ($arItem['DATA']['VIDEO']['SHOW'] && !empty($arParams['PROPERTY_VIDEO_VIEW'])) {
                $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_VIDEO_VIEW'],
                    'VALUE_XML_ID'
                ]);

                if (!empty($arProperty))
                    $arItem['DATA']['VIDEO']['VIEW'] = true;
            }

            unset($arProperty);
        }

        if ($arVisual['PICTURES']['SHOW'] && $bIsFiles) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_PICTURES'],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty)) {
                    foreach ($arProperty as $value) {
                        if (ArrayHelper::keyExists($value, $arCollection['FILES'])) {
                            if (!$arItem['DATA']['PICTURES']['SHOW'])
                                $arItem['DATA']['PICTURES']['SHOW'] = true;

                            $arItem['DATA']['PICTURES']['VALUES'][] = $arCollection['FILES'][$value];
                        }
                    }

                    unset($value);
                } else {
                    if (ArrayHelper::keyExists($arProperty, $arCollection['FILES'])) {
                        $arItem['DATA']['PICTURES']['SHOW'] = true;
                        $arItem['DATA']['PICTURES']['VALUES'][] = $arCollection['FILES'][$arProperty];
                    }
                }
            }

            unset($arProperty);
        }

        if ($arVisual['FILES']['SHOW'] && $bIsFiles) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_FILES'],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty)) {
                    foreach ($arProperty as $value) {
                        if (ArrayHelper::keyExists($value, $arCollection['FILES'])) {
                            if (!$arItem['DATA']['FILES']['SHOW'])
                                $arItem['DATA']['FILES']['SHOW'] = true;

                            $arItem['DATA']['FILES']['VALUES'][] = $arCollection['FILES'][$value];
                        }
                    }

                    unset($value);
                } else {
                    if (ArrayHelper::keyExists($arProperty, $arCollection['FILES'])) {
                        $arItem['DATA']['FILES']['SHOW'] = true;
                        $arItem['DATA']['FILES']['VALUES'][] = $arCollection['FILES'][$arProperty];
                    }
                }
            }
        }

        if ($bIsStaff) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_STAFF'],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty))
                    $arProperty = ArrayHelper::getFirstValue($arProperty);

                if (!empty($arCollection['STAFF'][$arProperty])) {
                    $arStaff = $arCollection['STAFF'][$arProperty];

                    if (!empty($arStaff['PICTURE']) && Type::isNumeric($arStaff['PICTURE']) && !empty($arCollection['FILES'][$arStaff['PICTURE']]))
                        $arStaff['PICTURE'] = $arCollection['FILES'][$arStaff['PICTURE']];
                    else
                        $arStaff['PICTURE'] = null;

                    if (!empty($arStaff['NAME']))
                        $arItem['DATA']['ANSWER']['NAME'] = $arStaff['NAME'];

                    if (!empty($arStaff['PICTURE'])) {
                        $arItem['DATA']['ANSWER']['PICTURE']['SHOW'] = $arVisual['ANSWER']['PICTURE']['SHOW'];
                        $arItem['DATA']['ANSWER']['PICTURE']['VALUE'] = $arStaff['PICTURE'];
                    }

                    if (!empty($arStaff['POSITION'])) {
                        $arItem['DATA']['ANSWER']['POSITION']['SHOW'] = $arVisual['ANSWER']['POSITION']['SHOW'];
                        $arItem['DATA']['ANSWER']['POSITION']['VALUE'] = $arStaff['POSITION'];
                    }

                    unset($arStaff);
                }
            }

            unset($arProperty);
        }
    }

    unset($arItem);
}

unset($arCollection, $bIsVideo, $bIsStaff, $bIsFiles);

$arResult['VISUAL'] = $arVisual;