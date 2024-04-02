<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'SECTIONS_MODE' => 'N',
    'SECTIONS_ROOT' => 'N',
    'SECTIONS_ROOT_NAME' => null,
    'SECTIONS_ROOT_DESCRIPTION' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PICTURE_SHOW' => 'N',
    'PICTURE_VIEW' => 'squared',
    'PROPERTY_POSITION' => null,
    'POSITION_SHOW' => 'N',
    'PROPERTY_PHONE' => null,
    'PHONE_SHOW' => 'N',
    'PROPERTY_EMAIL' => null,
    'EMAIL_SHOW' => 'N',
    'PROPERTY_SOCIAL_VK' => null,
    'PROPERTY_SOCIAL_FB' => null,
    'PROPERTY_SOCIAL_INST' => null,
    'PROPERTY_SOCIAL_TW' => null,
    'PROPERTY_SOCIAL_SKYPE' => null,
    'SOCIAL_SHOW' => 'N',
    'SOCIAL_SKYPE_ACTION' => 'chat',
    'FORM_ASK_USE' => 'N',
    'FORM_ASK_TEMPLATE' => null,
    'FORM_ASK_ID' => null,
    'FORM_ASK_FIELD' => null,
    'FORM_ASK_TITLE' => null,
    'FORM_ASK_BUTTON_TEXT' => null,
    'FORM_ASK_CONSENT_URL' => null,
    'PREVIEW_SHOW' => 'N',
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_COUNT' => 30
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'SECTIONS' => [
        'MODE' => $arParams['SECTIONS_MODE'] === 'Y',
        'ROOT' => $arParams['SECTIONS_ROOT'] === 'Y'
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'VIEW' => ArrayHelper::fromRange(['squared', 'rounded'], $arParams['PICTURE_VIEW'])
    ],
    'POSITION' => [
        'SHOW' => $arParams['POSITION_SHOW'] === 'Y'
    ],
    'PHONE' => [
        'SHOW' => $arParams['PHONE_SHOW'] === 'Y'
    ],
    'EMAIL' => [
        'SHOW' => $arParams['EMAIL_SHOW'] === 'Y'
    ],
    'SOCIAL' => [
        'SHOW' => $arParams['SOCIAL_SHOW'] === 'Y',
        'SKYPE' => [
            'ACTION' => ArrayHelper::fromRange(['chat', 'call'], $arParams['SOCIAL_SKYPE_ACTION'])
        ]
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y',
        'TRUNCATE' => [
            'USE' => $arParams['PREVIEW_TRUNCATE_USE'] === 'Y',
            'COUNT' => Type::toInteger($arParams['PREVIEW_TRUNCATE_COUNT'])
        ]
    ],
    'NAVIGATION' => [
        'SHOW' => [
            'TOP' => false,
            'BOTTOM' => false,
            'ALWAYS' => $arParams['PAGER_SHOW_ALWAYS']
        ],
        'COUNT' => Type::toInteger($arParams['NEWS_COUNT'])
    ]
];

if ($arVisual['PREVIEW']['TRUNCATE']['USE'] && !$arVisual['PREVIEW']['SHOW'])
    $arVisual['PREVIEW']['TRUNCATE']['USE'] = false;

if ($arVisual['PREVIEW']['TRUNCATE']['COUNT'] < 1)
    $arVisual['PREVIEW']['TRUNCATE']['COUNT'] = 30;

