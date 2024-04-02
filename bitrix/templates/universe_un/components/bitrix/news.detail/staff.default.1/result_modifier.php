<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PICTURE_SHOW' => 'N',
    'NAME_SHOW' => 'N',
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
    'PROPERTY_DESCRIPTION_HEADER' => null,
    'DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_HEADER_SHOW' => 'N',
    'BUTTON_BACK_SHOW' => 'N',
    'BUTTON_BACK_TEXT' => null,
    'PROPERTY_PROJECTS' => null,
    'PROJECTS_IBLOCK_TYPE' => null,
    'PROJECTS_IBLOCK_ID' => null,
    'PROJECTS_TEMPLATE' => null,
    'PROPERTY_REVIEWS' => null,
    'REVIEWS_IBLOCK_TYPE' => null,
    'REVIEWS_IBLOCK_ID' => null,
    'REVIEWS_TEMPLATE' => null
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y'
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
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
        'HEADER' => [
            'SHOW' => $arParams['DESCRIPTION_HEADER_SHOW'] === 'Y'
        ]
    ],
    'BUTTON' => [
        'BACK' => [
            'SHOW' => $arParams['BUTTON_BACK_SHOW'] === 'Y',
            'TEXT' => $arParams['BUTTON_BACK_TEXT']
        ]
    ],
    'PROJECTS' => [
        'SHOW' => !empty($arParams['PROPERTY_PROJECTS']) &&
            !empty($arParams['PROJECTS_IBLOCK_TYPE']) &&
            !empty($arParams['PROJECTS_IBLOCK_ID']) &&
            !empty($arParams['PROJECTS_TEMPLATE'])
    ],
    'REVIEWS' => [
        'SHOW' => !empty($arParams['PROPERTY_REVIEWS']) &&
            !empty($arParams['REVIEWS_IBLOCK_TYPE']) &&
            !empty($arParams['REVIEWS_IBLOCK_ID']) &&
            !empty($arParams['REVIEWS_TEMPLATE'])
    ]
];

$arResult['DATA'] = [
    'POSITION' => [
        'SHOW' => false,
        'VALUE' => null
    ],
    'PHONE' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'EMAIL' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'SOCIAL' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'DESCRIPTION' => [
        'HEADER' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ],
    'PROJECTS' => [
        'SHOW' => false,
        'VALUES' => []
    ],
    'REVIEWS' => [
        'SHOW' => false,
        'VALUES' => []
    ]
];

if (!empty($arParams['PROPERTY_POSITION'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_POSITION']
    ]);

    if (!empty($arProperty['VALUE'])) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['POSITION'] = [
                'SHOW' => $arVisual['POSITION']['SHOW'],
                'VALUE' => $arProperty['DISPLAY_VALUE']
            ];
        }
    }
}

if (!empty($arParams['PROPERTY_PHONE'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_PHONE']
    ]);

    if (!empty($arProperty['VALUE'])) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE'])) {
                foreach ($arProperty['DISPLAY_VALUE'] as $sValue)
                    if (!empty($sValue))
                        $arResult['DATA']['PHONE']['VALUES'][] = [
                            'VALUE' => $sValue,
                            'HTML' => StringHelper::replace($sValue, [
                                '(' => '',
                                ')' => '',
                                '-' => '',
                                ' ' => '',
                            ])
                        ];
            } else {
                $arResult['DATA']['PHONE']['VALUES'][] = [
                    'VALUE' => $arProperty['DISPLAY_VALUE'],
                    'HTML' => StringHelper::replace($arProperty['DISPLAY_VALUE'], [
                        '(' => '',
                        ')' => '',
                        '-' => '',
                        ' ' => '',
                    ])
                ];
            }

            if (!empty($arResult['DATA']['PHONE']['VALUES']))
                $arResult['DATA']['PHONE']['SHOW'] = $arVisual['PHONE']['SHOW'];
        }
    }
}

if (!empty($arParams['PROPERTY_EMAIL'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_EMAIL']
    ]);

    if (!empty($arProperty['VALUE'])) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE'])) {
                foreach ($arProperty['DISPLAY_VALUE'] as $sValue)
                    if (!empty($sValue))
                        $arResult['DATA']['EMAIL']['VALUES'][] = $sValue;
            } else {
                $arResult['DATA']['EMAIL']['VALUES'][] = $arProperty['DISPLAY_VALUE'];
            }

            if (!empty($arResult['DATA']['EMAIL']['VALUES']))
                $arResult['DATA']['EMAIL']['SHOW'] = $arVisual['EMAIL']['SHOW'];
        }
    }
}

$arSocialNames = [
    'VK',
    'FB',
    'INST',
    'TW',
    'SKYPE'
];

foreach ($arSocialNames as $name) {
    if (!empty($arParams['PROPERTY_SOCIAL_'.$name])) {
        $arProperty = ArrayHelper::getValue($arResult, [
            'PROPERTIES',
            $arParams['PROPERTY_SOCIAL_'.$name],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!$arResult['DATA']['SOCIAL']['SHOW'] && $arVisual['SOCIAL']['SHOW'])
                $arResult['DATA']['SOCIAL']['SHOW'] = true;

            $arResult['DATA']['SOCIAL']['VALUES'][$name] = $arProperty;
        }
    }
}

if (!empty($arParams['PROPERTY_DESCRIPTION_HEADER'])) {
    $arProperty = ArrayHelper::getValue($arResult, [
        'PROPERTIES',
        $arParams['PROPERTY_DESCRIPTION_HEADER']
    ]);

    if (!empty($arProperty['VALUE'])) {
        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $arProperty,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

            $arResult['DATA']['DESCRIPTION']['HEADER']['VALUE'] = $arProperty['DISPLAY_VALUE'];

            if (!empty($arResult['DATA']['DESCRIPTION']['HEADER']['VALUE']))
                $arResult['DATA']['DESCRIPTION']['HEADER']['SHOW'] = $arVisual['DESCRIPTION']['HEADER']['SHOW'];
        }
    }
}

if ($arVisual['PROJECTS']['SHOW']) {
    $arResult['DATA']['PROJECTS']['VALUES'] = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_PROJECTS'],
        'VALUE'
    ]);

    if (!empty($arResult['DATA']['PROJECTS']['VALUES'])) {
        $arResult['DATA']['PROJECTS']['SHOW'] = $arVisual['PROJECTS']['SHOW'];

        include(__DIR__.'/modifiers/projects.php');
    }
}

if ($arVisual['REVIEWS']['SHOW']) {
    $arResult['DATA']['REVIEWS']['VALUES'] = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_REVIEWS'],
        'VALUE'
    ]);

    if (!empty($arResult['DATA']['REVIEWS']['VALUES'])) {
        $arResult['DATA']['REVIEWS']['SHOW'] = $arVisual['REVIEWS']['SHOW'];

        include(__DIR__.'/modifiers/reviews.php');
    }
}

unset($arProperty);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$bFormAvailable = false;

if (Loader::includeModule('form') || Loader::includeModule('intec.startshop'))
    $bFormAvailable = true;

$arForm = [
    'ASK' => [
        'USE' => $bFormAvailable && $arParams['FORM_ASK_USE'] === 'Y',
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

unset($bFormAvailable, $arForm);