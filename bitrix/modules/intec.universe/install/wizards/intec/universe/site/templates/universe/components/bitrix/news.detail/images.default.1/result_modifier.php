<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\template\Properties;
use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 */

if (!CModule::IncludeModule('iblock') || !CModule::IncludeModule('intec.core'))
    return;

$arParams['CACHE_TYPE'] = $arParams['CACHE_TYPE_ORIGINAL'];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PRODUCTS' => [
        'SHOW' => $arParams['PRODUCTS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRODUCTS'])
    ],
    'SHARES' => [
        'SHOW' => $arParams['SHARES_SHOW'] === 'Y'
    ],
    'PROPERTY' => [
        'SHOW' => $arParams['PROPERTY_SHOW'] === 'Y' && !empty($arResult['DISPLAY_PROPERTIES']),
        'VALUES' => []
    ],
    'SLIDER' => [
        'SHOW' => false
    ]
];

$arResult['REGIONALITY'] = [
    'USE' => $arParams['REGIONALITY_USE'] === 'Y',
    'FILTER' => [
        'USE' => $arParams['REGIONALITY_FILTER_USE'] === 'Y',
        'PROPERTY' => $arParams['REGIONALITY_FILTER_PROPERTY'],
        'STRICT' => $arParams['REGIONALITY_FILTER_STRICT'] === 'Y'
    ]
];

if (empty($arParams['PRODUCTS_IBLOCK_ID']) || !Loader::includeModule('intec.regionality'))
    $arResult['REGIONALITY']['USE'] = false;

if (empty($arResult['REGIONALITY']['FILTER']['PROPERTY']))
    $arResult['REGIONALITY']['FILTER']['USE'] = false;

if (!empty($arResult['IBLOCK_SECTION_ID'])) {
    $rsItems = CIBlockElement::GetList([
            'SORT' => 'ASC'
        ], [
            'IBLOCK_SECTION_ID' => $arResult['IBLOCK_SECTION_ID'],
            'ACTIVE' => 'Y'
        ],
        false,
        false, [
            'ID',
            'IBLOCK_ID',
            'DETAIL_PICTURE',
            'PREVIEW_PICTURE',
            'DETAIL_PAGE_URL'
        ]
    );

    $rsItems->SetUrlTemplates($arParams['DETAIL_URL']);

    while($arItem = $rsItems->GetNext()) {
        $arResult['ITEMS'][] = $arItem;
    }

    unset($arItem, $rsItems);
}

if (!empty($arResult['ITEMS']) && count($arResult['ITEMS']) > 1) {
    $arVisual['SLIDER']['SHOW'] = true;
}

if ($arVisual['PROPERTY']['SHOW']) {
    foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
        if ($property['CODE'] == $arParams['PROPERTY_PRODUCTS'])
            continue;

        $arProperty = CIBlockFormatProperties::GetDisplayValue(
            $arResult,
            $property,
            false
        );

        if (!empty($arProperty['DISPLAY_VALUE'])) {
            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arVisual['PROPERTY']['VALUES'][] = [
                    'NAME' => $arProperty['NAME'],
                    'VALUE' => ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE'])
                ];
            else
                $arVisual['PROPERTY']['VALUES'][] = [
                    'NAME' => $arProperty['NAME'],
                    'VALUE' => $arProperty['DISPLAY_VALUE']
                ];
        }

        unset($arProperty);
    }

    unset($property);

    if (!empty($arVisual['PROPERTY']['VALUES']))
        $arVisual['PROPERTY']['SHOW'] = true;
}

if ($arVisual['SHARES']['SHOW'])
    include(__DIR__.'/modifiers/shares.php');

if ($arVisual['PRODUCTS']['SHOW'])
    include(__DIR__.'/modifiers/products.php');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);