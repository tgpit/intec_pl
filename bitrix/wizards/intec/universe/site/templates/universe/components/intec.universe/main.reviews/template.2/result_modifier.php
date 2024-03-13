<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
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
    'PROPERTY_POSITION' => null,
    'PROPERTY_LOGOTYPE' => null,
    'PROPERTY_LINK' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PREVIEW_TRUNCATE_USE' => 'N',
    'PREVIEW_TRUNCATE_WORDS' => 0,
    'COUNTER_SHOW' => 'N',
    'POSITION_SHOW' => 'N',
    'LOGOTYPE_SHOW' => 'N',
    'LOGOTYPE_LINK_USE' => 'N',
    'LOGOTYPE_LINK_BLANK' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N',
    'FOOTER_BUTTON_SHOW' => 'N',
    'FOOTER_BUTTON_TEXT' => null,
    'SEND_USE' => 'N',
    'SEND_TEMPLATE' => null,
    'SEND_TITLE' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PREVIEW' => [
        'TRUNCATE' => [
            'USE' => $arParams['PREVIEW_TRUNCATE_USE'] === 'Y',
            'WORDS' => Type::toInteger($arParams['PREVIEW_TRUNCATE_WORDS'])
        ]
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'POSITION' => [
        'SHOW' => $arParams['POSITION_SHOW'] === 'Y' && !empty($arParams['PROPERTY_POSITION'])
    ],
    'LOGOTYPE' => [
        'SHOW' => $arParams['LOGOTYPE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_LOGOTYPE']),
        'LINK' => [
            'USE' => $arParams['LOGOTYPE_LINK_USE'] === 'Y' && !empty($arParams['PROPERTY_LINK']),
            'BLANK' => $arParams['LOGOTYPE_LINK_BLANK'] === 'Y'
        ]
    ],
    'SLIDER' => [
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ],
    'SEND' => [
        'USE' => $arParams['SEND_USE'] === 'Y' && !empty($arParams['SEND_TEMPLATE'])
    ]
];

if ($arVisual['SLIDER']['AUTO']['TIME'] < 1)
    $arVisual['SLIDER']['AUTO']['TIME'] = 10000;

if ($arVisual['PREVIEW']['TRUNCATE']['WORDS'] < 1)
    $arVisual['PREVIEW']['TRUNCATE']['USE'] = false;

$arImages = [];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null,
        ],
        'POSITION' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'LOGOTYPE' => [
            'SHOW' => false,
            'PICTURE' => null,
            'URL' => [
                'USE' => false,
                'VALUE' => null
            ]
        ]
    ];

    if (!empty($arItem['PREVIEW_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];
    else if (!empty($arItem['DETAIL_TEXT']))
        $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['DETAIL_TEXT'];

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['VALUE'] = trim($arItem['DATA']['PREVIEW']['VALUE']);

    if ($arVisual['PREVIEW']['TRUNCATE']['USE'] && !empty($arItem['DATA']['PREVIEW']['VALUE'])) {
        $words = array_filter(
            explode(' ', Html::stripTags($arItem['DATA']['PREVIEW']['VALUE']))
        );

        if (count($words) > $arVisual['PREVIEW']['TRUNCATE']['WORDS']) {
            $words = ArrayHelper::slice($words, 0, $arVisual['PREVIEW']['TRUNCATE']['WORDS']);

            $lastKey = $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1];

            if (ArrayHelper::isIn(StringHelper::cut($lastKey, -1), ['.', ',', ':', ';']))
                $words[$arVisual['PREVIEW']['TRUNCATE']['WORDS'] - 1] = StringHelper::cut(
                    $lastKey,
                    0,
                    StringHelper::length($lastKey) - 1
                );

            $arItem['DATA']['PREVIEW']['VALUE'] = implode(' ', $words) . '...';

            unset($lastKey);
        }

        unset($words);
    }

    if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
        $arItem['DATA']['PREVIEW']['SHOW'] = true;

    if (!empty($arParams['PROPERTY_POSITION'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_POSITION']
        ]);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['POSITION']['SHOW'] = $arVisual['POSITION']['SHOW'];
                $arItem['DATA']['POSITION']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_LOGOTYPE'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_LOGOTYPE'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            if (Type::isNumeric($arProperty)) {
                $arItem['DATA']['LOGOTYPE']['PICTURE'] = $arProperty;
                $arImages[] = $arProperty;
            }
        }

        unset($arProperty);

        if (!empty($arParams['PROPERTY_LINK'])) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_LINK']
            ]);

            if (!empty($arProperty)) {
                $arProperty = CIBlockFormatProperties::GetDisplayValue(
                    $arItem,
                    $arProperty,
                    false
                );

                if (!empty($arProperty['DISPLAY_VALUE'])) {
                    if (Type::isArray($arProperty['DISPLAY_VALUE']))
                        $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                    $arItem['DATA']['LOGOTYPE']['URL']['USE'] = $arVisual['LOGOTYPE']['LINK']['USE'];
                    $arItem['DATA']['LOGOTYPE']['URL']['VALUE'] = StringHelper::replaceMacros($arProperty['DISPLAY_VALUE'], [
                        'SITE_DIR' => SITE_DIR
                    ]);
                }
            }

            unset($arProperty);
        }
    }
}

unset($arItem);

if (!empty($arImages)) {
    $arImages = Arrays::fromDBResult(CFile::GetList([], [
        '@ID' => implode(',', $arImages)
    ]))->indexBy('ID');

    if (!$arImages->isEmpty()) {
        foreach ($arResult['ITEMS'] as &$arItem) {
            $arImage = $arItem['DATA']['LOGOTYPE']['PICTURE'];

            if (!empty($arItem['DATA']['LOGOTYPE']['PICTURE']) && $arImages->exists($arImage)) {
                $arImage = $arImages->get($arImage);
                $arImage['SRC'] = CFile::GetFileSRC($arImage);

                $arItem['DATA']['LOGOTYPE']['SHOW'] = $arVisual['LOGOTYPE']['SHOW'];
                $arItem['DATA']['LOGOTYPE']['PICTURE'] = $arImage;
            }
        }

        unset($arItem, $arImage);
    }
}

unset($arImages);

if ($arVisual['SEND']['USE'])
    include(__DIR__.'/modifiers/send.php');

$arResult['BLOCKS']['FOOTER'] = [
    'SHOW' => $arParams['FOOTER_BUTTON_SHOW'] === 'Y',
    'TEXT' => !empty($arParams['FOOTER_BUTTON_TEXT']) ? trim($arParams['FOOTER_BUTTON_TEXT']) : null,
    'LINK' => null
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arResult['BLOCKS']['FOOTER']['LINK'] = StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR
    ]);

if (empty($arResult['BLOCKS']['FOOTER']['LINK']))
    $arResult['BLOCKS']['FOOTER']['SHOW'] = false;

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);

unset($arVisual);