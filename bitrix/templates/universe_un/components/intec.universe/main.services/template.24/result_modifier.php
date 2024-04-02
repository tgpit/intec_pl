<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 4,
    'PRICE_SHOW' => 'N',
    'PROPERTY_PRICE' => null,
    'PRICE_FORMAT_USE' => 'N',
    'CURRENCY' => null,
    'PROPERTY_CURRENCY' => null,
    'PRICE_FORMAT' => null,
    'PROPERTY_PRICE_FORMAT' => null,
    'PRICE_OLD_SHOW' => 'N',
    'PROPERTY_PRICE_OLD' => null,
    'SECTION_ELEMENTS_COUNT' => null,
    'LINK_USE' => 'N',
    'LINK_PICTURE_EFFECT_ZOOM' => 'N',
    'WHOLE_ELEMENT_LINK_USE' => 'N',
    'LINK_COLORING_NAME' => 'N',
    'ALL_ELEMENT_LINK_USE' => 'N',
    'MOBILE_MENU_COLUMN_USE' => 'N',
    'FOOTER_SHOW' => 'N',
    'FOOTER_POSITION' => 'center',
    'FOOTER_BUTTON_SHOW' => 'N',
    'FOOTER_BUTTON_TEXT' => null,
    'FOOTER_BUTTON_LINK' => null,
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PICTURE' => [
        'EFFECT' => $arParams['LINK_PICTURE_EFFECT_ZOOM'] === 'Y'
    ],
    'COLUMNS' => ArrayHelper::fromRange([4, 2, 3], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'WHOLE' => $arParams['WHOLE_ELEMENT_LINK_USE'] === 'Y',
        'COLORING' => $arParams['LINK_COLORING_NAME'] === 'N'
    ],
    'MOBILE' => [
        'MENU' => [
            'USE' => $arParams['MOBILE_MENU_COLUMN_USE'] === 'Y'
        ]
    ],
    'PRICE' => [
        'CURRENT' => [
            'SHOW' => $arParams['PRICE_SHOW'] === 'Y'
        ],
        'OLD' => [
            'SHOW' => $arParams['PRICE_OLD_SHOW'] === 'Y'
        ],
        'FORMAT' => [
            'USE' => $arParams['PRICE_FORMAT_USE'] === 'Y'
        ],
        'CURRENCY' => $arParams['CURRENCY']
    ],
    'ELEMENTS' => [
        'COUNT' => !empty(trim($arParams['SECTION_ELEMENTS_COUNT'])) ? $arParams['SECTION_ELEMENTS_COUNT'] : 0,
        'BUTTON' => [
            'SHOW' => $arParams['ELEMENT_BUTTON_SHOW'] === 'Y' && !empty($arParams['ELEMENT_BUTTON_TEXT']),
            'TEXT' => $arParams['ELEMENT_BUTTON_TEXT']
        ]
    ]
];

$arSectionsItems = [];
$arSectionFilter = [
    'ID' => []
];

