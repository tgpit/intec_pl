<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
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
    'PROPERTY_DISPLAY' => [],
    'PROPERTY_PREVIEW' => null,
    'TABS_USE' => 'N',
    'TABS_POSITION' => 'center',
    'PICTURE_SHOW' => 'N',
    'PREVIEW_SHOW' => 'N',
    'DISPLAY_SHOW' => 'N',
    'DETAIL_SHOW' => 'N',
    'DETAIL_TEXT' => null,
    'DETAIL_BLANK' => 'N',
    'MORE_SHOW' => 'N',
    'MORE_TEXT' => null,
    'MORE_BLANK' => 'N',
    'PRODUCTS_SHOW' => 'N',
    'PROPERTY_PRODUCTS' => null,
    'PRODUCTS_IBLOCK_TYPE' => null,
    'PRODUCTS_IBLOCK_ID' => null,
    'PRODUCTS_ELEMENTS_COUNT' => null,
    'PRODUCTS_FILTER' => 'collectionsFilter',
    'PRODUCTS_PRICE_CODE' => [],
    'PRODUCTS_CONVERT_CURRENCY' => 'N',
    'PRODUCTS_CURRENCY_ID' => null,
    'PRODUCTS_PRICE_VAT_INCLUDE' => 'Y',
    'PRODUCTS_SHOW_PRICE_COUNT' => 1,
    'PRODUCTS_SORT_BY' => 'SORT',
    'PRODUCTS_ORDER_BY' => 'ASC',
    'PRODUCTS_SECTION_URL' => null,
    'PRODUCTS_DETAIL_URL' => null,
], $arParams);

if (!defined('EDITOR') && $arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (Type::isArray($arParams['PROPERTY_DISPLAY']))
    $arParams['PROPERTY_DISPLAY'] = array_filter($arParams['PROPERTY_DISPLAY']);
else
    $arParams['PROPERTY_DISPLAY'] = [];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'TABS' => [
        'USE' => $arParams['TABS_USE'] === 'Y' && !empty($arResult['SECTIONS']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['TABS_POSITION'])
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'DISPLAY' => [
        'SHOW' => $arParams['DISPLAY_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DISPLAY'])
    ],
    'DETAIL'=> [
        'SHOW' => $arParams['DETAIL_SHOW'] === 'Y',
        'TEXT' => !empty($arParams['DETAIL_TEXT']) ? $arParams['DETAIL_TEXT'] : Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TEMPLATE_DETAIL_TEXT_DEFAULT'),
        'BLANK' => $arParams['DETAIL_BLANK'] === 'Y'
    ],
    'PRODUCTS' => [
        'SHOW' => $arParams['PRODUCTS_SHOW'] === 'Y' && !empty($arParams['PRODUCTS_IBLOCK_ID']) && !empty($arParams['PROPERTY_PRODUCTS'])
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PICTURE' => [
            'SHOW' => false,
            'VALUE' => []
        ],
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'DISPLAY' => [
            'SHOW' => false,
            'VALUES' => []
        ],
        'PRODUCTS' => [
            'SHOW' => false,
            'VALUES' => []
        ]
    ];

    if ($arVisual['PICTURE']['SHOW']) {
        if (!empty($arItem['PREVIEW_PICTURE']))
            $arItem['DATA']['PICTURE']['VALUE'] = $arItem['PREVIEW_PICTURE'];
        else if (!empty($arItem['DETAIL_PICTURE']))
            $arItem['DATA']['PICTURE']['VALUE'] = $arItem['DETAIL_PICTURE'];

        if (!empty($arItem['DATA']['PICTURE']['VALUE']))
            $arItem['DATA']['PICTURE']['SHOW'] = true;
    }

    if ($arVisual['PREVIEW']['SHOW']) {
        if (!empty($arParams['PROPERTY_PREVIEW'])) {
            $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PREVIEW']);

            if (!empty($arProperty['VALUE'])) {
                $arProperty = CIBlockFormatProperties::GetDisplayValue(
                    $arItem,
                    $arProperty,
                    false
                );

                if (!empty($arProperty['DISPLAY_VALUE'])) {
                    if (Type::isArray($arProperty['DISPLAY_VALUE']))
                        $arItem['DATA']['PREVIEW']['VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);
                    else
                        $arItem['DATA']['PREVIEW']['VALUE'] = $arProperty['DISPLAY_VALUE'];
                }
            }

            unset($arProperty);
        }

        if (empty($arItem['DATA']['PREVIEW']['VALUE']) && !empty($arItem['PREVIEW_TEXT']))
            $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];

        if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
            $arItem['DATA']['PREVIEW']['SHOW'] = true;
    }

    if ($arVisual['DISPLAY']['SHOW']) {
        foreach ($arParams['PROPERTY_DISPLAY'] as $property) {
            if (!ArrayHelper::keyExists($property, $arItem['PROPERTIES']))
                continue;

            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arItem['PROPERTIES'][$property],
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arItem['DATA']['DISPLAY']['VALUES'][] = [
                        'NAME' => $arProperty['NAME'],
                        'VALUE' => ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE'])
                    ];
                else
                    $arItem['DATA']['DISPLAY']['VALUES'][] = [
                        'NAME' => $arProperty['NAME'],
                        'VALUE' => $arProperty['DISPLAY_VALUE']
                    ];
            }

            unset($arProperty);
        }

        unset($property);

        if (!empty($arItem['DATA']['DISPLAY']['VALUES']))
            $arItem['DATA']['DISPLAY']['SHOW'] = true;
    }

    if ($arVisual['PRODUCTS']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_PRODUCTS'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty)) {
                foreach ($arProperty as $value)
                    $arItem['DATA']['PRODUCTS']['VALUES'][] = $value;

                unset($value);
            } else {
                $arItem['DATA']['PRODUCTS']['VALUES'][] = $arProperty;
            }
        }

        unset($arProperty);

        if (!empty($arItem['DATA']['PRODUCTS']['VALUES']))
            $arItem['DATA']['PRODUCTS']['SHOW'] = true;
    }
}

unset($arItem);

$arResult['BLOCKS']['MORE'] = [
    'SHOW' => false,
    'TEXT' => $arParams['MORE_TEXT'],
    'URL' => null,
    'BLANK' => $arParams['MORE_BLANK'] === 'Y',
    'POSITION' => 'center'
];

$sListPage = ArrayHelper::getValue($arParams, 'LIST_PAGE_URL');

if (!empty($sListPage)) {
    $sListPage = trim($sListPage);
    $sListPage = StringHelper::replaceMacros($sListPage, [
        'SITE_DIR' => SITE_DIR,
        'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
        'TEMPLATE_PATH' => $this->GetFolder().'/'
    ]);
} else {
    $sListPage = ArrayHelper::getFirstValue($arResult['ITEMS']);
    $sListPage = $sListPage['LIST_PAGE_URL'];
}

$arResult['BLOCKS']['MORE']['URL'] = $sListPage;

if (
    $arParams['MORE_SHOW'] === 'Y' &&
    !empty($arResult['BLOCKS']['MORE']['TEXT']) &&
    !empty($arResult['BLOCKS']['MORE']['URL'])
)
    $arResult['BLOCKS']['MORE']['SHOW'] = true;

if ($arVisual['PRODUCTS']['SHOW']) {
    include(__DIR__.'/modifiers/products.php');
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);