$arSectionsId = [];
$arSocialNames = ['VK', 'FB', 'INST', 'TW', 'SKYPE'];

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!ArrayHelper::isIn($arItem['IBLOCK_SECTION_ID'], $arSectionsId))
        $arSectionsId[] = $arItem['IBLOCK_SECTION_ID'];

    $arItem['DATA'] = [
        'POSITION' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'PHONE' => [
            'SHOW' => false,
            'VALUE' => null,
            'HTML' => null
        ],
        'EMAIL' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'SOCIAL' => [
            'SHOW' => false,
            'VALUES' => [
                'VK' => null,
                'FB' => null,
                'INST' => null,
                'TW' => null,
                'SKYPE' => null
            ]
        ],
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if (!empty($arParams['PROPERTY_POSITION'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_POSITION']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['POSITION'] = [
                    'SHOW' => $arVisual['POSITION']['SHOW'],
                    'VALUE' => $arProperty['DISPLAY_VALUE']
                ];
            }
        }
    }

    if (!empty($arParams['PROPERTY_PHONE'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PHONE']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PHONE'] = [
                    'SHOW' => $arVisual['PHONE']['SHOW'],
                    'VALUE' => $arProperty['DISPLAY_VALUE'],
                    'HTML' => StringHelper::replace($arProperty['DISPLAY_VALUE'], [
                        '(' => '',
                        ')' => '',
                        '-' => '',
                        ' ' => '',
                    ])
                ];
            }
        }
    }

    if (!empty($arParams['PROPERTY_EMAIL'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_EMAIL']
        ]);

        if (!empty($arProperty['VALUE'])) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['EMAIL'] = [
                    'SHOW' => $arVisual['EMAIL']['SHOW'],
                    'VALUE' => $arProperty['DISPLAY_VALUE']
                ];
            }
        }
    }

    foreach ($arSocialNames as $name) {
        if (!empty($arParams['PROPERTY_SOCIAL_'.$name])) {
            $arProperty = ArrayHelper::getValue($arItem, [
                'PROPERTIES',
                $arParams['PROPERTY_SOCIAL_'.$name],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty))
                    $arProperty = ArrayHelper::getFirstValue($arProperty);

                if (!$arItem['DATA']['SOCIAL']['SHOW'] && $arVisual['SOCIAL']['SHOW'])
                    $arItem['DATA']['SOCIAL']['SHOW'] = true;

                $arItem['DATA']['SOCIAL']['VALUES'][$name] = $arProperty;
            }
        }
    }

    if (!empty($arItem['PREVIEW_TEXT'])) {
        $sPreview = $arItem['PREVIEW_TEXT'];

        if ($arVisual['PREVIEW']['TRUNCATE']['USE']) {
            $sPreview = Html::stripTags($sPreview);

            if (!empty($sPreview)) {
                $sPreview = preg_split(
                    '/[\s]+/',
                    $sPreview,
                    $arVisual['PREVIEW']['TRUNCATE']['COUNT'] + 1
                );

                if (count($sPreview) > $arVisual['PREVIEW']['TRUNCATE']['COUNT']) {
                    $sPreview = ArrayHelper::slice(
                        $sPreview,
                        0,
                        $arVisual['PREVIEW']['TRUNCATE']['COUNT']
                    );

                    $sPreview = implode(' ', $sPreview).'...';
                } else {
                    $sPreview = $arItem['PREVIEW_TEXT'];
                }
            }
        }

        $arItem['DATA']['PREVIEW']['VALUE'] = $sPreview;

        unset($sPreview);

        if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
            $arItem['DATA']['PREVIEW']['SHOW'] = $arVisual['PREVIEW']['SHOW'];
    }
}

unset($arItem, $arProperty, $arSocialNames, $name);

if ($arVisual['SECTIONS']['MODE'])
    include(__DIR__.'/modifiers/sections.php');

unset($arSectionsId);

$bFormsAvailable = false;

if (Loader::includeModule('form') || Loader::includeModule('intec.startshop'))
    $bFormsAvailable = true;

$arForm = [
    'ASK' => [
        'USE' => $bFormsAvailable && $arParams['FORM_ASK_USE'] === 'Y',
        'TEMPLATE' => $arParams['FORM_ASK_TEMPLATE'],
        'ID' => $arParams['FORM_ASK_ID'],
        'FIELD' => $arParams['FORM_ASK_FIELD'],
        'TITLE' => $arParams['FORM_ASK_TITLE'],
        'BUTTON' => [
            'TEXT' => $arParams['FORM_ASK_BUTTON_TEXT']
        ],
        'CONSENT' => [
            'URL' => StringHelper::replaceMacros($arParams['FORM_ASK_CONSENT_URL'], [
                'SITE_DIR' => SITE_DIR
            ])
        ]
    ]
];

if ($arForm['ASK']['USE'] && (empty($arForm['ASK']['TEMPLATE']) || empty($arForm['ASK']['ID'])))
    $arForm['ASK']['USE'] = false;

$arResult['FORM'] = $arForm;

unset($bFormsAvailable, $arForm);

$arNavigation = [];

if (!empty($arResult['NAV_RESULT'])) {
    $arNavigation = [
        'PAGE' => [
            'COUNT' => $arResult['NAV_RESULT']->NavPageCount,
            'NUMBER' => $arResult['NAV_RESULT']->NavPageNomer,
        ],
        'NUMBER' => $arResult['NAV_RESULT']->NavNum
    ];

    if ($arVisual['NAVIGATION']['SHOW']['ALWAYS']) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    } else if ($arVisual['NAVIGATION']['COUNT'] > 0 && $arNavigation['PAGE']['COUNT'] > 1) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    }
} else {
    $arVisual['NAVIGATION']['SHOW']['TOP'] = false;
    $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = false;
}

$arResult['NAVIGATION'] = $arNavigation;

unset($arNavigation);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);