foreach ($arResult['ITEMS'] as $key => $arItem) {

    $bSectionExist = false;
    $sDescription = null;

    $arData = [
        'DESCRIPTION' => null,
        'PRICE' => [
            'CURRENT' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null,
            ],
            'OLD' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null,
            ],
            'CURRENCY' => null,
            'FORMAT' => null
        ]
    ];

    $sSectionId = $arItem['IBLOCK_SECTION_ID'];
    $arSectionsItems[$sSectionId][$key] = $arItem;

    if (!empty($arItem['PREVIEW_TEXT']))
        $arData['DESCRIPTION'] = $arItem['PREVIEW_TEXT'];
    else
        $arData['DESCRIPTION'] = $arItem['DETAIL_TEXT'];

    if (!empty($arData['DESCRIPTION']))
        $arData['DESCRIPTION'] = strip_tags($arData['DESCRIPTION']);

    if (!empty($arParams['PROPERTY_CURRENCY'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_CURRENCY'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!empty($arProperty)) {
                $arData['PRICE']['CURRENCY'] = trim($arProperty);
            }
        }
    } elseif (!empty($arParams['CURRENCY'])) {
        $arData['PRICE']['CURRENCY'] = trim($arParams['CURRENCY']);
    }

    if (!empty($arParams['PROPERTY_PRICE'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!empty($arProperty)) {
                $arData['PRICE']['CURRENT']['SHOW'] = true;
                $arData['PRICE']['CURRENT']['VALUE'] = number_format($arProperty, 0, ',', ' ');

                if ($arVisual['PRICE']['FORMAT']['USE']) {
                    if (!empty($arParams['PROPERTY_PRICE_FORMAT'])) {
                        $arProperty = ArrayHelper::getValue($arItem, [
                            'PROPERTIES',
                            $arParams['PROPERTY_PRICE_FORMAT'],
                            'VALUE'
                        ]);

                        if (!empty($arProperty)) {
                            if (Type::isArray($arProperty))
                                $arProperty = ArrayHelper::getFirstValue($arProperty);

                            if (!empty($arProperty)) {
                                $arData['PRICE']['FORMAT'] = $arProperty;
                                $arData['PRICE']['CURRENT']['PRINT'] = trim(StringHelper::replaceMacros(
                                    $arData['PRICE']['FORMAT'],
                                    [
                                        'VALUE' => $arData['PRICE']['CURRENT']['VALUE'],
                                        'CURRENCY' => $arData['PRICE']['CURRENCY']
                                    ]
                                ));
                            }
                        }
                    } elseif (!empty($arParams['PRICE_FORMAT'])) {
                        $arData['PRICE']['FORMAT'] = $arParams['PRICE_FORMAT'];
                        $arData['PRICE']['CURRENT']['PRINT'] = trim(StringHelper::replaceMacros(
                            $arData['PRICE']['FORMAT'],
                            [
                                'VALUE' => $arData['PRICE']['CURRENT']['VALUE'],
                                'CURRENCY' => $arData['PRICE']['CURRENCY']
                            ]
                        ));
                    } else {
                        $arData['PRICE']['CURRENT']['PRINT'] = $arData['PRICE']['CURRENT']['VALUE'] . ' ' . $arData['PRICE']['CURRENCY'];
                    }
                } else {
                    if (!empty($arData['PRICE']['CURRENCY'])) {
                        $arData['PRICE']['CURRENT']['PRINT'] = $arData['PRICE']['CURRENT']['VALUE'] . ' ' . $arData['PRICE']['CURRENCY'];
                    } else {
                        $arData['PRICE']['CURRENT']['PRINT'] = $arData['PRICE']['CURRENT']['VALUE'];
                    }
                }
            }
        }
    }

    if (!empty($arParams['PROPERTY_PRICE_OLD'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_OLD'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (!empty($arProperty)) {
                $arData['PRICE']['OLD']['SHOW'] = true;
                $arData['PRICE']['OLD']['VALUE'] = number_format($arProperty, 0, ',', ' ');

                if ($arVisual['PRICE']['FORMAT']['USE']) {
                    if (!empty($arParams['PROPERTY_PRICE_FORMAT'])) {
                        $arProperty = ArrayHelper::getValue($arItem, [
                            'PROPERTIES',
                            $arParams['PROPERTY_PRICE_FORMAT'],
                            'VALUE'
                        ]);

                        if (!empty($arProperty)) {
                            if (Type::isArray($arProperty))
                                $arProperty = ArrayHelper::getFirstValue($arProperty);

                            if (!empty($arProperty)) {
                                $arData['PRICE']['FORMAT'] = $arProperty;
                                $arData['PRICE']['OLD']['PRINT'] = trim(StringHelper::replaceMacros(
                                    $arData['PRICE']['FORMAT'],
                                    [
                                        'VALUE' => $arData['PRICE']['OLD']['VALUE'],
                                        'CURRENCY' => $arData['PRICE']['CURRENCY']
                                    ]
                                ));
                            }
                        }
                    } elseif (!empty($arParams['PRICE_FORMAT'])) {
                        $arData['PRICE']['FORMAT'] = $arParams['PRICE_FORMAT'];
                        $arData['PRICE']['OLD']['PRINT'] = trim(StringHelper::replaceMacros(
                            $arData['PRICE']['FORMAT'],
                            [
                                'VALUE' => $arData['PRICE']['OLD']['VALUE'],
                                'CURRENCY' => $arData['PRICE']['CURRENCY']
                            ]
                        ));
                    } else {
                        $arData['PRICE']['OLD']['PRINT'] = $arData['PRICE']['OLD']['VALUE'] . ' ' . $arData['PRICE']['CURRENCY'];
                    }
                } else {
                    if (!empty($arData['PRICE']['CURRENCY'])) {
                        $arData['PRICE']['OLD']['PRINT'] = $arData['PRICE']['OLD']['VALUE'] . ' ' . $arData['PRICE']['CURRENCY'];
                    } else {
                        $arData['PRICE']['OLD']['PRINT'] = $arData['PRICE']['OLD']['VALUE'];
                    }
                }
            }
        }
    }

    $arSectionsItems[$sSectionId][$key]['DATA'] = $arData;

    foreach ($arSectionFilter['ID'] as $sValue) {
        if ($sValue == $sSectionId)
            $bSectionExist = true;
    }

    if (!$bSectionExist)
        $arSectionFilter['ID'][] = $sSectionId;
}

$arSections = Arrays::fromDBResult(CIBlockSection::GetList(Array("SORT"=>"ASC"), $arSectionFilter))->indexBy('ID')->asArray();

$arResult['SECTIONS'] = $arSections;
$arResult['SECTION_ITEMS'] = $arSectionsItems;

$arFooter = [
    'SHOW' => $arParams['FOOTER_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['FOOTER_POSITION']),
    'BUTTON' => [
        'SHOW' => $arParams['FOOTER_BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['FOOTER_BUTTON_TEXT'],
        'LINK' => null
    ]
];

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arFooter['BUTTON']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

if (empty($arFooter['BUTTON']['TEXT']) || empty($arFooter['BUTTON']['LINK']))
    $arFooter['BUTTON']['SHOW'] = false;

if (!$arFooter['BUTTON']['SHOW'])
    $arFooter['SHOW'] = false;

$arResult['BLOCKS']['FOOTER'] = $arFooter;

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arFooter, $arMacros, $arVisual);